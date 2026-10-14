# Checklists y procedimientos manuales

Registrar por ejecución: ID del caso, fecha, versión, SO/navegador/dispositivo, resultado (aprobado/fallido/no aplicable/pendiente), evidencia y defecto. No aplicar el criterio de salida con casos pendientes. Las inspecciones visuales, experiencias con tecnologías de apoyo, encuestas y carga real no se reemplazan por emulación.

## Dispositivos físicos obligatorios

- [ ] Android de gama baja, aproximadamente 2 GB RAM; anotar marca, CPU, RAM, SO, navegador y red.
- [ ] Android de gama media/alta; anotar los mismos datos.
- [ ] iPhone/Safari, si está disponible; registrar versión de iOS.
- [ ] En los dos Android: vertical y horizontal, ancho de 360 px o el nativo, zoom 200 %, teclado virtual, foco del campo activo, menú/ Escape con teclado externo, botones de 44×44 px y sin scroll horizontal del documento.
- [ ] Completar login, lectura, búsqueda, comentario y creación/edición del panel; moderar como admin. Cancelar y confirmar una eliminación y restaurarla.
- [ ] Red 3G lenta/Slow 4G y ahorro de datos; guardar HAR y vídeo del tiempo hasta lectura. Medir LCP/INP/CLS con herramientas del dispositivo. No aceptar una emulación como prueba de batería o RAM de 2 GB.
- [ ] TalkBack en Android y VoiceOver en iPhone: ejecutar los mismos flujos, comprobar etiquetas, errores y anuncios de estado.

## Tecnologías de apoyo y usuarios

NVDA con Firefox y Chrome en Windows; JAWS si hay licencia; VoiceOver con Safari en macOS/iOS; TalkBack con Chrome Android. Sin ratón, navegar regiones/encabezados, leer una noticia, buscar, autenticarse, comentar y publicar como editor. Anotar comandos, anuncio real y bloqueos. Control por voz/conmutador: activar por nombre visible, sin gestos obligatorios. Alto contraste de Windows, oscuro/claro, zoom 200 %, reflujo 320 px y bookmarklet de espaciado (letras 0.12em, palabras 0.16em, párrafos 2em, interlineado 1.5).

Usabilidad: al menos 5 participantes por perfil del plan, incluyendo mayores de 60 años y personas con discapacidades; obtener consentimiento, usar datos ficticios, no ayudar durante la tarea y registrar éxito, tiempo, clics, errores y comentarios. Aplicar las 10 preguntas SUS originales, puntuar impares como respuesta−1 y pares como 5−respuesta, sumar y multiplicar por 2.5 (meta ≥ 80). Encuesta de estilo: ≥ 20 participantes, Likert 1–5, media ≥ 4.3. Evaluación experta: ≥ 3 diseñadores, rúbrica 0–4 en originalidad, armonía, identidad y legibilidad, media ≥ 3.5; conservar respuestas y cálculo.

## Respaldo, volumen y observabilidad

F-ADM-10: ejecutar mysqldump con --single-transaction --routines --events --default-character-set=utf8mb4 --result-file=RUTA_PRIVADA.sql; copiar uploads; restaurar en una BD limpia sin sobrescribir producción; comparar conteos de todas las tablas, roles, noticias, 100 comentarios, archivos y relaciones.

EF-25: ejecutar EXPLAIN del listado por categoría, listado por fecha y MATCH(title,content); medir en slow query log, comprobar índices y ausencia de consulta por tarjeta. EF-28: ampliar únicamente blog_noticias_test con tests/support/volume.php 100000; medir listados/búsqueda en Apache. EF-27/30: PerfMon de Windows (CPU/RAM), SHOW STATUS para conexiones y recursos durante carga; conservar gráficas. EF-31: detener únicamente servicios del entorno de ensayo, verificar 503 amigable y recuperación al reiniciar; nunca provocar fallos en una instalación con datos reales.

## Casos del plan, uno por uno

<a id="f-aut-01"></a>
### F-AUT-01 · Login válido de administrador

Precondición: Usuario admin existe. Ejecutar: Ingresar credenciales correctas. Resultado esperado: Acceso al panel; sesión iniciada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-02"></a>
### F-AUT-02 · Login con contraseña incorrecta

Precondición: Usuario existe. Ejecutar: Contraseña errónea. Resultado esperado: Mensaje genérico ("credenciales inválidas") sin revelar cuál campo falló.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-03"></a>
### F-AUT-03 · Bloqueo por intentos fallidos

Precondición: Política: 5 intentos. Ejecutar: 5 intentos consecutivos fallidos. Resultado esperado: Cuenta bloqueada temporalmente o CAPTCHA requerido.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-04"></a>
### F-AUT-04 · Campos vacíos en login

Precondición: —. Ejecutar: Enviar formulario vacío. Resultado esperado: Mensajes de validación por campo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-05"></a>
### F-AUT-05 · Inyección SQL en login

Precondición: —. Ejecutar: ' OR '1'='1' --. Resultado esperado: Acceso denegado; sin error SQL expuesto.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-06"></a>
### F-AUT-06 · Cierre de sesión

Precondición: Sesión activa. Ejecutar: Pulsar "Cerrar sesión" y retroceder en el navegador. Resultado esperado: Sesión destruida; no se muestra contenido protegido.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-07"></a>
### F-AUT-07 · Expiración de sesión por inactividad

Precondición: Sesión activa. Ejecutar: Esperar el tiempo configurado. Resultado esperado: Redirección al login; datos no guardados advertidos previamente.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-08"></a>
### F-AUT-08 · Recuperación de contraseña

Precondición: Usuario existe. Ejecutar: Solicitar enlace; usar enlace; restablecer. Resultado esperado: Token de un solo uso con caducidad; contraseña actualizada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-09"></a>
### F-AUT-09 · Reutilización de token de recuperación

