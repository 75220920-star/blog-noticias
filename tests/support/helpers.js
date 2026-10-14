import { execFileSync } from 'node:child_process';
import { expect } from '@playwright/test';
export function db(sql, params = []) {
  return JSON.parse(execFileSync(process.env.PHP_BIN || 'php', ['tests/support/db.php', 'query', Buffer.from(sql).toString('base64'), Buffer.from(JSON.stringify(params)).toString('base64')], { encoding: 'utf8' }));
}
export function mail(email) {
  return JSON.parse(execFileSync(process.env.PHP_BIN || 'php', ['tests/support/db.php', 'mail', email], { encoding: 'utf8' }));
}
export async function login(page, role = 'admin') {
  await page.goto('/login');
  await page.getByLabel('Correo electrónico', { exact: false }).fill(`${role}@pulso.test`);
  await page.getByLabel('Contraseña', { exact: false }).fill('DemoNoticias2026!');
  await page.getByRole('button', { name: 'Iniciar sesión', exact: true }).click();
  await expect(page.getByRole('status').first()).toContainText('Sesión iniciada');
}
export async function fillArticle(page, title) {
  await page.getByLabel('Título (máximo').fill(title);
  await page.getByLabel('Contenido HTML').fill('<p>Una noticia de prueba sobre la comunidad.</p><h2>Contexto</h2><p>Detalles y próximos pasos.</p>');
  await page.getByLabel('Categoría (obligatoria)').selectOption('1');
  await page.getByLabel('Texto alternativo').fill('Ilustración de la comunidad');
}
export async function noOverflow(page) {
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBeTruthy();
}
