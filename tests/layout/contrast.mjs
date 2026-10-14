import assert from 'node:assert/strict';
import { readFile, writeFile } from 'node:fs/promises';
const css = await readFile('public/assets/app.css', 'utf8');
const blocks = [css.match(/:root\s*\{([\s\S]*?)\}/)[1], css.match(/:root\[data-theme="dark"\]\s*\{([\s\S]*?)\}/)[1]];
const luminance = hex => {
  const rgb = hex.match(/[a-f\d]{2}/gi).map(value => parseInt(value, 16) / 255).map(value => value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4);
  return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
};
const results = [];
for (const [index, block] of blocks.entries()) {
  const tokens = Object.fromEntries([...block.matchAll(/--([a-z-]+):\s*(#[a-f\d]{6})\s*;/gi)].map(match => [match[1], match[2]]));
  for (const [id, foreground, background, min] of [
    ['A-VIS-06', 'ink', 'canvas', 7], ['A-VIS-05', 'muted', 'paper', 4.5],
    ['A-VIS-05', 'muted', 'canvas', 4.5], ['A-VIS-05', 'primary', 'paper', 4.5],
    ['A-VIS-05', 'on-primary', 'primary', 4.5], ['A-VIS-05', 'secondary', 'paper', 4.5],
    ['A-VIS-07', 'line', 'paper', 3], ['A-MOT-04', 'focus', 'paper', 3],
    ['A-VIS-05', 'danger', 'paper', 4.5]
  ]) {
    const a = luminance(tokens[foreground]), b = luminance(tokens[background]);
    const ratio = (Math.max(a, b) + .05) / (Math.min(a, b) + .05);
    assert(ratio >= min, `${id} ${index}: ${foreground}/${background}=${ratio}`);
    results.push({ id, mode: index ? 'oscuro' : 'claro', foreground, background, ratio: Number(ratio.toFixed(2)), min });
  }
}
await writeFile('work/contrast.json', JSON.stringify(results, null, 2));
console.log(`A-VIS-05 A-VIS-06 A-VIS-07 A-MOT-04: ${results.length} pares de tokens cumplen contraste (no sustituye revisión de páginas).`);