Precondición: Token ya usado. Ejecutar: Reabrir enlace. Resultado esperado: Token rechazado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-10"></a>
### F-AUT-10 · Registro de lector con datos válidos

Precondición: —. Ejecutar: Completar formulario. Resultado esperado: Cuenta creada; confirmación por correo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-11"></a>
### F-AUT-11 · Registro con correo duplicado

Precondición: Correo ya registrado. Ejecutar: Registrar el mismo correo. Resultado esperado: Mensaje de error; sin duplicado en BD.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-12"></a>
### F-AUT-12 · Política de contraseñas

Precondición: —. Ejecutar: Contraseñas de 7, 8 y 128 caracteres; sin mayúscula/número. Resultado esperado: Rechazo de las que incumplen; contraseñas almacenadas con hash (password_hash).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-aut-13"></a>
### F-AUT-13 · Fijación y regeneración de sesión

Precondición: —. Ejecutar: Verificar ID de sesión antes/después del login. Resultado esperado: session_regenerate_id aplicado; cookies HttpOnly, Secure, SameSite.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-01"></a>
### F-ROL-01 · Visitante accede a /admin

Precondición: Sin sesión. Ejecutar: Acceder por URL directa. Resultado esperado: Redirección al login (403/302).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-02"></a>
### F-ROL-02 · Lector accede al panel admin

Precondición: Sesión de lector. Ejecutar: URL directa. Resultado esperado: Acceso denegado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-03"></a>
### F-ROL-03 · Editor intenta gestionar usuarios

Precondición: Sesión de editor. Ejecutar: Acceder a módulo de usuarios. Resultado esperado: Denegado según matriz de permisos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-04"></a>
### F-ROL-04 · Editor edita noticia ajena

Precondición: Noticia de otro editor. Ejecutar: Editar por ID. Resultado esperado: Según política: permitido o denegado, de forma consistente.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-05"></a>
### F-ROL-05 · Manipulación de ID (IDOR)

Precondición: Sesión de lector. Ejecutar: Cambiar ?id= para ver o editar recursos ajenos. Resultado esperado: Acceso denegado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-rol-06"></a>
### F-ROL-06 · Escalada de privilegios por parámetro

Precondición: Sesión de lector. Ejecutar: Enviar role=admin en perfil. Resultado esperado: Campo ignorado en servidor.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-01"></a>
### F-NOT-01 · Crear noticia válida

Precondición: Sesión admin. Ejecutar: Título, contenido, categoría, imagen, etiquetas. Resultado esperado: Noticia guardada; visible según estado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-02"></a>
### F-NOT-02 · Título vacío

Precondición: —. Ejecutar: Guardar sin título. Resultado esperado: Validación; no se guarda.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-03"></a>
### F-NOT-03 · Límite de título

Precondición: Límite 150 caracteres. Ejecutar: 149, 150 y 151 caracteres. Resultado esperado: 149 y 150 aceptados; 151 rechazado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-04"></a>
### F-NOT-04 · Contenido con HTML/JS

Precondición: —. Ejecutar: Insertar <script>alert(1)</script>. Resultado esperado: Contenido sanitizado; script no se ejecuta en portada ni panel.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-05"></a>
### F-NOT-05 · Subida de imagen válida

Precondición: —. Ejecutar: JPG/PNG/WebP de 1 MB. Resultado esperado: Imagen subida, redimensionada y con campo de texto alternativo obligatorio.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-06"></a>
### F-NOT-06 · Subida de archivo inválido

Precondición: —. Ejecutar: .php, .exe, .svg con script. Resultado esperado: Rechazo por tipo MIME y extensión; no ejecutable en servidor.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-07"></a>
### F-NOT-07 · Tamaño máximo de archivo

Precondición: Límite 5 MB. Ejecutar: Subir 5 MB y 5.01 MB. Resultado esperado: El primero aceptado, el segundo rechazado con mensaje claro.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-08"></a>
### F-NOT-08 · Editar noticia existente

Precondición: Noticia existe. Ejecutar: Modificar título y contenido. Resultado esperado: Cambios persistidos; fecha de modificación actualizada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-09"></a>
### F-NOT-09 · Eliminar noticia con confirmación

Precondición: Noticia existe. Ejecutar: Eliminar → confirmar / cancelar. Resultado esperado: Confirmar elimina (o mueve a papelera); cancelar no altera nada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-10"></a>
### F-NOT-10 · Flujo de estados

Precondición: —. Ejecutar: Borrador → Revisión → Publicado → Archivado. Resultado esperado: Solo transiciones válidas; noticia no publicada es invisible al público.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-11"></a>
### F-NOT-11 · Programar publicación

Precondición: —. Ejecutar: Fecha futura. Resultado esperado: Se publica automáticamente a la hora indicada (cron).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-12"></a>
### F-NOT-12 · Slug / URL amigable único

Precondición: Slug existente. Ejecutar: Crear noticia con el mismo título. Resultado esperado: Slug único generado (sufijo numérico).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-13"></a>
### F-NOT-13 · Edición concurrente

Precondición: Dos editores abren la misma noticia. Ejecutar: Ambos guardan. Resultado esperado: Advertencia de conflicto o bloqueo; sin pérdida silenciosa.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-14"></a>
### F-NOT-14 · Autoguardado de borrador

Precondición: Editor abierto. Ejecutar: Esperar intervalo. Resultado esperado: Borrador recuperado tras recargar.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-not-15"></a>
### F-NOT-15 · Caracteres especiales y Unicode

Precondición: —. Ejecutar: Tildes, ñ, emojis, RTL. Resultado esperado: Se almacenan y muestran correctamente (UTF-8 / utf8mb4).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-01"></a>
### F-ADM-01 · Crear, editar y eliminar categoría

