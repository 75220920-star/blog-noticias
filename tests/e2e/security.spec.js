import { test, expect } from '@playwright/test';
import { login, fillArticle, db } from '../support/helpers.js';

test('F-AUT-02 F-AUT-03 F-AUT-05: SQLi denegado y bloqueo tras cinco fallos', async ({ page }) => {
  db('UPDATE users SET failed_attempts=0,locked_until=NULL WHERE id=7');
  await page.goto('/login');
  await page.getByLabel('Correo electrónico').fill("' OR '1'='1' --");
  await page.getByLabel('Contraseña').fill('incorrecta');
  // Omite validación cliente para probar también el servidor.
  await page.locator('form').filter({ has: page.locator('#email') }).evaluate(form => form.noValidate = true);
  await page.getByRole('button', { name: 'Iniciar sesión', exact: true }).click();
  await expect(page).toHaveURL(/login/);
  for (let i = 0; i < 5; i++) {
    await page.goto('/login');
    await page.getByLabel('Correo electrónico').fill('lector4@pulso.test');
    await page.getByLabel('Contraseña').fill('incorrecta');
    await page.getByRole('button', { name: 'Iniciar sesión', exact: true }).click();
    await expect(page.locator('#errors')).toContainText('Credenciales inválidas');
  }
  expect(db('SELECT locked_until FROM users WHERE id=7')[0].locked_until).not.toBeNull();
  db('UPDATE users SET failed_attempts=0,locked_until=NULL WHERE id=7');
});

test('F-PUB-15: POST sin CSRF es rechazado', async ({ request }) => {
  const response = await request.post('/login', { form: { email: 'admin@pulso.test', password: 'DemoNoticias2026!' } });
  expect(response.status()).toBe(403);
  expect(await response.text()).toContain('El formulario caducó');
});

test('F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR', async ({ page }) => {
  await page.goto('/admin');
  await expect(page).toHaveURL(/login/);
  await login(page, 'lector2');
  expect((await page.goto('/admin')).status()).toBe(403);
  await page.getByRole('button', { name: 'Cerrar sesión' }).click();
  await login(page, 'editor1');
  expect((await page.goto('/admin/usuarios')).status()).toBe(403);
  expect((await page.goto('/admin/configuracion')).status()).toBe(403);
  const other = db('SELECT id FROM articles WHERE author_id=3 AND deleted_at IS NULL LIMIT 1')[0];
  expect((await page.goto(`/admin/noticias/${other.id}/editar`)).status()).toBe(403);
});

test('F-NOT-04: XSS persistente se elimina al publicar', async ({ page }) => {
  await login(page);
  await page.goto('/admin/noticias/nueva');
  await fillArticle(page, `Seguridad XSS ${Date.now()}`);
  await page.getByLabel('Contenido HTML').fill('<p onclick="alert(1)">Texto seguro</p><script>alert(1)</script><a href="javascript:alert(1)">Enlace</a>');
  await page.getByLabel('Estado (obligatorio)').selectOption('publicado');
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  const id = page.url().match(/noticias\/(\d+)/)[1];
  const article = db('SELECT slug,content FROM articles WHERE id=?', [id])[0];
  expect(article.content).not.toMatch(/script|onclick/);
  await page.goto('/noticia/' + article.slug);
  await expect(page.locator('.article-body')).toContainText('Texto seguro');
  expect(await page.locator('.article-body').innerHTML()).not.toMatch(/script|onclick/);
});

test('F-NOT-05 F-NOT-06 A-VIS-02: alt obligatorio y tipos de archivo', async ({ page }) => {
  await login(page);
  await page.goto('/admin/noticias/nueva');
  await fillArticle(page, `Imagen segura ${Date.now()}`);
  await page.getByLabel('Texto alternativo').fill('');
  await page.locator('#article-form').evaluate(form => form.noValidate = true);
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.locator('#errors')).toContainText('texto alternativo');
  await page.getByLabel('Texto alternativo').fill('Imagen de prueba');
  await page.getByLabel('Imagen destacada').setInputFiles({ name: 'ataque.php', mimeType: 'application/x-httpd-php', buffer: Buffer.from('<?php echo "ataque";') });
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.locator('#errors')).toContainText('Formato no permitido');
  await page.getByLabel('Imagen destacada').setInputFiles('public/assets/icon-192.png');
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.getByRole('status').first()).toContainText('Noticia guardada');
});

test('F-ADM-05: último administrador no se elimina ni desactiva', async ({ page }) => {
  await login(page);
  await page.goto('/admin/usuarios/1/editar');
  await page.getByLabel('Cuenta activa').uncheck();
  await page.getByRole('button', { name: 'Guardar usuario' }).click();
  await expect(page.locator('#errors')).toContainText('al menos un administrador');
  await page.goto('/admin/usuarios?q=admin%40pulso.test');
  await page.getByRole('button', { name: /^Eliminar/ }).click();
  await page.getByRole('button', { name: 'Confirmar eliminación' }).click();
  await expect(page.getByRole('status').first()).toContainText('último administrador');
});

test('F-AUT-13: cookie protegida y regeneración al autenticar', async ({ page, context }) => {
  await page.goto('/login');
  const before = (await context.cookies()).find(cookie => cookie.name === 'pulso_session');
  await login(page);
  const after = (await context.cookies()).find(cookie => cookie.name === 'pulso_session');
  expect(after.value).not.toBe(before.value);
  expect(after.httpOnly).toBe(true);
  expect(after.sameSite).toBe('Lax');
});
