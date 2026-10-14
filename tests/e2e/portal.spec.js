import { test, expect } from '@playwright/test';
import { db } from '../support/helpers.js';

test('F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404', async ({ page }) => {
  await page.goto('/');
  await expect(page.locator('.lead-story')).toBeVisible();
  await page.goto('/categoria/tecnologia');
  const cards = page.locator('.news-card');
  expect(await cards.count()).toBeGreaterThan(0);
  for (const label of await cards.locator('.category-label').allTextContents()) expect(label).toBe('Tecnología');
  await cards.locator('h3 a').first().click();
  await expect(page.locator('.article-header h1')).toBeVisible();
  await expect(page.locator('.article-image img')).toHaveAttribute('alt', /.+/);
  await expect(page.locator('meta[property="og:type"]')).toHaveAttribute('content', 'article');
  await expect(page.getByRole('link', { name: 'Compartir en Facebook' })).toHaveAttribute('href', /sharer.php\?u=/);
  await page.goto('/etiqueta/comunidad');
  await expect(page.locator('h1')).toHaveText('Comunidad');
  expect((await page.goto('/noticia/no-existe')).status()).toBe(404);
});

test('F-PUB-02: paginación inválida se normaliza', async ({ page }) => {
  for (const value of ['0', 'abc', '9999', '-1', '1%20OR%201=1']) {
    expect((await page.goto('/noticias?page=' + value)).status()).toBe(200);
    await expect(page.locator('.news-card').first()).toBeVisible();
    await expect(page.locator('.pagination [aria-current="page"]')).toHaveCount(1);
  }
});

test('F-AUT-04 U-07 U-08 A-COG-03: errores de servidor asociados y correo preservado', async ({ page }) => {
  await page.goto('/login');
  await page.locator('form').filter({ has: page.locator('#email') }).evaluate(form => form.noValidate = true);
  await page.getByRole('button', { name: 'Iniciar sesión', exact: true }).click();
  await expect(page.locator('#errors')).toBeFocused();
  await expect(page.locator('#email')).toHaveAttribute('aria-describedby', 'error-email');
  await expect(page.locator('#password')).toHaveAttribute('aria-describedby', 'error-password');
});
