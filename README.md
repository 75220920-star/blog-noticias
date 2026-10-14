# Pulso · Blog de noticias autoadministrable

PHP 8.2+, PDO, MySQL/MariaDB, Apache; MVC sencillo, sin framework PHP. Interfaz en español con portal, cuentas verificadas, panel con roles y diseño mobile-first. El código se instala en `C:\xampp\htdocs\blog-noticias`; la única carpeta pública es `public/`.

## Requisitos

- XAMPP con Apache y MySQL/MariaDB iniciado desde su panel de control.
- PHP 8.2 o posterior con PDO MySQL, mbstring, DOM, fileinfo, GD, soporte WebP y Argon2id (incluido en este XAMPP). En `C:\xampp\php\php.ini` habilitar `extension=gd`, `extension=pdo_mysql` y `extension=mbstring` si están desactivadas; reiniciar Apache. Comprobar con `php -m`, `php -r "var_dump(function_exists('imagewebp'));"` y `php -r "print_r(password_algos());"`.
- Ajustar `upload_max_filesize=5M`, `post_max_size=8M`, `memory_limit=128M`. Se limitan imágenes a 12 megapíxeles para evitar descompresión excesiva.
- Composer 2, Node.js 20.19+ y npm para las herramientas de desarrollo. El portal no necesita Node, Composer, `vendor/` ni `node_modules/` para atender peticiones.
- k6 instalado por separado para carga. Chrome/Chromium para Lighthouse; Playwright descarga Chromium, Firefox y WebKit.

## Instalación en XAMPP

1. Copiar el contenido de esta entrega a `C:\xampp\htdocs\blog-noticias`, conservando la estructura. No mover archivos privados dentro de `public/`.
2. Iniciar Apache y MySQL en el panel de XAMPP. Verificar que los puertos 80 y 3306 están libres o adaptar las direcciones.
3. Abrir phpMyAdmin e importar primero `database/schema.sql` y después `database/seed.sql`. El esquema crea `blog_noticias` con utf8mb4. Importar una sola vez sobre una BD vacía; no ejecutar sobre datos reales existentes. La semilla contiene 1 admin, 2 editores, 5 lectores, 8 categorías, 60 noticias y 100 comentarios; 54 noticias están publicadas.
4. Copiar `config/config.example.php` a `config/config.php` si no existe. Cambiar DSN, usuario, contraseña, `base_path` y `url` según el servidor. El ejemplo utiliza root sin contraseña SOLO como valor predeterminado de XAMPP local. Usar un usuario MySQL dedicado en cualquier instalación pública. `BLOG_DB_DSN`, `BLOG_DB_USER`, `BLOG_DB_PASSWORD`, `BLOG_BASE_PATH`, `BLOG_URL` y `BLOG_DEBUG=1` pueden sobrescribir la configuración sin poner secretos en el repositorio.
5. En `C:\xampp\apache\conf\httpd.conf`, habilitar `LoadModule rewrite_module modules/mod_rewrite.so` y permitir `AllowOverride All` en el directorio de htdocs. Mantener mod_headers y mod_deflate habilitados. Reiniciar Apache.
6. En una terminal de esta carpeta ejecutar `composer install`, `npm install` y `npm run build`. Se incluyen los assets ya compilados para poder abrir el portal antes de instalar herramientas de QA. En PowerShell, si la política impide ejecutar npm.ps1, usar `npm.cmd`.
7. Abrir `http://localhost/blog-noticias/public/`. El acceso a `/blog-noticias/app`, `/config`, `/database` y `/storage` debe devolver 403. `.htaccess` raíz deniega el código privado y el de `public/` habilita el portal.
8. Dar al usuario de Apache permiso de escritura solo en `storage/cache`, `storage/logs` y `public/uploads`. No habilitar listado ni ejecución de scripts en uploads. GD renombra y recodifica JPG/PNG/WebP en 1200×675 y 480×270, eliminando metadatos.
9. Configurar un correo real de contacto en el panel antes de publicar. En producción mantener `debug=false`, servir por HTTPS y cambiar todas las claves demo. Secure se habilita cuando Apache informa HTTPS; detrás de un proxy, configurar HTTPS en el servidor, sin confiar automáticamente en cabeceras de cliente.