Precondición: Sesión admin. Ejecutar: CRUD completo. Resultado esperado: Reflejado en menú público.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-02"></a>
### F-ADM-02 · Eliminar categoría con noticias

Precondición: Categoría en uso. Ejecutar: Intentar eliminar. Resultado esperado: Bloqueo o reasignación obligatoria; sin noticias huérfanas.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-03"></a>
### F-ADM-03 · Crear usuario con rol

Precondición: —. Ejecutar: Datos y rol. Resultado esperado: Usuario creado con permisos correctos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-04"></a>
### F-ADM-04 · Desactivar usuario

Precondición: Usuario activo. Ejecutar: Desactivar. Resultado esperado: No puede iniciar sesión; su contenido permanece.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-05"></a>
### F-ADM-05 · Admin no puede eliminarse a sí mismo (último admin)

Precondición: Un solo admin. Ejecutar: Intentar eliminar. Resultado esperado: Operación bloqueada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-06"></a>
### F-ADM-06 · Moderar comentario

Precondición: Comentario pendiente. Ejecutar: Aprobar, rechazar, marcar spam. Resultado esperado: Estado actualizado; visibilidad pública coherente.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-07"></a>
### F-ADM-07 · Configuración del sitio

Precondición: —. Ejecutar: Cambiar nombre, logo, redes, SEO. Resultado esperado: Cambios aplicados en todo el sitio.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-08"></a>
### F-ADM-08 · Dashboard con estadísticas

Precondición: Datos semilla. Ejecutar: Abrir panel. Resultado esperado: Totales coinciden con los registros reales de la BD.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-09"></a>
### F-ADM-09 · Registro de auditoría

Precondición: —. Ejecutar: Realizar acciones sensibles. Resultado esperado: Log con usuario, acción, fecha e IP.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-adm-10"></a>
### F-ADM-10 · Copia de seguridad y restauración

Precondición: —. Ejecutar: Generar respaldo; restaurar en entorno limpio. Resultado esperado: Datos íntegros tras restaurar.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-01"></a>
### F-PUB-01 · Ver portada

Precondición: Noticias publicadas. Ejecutar: Abrir /. Resultado esperado: Noticias recientes ordenadas por fecha desc.; solo publicadas.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-02"></a>
### F-PUB-02 · Paginación

Precondición: > 10 noticias. Ejecutar: Navegar páginas; parámetro page=0, page=9999, page=abc. Resultado esperado: Navegación correcta; valores inválidos manejados sin error.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-03"></a>
### F-PUB-03 · Filtrar por categoría y etiqueta

Precondición: —. Ejecutar: Seleccionar categoría. Resultado esperado: Solo noticias de esa categoría.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-04"></a>
### F-PUB-04 · Detalle de noticia

Precondición: —. Ejecutar: Abrir noticia. Resultado esperado: Título, autor, fecha, imagen y contenido completos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-05"></a>
### F-PUB-05 · Noticia inexistente

Precondición: —. Ejecutar: URL con ID inválido. Resultado esperado: Página 404 amigable.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-06"></a>
### F-PUB-06 · Búsqueda con resultados

Precondición: —. Ejecutar: Palabra clave existente. Resultado esperado: Resultados relevantes, resaltados y paginados.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-07"></a>
### F-PUB-07 · Búsqueda sin resultados

Precondición: —. Ejecutar: Término inexistente. Resultado esperado: Mensaje claro con sugerencias.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-08"></a>
### F-PUB-08 · Búsqueda con caracteres peligrosos

Precondición: —. Ejecutar: %, ', <script>. Resultado esperado: Escapado; sin error SQL ni XSS reflejado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-09"></a>
### F-PUB-09 · Publicar comentario como lector

Precondición: Sesión de lector. Ejecutar: Enviar comentario válido. Resultado esperado: Se envía a moderación o se publica según configuración.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-10"></a>
### F-PUB-10 · Comentario vacío o excesivo

Precondición: Límite 1000 caracteres. Ejecutar: 0, 1000 y 1001 caracteres. Resultado esperado: Vacío y 1001 rechazados; 1000 aceptado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-11"></a>
### F-PUB-11 · Anti-spam en comentarios

Precondición: —. Ejecutar: Envíos repetidos rápidos. Resultado esperado: Limitación de tasa o CAPTCHA accesible.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-12"></a>
### F-PUB-12 · Suscripción al boletín

Precondición: —. Ejecutar: Correo válido / inválido. Resultado esperado: Alta con confirmación; inválido rechazado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-13"></a>
### F-PUB-13 · Compartir en redes

Precondición: —. Ejecutar: Pulsar botones. Resultado esperado: Enlace correcto y metadatos Open Graph.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-14"></a>
### F-PUB-14 · Feed RSS

Precondición: —. Ejecutar: Abrir /rss.xml. Resultado esperado: XML válido con últimas noticias.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-15"></a>
### F-PUB-15 · Protección CSRF

Precondición: Sesión activa. Ejecutar: Enviar formulario sin token o con token inválido. Resultado esperado: Solicitud rechazada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="f-pub-16"></a>
### F-PUB-16 · Gestión de cookies

Precondición: Primera visita. Ejecutar: Aceptar / rechazar. Resultado esperado: Preferencia respetada y persistida.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-01"></a>
### U-01 · Reconocibilidad del propósito

Revisar Reconocibilidad del propósito · Mostrar la portada 5 segundos y preguntar qué es el sitio · ≥ 90 % identifica que es un sitio de noticias · % de aciertos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-02"></a>
### U-02 · Navegación principal clara

Revisar Navegación principal clara · Pedir encontrar una categoría específica · ≥ 90 % éxito en ≤ 3 clics · Tasa de éxito, clics.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-03"></a>
### U-03 · Tarea: encontrar una noticia por búsqueda

