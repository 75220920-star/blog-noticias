import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import { login, db } from '../support/helpers.js';

test('A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas', async ({ page }) => {
  const article = db("SELECT slug FROM articles WHERE status='publicado' AND deleted_at IS NULL LIMIT 1")[0];
  const paths = ['/', '/noticias', '/categoria/actualidad', '/etiqueta/comunidad', '/buscar?q=comunidad', '/buscar?q=nuncaexistezzz', '/noticia/' + article.slug, '/login', '/registro', '/recuperar', '/restablecer/token-invalido', '/confirmar/token-invalido', '/boletin/confirmar/token-invalido', '/accesibilidad', '/no-existe'];
  for (const path of paths) {
    await page.goto(path);
    await expect(page.locator('html')).toHaveAttribute('lang', 'es');
    await expect(page.locator('h1')).toHaveCount(1);
    const result = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa']).analyze();
    expect(result.violations, path).toEqual([]);
  }
});

test('A-TEC-01 A-TEC-05: axe en todas las plantillas del panel y página 403', async ({ page }) => {
  await login(page);
  const paths = ['/admin', '/admin/noticias', '/admin/noticias?trash=1', '/admin/noticias/nueva', '/admin/noticias/1/editar', '/admin/categorias', '/admin/etiquetas', '/admin/usuarios', '/admin/usuarios/nuevo', '/admin/usuarios/1/editar', '/admin/comentarios', '/admin/configuracion', '/admin/auditoria'];
  for (const path of paths) {
    await page.goto(path);
    await expect(page.locator('h1')).toHaveCount(1);
    const result = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa']).analyze();
    expect(result.violations, path).toEqual([]);
  }
  await page.getByRole('button', { name: 'Cerrar sesión' }).click();
  await login(page, 'lector1');
  await page.goto('/admin');
  expect((await new AxeBuilder({ page }).analyze()).violations).toEqual([]);
});

test('A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape', async ({ page }) => {
  await page.goto('/');
  await page.keyboard.press('Tab');
  await expect(page.getByRole('link', { name: 'Saltar al contenido' })).toBeFocused();
  await page.keyboard.press('Enter');
  await expect(page.locator('main')).toBeFocused();
  await login(page);
  await page.goto('/admin/noticias');
  const trigger = page.getByRole('button', { name: /^Eliminar/ }).first();
  await trigger.focus();
  await page.keyboard.press('Enter');
  await expect(page.locator('#confirm-dialog')).toBeVisible();
  await expect(page.getByRole('button', { name: 'Cancelar', exact: true })).toBeFocused();
  await page.keyboard.press('Escape');
  await expect(page.locator('#confirm-dialog')).not.toBeVisible();
  await expect(trigger).toBeFocused();
});

test('A-VIS-05 A-VIS-06 A-VIS-16 E-12: modo oscuro y contraste de lectura', async ({ page }) => {
  await page.emulateMedia({ colorScheme: 'dark' });
  const article = db("SELECT slug FROM articles WHERE status='publicado' AND deleted_at IS NULL LIMIT 1")[0];
  await page.goto('/noticia/' + article.slug);
  expect((await new AxeBuilder({ page }).analyze()).violations).toEqual([]);
  const contrast = await page.locator('.article-body').evaluate(element => {
    const style = getComputedStyle(element);
    const lum = value => {
      const rgb = value.match(/\d+/g).slice(0, 3).map(Number).map(c => { c /= 255; return c <= .04045 ? c / 12.92 : ((c + .055) / 1.055) ** 2.4; });
      return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
    };
    const a = lum(style.color), b = lum(style.backgroundColor);
    return (Math.max(a, b) + .05) / (Math.min(a, b) + .05);
  });
  expect(contrast).toBeGreaterThanOrEqual(7);
});
