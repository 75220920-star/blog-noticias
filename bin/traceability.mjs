import { readFile, writeFile, readdir } from 'node:fs/promises';
const plan = await readFile('docs/plan-de-pruebas.md', 'utf8');
const cases = plan.split(/\r?\n/).filter(line => /^(F-[A-Z]+-\d+|U-\d+|A-[A-Z]+-\d+|E-\d+|EF-\d+)\t/.test(line)).map(line => line.split('\t'));
const mapping = new Map();
for (const dir of ['tests/unit', 'tests/e2e', 'tests/a11y', 'tests/mobile']) {
  for (const file of await readdir(dir)) {
    if (!/\.(php|js)$/.test(file)) continue;
    const path = `${dir}/${file}`;
    const source = await readFile(path, 'utf8');
    const titles = [...source.matchAll(/(?:test\('([^']+)'|TestDox\('([^']+)')/g)].map(match => match[1] || match[2]);
    for (const title of titles) for (const match of title.matchAll(/\b(F-[A-Z]+-\d+|U-\d+|A-[A-Z]+-\d+|E-\d+|EF-\d+)\b/g)) {
      const entries = mapping.get(match[1]) || [];
      entries.push(`\`${path}\` — ${title}`);
      mapping.set(match[1], entries);
    }
  }
}
for (const id of ['EF-01', 'EF-03', 'EF-04', 'EF-05', 'EF-06']) mapping.set(id, ['`tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse']);
for (const id of ['EF-20', 'EF-21', 'EF-22', 'EF-23', 'EF-24', 'EF-25', 'EF-28']) mapping.set(id, ['`tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual.']);
for (const id of ['A-VIS-05', 'A-VIS-06', 'A-VIS-07', 'A-MOT-04']) mapping.set(id, [...(mapping.get(id) || []), '`tests/layout/contrast.mjs` — contraste calculado de 18 pares de tokens en claro y oscuro; no evalúa la composición final.']);
const escape = text => text.replaceAll('|', '\\|').replace(/[\r\n]/g, ' ');
let matrix = '# Matriz de trazabilidad\n\nFuente: plan original recibido desde `C:\\xampp\\htdocs\\blog-noticias\\docs\\plan-de-pruebas.md`, conservado íntegro. Se incluyen todos sus IDs. Cada fila distingue comprobaciones automatizadas de procedimientos manuales; tener una prueba escrita no significa que se ejecutó ni que cubra los aspectos subjetivos del caso.\n\n| Caso | Escenario del plan | Archivo y nombre de prueba / manual | Procedimiento complementario |\n|---|---|---|---|\n';
let checklist = '# Checklists y procedimientos manuales\n\nRegistrar por ejecución: ID del caso, fecha, versión, SO/navegador/dispositivo, resultado (aprobado/fallido/no aplicable/pendiente), evidencia y defecto. No aplicar el criterio de salida con casos pendientes. Las inspecciones visuales, experiencias con tecnologías de apoyo, encuestas y carga real no se reemplazan por emulación.\n\n## Dispositivos físicos obligatorios\n\n- [ ] Android de gama baja, aproximadamente 2 GB RAM; anotar marca, CPU, RAM, SO, navegador y red.\n- [ ] Android de gama media/alta; anotar los mismos datos.\n- [ ] iPhone/Safari, si está disponible; registrar versión de iOS.\n- [ ] En los dos Android: vertical y horizontal, ancho de 360 px o el nativo, zoom 200 %, teclado virtual, foco del campo activo, menú/ Escape con teclado externo, botones de 44×44 px y sin scroll horizontal del documento.\n- [ ] Completar login, lectura, búsqueda, comentario y creación/edición del panel; moderar como admin. Cancelar y confirmar una eliminación y restaurarla.\n- [ ] Red 3G lenta/Slow 4G y ahorro de datos; guardar HAR y vídeo del tiempo hasta lectura. Medir LCP/INP/CLS con herramientas del dispositivo. No aceptar una emulación como prueba de batería o RAM de 2 GB.\n- [ ] TalkBack en Android y VoiceOver en iPhone: ejecutar los mismos flujos, comprobar etiquetas, errores y anuncios de estado.\n\n## Tecnologías de apoyo y usuarios\n\nNVDA con Firefox y Chrome en Windows; JAWS si hay licencia; VoiceOver con Safari en macOS/iOS; TalkBack con Chrome Android. Sin ratón, navegar regiones/encabezados, leer una noticia, buscar, autenticarse, comentar y publicar como editor. Anotar comandos, anuncio real y bloqueos. Control por voz/conmutador: activar por nombre visible, sin gestos obligatorios. Alto contraste de Windows, oscuro/claro, zoom 200 %, reflujo 320 px y bookmarklet de espaciado (letras 0.12em, palabras 0.16em, párrafos 2em, interlineado 1.5).\n\nUsabilidad: al menos 5 participantes por perfil del plan, incluyendo mayores de 60 años y personas con discapacidades; obtener consentimiento, usar datos ficticios, no ayudar durante la tarea y registrar éxito, tiempo, clics, errores y comentarios. Aplicar las 10 preguntas SUS originales, puntuar impares como respuesta−1 y pares como 5−respuesta, sumar y multiplicar por 2.5 (meta ≥ 80). Encuesta de estilo: ≥ 20 participantes, Likert 1–5, media ≥ 4.3. Evaluación experta: ≥ 3 diseñadores, rúbrica 0–4 en originalidad, armonía, identidad y legibilidad, media ≥ 3.5; conservar respuestas y cálculo.\n\n## Respaldo, volumen y observabilidad\n\nF-ADM-10: ejecutar mysqldump con --single-transaction --routines --events --default-character-set=utf8mb4 --result-file=RUTA_PRIVADA.sql; copiar uploads; restaurar en una BD limpia sin sobrescribir producción; comparar conteos de todas las tablas, roles, noticias, 100 comentarios, archivos y relaciones.\n\nEF-25: ejecutar EXPLAIN del listado por categoría, listado por fecha y MATCH(title,content); medir en slow query log, comprobar índices y ausencia de consulta por tarjeta. EF-28: ampliar únicamente blog_noticias_test con tests/support/volume.php 100000; medir listados/búsqueda en Apache. EF-27/30: PerfMon de Windows (CPU/RAM), SHOW STATUS para conexiones y recursos durante carga; conservar gráficas. EF-31: detener únicamente servicios del entorno de ensayo, verificar 503 amigable y recuperación al reiniciar; nunca provocar fallos en una instalación con datos reales.\n\n## Casos del plan, uno por uno\n\n';
const conditional = new Set(['A-AUD-01', 'A-AUD-02', 'A-AUD-03', 'A-AUD-05', 'A-AUD-08', 'A-MOT-10', 'EF-18']);
for (const fields of cases) {
  const [id, scenario] = fields;
  const functional = id.startsWith('F-');
  const procedure = functional ? `Precondición: ${fields[2]}. Ejecutar: ${fields[3]}. Resultado esperado: ${fields[4]}.` : `Revisar ${fields.slice(1, -1).join(' · ')}.`;
  const auto = mapping.get(id);
  matrix += `| ${id} | ${escape(scenario)} | ${escape(auto ? auto.join('<br>') : 'manual')} | [${id}](checklists-manuales.md#${id.toLowerCase()}) |\n`;
  checklist += `<a id="${id.toLowerCase()}"></a>\n### ${id} · ${scenario}\n\n${procedure}\n\n`;
  if (conditional.has(id)) checklist += 'Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.\n\n';
  else checklist += '- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.\n- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.\n- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.\n\n';
}
await writeFile('docs/matriz-trazabilidad.md', matrix);
await writeFile('docs/checklists-manuales.md', checklist);
console.log(`Trazabilidad: ${cases.length} casos originales; ${cases.filter(c => mapping.has(c[0])).length} con comprobaciones automatizadas y revisión complementaria.`);