Revisar Tarea: encontrar una noticia por búsqueda · Tarea guiada sin ayuda · ≥ 90 % éxito; tiempo ≤ 30 s · Éxito, tiempo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-04"></a>
### U-04 · Tarea del admin: publicar una noticia

Revisar Tarea del admin: publicar una noticia · Editor novato sin capacitación · ≥ 80 % completa en ≤ 5 min sin ayuda · Éxito, tiempo, errores.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-05"></a>
### U-05 · Curva de aprendizaje

Revisar Curva de aprendizaje · Repetir la tarea 3 veces · Tiempo disminuye ≥ 30 % entre 1.ª y 3.ª · Tiempo por iteración.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-06"></a>
### U-06 · Visibilidad del estado del sistema

Revisar Visibilidad del estado del sistema · Observar tras guardar, subir, buscar · Siempre hay retroalimentación (mensaje, indicador de carga) en ≤ 1 s · Observación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-07"></a>
### U-07 · Prevención de errores

Revisar Prevención de errores · Intentar acciones destructivas · Confirmación en eliminaciones; datos del formulario preservados ante error · Lista de verificación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-08"></a>
### U-08 · Mensajes de error comprensibles

Revisar Mensajes de error comprensibles · Provocar 10 errores comunes · Mensajes en lenguaje claro, indican causa y solución, sin códigos técnicos · % de mensajes conformes.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-09"></a>
### U-09 · Consistencia y estándares

Revisar Consistencia y estándares · Auditar patrones (botones, iconos, ubicación) · 0 inconsistencias de comportamiento entre pantallas · Lista de verificación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-10"></a>
### U-10 · Responsividad (móvil, tablet, escritorio)

Revisar Responsividad (móvil, tablet, escritorio) · Probar 360, 768, 1280, 1920 px · Sin desplazamiento horizontal; todas las funciones disponibles · Revisión.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-11"></a>
### U-11 · Facilidad táctil

Revisar Facilidad táctil · Prueba en móvil con pulgar · Objetivos ≥ 44×44 px con separación; formularios con teclado adecuado (type=email, inputmode) · Medición.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-12"></a>
### U-12 · Formularios usables

Revisar Formularios usables · Revisar etiquetas, autocompletado, campos obligatorios · Etiqueta visible por campo; autocomplete correcto; obligatoriedad indicada de forma no solo cromática · Lista de verificación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-13"></a>
### U-13 · Legibilidad del contenido

Revisar Legibilidad del contenido · Evaluar tamaño, interlineado, longitud de línea · Texto base ≥ 16 px, interlineado ≥ 1.5, línea ≤ 80 caracteres · Medición.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-14"></a>
### U-14 · Ayuda y documentación del admin

Revisar Ayuda y documentación del admin · Revisar ayudas contextuales · Tooltips y guía rápida disponibles en funciones complejas · Revisión.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-15"></a>
### U-15 · Migas de pan y orientación

Revisar Migas de pan y orientación · Navegar 3 niveles · El usuario siempre sabe dónde está y cómo volver · Observación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-16"></a>
### U-16 · Deshacer y control del usuario

Revisar Deshacer y control del usuario · Cancelar, volver, restaurar de papelera · Salidas de emergencia disponibles · Lista de verificación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-17"></a>
### U-17 · Usabilidad para adultos mayores

Revisar Usabilidad para adultos mayores · Pruebas con usuarios ≥ 60 años · ≥ 80 % éxito en tareas básicas · Éxito.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-18"></a>
### U-18 · Usabilidad para personas con discapacidad

Revisar Usabilidad para personas con discapacidad · Pruebas con usuarios con baja visión, ceguera, discapacidad motora y cognitiva · ≥ 80 % éxito de tareas clave con sus tecnologías de apoyo · Éxito, SUS.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-19"></a>
### U-19 · Satisfacción global

Revisar Satisfacción global · Cuestionario SUS tras las tareas · SUS ≥ 80 (grado "excelente") · Puntuación SUS.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="u-20"></a>
### U-20 · Página 404 y estados vacíos

Revisar Página 404 y estados vacíos · Provocar 404 y listas vacías · Mensaje útil con enlaces de retorno y búsqueda · Observación.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-01"></a>
### A-VIS-01 · 1.1.1

Revisar 1.1.1 · Todas las imágenes informativas tienen alt descriptivo; decorativas con alt="" · axe + revisión manual · 0 imágenes sin alternativa.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-02"></a>
### A-VIS-02 · 1.1.1

Revisar 1.1.1 · Campo "texto alternativo" obligatorio al subir imagen en el panel · Funcional · No se puede publicar imagen informativa sin alt.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-03"></a>
### A-VIS-03 · 1.3.1 / 4.1.2

Revisar 1.3.1 / 4.1.2 · Estructura semántica: header, nav, main, article, footer; un solo h1; jerarquía de títulos sin saltos · Inspección + NVDA · Estructura correcta; regiones navegables.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-04"></a>
### A-VIS-04 · 2.4.1

Revisar 2.4.1 · Enlace "Saltar al contenido" funcional · Teclado · Primer Tab lo muestra; salta a main.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-05"></a>
### A-VIS-05 · 1.4.3

Revisar 1.4.3 · Contraste de texto ≥ 4.5:1 (3:1 en texto grande) · Colour Contrast Analyser · 0 incumplimientos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-06"></a>
### A-VIS-06 · 1.4.6 (AAA)

Revisar 1.4.6 (AAA) · Contraste ≥ 7:1 en texto de lectura de artículos · Colour Contrast Analyser · Cumple en cuerpo de noticia.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-07"></a>
### A-VIS-07 · 1.4.11

Revisar 1.4.11 · Contraste ≥ 3:1 en bordes de campos, iconos y botones · Medición · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-08"></a>
### A-VIS-08 · 1.4.1