## Credenciales de demostración

Todas usan **`DemoNoticias2026!`** y están verificadas:

| Rol | Correo |
|---|---|
| Administrador | `admin@pulso.test` |
| Editor 1 | `editor1@pulso.test` |
| Editor 2 | `editor2@pulso.test` |
| Lectores | `lector1@pulso.test` a `lector5@pulso.test` |

Los editores solo gestionan sus noticias. Las categorías, etiquetas, usuarios, moderación, configuración y auditoría son exclusivas del administrador. Las cuentas desactivadas pierden acceso en la siguiente solicitud. No puede eliminarse, desactivarse ni degradarse el último administrador activo, incluso con solicitudes concurrentes.

## Uso y recuperación local

Registro y boletín necesitan confirmación. En este entorno **no se envía correo externo**: cada mensaje y enlace se guarda como una línea JSON privada en `storage/logs/mail.log`. Abrir el enlace y pulsar Confirmar. Las confirmaciones caducan a las 24 horas. La recuperación de clave dura 30 minutos y su token se invalida al usarlo; solo se almacena su hash en la BD. No publicar ni adjuntar ese log a reportes.

Noticias: borrador, revisión, publicado y archivado. La política permite pasar entre los cuatro estados mediante guardado autorizado; solo `publicado` con fecha vencida es visible. Para programar elegir Publicado y una fecha futura, en hora de Lima. Las consultas comprueban la fecha aun sin cron; la caché de portada dura hasta 30 segundos. Para invalidarla cada minuto, crear una tarea de Windows que ejecute `C:\xampp\php\php.exe C:\xampp\htdocs\blog-noticias\bin\publish.php` (o cron equivalente). El guardado, la eliminación, la restauración y los cambios de catálogos/configuración invalidan la caché de inmediato.

El editor es un textarea HTML accesible con instrucciones de etiquetas y validación de niveles h2/h3/h4. No admite imágenes dentro del contenido ni multimedia; se utiliza una imagen destacada con alt obligatorio. El HTML se reconstruye con lista blanca, sin atributos de eventos ni enlaces javascript. El borrador se guarda tras 600 ms sin teclear en el almacenamiento local del navegador y se recupera con un botón al recargar. No es autoguardado en el servidor ni sincronización entre dispositivos; las imágenes deben volver a seleccionarse tras un error. Evitar equipos compartidos para borradores sensibles.

Las noticias y los usuarios van a una papelera reversible. Las eliminaciones de categorías/etiquetas se confirman y son definitivas; una categoría en uso exige reasignación, incluyendo noticias en papelera. Comentarios limitados a lectores, 1000 caracteres, un envío por minuto, honeypot y tiempo mínimo de 2 segundos. Se revisan antes de mostrarse. Los contadores son consultas reales y el registro de auditoría almacena actor, acción, entidad, fecha e IP.

## Pruebas ejecutables y trazabilidad

El plan original de `docs/plan-de-pruebas.md` fue leído antes de implementar y se conserva íntegro. `docs/matriz-trazabilidad.md` cubre cada ID, indicando automatización real o un procedimiento manual. Las comprobaciones automáticas no acreditan por sí solas la conformidad WCAG ni la satisfacción de usuarios.

```powershell
composer test
composer lint
npm run build
npx playwright install chromium firefox webkit
# Las pruebas integradas BORRAN y recrean únicamente blog_noticias_test.
$env:BLOG_DB_DSN='mysql:host=127.0.0.1;port=3306;dbname=blog_noticias_test;charset=utf8mb4'
npm run test:e2e
npm run test:a11y
npm run test:mobile
npm run test:perf
npm run test:load
```

