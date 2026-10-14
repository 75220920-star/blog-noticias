import { test, expect } from '@playwright/test';
import { login, fillArticle, db, mail } from '../support/helpers.js';

test('F-AUT-01 F-AUT-06: administrador inicia y cierra sesión', async ({ page }) => {
  await login(page);
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Tu sala de redacción');
  await page.getByRole('button', { name: 'Cerrar sesión' }).click();
  await page.goto('/admin');
  await expect(page).toHaveURL(/login/);
});

test('F-NOT-01 F-NOT-08 F-NOT-09 U-16: crear, editar, cancelar, eliminar y restaurar noticia', async ({ page }) => {
  await login(page);
  await page.goto('/admin/noticias/nueva');
  const title = `Reportaje QA ${Date.now()}`;
  await fillArticle(page, title);
  await page.getByLabel('Estado (obligatorio)').selectOption('publicado');
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.getByRole('status').first()).toContainText('Noticia guardada');
  const id = page.url().match(/noticias\/(\d+)/)[1];
  await page.getByLabel('Título (máximo').fill(`${title} editado`);
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await page.goto(`/admin/noticias?q=${encodeURIComponent(title)}`);
  await page.getByRole('button', { name: /^Eliminar/ }).click();
  await page.getByRole('button', { name: 'Cancelar', exact: true }).click();
  expect(db('SELECT deleted_at FROM articles WHERE id=?', [id])[0].deleted_at).toBeNull();
  await page.getByRole('button', { name: /^Eliminar/ }).click();
  await page.getByRole('button', { name: 'Confirmar eliminación' }).click();
  expect(db('SELECT deleted_at FROM articles WHERE id=?', [id])[0].deleted_at).not.toBeNull();
  await page.goto(`/admin/noticias?trash=1&q=${encodeURIComponent(title)}`);
  await page.getByRole('button', { name: /^Restaurar/ }).click();
  expect(db('SELECT deleted_at FROM articles WHERE id=?', [id])[0].deleted_at).toBeNull();
});

test('F-PUB-06 F-PUB-07 F-PUB-08: búsqueda, resaltado y estados vacíos', async ({ page }) => {
  await page.goto('/buscar?q=comunidad');
  await expect(page.locator('.news-card').first()).toBeVisible();
  await page.goto('/buscar?q=zzzznuncaexiste');
  await expect(page.getByRole('heading', { name: 'No encontramos resultados' })).toBeVisible();
  await page.goto('/buscar?q=' + encodeURIComponent("<script>alert(1)</script>'%"));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Resultados de búsqueda');
});

test('F-PUB-09 F-PUB-11 F-ADM-06: comentar, limitar tasa y moderar', async ({ page }) => {
  db("UPDATE comments SET created_at=DATE_SUB(NOW(),INTERVAL 2 MINUTE) WHERE user_id=4");
  const article = db("SELECT slug,id FROM articles WHERE status='publicado' AND deleted_at IS NULL LIMIT 1")[0];
  await login(page, 'lector1');
  await page.goto('/noticia/' + article.slug);
  await page.waitForTimeout(2100);
  const message = `Comentario de prueba ${Date.now()}`;
  await page.getByLabel('Tu comentario').fill(message);
  await page.getByRole('button', { name: 'Enviar a moderación' }).click();
  await expect(page.getByRole('status').first()).toContainText('pendiente de moderación');
  await page.getByLabel('Tu comentario').fill(message + ' repetido');
  await page.getByRole('button', { name: 'Enviar a moderación' }).click();
  await expect(page.locator('#errors')).toContainText('Espera un minuto');
  await page.getByRole('button', { name: 'Cerrar sesión' }).click();
  await login(page);
  await page.goto('/admin/comentarios?q=' + encodeURIComponent(message));
  await page.getByRole('button', { name: /^Aprobar/ }).click();
  await page.goto('/noticia/' + article.slug);
  await expect(page.locator('.comment').filter({ hasText: message })).toHaveCount(1);
});

test('F-NOT-13: control de edición concurrente conserva el primer guardado', async ({ page, context }) => {
  await login(page);
  const article = db('SELECT id,title FROM articles WHERE deleted_at IS NULL LIMIT 1')[0];
  await page.goto(`/admin/noticias/${article.id}/editar`);
  const second = await context.newPage();
  await second.goto(`/admin/noticias/${article.id}/editar`);
  await page.getByLabel('Título (máximo').fill(article.title + ' A');
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await second.getByLabel('Título (máximo').fill(article.title + ' B');
  await second.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(second.locator('#errors')).toContainText('Conflicto');
  expect(db('SELECT title FROM articles WHERE id=?', [article.id])[0].title).toBe(article.title + ' A');
  db('UPDATE articles SET title=? WHERE id=?', [article.title, article.id]);
});