Revisar 1.4.1 · Información no transmitida solo por color (errores, enlaces, estados) · Simulador de daltonismo · Existe indicador adicional (icono, texto, subrayado).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-09"></a>
### A-VIS-09 · 1.4.4

Revisar 1.4.4 · Zoom de texto al 200 % · Navegador · Sin pérdida de contenido o funcionalidad.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-10"></a>
### A-VIS-10 · 1.4.10

Revisar 1.4.10 · Reflujo a 320 px de ancho (equivale a zoom 400 %) · Navegador · Sin scroll horizontal en texto.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-11"></a>
### A-VIS-11 · 1.4.12

Revisar 1.4.12 · Espaciado de texto aumentado (interlineado 1.5, párrafos 2×, letras 0.12em) · Bookmarklet · Sin recortes ni solapamientos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-12"></a>
### A-VIS-12 · 1.4.5

Revisar 1.4.5 · No se usan imágenes de texto (salvo logotipo) · Inspección · 0 casos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-13"></a>
### A-VIS-13 · 1.4.13

Revisar 1.4.13 · Contenido emergente (tooltips, menús) descartable, persistente y que se puede apuntar · Manual · Cumple los tres requisitos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-14"></a>
### A-VIS-14 · 4.1.3

Revisar 4.1.3 · Mensajes de estado (guardado, error, resultados de búsqueda) anunciados · NVDA / VoiceOver · Se anuncian mediante role="status" o aria-live sin mover el foco.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-15"></a>
### A-VIS-15 · Tecnologías de asistencia

Revisar Tecnologías de asistencia · Tareas completas con NVDA+Firefox/Chrome, JAWS, VoiceOver (macOS/iOS), TalkBack · Prueba manual con script · Lectura de portada, noticia, búsqueda, login y publicación sin bloqueos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-vis-16"></a>
### A-VIS-16 · 1.4.8 / modos

Revisar 1.4.8 / modos · Modo de alto contraste de Windows y modo oscuro (prefers-color-scheme, forced-colors) · Sistema operativo · Interfaz legible y operable.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-aud-01"></a>
### A-AUD-01 · 1.2.2

Revisar 1.2.2 · Videos incrustados con subtítulos sincronizados · Revisión manual · Subtítulos disponibles y precisos.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-aud-02"></a>
### A-AUD-02 · 1.2.1

Revisar 1.2.1 · Audio (podcast) con transcripción textual · Revisión · Transcripción accesible junto al reproductor.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-aud-03"></a>
### A-AUD-03 · 1.2.3 / 1.2.5

Revisar 1.2.3 / 1.2.5 · Audiodescripción o alternativa en vídeos con información visual · Revisión · Disponible.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-aud-04"></a>
### A-AUD-04 · 1.4.2

Revisar 1.4.2 · Audio con reproducción automática · Carga de página · Ninguno reproduce solo > 3 s sin control de pausa/volumen.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-aud-05"></a>
### A-AUD-05 · 1.2.4

Revisar 1.2.4 · Emisiones en vivo (si existen) con subtítulos · Revisión · Subtítulos en directo.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-aud-06"></a>
### A-AUD-06 · 3.3 / General

Revisar 3.3 / General · Alertas del sistema (errores, confirmaciones) no dependen únicamente de sonido · Silenciar el dispositivo · Alertas visuales siempre presentes.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-aud-07"></a>
### A-AUD-07 · 3.1.5 (AAA)

Revisar 3.1.5 (AAA) · Lengua de señas o resumen en lenguaje sencillo para contenidos clave (opcional) · Revisión · Disponible o justificado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-aud-08"></a>
### A-AUD-08 · Reproductor

Revisar Reproductor · Controles del reproductor operables por teclado y etiquetados · Teclado + lector · Cumple.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-mot-01"></a>
### A-MOT-01 · 2.1.1

Revisar 2.1.1 · Todas las funciones (portal y panel admin) operables solo con teclado · Teclado · 100 % de tareas completables.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-02"></a>
### A-MOT-02 · 2.1.2

Revisar 2.1.2 · Sin trampas de teclado (editor WYSIWYG, modales, reproductores) · Teclado · Siempre se puede salir con Tab/Esc.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-03"></a>
### A-MOT-03 · 2.4.3

Revisar 2.4.3 · Orden de foco lógico · Tab · Orden coincide con el orden visual y de lectura.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-04"></a>
### A-MOT-04 · 2.4.7 / 2.4.11

Revisar 2.4.7 / 2.4.11 · Foco visible y no oculto por barras fijas · Teclado · Indicador claro (contraste ≥ 3:1) y no tapado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-05"></a>
### A-MOT-05 · 2.5.8

Revisar 2.5.8 · Tamaño de objetivo ≥ 24×24 px (se exige 44×44 px como objetivo interno) · Medición · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-06"></a>
### A-MOT-06 · 2.5.1

Revisar 2.5.1 · Gestos complejos (arrastrar, pellizcar) con alternativa de un solo puntero · Dispositivo táctil · Existe alternativa simple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-07"></a>
### A-MOT-07 · 2.5.7

Revisar 2.5.7 · Arrastrar y soltar (ordenar noticias en admin, subir archivos) con alternativa sin arrastre · Manual · Botones o selector disponibles.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-08"></a>
### A-MOT-08 · 2.5.2

Revisar 2.5.2 · Cancelación de puntero: acciones se activan al soltar, no al presionar · Ratón/táctil · Se puede cancelar.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-09"></a>
### A-MOT-09 · 2.2.1

Revisar 2.2.1 · Tiempos de sesión ajustables o con aviso previo · Esperar expiración · Aviso con opción de extender (≥ 20 s para responder).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-10"></a>
### A-MOT-10 · 2.5.4