Playwright inicia PHP con GD y límites de subida en `127.0.0.1:8088`. El servidor de MySQL debe estar activo y el usuario de pruebas debe poder crear/eliminar SOLO `blog_noticias_test`. La comprobación de nombre impide reiniciar la BD principal. Ejecutar comandos secuencialmente: cada suite recrea los datos y no debe correr simultáneamente contra la misma BD. Se ejecutan proyectos Chromium, Pixel 5, iPhone 12/WebKit y gama baja 360×640; el último aplica CPU ×6 y red 3G simulada. Firefox también verifica móvil. La emulación no reemplaza los dispositivos físicos. Las trazas y capturas se guardan en `test-results/`; el reporte HTML en `playwright-report/`.

Lighthouse usa preset móvil explícito, CPU ×4, tres ejecuciones, Performance ≥ 90, Accessibility 100, LCP ≤ 2500 ms, CLS ≤ 0.1, TBT ≤ 200 ms y peso ≤ 1 MB. Debe ejecutarse sobre Apache iniciado en la URL de XAMPP. Para otra URL cambiar `tests/perf/lighthouserc.json`; para Chrome definir `CHROME_PATH`. TBT es diagnóstico de laboratorio, **no sustituye INP**: medir interacción con DevTools/Web Vitals y datos de campo cuando haya tráfico. Para red 3G repetir con 400 ms, 400 kbps y CPU ×6; verificar LCP ≤ 4 s y lectura ≤ 5 s. No se atribuyen métricas sin ejecutar y conservar los informes.

```powershell
# Solo ejecutar contra infraestructura de pruebas preparada.
k6 run tests/perf/load.js
k6 run -e MODE=spike tests/perf/load.js
k6 run -e MODE=stress tests/perf/load.js
k6 run -e MODE=soak tests/perf/load.js
```

k6: 100 usuarios constantes durante 10 minutos, pico 50→1000 en un minuto y recuperación en 2 minutos, escalones de estrés y resistencia de 4 horas. `BASE_URL` cambia el destino. No se ejecutan estas cargas sobre el servidor de desarrollo de PHP, que atiende solicitudes de forma secuencial; usar Apache en un entorno de ensayo. El plan pide también 500 noticias/20 categorías/50 usuarios/2000 comentarios; el pedido de entrega requiere 60/8/8/100. Para ampliar únicamente la BD de QA ejecutar `php tests/support/volume.php 500`, y para EF-28 `php tests/support/volume.php 100000`, después de un reinicio de pruebas. Los datos ampliados son sintéticos, no una nueva semilla editorial.

## Celulares reales

1. Conectar PC y celular a la misma Wi-Fi. Obtener la IPv4 de la PC con `ipconfig`, por ejemplo 192.168.1.20. Abrir `http://192.168.1.20/blog-noticias/public/` en Android o iPhone; no usar localhost en el celular.
2. Permitir Apache en el firewall de Windows para la red privada. Comprobar que Apache escucha en esa IP, que el router no aísla clientes y que ambos equipos están en la misma subred. No abrir la BD ni phpMyAdmin a Internet.
3. Para compartir y enlaces de recuperación desde el celular, ajustar `url` a `http://IP-DE-LA-PC/blog-noticias/public`; `base_path` conserva su valor. Para Lighthouse mantener la URL apropiada al equipo que ejecuta la prueba.
4. Android: activar opciones de desarrollador y depuración USB, autorizar la PC, conectar por USB y abrir `chrome://inspect/#devices` en Chrome de la PC; inspeccionar la pestaña del celular. iPhone: activar Inspector web en Safari del dispositivo y usar Safari → Desarrollo en un Mac con el dispositivo conectado. Desde Windows hacer la revisión funcional y usar WebKit emulado; no equivale a inspección remota de Safari.
5. Alternativa con túnel HTTPS: con ngrok instalado ejecutar `ngrok http 80`, o con Cloudflare Tunnel `cloudflared tunnel --url http://localhost:80`. Abrir `https://DOMINIO-DEL-TUNEL/blog-noticias/public/` y configurar `url` con esa base. Son servicios externos con condiciones propias. Para exposición pública configurar el origen del túnel SOLO hacia `public/` en un VirtualHost dedicado, proteger otras aplicaciones de XAMPP, cambiar claves demo y activar HTTPS de forma correcta en Apache. No se configura ni publica un túnel automáticamente.
6. Seguir `docs/checklists-manuales.md` en al menos un Android de gama baja (2 GB de RAM, red lenta) y un celular de gama media/alta; añadir un iPhone cuando esté disponible. Probar orientación, zoom, teclado virtual, TalkBack/VoiceOver, inicio de sesión, lectura, búsqueda, comentario y edición del panel.