test('F-AUT-08 F-AUT-09: recuperación local y token de un solo uso', async ({ page }) => {
  await page.goto('/recuperar');
  await page.getByLabel('Correo electrónico').fill('lector5@pulso.test');
  await page.getByRole('button', { name: 'Solicitar enlace' }).click();
  const link = mail('lector5@pulso.test').url;
  await page.goto(link);
  await page.getByLabel('Contraseña', { exact: false }).fill('Temporal123!');
  await page.getByRole('button', { name: 'Guardar contraseña' }).click();
  await expect(page.getByRole('status').first()).toContainText('Contraseña actualizada');
  await page.goto(link);
  await page.getByLabel('Contraseña', { exact: false }).fill('OtraTemporal123!');
  await page.getByRole('button', { name: 'Guardar contraseña' }).click();
  await expect(page.locator('#errors')).toContainText('caducó o ya se usó');
  db('UPDATE users SET password_hash=(SELECT password_hash FROM (SELECT password_hash FROM users WHERE id=1) AS demo) WHERE id=8');
});

test('F-AUT-10 F-AUT-11 F-ROL-06: registro confirmado ignora role del cliente', async ({ page }) => {
  const email = `qa${Date.now()}@example.test`;
  await page.goto('/registro');
  await page.getByLabel('Nombre', { exact: false }).fill('Lector QA');
  await page.getByLabel('Correo electrónico').fill(email);
  await page.getByLabel('Contraseña', { exact: false }).fill('Registro123!');
  await page.locator('form').filter({ has: page.locator('#name') }).evaluate(form => { const input = document.createElement('input'); input.name = 'role'; input.value = 'administrador'; form.append(input); });
  await page.getByRole('button', { name: 'Crear cuenta', exact: true }).click();
  expect(db('SELECT role_id FROM users WHERE email=?', [email])[0].role_id).toBe(3);
  await page.goto(mail(email).url);
  await page.getByRole('button', { name: 'Confirmar', exact: true }).click();
  await expect(page.getByRole('status').first()).toContainText('Correo confirmado');
  await page.goto('/registro');
  await page.getByLabel('Nombre', { exact: false }).fill('Otro lector');
  await page.getByLabel('Correo electrónico').fill(email);
  await page.getByLabel('Contraseña', { exact: false }).fill('Registro123!');
  await page.getByRole('button', { name: 'Crear cuenta', exact: true }).click();
  await expect(page.locator('#errors')).toContainText('ya está registrado');
});

test('F-PUB-12 F-PUB-14 F-PUB-16: boletín confirmado, RSS y cookies', async ({ page, request }) => {
  await page.goto('/');
  await page.getByRole('button', { name: 'Rechazar opcionales' }).click();
  await expect(page.locator('.cookies')).toHaveCount(0);
  const email = `boletin${Date.now()}@example.test`;
  await page.locator('.newsletter').getByLabel('Correo electrónico').fill(email);
  await page.getByRole('button', { name: 'Quiero suscribirme' }).click();
  await page.goto(mail(email).url);
  await page.getByRole('button', { name: 'Confirmar', exact: true }).click();
  expect(db('SELECT confirmed_at FROM subscribers WHERE email=?', [email])[0].confirmed_at).not.toBeNull();
  const rss = await request.get('/rss.xml');
  expect(rss.status()).toBe(200);
  expect(rss.headers()['content-type']).toContain('application/rss+xml');
  expect(await rss.text()).toContain('<rss version="2.0">');
});

test('F-NOT-14: borrador local se recupera después de recargar', async ({ page }) => {
  await login(page, 'editor1');
  await page.goto('/admin/noticias/nueva');
  await page.getByLabel('Título (máximo').fill('Mi borrador recuperable');
  await expect(page.locator('#draft-status')).toContainText('Borrador guardado');
  await page.reload();
  await page.getByRole('button', { name: 'Recuperar borrador local' }).click();
  await expect(page.getByLabel('Título (máximo')).toHaveValue('Mi borrador recuperable');
});