Revisar 2.5.4 · Funciones por movimiento (agitar) con alternativa · Móvil · Alternativa o no aplica.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="a-mot-11"></a>
### A-MOT-11 · 3.2.1 / 3.2.2

Revisar 3.2.1 / 3.2.2 · Sin cambios de contexto inesperados al enfocar o introducir datos · Manual · Comportamiento predecible.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-12"></a>
### A-MOT-12 · Entrada alternativa

Revisar Entrada alternativa · Operación con control por voz (Dragon / Control por voz) y conmutador · Manual · Los nombres accesibles coinciden con el texto visible (2.5.3).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-13"></a>
### A-MOT-13 · 3.3.7

Revisar 3.3.7 · Evitar reintroducir información ya dada en un mismo proceso (entrada redundante) · Manual · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-mot-14"></a>
### A-MOT-14 · 3.3.8

Revisar 3.3.8 · Autenticación accesible: sin pruebas cognitivas obligatorias; permite pegar contraseña y gestor de contraseñas · Manual · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-01"></a>
### A-COG-01 · 3.1.1 / 3.1.2

Revisar 3.1.1 / 3.1.2 · Idioma de página (lang="es") y de fragmentos en otro idioma · Inspección · Declarado correctamente.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-02"></a>
### A-COG-02 · 3.1.3–3.1.5

Revisar 3.1.3–3.1.5 · Lenguaje claro: nivel de lectura adecuado; glosario de términos técnicos; siglas explicadas · Revisión (índice de legibilidad Fernández-Huerta) · Contenido de interfaz en lenguaje sencillo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-03"></a>
### A-COG-03 · 3.3.1 / 3.3.3

Revisar 3.3.1 / 3.3.3 · Errores identificados en texto, con sugerencias de corrección · Provocar errores · Descripción clara por campo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-04"></a>
### A-COG-04 · 3.3.2

Revisar 3.3.2 · Etiquetas e instrucciones visibles en formularios · Inspección · Todas presentes (no solo placeholder).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-05"></a>
### A-COG-05 · 3.3.4

Revisar 3.3.4 · Acciones críticas reversibles, verificables o confirmables · Funcional · Cumple en eliminaciones y publicaciones.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-06"></a>
### A-COG-06 · 3.2.3 / 3.2.4

Revisar 3.2.3 / 3.2.4 · Navegación e identificación de componentes consistentes · Auditoría · Mismo orden y nombres en todas las páginas.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-07"></a>
### A-COG-07 · 3.2.6

Revisar 3.2.6 · Ayuda consistente (contacto, ayuda) en la misma ubicación relativa · Auditoría · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-08"></a>
### A-COG-08 · 2.2.2

Revisar 2.2.2 · Contenido en movimiento (carruseles, ticker de noticias) pausable · Manual · Control de pausa disponible; sin autoplay obligatorio.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-09"></a>
### A-COG-09 · 2.3.1

Revisar 2.3.1 · Sin destellos > 3 por segundo · Herramienta PEAT · 0 incidencias.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-10"></a>
### A-COG-10 · 2.2.6

Revisar 2.2.6 · Aviso de tiempo de inactividad que cause pérdida de datos · Manual · Se informa al usuario.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-11"></a>
### A-COG-11 · 1.3.5

Revisar 1.3.5 · Propósito de campos identificado (autocomplete) · Inspección · Presente en nombre, correo, contraseña.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-12"></a>
### A-COG-12 · Cognitiva

Revisar Cognitiva · Modo de lectura simplificado / sin distracciones en las noticias · Funcional · Disponible o justificado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-13"></a>
### A-COG-13 · Cognitiva

Revisar Cognitiva · Reducción de movimiento (prefers-reduced-motion) respetada · Configuración SO · Animaciones desactivadas o reducidas.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-cog-14"></a>
### A-COG-14 · Cognitiva

Revisar Cognitiva · Estructura de contenido en párrafos cortos, títulos descriptivos, listas · Revisión editorial · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-01"></a>
### A-TEC-01 · Análisis automatizado de todas las plantillas

Revisar Análisis automatizado de todas las plantillas · axe-core, WAVE, Lighthouse, Pa11y en CI · 0 violaciones críticas/serias; Lighthouse Accessibility = 100.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-02"></a>
### A-TEC-02 · HTML válido

Revisar HTML válido · W3C Validator · 0 errores (IDs únicos, anidamiento correcto).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-03"></a>
### A-TEC-03 · Uso correcto de ARIA (primera regla: usar HTML nativo)

Revisar Uso correcto de ARIA (primera regla: usar HTML nativo) · axe + revisión · Sin roles redundantes ni conflictivos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-04"></a>
### A-TEC-04 · Árbol de accesibilidad

Revisar Árbol de accesibilidad · DevTools · Nombre, rol y estado correctos en controles personalizados.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-05"></a>
### A-TEC-05 · Accesibilidad del panel admin y editor de contenido (ATAG 2.0)

Revisar Accesibilidad del panel admin y editor de contenido (ATAG 2.0) · Manual · El editor es accesible y ayuda a crear contenido accesible (alt obligatorio, jerarquía de títulos validada).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-06"></a>
### A-TEC-06 · Orientación

Revisar Orientación · Rotar dispositivo · Funciona en vertical y horizontal (1.3.4).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-07"></a>
### A-TEC-07 · Declaración de accesibilidad

Revisar Declaración de accesibilidad · Revisión · Página con nivel de conformidad, contacto y canal de comentarios.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="a-tec-08"></a>
### A-TEC-08 · CAPTCHA accesible

Revisar CAPTCHA accesible · Manual · Alternativa no visual o sin CAPTCHA (honeypot, tiempo, tokens).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-01"></a>
### E-01 · Guía de estilo definida

