import { test, expect } from '@playwright/test';
import { login, fillArticle, noOverflow, db } from '../support/helpers.js';

test.beforeEach(async ({ page, browserName }, info) => {
  if (info.project.name === 'Gama baja' && browserName === 'chromium') {
    const cdp = await page.context().newCDPSession(page);
    await cdp.send('Emulation.setCPUThrottlingRate', { rate: 6 });
    await cdp.send('Network.enable');
    await cdp.send('Network.emulateNetworkConditions', { offline: false, latency: 400, downloadThroughput: 50 * 1024, uploadThroughput: 20 * 1024 });
  }
});

test('U-10 E-11 A-VIS-10: reflujo en 320, 360, 768, 1280 y 1920 píxeles', async ({ page }) => {
  for (const width of [320, 360, 768, 1280, 1920]) {
    await page.setViewportSize({ width, height: 800 });
    await page.goto('/');
    await noOverflow(page);
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
  }
});

test('U-11 A-MOT-05 U-12: menú móvil, Escape, controles táctiles y formulario', async ({ page }) => {
  await page.setViewportSize({ width: 360, height: 640 });
  await page.goto('/');
  const menu = page.getByRole('button', { name: 'Menú', exact: true });
  await expect(menu).toHaveAttribute('aria-expanded', 'false');
  await menu.click();
  await expect(menu).toHaveAttribute('aria-expanded', 'true');
  await page.keyboard.press('Escape');
  await expect(menu).toHaveAttribute('aria-expanded', 'false');
  const targets = await page.locator('button:visible, .button:visible, #main-menu a:visible').evaluateAll(elements => elements.map(element => ({ name: element.textContent, width: element.getBoundingClientRect().width, height: element.getBoundingClientRect().height })));
  for (const target of targets) { expect(target.height, target.name).toBeGreaterThanOrEqual(44); expect(target.width, target.name).toBeGreaterThanOrEqual(44); }
  await page.goto('/login');
  await expect(page.locator('#email')).toHaveAttribute('type', 'email');
  await expect(page.locator('#email')).toHaveAttribute('autocomplete', 'email');
  expect(await page.locator('#email').evaluate(element => parseFloat(getComputedStyle(element).fontSize))).toBeGreaterThanOrEqual(16);
});

test('EF-17 F-PUB-04 F-PUB-06 F-PUB-09: lector móvil lee, busca y comenta', async ({ page }) => {
  db("UPDATE comments SET created_at=DATE_SUB(NOW(),INTERVAL 2 MINUTE) WHERE user_id=6");
  await login(page, 'lector3');
  await page.goto('/buscar?q=escuelas');
  await page.locator('.news-card h3 a').first().click();
  await expect(page.locator('.article-body')).toBeVisible();
  await noOverflow(page);
  await page.waitForTimeout(2100);
  await page.getByLabel('Tu comentario').fill('Comentario desde un dispositivo móvil ' + Date.now());
  await page.getByRole('button', { name: 'Enviar a moderación' }).click();
  await expect(page.getByRole('status').first()).toContainText('pendiente de moderación');
});

test('U-10 A-TEC-06 EF-34: panel móvil, creación y orientación horizontal', async ({ page }) => {
  await login(page, 'editor2');
  await page.goto('/admin/noticias/nueva');
  await fillArticle(page, `Publicación móvil ${Date.now()}`);
  await page.getByRole('button', { name: 'Guardar noticia' }).click();
  await expect(page.getByRole('status').first()).toContainText('Noticia guardada');
  await page.goto('/admin/noticias');
  await noOverflow(page);
  await page.setViewportSize({ width: 640, height: 360 });
  await noOverflow(page);
  await expect(page.locator('.table-wrap')).toBeVisible();
});

test('EF-12: lectura y login funcionan sin JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 360, height: 640 }, baseURL: 'http://127.0.0.1:8088' });
  const page = await context.newPage();
  await page.goto('/');
  await expect(page.locator('#main-menu')).toBeVisible();
  await page.locator('.news-card h3 a').first().click();
  await expect(page.locator('.article-body')).toBeVisible();
  await noOverflow(page);
  await login(page, 'lector2');
  await context.close();
});

test('EF-15 EF-08: Save-Data usa imagen ligera con dimensiones', async ({ page }) => {
  await page.setExtraHTTPHeaders({ 'Save-Data': 'on' });
  await page.goto('/');
  await expect(page.locator('.lead-image img')).toHaveAttribute('src', /-480\.webp$/);
  await expect(page.locator('.lead-image img')).toHaveAttribute('width', '1200');
  await expect(page.locator('.lead-image img')).not.toHaveAttribute('srcset', /1200w/);
});
