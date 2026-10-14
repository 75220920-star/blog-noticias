import { readFile, writeFile } from 'node:fs/promises';
const css = await readFile('public/assets/app.css');
const js = await readFile('public/assets/app.js', 'utf8');
// Minificación conservadora sin dependencias. Mantiene saltos de línea de JS
// para preservar la inserción automática de punto y coma y los literales.
const compactCss = css.toString().replace(/\/\*[\s\S]*?\*\//g, '').replace(/\s+/g, ' ').replace(/\s*([{}:;,])\s*/g, '$1').trim();
const compactJs = js.split('\n').filter(line => !line.trim().startsWith('//')).map(line => line.trim()).filter(Boolean).join('\n');
await writeFile('public/assets/app.min.css', compactCss);
await writeFile('public/assets/app.min.js', compactJs);
console.log('CSS y JS minificados correctamente.');