Revisar Guía de estilo definida · Existe documento (paleta, tipografía, espaciado, iconografía, componentes) y se implementa · Revisión documental · Guía completa y trazable al CSS (variables / tokens de diseño).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-02"></a>
### E-02 · Paleta de colores

Revisar Paleta de colores · Se usan solo los colores definidos (primario, secundario, neutros, estados) · Inspección CSS / auditoría · 0 colores fuera de la paleta; contraste conforme a WCAG.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-03"></a>
### E-03 · Tipografía

Revisar Tipografía · Máximo 2 familias; escala tipográfica coherente; jerarquía clara (títulos, subtítulos, cuerpo) · Inspección · Cumple escala definida; fuentes con fallback.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-04"></a>
### E-04 · Coherencia entre páginas

Revisar Coherencia entre páginas · Cabecera, pie, botones, formularios y tarjetas idénticos en todas las vistas · Comparación visual · 0 inconsistencias.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-05"></a>
### E-05 · Coherencia entre portal y panel admin

Revisar Coherencia entre portal y panel admin · Mismos tokens de diseño y componentes · Comparación · Misma identidad visual.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-06"></a>
### E-06 · Retícula y espaciado

Revisar Retícula y espaciado · Uso de rejilla (8 pt / 12 columnas); márgenes y alineaciones uniformes · Superposición de rejilla · Desviación ≤ 2 px.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-07"></a>
### E-07 · Jerarquía visual

Revisar Jerarquía visual · La noticia principal destaca; flujo de lectura natural · Prueba de mirada (5 s) / mapa de calor · ≥ 80 % identifica el elemento principal.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-08"></a>
### E-08 · Imágenes

Revisar Imágenes · Formato, proporción y calidad uniformes; sin distorsión ni pixelado; recorte consistente · Inspección · Cumple relación de aspecto definida.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-09"></a>
### E-09 · Estados de componentes

Revisar Estados de componentes · Normal, hover, foco, activo, deshabilitado y error definidos y coherentes · Inspección · Todos presentes y consistentes.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-10"></a>
### E-10 · Iconografía

Revisar Iconografía · Un único conjunto de iconos, tamaño y trazo uniformes · Inspección · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-11"></a>
### E-11 · Responsividad visual

Revisar Responsividad visual · Diseño se adapta con elegancia en 360, 768, 1280 y 1920 px; sin elementos cortados · Capturas comparativas · Cumple en todos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-12"></a>
### E-12 · Modo oscuro (si aplica)

Revisar Modo oscuro (si aplica) · Paleta alternativa coherente y con contraste adecuado · Comparación · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-13"></a>
### E-13 · Microinteracciones y animaciones

Revisar Microinteracciones y animaciones · Transiciones sutiles (150–300 ms), propósito claro, respetan prefers-reduced-motion · Revisión · Cumple.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-14"></a>
### E-14 · Impresión

Revisar Impresión · Hoja de estilos de impresión para noticias (sin menús ni anuncios) · Vista previa de impresión · Legible y ordenada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-15"></a>
### E-15 · Lenguaje y tono de interfaz

Revisar Lenguaje y tono de interfaz · Textos de interfaz coherentes (voz, tratamiento, mayúsculas) · Revisión editorial · Sin variaciones.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-16"></a>
### E-16 · Calidad del código CSS

Revisar Calidad del código CSS · Convención de nombres (BEM), sin estilos en línea, linter sin errores · Stylelint · 0 errores.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-17"></a>
### E-17 · Percepción estética

Revisar Percepción estética · Encuesta a ≥ 20 usuarios (diseño moderno, ordenado, agradable, profesional) · Likert 1–5 · Media ≥ 4.3.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-18"></a>
### E-18 · Evaluación experta

Revisar Evaluación experta · Panel de ≥ 3 diseñadores UI con rúbrica (originalidad, armonía, identidad) · Rúbrica 0–4 · Puntuación ≥ 3.5.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-19"></a>
### E-19 · Consistencia entre navegadores

Revisar Consistencia entre navegadores · Renderizado equivalente en Chrome, Firefox, Safari y Edge · Pruebas visuales de regresión (Percy, BackstopJS) · Diferencia visual ≤ 1 %.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="e-20"></a>
### E-20 · Estilo vs. accesibilidad

Revisar Estilo vs. accesibilidad · Ningún elemento decorativo reduce legibilidad ni contraste · Revisión cruzada con A-VIS · 0 conflictos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-01"></a>
### EF-01 · LCP (Largest Contentful Paint)

Revisar LCP (Largest Contentful Paint) · Lighthouse / WebPageTest · ≤ 2.5 s (gama media); ≤ 4 s (recursos limitados, red 3G).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-02"></a>
### EF-02 · INP (Interaction to Next Paint)

Revisar INP (Interaction to Next Paint) · Lighthouse / CrUX · ≤ 200 ms.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-03"></a>
### EF-03 · CLS (Cumulative Layout Shift)

Revisar CLS (Cumulative Layout Shift) · Lighthouse · ≤ 0.1 (dimensiones fijas en imágenes y anuncios).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-04"></a>
### EF-04 · FCP, TBT y Speed Index

Revisar FCP, TBT y Speed Index · Lighthouse · FCP ≤ 1.8 s; TBT ≤ 200 ms.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-05"></a>
### EF-05 · Puntuación global

Revisar Puntuación global · Lighthouse (móvil con throttling) · Performance ≥ 90.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-06"></a>
### EF-06 · Peso total de página

Revisar Peso total de página · DevTools Network · ≤ 1 MB portada; ≤ 1.5 MB noticia (idealmente).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-07"></a>
### EF-07 · Número de peticiones HTTP

Revisar Número de peticiones HTTP · DevTools · ≤ 40 peticiones; recursos críticos priorizados.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-08"></a>
### EF-08 · Optimización de imágenes

