# Matriz de trazabilidad

Fuente: plan original recibido desde `C:\xampp\htdocs\blog-noticias\docs\plan-de-pruebas.md`, conservado íntegro. Se incluyen todos sus IDs. Cada fila distingue comprobaciones automatizadas de procedimientos manuales; tener una prueba escrita no significa que se ejecutó ni que cubra los aspectos subjetivos del caso.

| Caso | Escenario del plan | Archivo y nombre de prueba / manual | Procedimiento complementario |
|---|---|---|---|
| F-AUT-01 | Login válido de administrador | `tests/e2e/flows.spec.js` — F-AUT-01 F-AUT-06: administrador inicia y cierra sesión | [F-AUT-01](checklists-manuales.md#f-aut-01) |
| F-AUT-02 | Login con contraseña incorrecta | `tests/e2e/security.spec.js` — F-AUT-02 F-AUT-03 F-AUT-05: SQLi denegado y bloqueo tras cinco fallos | [F-AUT-02](checklists-manuales.md#f-aut-02) |
| F-AUT-03 | Bloqueo por intentos fallidos | `tests/e2e/security.spec.js` — F-AUT-02 F-AUT-03 F-AUT-05: SQLi denegado y bloqueo tras cinco fallos | [F-AUT-03](checklists-manuales.md#f-aut-03) |
| F-AUT-04 | Campos vacíos en login | `tests/unit/CoreTest.php` — F-AUT-04 F-PUB-12: correo inválido y parámetros malformados<br>`tests/e2e/portal.spec.js` — F-AUT-04 U-07 U-08 A-COG-03: errores de servidor asociados y correo preservado | [F-AUT-04](checklists-manuales.md#f-aut-04) |
| F-AUT-05 | Inyección SQL en login | `tests/e2e/security.spec.js` — F-AUT-02 F-AUT-03 F-AUT-05: SQLi denegado y bloqueo tras cinco fallos | [F-AUT-05](checklists-manuales.md#f-aut-05) |
| F-AUT-06 | Cierre de sesión | `tests/e2e/flows.spec.js` — F-AUT-01 F-AUT-06: administrador inicia y cierra sesión | [F-AUT-06](checklists-manuales.md#f-aut-06) |
| F-AUT-07 | Expiración de sesión por inactividad | manual | [F-AUT-07](checklists-manuales.md#f-aut-07) |
| F-AUT-08 | Recuperación de contraseña | `tests/e2e/flows.spec.js` — F-AUT-08 F-AUT-09: recuperación local y token de un solo uso | [F-AUT-08](checklists-manuales.md#f-aut-08) |
| F-AUT-09 | Reutilización de token de recuperación | `tests/e2e/flows.spec.js` — F-AUT-08 F-AUT-09: recuperación local y token de un solo uso | [F-AUT-09](checklists-manuales.md#f-aut-09) |
| F-AUT-10 | Registro de lector con datos válidos | `tests/e2e/flows.spec.js` — F-AUT-10 F-AUT-11 F-ROL-06: registro confirmado ignora role del cliente | [F-AUT-10](checklists-manuales.md#f-aut-10) |
| F-AUT-11 | Registro con correo duplicado | `tests/e2e/flows.spec.js` — F-AUT-10 F-AUT-11 F-ROL-06: registro confirmado ignora role del cliente | [F-AUT-11](checklists-manuales.md#f-aut-11) |
| F-AUT-12 | Política de contraseñas | `tests/unit/CoreTest.php` — F-AUT-12: política de contraseñas y almacenamiento seguro | [F-AUT-12](checklists-manuales.md#f-aut-12) |
| F-AUT-13 | Fijación y regeneración de sesión | `tests/e2e/security.spec.js` — F-AUT-13: cookie protegida y regeneración al autenticar | [F-AUT-13](checklists-manuales.md#f-aut-13) |
| F-ROL-01 | Visitante accede a /admin | `tests/e2e/security.spec.js` — F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR | [F-ROL-01](checklists-manuales.md#f-rol-01) |
| F-ROL-02 | Lector accede al panel admin | `tests/unit/CoreTest.php` — F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: matriz de permisos e IDOR<br>`tests/e2e/security.spec.js` — F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR | [F-ROL-02](checklists-manuales.md#f-rol-02) |
| F-ROL-03 | Editor intenta gestionar usuarios | `tests/unit/CoreTest.php` — F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: matriz de permisos e IDOR<br>`tests/e2e/security.spec.js` — F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR | [F-ROL-03](checklists-manuales.md#f-rol-03) |
| F-ROL-04 | Editor edita noticia ajena | `tests/unit/CoreTest.php` — F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: matriz de permisos e IDOR<br>`tests/e2e/security.spec.js` — F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR | [F-ROL-04](checklists-manuales.md#f-rol-04) |
| F-ROL-05 | Manipulación de ID (IDOR) | `tests/unit/CoreTest.php` — F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: matriz de permisos e IDOR<br>`tests/e2e/security.spec.js` — F-ROL-01 F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: autorización de rutas e IDOR | [F-ROL-05](checklists-manuales.md#f-rol-05) |
| F-ROL-06 | Escalada de privilegios por parámetro | `tests/e2e/flows.spec.js` — F-AUT-10 F-AUT-11 F-ROL-06: registro confirmado ignora role del cliente | [F-ROL-06](checklists-manuales.md#f-rol-06) |
| F-NOT-01 | Crear noticia válida | `tests/e2e/flows.spec.js` — F-NOT-01 F-NOT-08 F-NOT-09 U-16: crear, editar, cancelar, eliminar y restaurar noticia | [F-NOT-01](checklists-manuales.md#f-not-01) |
| F-NOT-02 | Título vacío | `tests/unit/CoreTest.php` — F-NOT-02 F-PUB-10: campos vacíos, espacios y límite de comentario | [F-NOT-02](checklists-manuales.md#f-not-02) |
| F-NOT-03 | Límite de título | `tests/unit/CoreTest.php` — F-NOT-03: límites de 149, 150 y 151 caracteres | [F-NOT-03](checklists-manuales.md#f-not-03) |
| F-NOT-04 | Contenido con HTML/JS | `tests/unit/CoreTest.php` — F-NOT-04 F-PUB-08: lista blanca contra XSS<br>`tests/e2e/security.spec.js` — F-NOT-04: XSS persistente se elimina al publicar | [F-NOT-04](checklists-manuales.md#f-not-04) |
| F-NOT-05 | Subida de imagen válida | `tests/e2e/security.spec.js` — F-NOT-05 F-NOT-06 A-VIS-02: alt obligatorio y tipos de archivo | [F-NOT-05](checklists-manuales.md#f-not-05) |
| F-NOT-06 | Subida de archivo inválido | `tests/e2e/security.spec.js` — F-NOT-05 F-NOT-06 A-VIS-02: alt obligatorio y tipos de archivo | [F-NOT-06](checklists-manuales.md#f-not-06) |
| F-NOT-07 | Tamaño máximo de archivo | `tests/e2e/admin.spec.js` — F-NOT-07: 5 MB admitidos y 5 MB más un byte rechazado | [F-NOT-07](checklists-manuales.md#f-not-07) |
| F-NOT-08 | Editar noticia existente | `tests/e2e/flows.spec.js` — F-NOT-01 F-NOT-08 F-NOT-09 U-16: crear, editar, cancelar, eliminar y restaurar noticia | [F-NOT-08](checklists-manuales.md#f-not-08) |
| F-NOT-09 | Eliminar noticia con confirmación | `tests/e2e/flows.spec.js` — F-NOT-01 F-NOT-08 F-NOT-09 U-16: crear, editar, cancelar, eliminar y restaurar noticia | [F-NOT-09](checklists-manuales.md#f-not-09) |
| F-NOT-10 | Flujo de estados | `tests/e2e/admin.spec.js` — F-NOT-10 F-NOT-11: estados privados y fecha de publicación programada | [F-NOT-10](checklists-manuales.md#f-not-10) |
| F-NOT-11 | Programar publicación | `tests/e2e/admin.spec.js` — F-NOT-10 F-NOT-11: estados privados y fecha de publicación programada | [F-NOT-11](checklists-manuales.md#f-not-11) |
| F-NOT-12 | Slug / URL amigable único | `tests/unit/CoreTest.php` — F-NOT-12 F-NOT-15: slug Unicode único | [F-NOT-12](checklists-manuales.md#f-not-12) |
| F-NOT-13 | Edición concurrente | `tests/e2e/flows.spec.js` — F-NOT-13: control de edición concurrente conserva el primer guardado | [F-NOT-13](checklists-manuales.md#f-not-13) |
| F-NOT-14 | Autoguardado de borrador | `tests/e2e/flows.spec.js` — F-NOT-14: borrador local se recupera después de recargar | [F-NOT-14](checklists-manuales.md#f-not-14) |
| F-NOT-15 | Caracteres especiales y Unicode | `tests/unit/CoreTest.php` — F-NOT-12 F-NOT-15: slug Unicode único | [F-NOT-15](checklists-manuales.md#f-not-15) |
| F-ADM-01 | Crear, editar y eliminar categoría | `tests/e2e/admin.spec.js` — F-ADM-01 F-ADM-02: categoría en uso requiere reasignación | [F-ADM-01](checklists-manuales.md#f-adm-01) |
| F-ADM-02 | Eliminar categoría con noticias | `tests/e2e/admin.spec.js` — F-ADM-01 F-ADM-02: categoría en uso requiere reasignación | [F-ADM-02](checklists-manuales.md#f-adm-02) |
| F-ADM-03 | Crear usuario con rol | `tests/e2e/admin.spec.js` — F-ADM-03 F-ADM-04: crear lector desactivado y denegar login | [F-ADM-03](checklists-manuales.md#f-adm-03) |
| F-ADM-04 | Desactivar usuario | `tests/e2e/admin.spec.js` — F-ADM-03 F-ADM-04: crear lector desactivado y denegar login | [F-ADM-04](checklists-manuales.md#f-adm-04) |
| F-ADM-05 | Admin no puede eliminarse a sí mismo (último admin) | `tests/e2e/security.spec.js` — F-ADM-05: último administrador no se elimina ni desactiva | [F-ADM-05](checklists-manuales.md#f-adm-05) |
| F-ADM-06 | Moderar comentario | `tests/e2e/flows.spec.js` — F-PUB-09 F-PUB-11 F-ADM-06: comentar, limitar tasa y moderar | [F-ADM-06](checklists-manuales.md#f-adm-06) |
| F-ADM-07 | Configuración del sitio | `tests/e2e/admin.spec.js` — F-ADM-07 F-ADM-08 F-ADM-09: configuración, estadísticas y auditoría reales | [F-ADM-07](checklists-manuales.md#f-adm-07) |
| F-ADM-08 | Dashboard con estadísticas | `tests/e2e/admin.spec.js` — F-ADM-07 F-ADM-08 F-ADM-09: configuración, estadísticas y auditoría reales | [F-ADM-08](checklists-manuales.md#f-adm-08) |
| F-ADM-09 | Registro de auditoría | `tests/e2e/admin.spec.js` — F-ADM-07 F-ADM-08 F-ADM-09: configuración, estadísticas y auditoría reales | [F-ADM-09](checklists-manuales.md#f-adm-09) |
| F-ADM-10 | Copia de seguridad y restauración | manual | [F-ADM-10](checklists-manuales.md#f-adm-10) |
| F-PUB-01 | Ver portada | `tests/e2e/portal.spec.js` — F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404 | [F-PUB-01](checklists-manuales.md#f-pub-01) |
| F-PUB-02 | Paginación | `tests/unit/CoreTest.php` — F-PUB-02: páginas inválidas y fuera de rango<br>`tests/e2e/portal.spec.js` — F-PUB-02: paginación inválida se normaliza | [F-PUB-02](checklists-manuales.md#f-pub-02) |
| F-PUB-03 | Filtrar por categoría y etiqueta | `tests/e2e/portal.spec.js` — F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404 | [F-PUB-03](checklists-manuales.md#f-pub-03) |
| F-PUB-04 | Detalle de noticia | `tests/e2e/portal.spec.js` — F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404<br>`tests/mobile/mobile.spec.js` — EF-17 F-PUB-04 F-PUB-06 F-PUB-09: lector móvil lee, busca y comenta | [F-PUB-04](checklists-manuales.md#f-pub-04) |
| F-PUB-05 | Noticia inexistente | `tests/e2e/portal.spec.js` — F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404 | [F-PUB-05](checklists-manuales.md#f-pub-05) |
| F-PUB-06 | Búsqueda con resultados | `tests/e2e/flows.spec.js` — F-PUB-06 F-PUB-07 F-PUB-08: búsqueda, resaltado y estados vacíos<br>`tests/mobile/mobile.spec.js` — EF-17 F-PUB-04 F-PUB-06 F-PUB-09: lector móvil lee, busca y comenta | [F-PUB-06](checklists-manuales.md#f-pub-06) |
| F-PUB-07 | Búsqueda sin resultados | `tests/e2e/flows.spec.js` — F-PUB-06 F-PUB-07 F-PUB-08: búsqueda, resaltado y estados vacíos | [F-PUB-07](checklists-manuales.md#f-pub-07) |
| F-PUB-08 | Búsqueda con caracteres peligrosos | `tests/unit/CoreTest.php` — F-NOT-04 F-PUB-08: lista blanca contra XSS<br>`tests/e2e/flows.spec.js` — F-PUB-06 F-PUB-07 F-PUB-08: búsqueda, resaltado y estados vacíos | [F-PUB-08](checklists-manuales.md#f-pub-08) |
| F-PUB-09 | Publicar comentario como lector | `tests/e2e/flows.spec.js` — F-PUB-09 F-PUB-11 F-ADM-06: comentar, limitar tasa y moderar<br>`tests/mobile/mobile.spec.js` — EF-17 F-PUB-04 F-PUB-06 F-PUB-09: lector móvil lee, busca y comenta | [F-PUB-09](checklists-manuales.md#f-pub-09) |
| F-PUB-10 | Comentario vacío o excesivo | `tests/unit/CoreTest.php` — F-NOT-02 F-PUB-10: campos vacíos, espacios y límite de comentario | [F-PUB-10](checklists-manuales.md#f-pub-10) |
| F-PUB-11 | Anti-spam en comentarios | `tests/e2e/flows.spec.js` — F-PUB-09 F-PUB-11 F-ADM-06: comentar, limitar tasa y moderar | [F-PUB-11](checklists-manuales.md#f-pub-11) |
| F-PUB-12 | Suscripción al boletín | `tests/unit/CoreTest.php` — F-AUT-04 F-PUB-12: correo inválido y parámetros malformados<br>`tests/e2e/flows.spec.js` — F-PUB-12 F-PUB-14 F-PUB-16: boletín confirmado, RSS y cookies | [F-PUB-12](checklists-manuales.md#f-pub-12) |
| F-PUB-13 | Compartir en redes | `tests/e2e/portal.spec.js` — F-PUB-01 F-PUB-03 F-PUB-04 F-PUB-05 F-PUB-13: portada, filtros, noticia, Open Graph y 404 | [F-PUB-13](checklists-manuales.md#f-pub-13) |
| F-PUB-14 | Feed RSS | `tests/e2e/flows.spec.js` — F-PUB-12 F-PUB-14 F-PUB-16: boletín confirmado, RSS y cookies | [F-PUB-14](checklists-manuales.md#f-pub-14) |
| F-PUB-15 | Protección CSRF | `tests/unit/CoreTest.php` — F-PUB-15: CSRF rechaza tokens vacíos, distintos y arrays<br>`tests/e2e/security.spec.js` — F-PUB-15: POST sin CSRF es rechazado | [F-PUB-15](checklists-manuales.md#f-pub-15) |
| F-PUB-16 | Gestión de cookies | `tests/e2e/flows.spec.js` — F-PUB-12 F-PUB-14 F-PUB-16: boletín confirmado, RSS y cookies | [F-PUB-16](checklists-manuales.md#f-pub-16) |
| U-01 | Reconocibilidad del propósito | manual | [U-01](checklists-manuales.md#u-01) |
| U-02 | Navegación principal clara | manual | [U-02](checklists-manuales.md#u-02) |
| U-03 | Tarea: encontrar una noticia por búsqueda | manual | [U-03](checklists-manuales.md#u-03) |
| U-04 | Tarea del admin: publicar una noticia | manual | [U-04](checklists-manuales.md#u-04) |
| U-05 | Curva de aprendizaje | manual | [U-05](checklists-manuales.md#u-05) |
| U-06 | Visibilidad del estado del sistema | manual | [U-06](checklists-manuales.md#u-06) |
| U-07 | Prevención de errores | `tests/e2e/portal.spec.js` — F-AUT-04 U-07 U-08 A-COG-03: errores de servidor asociados y correo preservado | [U-07](checklists-manuales.md#u-07) |
| U-08 | Mensajes de error comprensibles | `tests/e2e/portal.spec.js` — F-AUT-04 U-07 U-08 A-COG-03: errores de servidor asociados y correo preservado | [U-08](checklists-manuales.md#u-08) |
| U-09 | Consistencia y estándares | manual | [U-09](checklists-manuales.md#u-09) |
| U-10 | Responsividad (móvil, tablet, escritorio) | `tests/mobile/mobile.spec.js` — U-10 E-11 A-VIS-10: reflujo en 320, 360, 768, 1280 y 1920 píxeles<br>`tests/mobile/mobile.spec.js` — U-10 A-TEC-06 EF-34: panel móvil, creación y orientación horizontal | [U-10](checklists-manuales.md#u-10) |
| U-11 | Facilidad táctil | `tests/mobile/mobile.spec.js` — U-11 A-MOT-05 U-12: menú móvil, Escape, controles táctiles y formulario | [U-11](checklists-manuales.md#u-11) |
| U-12 | Formularios usables | `tests/mobile/mobile.spec.js` — U-11 A-MOT-05 U-12: menú móvil, Escape, controles táctiles y formulario | [U-12](checklists-manuales.md#u-12) |
| U-13 | Legibilidad del contenido | manual | [U-13](checklists-manuales.md#u-13) |
| U-14 | Ayuda y documentación del admin | manual | [U-14](checklists-manuales.md#u-14) |
| U-15 | Migas de pan y orientación | manual | [U-15](checklists-manuales.md#u-15) |
| U-16 | Deshacer y control del usuario | `tests/e2e/flows.spec.js` — F-NOT-01 F-NOT-08 F-NOT-09 U-16: crear, editar, cancelar, eliminar y restaurar noticia | [U-16](checklists-manuales.md#u-16) |
| U-17 | Usabilidad para adultos mayores | manual | [U-17](checklists-manuales.md#u-17) |
| U-18 | Usabilidad para personas con discapacidad | manual | [U-18](checklists-manuales.md#u-18) |
| U-19 | Satisfacción global | manual | [U-19](checklists-manuales.md#u-19) |
| U-20 | Página 404 y estados vacíos | manual | [U-20](checklists-manuales.md#u-20) |
| A-VIS-01 | 1.1.1 | `tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas | [A-VIS-01](checklists-manuales.md#a-vis-01) |
| A-VIS-02 | 1.1.1 | `tests/e2e/security.spec.js` — F-NOT-05 F-NOT-06 A-VIS-02: alt obligatorio y tipos de archivo | [A-VIS-02](checklists-manuales.md#a-vis-02) |
| A-VIS-03 | 1.3.1 / 4.1.2 | `tests/unit/CoreTest.php` — A-TEC-05 A-VIS-03: editor valida jerarquía de encabezados<br>`tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas | [A-VIS-03](checklists-manuales.md#a-vis-03) |
| A-VIS-04 | 2.4.1 | `tests/a11y/templates.spec.js` — A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape | [A-VIS-04](checklists-manuales.md#a-vis-04) |
| A-VIS-05 | 1.4.3 | `tests/a11y/templates.spec.js` — A-VIS-05 A-VIS-06 A-VIS-16 E-12: modo oscuro y contraste de lectura<br>`tests/layout/contrast.mjs` — contraste calculado de 18 pares de tokens en claro y oscuro; no evalúa la composición final. | [A-VIS-05](checklists-manuales.md#a-vis-05) |
| A-VIS-06 | 1.4.6 (AAA) | `tests/a11y/templates.spec.js` — A-VIS-05 A-VIS-06 A-VIS-16 E-12: modo oscuro y contraste de lectura<br>`tests/layout/contrast.mjs` — contraste calculado de 18 pares de tokens en claro y oscuro; no evalúa la composición final. | [A-VIS-06](checklists-manuales.md#a-vis-06) |
| A-VIS-07 | 1.4.11 | `tests/layout/contrast.mjs` — contraste calculado de 18 pares de tokens en claro y oscuro; no evalúa la composición final. | [A-VIS-07](checklists-manuales.md#a-vis-07) |
| A-VIS-08 | 1.4.1 | manual | [A-VIS-08](checklists-manuales.md#a-vis-08) |
| A-VIS-09 | 1.4.4 | manual | [A-VIS-09](checklists-manuales.md#a-vis-09) |
| A-VIS-10 | 1.4.10 | `tests/mobile/mobile.spec.js` — U-10 E-11 A-VIS-10: reflujo en 320, 360, 768, 1280 y 1920 píxeles | [A-VIS-10](checklists-manuales.md#a-vis-10) |
| A-VIS-11 | 1.4.12 | manual | [A-VIS-11](checklists-manuales.md#a-vis-11) |
| A-VIS-12 | 1.4.5 | manual | [A-VIS-12](checklists-manuales.md#a-vis-12) |
| A-VIS-13 | 1.4.13 | manual | [A-VIS-13](checklists-manuales.md#a-vis-13) |
| A-VIS-14 | 4.1.3 | manual | [A-VIS-14](checklists-manuales.md#a-vis-14) |
| A-VIS-15 | Tecnologías de asistencia | manual | [A-VIS-15](checklists-manuales.md#a-vis-15) |
| A-VIS-16 | 1.4.8 / modos | `tests/a11y/templates.spec.js` — A-VIS-05 A-VIS-06 A-VIS-16 E-12: modo oscuro y contraste de lectura | [A-VIS-16](checklists-manuales.md#a-vis-16) |
| A-AUD-01 | 1.2.2 | manual | [A-AUD-01](checklists-manuales.md#a-aud-01) |
| A-AUD-02 | 1.2.1 | manual | [A-AUD-02](checklists-manuales.md#a-aud-02) |
| A-AUD-03 | 1.2.3 / 1.2.5 | manual | [A-AUD-03](checklists-manuales.md#a-aud-03) |
| A-AUD-04 | 1.4.2 | manual | [A-AUD-04](checklists-manuales.md#a-aud-04) |
| A-AUD-05 | 1.2.4 | manual | [A-AUD-05](checklists-manuales.md#a-aud-05) |
| A-AUD-06 | 3.3 / General | manual | [A-AUD-06](checklists-manuales.md#a-aud-06) |
| A-AUD-07 | 3.1.5 (AAA) | manual | [A-AUD-07](checklists-manuales.md#a-aud-07) |
| A-AUD-08 | Reproductor | manual | [A-AUD-08](checklists-manuales.md#a-aud-08) |
| A-MOT-01 | 2.1.1 | `tests/a11y/templates.spec.js` — A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape | [A-MOT-01](checklists-manuales.md#a-mot-01) |
| A-MOT-02 | 2.1.2 | `tests/a11y/templates.spec.js` — A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape | [A-MOT-02](checklists-manuales.md#a-mot-02) |
| A-MOT-03 | 2.4.3 | `tests/a11y/templates.spec.js` — A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape | [A-MOT-03](checklists-manuales.md#a-mot-03) |
| A-MOT-04 | 2.4.7 / 2.4.11 | `tests/a11y/templates.spec.js` — A-VIS-04 A-MOT-01 A-MOT-02 A-MOT-03 A-MOT-04: teclado, salto y modal con Escape<br>`tests/layout/contrast.mjs` — contraste calculado de 18 pares de tokens en claro y oscuro; no evalúa la composición final. | [A-MOT-04](checklists-manuales.md#a-mot-04) |
| A-MOT-05 | 2.5.8 | `tests/mobile/mobile.spec.js` — U-11 A-MOT-05 U-12: menú móvil, Escape, controles táctiles y formulario | [A-MOT-05](checklists-manuales.md#a-mot-05) |
| A-MOT-06 | 2.5.1 | manual | [A-MOT-06](checklists-manuales.md#a-mot-06) |
| A-MOT-07 | 2.5.7 | manual | [A-MOT-07](checklists-manuales.md#a-mot-07) |
| A-MOT-08 | 2.5.2 | manual | [A-MOT-08](checklists-manuales.md#a-mot-08) |
| A-MOT-09 | 2.2.1 | manual | [A-MOT-09](checklists-manuales.md#a-mot-09) |
| A-MOT-10 | 2.5.4 | manual | [A-MOT-10](checklists-manuales.md#a-mot-10) |
| A-MOT-11 | 3.2.1 / 3.2.2 | manual | [A-MOT-11](checklists-manuales.md#a-mot-11) |
| A-MOT-12 | Entrada alternativa | manual | [A-MOT-12](checklists-manuales.md#a-mot-12) |
| A-MOT-13 | 3.3.7 | manual | [A-MOT-13](checklists-manuales.md#a-mot-13) |
| A-MOT-14 | 3.3.8 | manual | [A-MOT-14](checklists-manuales.md#a-mot-14) |
| A-COG-01 | 3.1.1 / 3.1.2 | `tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas | [A-COG-01](checklists-manuales.md#a-cog-01) |
| A-COG-02 | 3.1.3–3.1.5 | manual | [A-COG-02](checklists-manuales.md#a-cog-02) |
| A-COG-03 | 3.3.1 / 3.3.3 | `tests/e2e/portal.spec.js` — F-AUT-04 U-07 U-08 A-COG-03: errores de servidor asociados y correo preservado | [A-COG-03](checklists-manuales.md#a-cog-03) |
| A-COG-04 | 3.3.2 | manual | [A-COG-04](checklists-manuales.md#a-cog-04) |
| A-COG-05 | 3.3.4 | manual | [A-COG-05](checklists-manuales.md#a-cog-05) |
| A-COG-06 | 3.2.3 / 3.2.4 | manual | [A-COG-06](checklists-manuales.md#a-cog-06) |
| A-COG-07 | 3.2.6 | manual | [A-COG-07](checklists-manuales.md#a-cog-07) |
| A-COG-08 | 2.2.2 | manual | [A-COG-08](checklists-manuales.md#a-cog-08) |
| A-COG-09 | 2.3.1 | manual | [A-COG-09](checklists-manuales.md#a-cog-09) |
| A-COG-10 | 2.2.6 | manual | [A-COG-10](checklists-manuales.md#a-cog-10) |
| A-COG-11 | 1.3.5 | manual | [A-COG-11](checklists-manuales.md#a-cog-11) |
| A-COG-12 | Cognitiva | manual | [A-COG-12](checklists-manuales.md#a-cog-12) |
| A-COG-13 | Cognitiva | manual | [A-COG-13](checklists-manuales.md#a-cog-13) |
| A-COG-14 | Cognitiva | manual | [A-COG-14](checklists-manuales.md#a-cog-14) |
| A-TEC-01 | Análisis automatizado de todas las plantillas | `tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas<br>`tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-05: axe en todas las plantillas del panel y página 403 | [A-TEC-01](checklists-manuales.md#a-tec-01) |
| A-TEC-02 | HTML válido | manual | [A-TEC-02](checklists-manuales.md#a-tec-02) |
| A-TEC-03 | Uso correcto de ARIA (primera regla: usar HTML nativo) | `tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-03 A-VIS-01 A-VIS-03 A-COG-01: axe en todas las plantillas públicas | [A-TEC-03](checklists-manuales.md#a-tec-03) |
| A-TEC-04 | Árbol de accesibilidad | manual | [A-TEC-04](checklists-manuales.md#a-tec-04) |
| A-TEC-05 | Accesibilidad del panel admin y editor de contenido (ATAG 2.0) | `tests/unit/CoreTest.php` — A-TEC-05 A-VIS-03: editor valida jerarquía de encabezados<br>`tests/a11y/templates.spec.js` — A-TEC-01 A-TEC-05: axe en todas las plantillas del panel y página 403 | [A-TEC-05](checklists-manuales.md#a-tec-05) |
| A-TEC-06 | Orientación | `tests/mobile/mobile.spec.js` — U-10 A-TEC-06 EF-34: panel móvil, creación y orientación horizontal | [A-TEC-06](checklists-manuales.md#a-tec-06) |
| A-TEC-07 | Declaración de accesibilidad | manual | [A-TEC-07](checklists-manuales.md#a-tec-07) |
| A-TEC-08 | CAPTCHA accesible | manual | [A-TEC-08](checklists-manuales.md#a-tec-08) |
| E-01 | Guía de estilo definida | manual | [E-01](checklists-manuales.md#e-01) |
| E-02 | Paleta de colores | manual | [E-02](checklists-manuales.md#e-02) |
| E-03 | Tipografía | manual | [E-03](checklists-manuales.md#e-03) |
| E-04 | Coherencia entre páginas | manual | [E-04](checklists-manuales.md#e-04) |
| E-05 | Coherencia entre portal y panel admin | manual | [E-05](checklists-manuales.md#e-05) |
| E-06 | Retícula y espaciado | manual | [E-06](checklists-manuales.md#e-06) |
| E-07 | Jerarquía visual | manual | [E-07](checklists-manuales.md#e-07) |
| E-08 | Imágenes | manual | [E-08](checklists-manuales.md#e-08) |
| E-09 | Estados de componentes | manual | [E-09](checklists-manuales.md#e-09) |
| E-10 | Iconografía | manual | [E-10](checklists-manuales.md#e-10) |
| E-11 | Responsividad visual | `tests/mobile/mobile.spec.js` — U-10 E-11 A-VIS-10: reflujo en 320, 360, 768, 1280 y 1920 píxeles | [E-11](checklists-manuales.md#e-11) |
| E-12 | Modo oscuro (si aplica) | `tests/a11y/templates.spec.js` — A-VIS-05 A-VIS-06 A-VIS-16 E-12: modo oscuro y contraste de lectura | [E-12](checklists-manuales.md#e-12) |
| E-13 | Microinteracciones y animaciones | manual | [E-13](checklists-manuales.md#e-13) |
| E-14 | Impresión | manual | [E-14](checklists-manuales.md#e-14) |
| E-15 | Lenguaje y tono de interfaz | manual | [E-15](checklists-manuales.md#e-15) |
| E-16 | Calidad del código CSS | manual | [E-16](checklists-manuales.md#e-16) |
| E-17 | Percepción estética | manual | [E-17](checklists-manuales.md#e-17) |
| E-18 | Evaluación experta | manual | [E-18](checklists-manuales.md#e-18) |
| E-19 | Consistencia entre navegadores | manual | [E-19](checklists-manuales.md#e-19) |
| E-20 | Estilo vs. accesibilidad | manual | [E-20](checklists-manuales.md#e-20) |
| EF-01 | LCP (Largest Contentful Paint) | `tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse | [EF-01](checklists-manuales.md#ef-01) |
| EF-02 | INP (Interaction to Next Paint) | manual | [EF-02](checklists-manuales.md#ef-02) |
| EF-03 | CLS (Cumulative Layout Shift) | `tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse | [EF-03](checklists-manuales.md#ef-03) |
| EF-04 | FCP, TBT y Speed Index | `tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse | [EF-04](checklists-manuales.md#ef-04) |
| EF-05 | Puntuación global | `tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse | [EF-05](checklists-manuales.md#ef-05) |
| EF-06 | Peso total de página | `tests/perf/lighthouserc.json` — aserciones móviles de LCP, CLS, TBT, Performance y bytes; salida en work/lighthouse | [EF-06](checklists-manuales.md#ef-06) |
| EF-07 | Número de peticiones HTTP | manual | [EF-07](checklists-manuales.md#ef-07) |
| EF-08 | Optimización de imágenes | `tests/mobile/mobile.spec.js` — EF-15 EF-08: Save-Data usa imagen ligera con dimensiones | [EF-08](checklists-manuales.md#ef-08) |
| EF-09 | Minificación y compresión | manual | [EF-09](checklists-manuales.md#ef-09) |
| EF-10 | Caché del navegador | manual | [EF-10](checklists-manuales.md#ef-10) |
| EF-11 | JavaScript mínimo | manual | [EF-11](checklists-manuales.md#ef-11) |
| EF-12 | Funcionamiento sin JS (mejora progresiva) | `tests/mobile/mobile.spec.js` — EF-12: lectura y login funcionan sin JavaScript | [EF-12](checklists-manuales.md#ef-12) |
| EF-13 | Uso de CPU y memoria en cliente | manual | [EF-13](checklists-manuales.md#ef-13) |
| EF-14 | Consumo de batería y datos | manual | [EF-14](checklists-manuales.md#ef-14) |
| EF-15 | Modo de datos reducido | `tests/mobile/mobile.spec.js` — EF-15 EF-08: Save-Data usa imagen ligera con dimensiones | [EF-15](checklists-manuales.md#ef-15) |
| EF-16 | Fuentes web | manual | [EF-16](checklists-manuales.md#ef-16) |
| EF-17 | Dispositivo de recursos limitados | `tests/mobile/mobile.spec.js` — EF-17 F-PUB-04 F-PUB-06 F-PUB-09: lector móvil lee, busca y comenta | [EF-17](checklists-manuales.md#ef-17) |
| EF-18 | Modo sin conexión | manual | [EF-18](checklists-manuales.md#ef-18) |
| EF-20 | TTFB (Time to First Byte) | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-20](checklists-manuales.md#ef-20) |
| EF-21 | Prueba de carga nominal | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-21](checklists-manuales.md#ef-21) |
| EF-22 | Prueba de estrés | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-22](checklists-manuales.md#ef-22) |
| EF-23 | Prueba de picos (noticia viral) | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-23](checklists-manuales.md#ef-23) |
| EF-24 | Prueba de resistencia (soak) | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-24](checklists-manuales.md#ef-24) |
| EF-25 | Consultas SQL eficientes | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-25](checklists-manuales.md#ef-25) |
| EF-26 | Caché del servidor | manual | [EF-26](checklists-manuales.md#ef-26) |
| EF-27 | Uso de CPU/RAM del servidor | manual | [EF-27](checklists-manuales.md#ef-27) |
| EF-28 | Paginación y volumen de datos | `tests/perf/load.js` — etiquetas de caso en solicitudes/checks; ejecutar el perfil correspondiente. TTFB, EXPLAIN y volumen exigen además revisión manual. | [EF-28](checklists-manuales.md#ef-28) |
| EF-29 | Subida de archivos pesados | manual | [EF-29](checklists-manuales.md#ef-29) |
| EF-30 | Conexiones a BD | manual | [EF-30](checklists-manuales.md#ef-30) |
| EF-31 | Recuperación ante fallos | manual | [EF-31](checklists-manuales.md#ef-31) |
| EF-32 | Escalabilidad | manual | [EF-32](checklists-manuales.md#ef-32) |
| EF-33 | Compresión y HTTP/2 o HTTP/3 | manual | [EF-33](checklists-manuales.md#ef-33) |
| EF-34 | Rendimiento del panel admin | `tests/mobile/mobile.spec.js` — U-10 A-TEC-06 EF-34: panel móvil, creación y orientación horizontal | [EF-34](checklists-manuales.md#ef-34) |
