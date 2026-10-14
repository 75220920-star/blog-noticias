import { test, expect } from '@playwright/test';
import { login, fillArticle, db } from '../support/helpers.js';

test('F-ADM-01 F-ADM-02: categoría en uso requiere reasignación', async ({ page }) => {
  await login(page);
  await page.goto('/admin/categorias');
  const block = page.locator('.catalog-grid section').filter({ has: page.getByRole('heading', { name: 'Actualidad', exact: true }) });
  await block.getByRole('button', { name: /^Eliminar/ }).click();
  await page.getByRole('button', { name: 'Confirmar eliminación' }).click();
  await expect(page.getByRole('status').first()).toContainText('tiene noticias');
  await page.getByLabel('Nombre del nuevo elemento').fill('Categoría QA ' + Date.now());
  await page.getByRole('button', { name: 'Crear categoría' }).click();
  await expect(page.getByRole('status').first()).toContainText('Catálogo actualizado');
});

test('F-ADM-03 F-ADM-04: crear lector desactivado y denegar login', async ({ page }) => {
  await login(page);
  await page.goto('/admin/usuarios/nuevo');
  const email = `inactivo${Date.now()}@example.test`;
  await page.getByLabel('Nombre', { exact: false }).fill('Usuario desactivado QA');
  await page.getByLabel('Correo electrónico').fill(email);
  await page.getByLabel('Contraseña', { exact: false }).fill('Inactivo123!');
  await page.getByLabel('Rol (obligatorio)').selectOption('3');
  await page.getByLabel('Cuenta activa').uncheck();
  await page.getByRole('button', { name: 'Guardar usuario' }).click();
  await page.getByRole('button', { name: 'Cerrar sesión' }).click();
  await page.getByLabel('Correo electrónico').fill(email);
  await page.getByLabel('Contraseña', { exact: false }).fill('Inactivo123!');
  await page.getByRole('button', { name: 'Iniciar sesión', exact: true }).click();
  await expect(page.locator('#errors')).toContainText('Credenciales inválidas');
});

test('F-ADM-07 F-ADM-08 F-ADM-09: configuración, estadísticas y auditoría reales', async ({ page }) => {
  await login(page);
  const count = db('SELECT COUNT(*) AS total FROM articles WHERE deleted_at IS NULL')[0].total;
  await expect(page.locator('.stat').filter({ hasText: /^Noticias/ }).locator('strong')).toHaveText(String(count));
  await page.goto('/admin/configuracion');
  await page.getByLabel('Nombre del sitio').fill('Pulso QA');
  await page.getByRole('button', { name: 'Guardar configuración' }).click();
  await page.goto('/');
  await expect(page.locator('.brand')).toContainText('Pulso QA');
  await page.goto('/admin/auditoria');
  await expect(page.locator('table')).toContainText('guardar_configuracion');
  await page.goto('/admin/configuracion');
  await page.getByLabel('Nombre del sitio').fill('Pulso');
  await page.getByRole('button', { name: 'Guardar configuración' }).click();
});

test('F-NOT-10 F-NOT-11: estados privados y fecha de publicación programada', async ({ page }) => {
  await login(page);
  await page.goto('/admin/noticias/nueva');
  await fillArticle(page, `Noticia programada QA ${Date.now()}`);
  await page.getByLabel('Estado (obligatorio)').selectOption('publicado');
  await page.getByLabel('Fecha de publicación').fill('2099-01-01T12:00');
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  const id = page.url().match(/noticias\/(\d+)/)[1];
  const article = db('SELECT slug FROM articles WHERE id=?', [id])[0];
  expect((await page.goto('/noticia/' + article.slug)).status()).toBe(404);
  db('UPDATE articles SET published_at=DATE_SUB(NOW(),INTERVAL 1 MINUTE) WHERE id=?', [id]);
  expect((await page.goto('/noticia/' + article.slug)).status()).toBe(200);
  for (const status of ['revision', 'archivado', 'borrador']) {
    db('UPDATE articles SET status=? WHERE id=?', [status, id]);
    expect((await page.goto('/noticia/' + article.slug)).status()).toBe(404);
  }
});

test('F-NOT-07: 5 MB admitidos y 5 MB más un byte rechazado', async ({ page }) => {
  const { readFile } = await import('node:fs/promises');
  const png = await readFile('public/assets/icon-192.png');
  const data = Buffer.concat([png, Buffer.alloc(5 * 1024 * 1024 - png.length)]);
  await login(page);
  await page.goto('/admin/noticias/nueva');
  await fillArticle(page, `Límite de imagen QA ${Date.now()}`);
  await page.getByLabel('Imagen destacada').setInputFiles({ name: 'grande.png', mimeType: 'image/png', buffer: Buffer.concat([data, Buffer.from('x')]) });
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.locator('#errors')).toContainText('supera 5 MB');
  await page.getByLabel('Imagen destacada').setInputFiles({ name: 'limite.png', mimeType: 'image/png', buffer: data });
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.getByRole('status').first()).toContainText('Noticia guardada');
});