`manifest.webmanifest`, iconos PNG 192/512, icono SVG, theme-color y apple-touch-icon permiten guardar la web en pantalla de inicio. No se instala service worker: no hay lectura sin conexión y no se cachean sesiones privadas. La instalación como PWA depende del navegador y puede exigir HTTPS, excepto localhost.

## Caché, recursos y operación

CSS/JS propios, sin librerías en el navegador. `npm run build` minifica de forma conservadora con Node estándar y no necesita dependencias instaladas. WebP, srcset/sizes, dimensiones explícitas, carga diferida debajo del pliegue y versión ligera con Save-Data. La portada solo cachea datos, nunca tokens CSRF ni HTML de una sesión. Estáticos con caché de siete días; incrementar versión de URL o limpiar caché al desplegar cambios. mod_deflate comprime texto; HTTPS/HTTP2 y Brotli requieren configuración del servidor y no se garantizan en XAMPP predeterminado.

Para OPcache habilitar `zend_extension=opcache`, `opcache.enable=1`, `opcache.memory_consumption=128`, `opcache.max_accelerated_files=10000` en php.ini; en desarrollo usar `opcache.validate_timestamps=1`. Reiniciar Apache y comprobar `php -i` o una inspección privada. No dejar phpinfo público. Indexación FULLTEXT para búsqueda; listas con JOIN y paginación, sin consultas por tarjeta. Términos muy cortos o palabras excluidas por el motor FULLTEXT pueden no devolver resultados; su tamaño mínimo se configura en MySQL/MariaDB y exige reconstruir el índice.

Respaldo F-ADM-10: usar mysqldump con `--single-transaction --routines --events --default-character-set=utf8mb4`, guardar también uploads y la configuración en almacenamiento privado. Restaurar el dump en una BD limpia con mysql/phpMyAdmin, copiar uploads y comparar conteos, hashes de archivos, roles y claves foráneas. En Windows usar `--result-file` para evitar que PowerShell recodifique SQL. Usar `-p` para solicitar contraseña, nunca escribir una clave real en comandos o documentos.

## Limitaciones y estado de verificación

- PHP lint, PHPUnit y build tienen resultados registrados en `docs/informe-ejecucion.md`; las pruebas que no pudieron ejecutarse figuran como pendientes, no como aprobadas.
- No hay envío SMTP ni distribución de boletines: se implementan alta y confirmación local mediante log. Incorporar transporte de correo real requiere configuración y validación antes de producción.
- No hay multimedia, arrastre, CAPTCHA cognitivo, carrousel, autoplay, servicio offline ni editor WYSIWYG; las comprobaciones condicionales se marcan no aplicables con justificación.
- Los objetivos Lighthouse/INP/carga y la conformidad WCAG completa dependen del servidor, mediciones y revisión humana. El criterio de salida del plan no se declara cumplido sin esos resultados.
- La ejecución de Composer debe hacerse con GD habilitado; el entorno de generación lo cargó con `-d extension=gd` para crear las imágenes.
- La recuperación de tokens invalida enlaces anteriores, pero no revoca automáticamente otras sesiones ya iniciadas en otros dispositivos. Las cookies de sesión necesarias se conservan al rechazar cookies opcionales.
- En despliegues con varias instancias sustituir la caché local de archivos por una caché compartida e introducir limitación de tasa por IP en el servidor frontal. La limitación de comentarios actual es por cuenta y la de login por cuenta registrada.

No se incluyen credenciales reales, dependencias de ejecución del frontend ni funciones inconclusas. Los procedimientos manuales se entregan para ejecutar y registrar, sin inventar certificaciones o encuestas.