Revisar Optimización de imágenes · Inspección · WebP/AVIF, srcset/sizes, loading="lazy" bajo el pliegue.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-09"></a>
### EF-09 · Minificación y compresión

Revisar Minificación y compresión · Inspección de cabeceras · CSS/JS minificados; Gzip o Brotli activo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-10"></a>
### EF-10 · Caché del navegador

Revisar Caché del navegador · Cabeceras · Cache-Control/ETag para estáticos; reutilización en 2.ª visita.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-11"></a>
### EF-11 · JavaScript mínimo

Revisar JavaScript mínimo · Cobertura de código (DevTools) · ≤ 170 KB de JS comprimido; sin librerías no usadas; scripts defer/async.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-12"></a>
### EF-12 · Funcionamiento sin JS (mejora progresiva)

Revisar Funcionamiento sin JS (mejora progresiva) · Deshabilitar JS · Contenido de lectura accesible.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-13"></a>
### EF-13 · Uso de CPU y memoria en cliente

Revisar Uso de CPU y memoria en cliente · Performance Monitor, Task Manager · Sin fugas de memoria tras 10 min de uso; CPU en reposo ≈ 0 %.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-14"></a>
### EF-14 · Consumo de batería y datos

Revisar Consumo de batería y datos · Perfil en dispositivo real / modo "Ahorro de datos" · Respeta Save-Data; sin sondeos innecesarios; sin autoplay de vídeo.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-15"></a>
### EF-15 · Modo de datos reducido

Revisar Modo de datos reducido · Cabecera Save-Data / prefers-reduced-data · Imágenes de menor calidad o diferidas.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-16"></a>
### EF-16 · Fuentes web

Revisar Fuentes web · Inspección · font-display: swap; subconjunto de caracteres; ≤ 2 familias.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-17"></a>
### EF-17 · Dispositivo de recursos limitados

Revisar Dispositivo de recursos limitados · Móvil real gama baja, 3G · Noticia legible e interactiva en ≤ 5 s.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-18"></a>
### EF-18 · Modo sin conexión

Revisar Modo sin conexión · Service Worker (si se implementa) · Última noticia visitada disponible offline.

Versión actual: no aplicable por ausencia de multimedia, control por movimiento o servicio offline. Registrar la justificación; si se introduce la función, ejecutar el procedimiento completo antes de publicar.

<a id="ef-20"></a>
### EF-20 · TTFB (Time to First Byte)

Revisar TTFB (Time to First Byte) · WebPageTest · ≤ 600 ms (objetivo ≤ 200 ms con caché).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-21"></a>
### EF-21 · Prueba de carga nominal

Revisar Prueba de carga nominal · JMeter/k6: 100 usuarios concurrentes, 10 min · p95 ≤ 2 s; tasa de errores < 1 %.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-22"></a>
### EF-22 · Prueba de estrés

Revisar Prueba de estrés · Incremento hasta el punto de quiebre · Se identifica el límite; degradación controlada (sin corrupción).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-23"></a>
### EF-23 · Prueba de picos (noticia viral)

Revisar Prueba de picos (noticia viral) · Salto de 50 a 1000 usuarios en 1 min · Sistema se mantiene o se recupera en < 2 min.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-24"></a>
### EF-24 · Prueba de resistencia (soak)

Revisar Prueba de resistencia (soak) · Carga moderada por 4–8 h · Sin fugas de memoria ni deterioro del tiempo de respuesta.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-25"></a>
### EF-25 · Consultas SQL eficientes

Revisar Consultas SQL eficientes · EXPLAIN, slow query log · Sin consultas N+1; índices en slug, categoría y fecha; consultas ≤ 100 ms.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-26"></a>
### EF-26 · Caché del servidor

Revisar Caché del servidor · OPcache, caché de página/fragmentos, Redis/Memcached · OPcache activo; razón de aciertos de caché ≥ 80 % en portada.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-27"></a>
### EF-27 · Uso de CPU/RAM del servidor

Revisar Uso de CPU/RAM del servidor · top, monitoreo · CPU < 70 %, RAM estable bajo carga nominal.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-28"></a>
### EF-28 · Paginación y volumen de datos

Revisar Paginación y volumen de datos · BD con 100 000 noticias · Listados y búsqueda con tiempos estables (≤ 1 s).

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-29"></a>
### EF-29 · Subida de archivos pesados

Revisar Subida de archivos pesados · Imagen de 5 MB · Procesada sin agotar memory_limit ni max_execution_time; redimensionado optimizado.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-30"></a>
### EF-30 · Conexiones a BD

Revisar Conexiones a BD · Monitoreo · Cierre/reutilización correcta; sin agotar max_connections.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-31"></a>
### EF-31 · Recuperación ante fallos

Revisar Recuperación ante fallos · Reinicio de servicios / BD caída · Mensaje amigable; reanudación automática.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-32"></a>
### EF-32 · Escalabilidad

Revisar Escalabilidad · Aumentar recursos o instancias · Mejora proporcional del rendimiento.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-33"></a>
### EF-33 · Compresión y HTTP/2 o HTTP/3

Revisar Compresión y HTTP/2 o HTTP/3 · Cabeceras · Activos.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

<a id="ef-34"></a>
### EF-34 · Rendimiento del panel admin

Revisar Rendimiento del panel admin · Editor con artículo de 10 000 palabras y 50 imágenes · Edición fluida; guardado ≤ 3 s.

- [ ] Ejecutar en el entorno de referencia y comparar todos los criterios del caso.
- [ ] Registrar evidencia específica: captura/log/medición y, para pruebas de usuario o tecnología de apoyo, resultado observado y comandos empleados.
- [ ] Anotar resultado y defecto; no aprobar por inspección del código solamente.

