# Informe de ejecución · 13 de octubre de 2026

Entrega: Pulso, PHP 8.2.12/XAMPP, Windows; generación y verificaciones en un entorno restringido. Se leyó y conservó el plan original de la instalación XAMPP. El código está preparado para su instalación, pero no se acredita el criterio de salida del plan.

| Verificación | Resultado | Evidencia / límite |
|---|---|---|
| Sintaxis PHP de app, config, public, bin y tests | Aprobado | `php -l` en todos los archivos propios; registro de entrega en outputs |
| PHPUnit | Aprobado | `composer test`: 13 pruebas, 64 aserciones; Validator, permisos Auth, Slug, Sanitizer y CSRF |
| PSR-12 obligatorio | Aprobado | `composer lint`: sin errores; inspección completa informa advertencias de longitud recomendada de línea, sin infringir el límite obligatorio de PSR-12 |
| Sintaxis JS | Aprobado | `node --check` en scripts de aplicación, configuración, build y pruebas (k6 validado como módulo) |
| Build | Aprobado | `npm run build`, sin dependencias; assets minificados incluidos |
| Contraste de tokens | Aprobado | 18 pares, claro/oscuro; cuerpo de lectura ≥ 7:1; `tests/layout/contrast.mjs` |
| Render PHP de presentación | Aprobado | Portada, detalle, editor y tabla renderizados con fixtures de semilla, sin errores PHP; no comprueba consultas ni navegación |
| Semilla | Generada, importación pendiente | 8 usuarios, 8 categorías, 60 artículos, 100 comentarios; hashes Argon2id e imágenes WebP 1200/480; conteos por inspección SQL, sin ejecutar importación |
| Playwright E2E/seguridad | Pendiente por entorno | npm falló con ECONNRESET; conexión TCP y pipe local a MariaDB no disponibles en la ejecución restringida |
| axe en todas las plantillas | Pendiente por entorno | Suite entregada; no se atribuyen resultados de axe |
| Emulación Pixel 5, iPhone 12 y gama baja | Pendiente por entorno | Suite entregada; no se atribuye compatibilidad aprobada sin ejecución |
| Revisión visual de reflujo | Pendiente por entorno | Edge headless terminó durante su inicio bajo el entorno restringido; no se generaron capturas aprobadas |
| Lighthouse CI | Pendiente | Configuración móvil entregada; no se inventan valores LCP/INP/CLS ni puntuaciones |
| k6 nominal, pico, estrés, resistencia | Pendiente | Scripts entregados; no ejecutar cargas sobre el servidor de desarrollo |
| Manual / tecnologías de apoyo / SUS / estilo / celulares reales | Pendiente | Procedimientos en checklists-manuales.md; no se simulan participantes ni evaluaciones |

La falta de resultados integrados impide afirmar «100 % P1 aprobado», «≥ 95 % total», «WCAG AA conforme» o «Lighthouse ≥ 90». La matriz enumera 193 casos; 103 tienen alguna comprobación automatizada y todos tienen procedimiento complementario. La cobertura automatizada es parcial para los criterios subjetivos y condicionales.

## Obstáculos del entorno

PHPUnit se instaló con Composer. La comprobación `is_readable` de PHP devolvía falso con el token restringido de Windows pese a que se podía incluir el archivo; el runner unitario carga explícitamente bootstrap y sigue usando PHPUnit. Los slugs con tildes tenían diferencias de iconv en Windows; se normalizó español y las pruebas quedaron aprobadas.

La instalación npm fue intentada con caché propia en work y produjo ECONNRESET en registry.npmjs.org. Se intentó MariaDB en carpetas aisladas de work, sin tocar datos de XAMPP. Las conexiones locales no pudieron establecerse. También falló el lanzamiento de Edge headless para inspección de presentación. Estos resultados se consideran limitaciones de ejecución, no defectos funcionales demostrados ni aprobación de los casos.

## Plantilla para completar cada caso

| ID del caso | Resultado | Evidencia | Defecto | Severidad | Estado | Entorno / versión | Responsable / fecha |
|---|---|---|---|---|---|---|---|
| | Aprobado / Fallido / Pendiente / No aplicable justificado | Ruta de captura, traza, medición o log | DEF- | Crítica / Alta / Media / Baja | Nuevo / Retest / Cerrado | | |

Antes de cerrar la evaluación: importar SQL en MySQL operativo, instalar dependencias y navegadores, ejecutar suites secuencialmente, medir en Apache, revisar todos los casos manuales y resolver defectos altos/críticos. Mantener informe inicial y agregar ejecuciones fechadas, sin reemplazar pruebas pendientes por resultados supuestos.
