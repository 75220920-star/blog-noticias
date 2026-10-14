import { mkdir, writeFile } from 'node:fs/promises';
import { pathToFileURL } from 'node:url';
import { resolve } from 'node:path';
const { chromium } = await import(process.env.PLAYWRIGHT_MODULE || '@playwright/test');
const browser = await chromium.launch({ headless: true, executablePath: process.env.BROWSER_EXECUTABLE || undefined });
const context = await browser.newContext();
const page = await context.newPage();
const report = [];
await mkdir('work/layout', { recursive: true });
try {
  for (const name of ['home', 'article', 'editor', 'table']) {
    for (const width of [320, 360, 768, 1280, 1920]) {
      await page.setViewportSize({ width, height: 900 });
      await page.goto(pathToFileURL(resolve(`work/layout/${name}.html`)).href);
      const result = await page.evaluate(() => ({ width: innerWidth, scroll: document.documentElement.scrollWidth, headings: document.querySelectorAll('h1').length, imageMissing: [...document.images].filter(img => !img.complete || !img.naturalWidth).length }));
      if (result.scroll > result.width || result.headings !== 1 || result.imageMissing) throw new Error(`${name} ${width}: ${JSON.stringify(result)}`);
      report.push({ caso: 'U-10 E-11 A-VIS-10 A-VIS-03', template: name, ...result, result: 'aprobado solo presentación con fixtures' });
      if (width === 360 || width === 1280) await page.screenshot({ path: `work/layout/${name}-${width}.png`, fullPage: true });
    }
  }
  await writeFile('work/layout/results.json', JSON.stringify(report, null, 2));
  console.log(`U-10 E-11 A-VIS-10 A-VIS-03: ${report.length} combinaciones de presentación sin desbordamiento; un h1 e imágenes cargadas.`);
} finally { await browser.close(); }
