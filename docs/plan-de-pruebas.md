PARTE 2: Diseño del Plan de Pruebas del Blog de Noticias en PHP
2.1 Información general del plan
Campo	Descripción
Producto bajo prueba	Blog de noticias autoadministrable (PHP + MySQL/MariaDB)
Roles de usuario	Visitante anónimo, Lector registrado, Editor/Redactor, Administrador
Módulos	Portada y categorías, detalle de noticia, búsqueda, comentarios, suscripción/boletín, autenticación, panel de administración (CRUD de noticias, categorías, usuarios, comentarios, medios, configuración del sitio)
Enfoque	Basado en riesgos, con ISO/IEC 25010 como modelo de calidad
Estándares	ISTQB, ISO/IEC 25010/25023, ISO/IEC/IEEE 29119, WCAG 2.2 AA (+ AAA selectivo), OWASP Top 10
Herramientas	PHPUnit, Selenium/Playwright, Postman, JMeter/k6, axe DevTools, WAVE, Lighthouse, NVDA, VoiceOver, Colour Contrast Analyser, BrowserStack
Entornos de datos	BD semilla con 500 noticias, 20 categorías, 50 usuarios y 2000 comentarios
Criterio de salida	100 % de casos P1 aprobados; ≥ 95 % del total; 0 defectos críticos/altos abiertos; WCAG AA sin violaciones; Lighthouse ≥ 90 en Performance y 100 en Accessibility

Convenciones: Prioridad: A (alta), M (media), B (baja). Técnica: PE = partición de equivalencia, VL = valores límite, TD = tabla de decisión, TE = transición de estados, EXP = exploratoria, SEG = seguridad.

2.2 CRITERIO 1: FUNCIONALIDAD
2.2.1 Autenticación y gestión de sesiones
ID	Escenario	Precondición	Pasos / Datos	Resultado esperado	Téc.	Pri.
F-AUT-01	Login válido de administrador	Usuario admin existe	Ingresar credenciales correctas	Acceso al panel; sesión iniciada	PE	A
F-AUT-02	Login con contraseña incorrecta	Usuario existe	Contraseña errónea	Mensaje genérico ("credenciales inválidas") sin revelar cuál campo falló	PE	A
F-AUT-03	Bloqueo por intentos fallidos	Política: 5 intentos	5 intentos consecutivos fallidos	Cuenta bloqueada temporalmente o CAPTCHA requerido	VL	A
F-AUT-04	Campos vacíos en login	—	Enviar formulario vacío	Mensajes de validación por campo	PE	M
F-AUT-05	Inyección SQL en login	—	' OR '1'='1' --	Acceso denegado; sin error SQL expuesto	SEG	A
F-AUT-06	Cierre de sesión	Sesión activa	Pulsar "Cerrar sesión" y retroceder en el navegador	Sesión destruida; no se muestra contenido protegido	TE	A
F-AUT-07	Expiración de sesión por inactividad	Sesión activa	Esperar el tiempo configurado	Redirección al login; datos no guardados advertidos previamente	TE	M
F-AUT-08	Recuperación de contraseña	Usuario existe	Solicitar enlace; usar enlace; restablecer	Token de un solo uso con caducidad; contraseña actualizada	TD	A
F-AUT-09	Reutilización de token de recuperación	Token ya usado	Reabrir enlace	Token rechazado	SEG	A
F-AUT-10	Registro de lector con datos válidos	—	Completar formulario	Cuenta creada; confirmación por correo	PE	A
F-AUT-11	Registro con correo duplicado	Correo ya registrado	Registrar el mismo correo	Mensaje de error; sin duplicado en BD	PE	A
F-AUT-12	Política de contraseñas	—	Contraseñas de 7, 8 y 128 caracteres; sin mayúscula/número	Rechazo de las que incumplen; contraseñas almacenadas con hash (password_hash)	VL	A
F-AUT-13	Fijación y regeneración de sesión	—	Verificar ID de sesión antes/después del login	session_regenerate_id aplicado; cookies HttpOnly, Secure, SameSite	SEG	A
2.2.2 Control de acceso por rol (autorización)
ID	Escenario	Precondición	Pasos / Datos	Resultado esperado	Téc.	Pri.
F-ROL-01	Visitante accede a /admin	Sin sesión	Acceder por URL directa	Redirección al login (403/302)	TD	A
F-ROL-02	Lector accede al panel admin	Sesión de lector	URL directa	Acceso denegado	TD	A
F-ROL-03	Editor intenta gestionar usuarios	Sesión de editor	Acceder a módulo de usuarios	Denegado según matriz de permisos	TD	A
F-ROL-04	Editor edita noticia ajena	Noticia de otro editor	Editar por ID	Según política: permitido o denegado, de forma consistente	TD	A
F-ROL-05	Manipulación de ID (IDOR)	Sesión de lector	Cambiar ?id= para ver o editar recursos ajenos	Acceso denegado	SEG	A
F-ROL-06	Escalada de privilegios por parámetro	Sesión de lector	Enviar role=admin en perfil	Campo ignorado en servidor	SEG	A
2.2.3 Administrador: gestión de noticias (CRUD)
ID	Escenario	Precondición	Pasos / Datos	Resultado esperado	Téc.	Pri.
F-NOT-01	Crear noticia válida	Sesión admin	Título, contenido, categoría, imagen, etiquetas	Noticia guardada; visible según estado	PE	A
F-NOT-02	Título vacío	—	Guardar sin título	Validación; no se guarda	PE	A
F-NOT-03	Límite de título	Límite 150 caracteres	149, 150 y 151 caracteres	149 y 150 aceptados; 151 rechazado	VL	A
F-NOT-04	Contenido con HTML/JS	—	Insertar <script>alert(1)</script>	Contenido sanitizado; script no se ejecuta en portada ni panel	SEG	A
F-NOT-05	Subida de imagen válida	—	JPG/PNG/WebP de 1 MB	Imagen subida, redimensionada y con campo de texto alternativo obligatorio	PE	A
F-NOT-06	Subida de archivo inválido	—	.php, .exe, .svg con script	Rechazo por tipo MIME y extensión; no ejecutable en servidor	SEG	A
F-NOT-07	Tamaño máximo de archivo	Límite 5 MB	Subir 5 MB y 5.01 MB	El primero aceptado, el segundo rechazado con mensaje claro	VL	M
F-NOT-08	Editar noticia existente	Noticia existe	Modificar título y contenido	Cambios persistidos; fecha de modificación actualizada	PE	A
F-NOT-09	Eliminar noticia con confirmación	Noticia existe	Eliminar → confirmar / cancelar	Confirmar elimina (o mueve a papelera); cancelar no altera nada	TE	A
F-NOT-10	Flujo de estados	—	Borrador → Revisión → Publicado → Archivado	Solo transiciones válidas; noticia no publicada es invisible al público	TE	A
F-NOT-11	Programar publicación	—	Fecha futura	Se publica automáticamente a la hora indicada (cron)	TE	M
F-NOT-12	Slug / URL amigable único	Slug existente	Crear noticia con el mismo título	Slug único generado (sufijo numérico)	PE	M
F-NOT-13	Edición concurrente	Dos editores abren la misma noticia	Ambos guardan	Advertencia de conflicto o bloqueo; sin pérdida silenciosa	EXP	M
F-NOT-14	Autoguardado de borrador	Editor abierto	Esperar intervalo	Borrador recuperado tras recargar	TE	B
F-NOT-15	Caracteres especiales y Unicode	—	Tildes, ñ, emojis, RTL	Se almacenan y muestran correctamente (UTF-8 / utf8mb4)	PE	A
2.2.4 Administrador: categorías, etiquetas, usuarios, comentarios y configuración
ID	Escenario	Precondición	Pasos / Datos	Resultado esperado	Téc.	Pri.
F-ADM-01	Crear, editar y eliminar categoría	Sesión admin	CRUD completo	Reflejado en menú público	PE	A
F-ADM-02	Eliminar categoría con noticias	Categoría en uso	Intentar eliminar	Bloqueo o reasignación obligatoria; sin noticias huérfanas	TD	A
F-ADM-03	Crear usuario con rol	—	Datos y rol	Usuario creado con permisos correctos	PE	A
F-ADM-04	Desactivar usuario	Usuario activo	Desactivar	No puede iniciar sesión; su contenido permanece	TE	A
F-ADM-05	Admin no puede eliminarse a sí mismo (último admin)	Un solo admin	Intentar eliminar	Operación bloqueada	TD	M
F-ADM-06	Moderar comentario	Comentario pendiente	Aprobar, rechazar, marcar spam	Estado actualizado; visibilidad pública coherente	TE	A
F-ADM-07	Configuración del sitio	—	Cambiar nombre, logo, redes, SEO	Cambios aplicados en todo el sitio	PE	M
F-ADM-08	Dashboard con estadísticas	Datos semilla	Abrir panel	Totales coinciden con los registros reales de la BD	PE	M
F-ADM-09	Registro de auditoría	—	Realizar acciones sensibles	Log con usuario, acción, fecha e IP	PE	M
F-ADM-10	Copia de seguridad y restauración	—	Generar respaldo; restaurar en entorno limpio	Datos íntegros tras restaurar	EXP	A
2.2.5 Usuarios públicos (visitante y lector)
ID	Escenario	Precondición	Pasos / Datos	Resultado esperado	Téc.	Pri.
F-PUB-01	Ver portada	Noticias publicadas	Abrir /	Noticias recientes ordenadas por fecha desc.; solo publicadas	PE	A
F-PUB-02	Paginación	> 10 noticias	Navegar páginas; parámetro page=0, page=9999, page=abc	Navegación correcta; valores inválidos manejados sin error	VL	A
F-PUB-03	Filtrar por categoría y etiqueta	—	Seleccionar categoría	Solo noticias de esa categoría	PE	A
F-PUB-04	Detalle de noticia	—	Abrir noticia	Título, autor, fecha, imagen y contenido completos	PE	A
F-PUB-05	Noticia inexistente	—	URL con ID inválido	Página 404 amigable	PE	A
F-PUB-06	Búsqueda con resultados	—	Palabra clave existente	Resultados relevantes, resaltados y paginados	PE	A
F-PUB-07	Búsqueda sin resultados	—	Término inexistente	Mensaje claro con sugerencias	PE	M
F-PUB-08	Búsqueda con caracteres peligrosos	—	%, ', <script>	Escapado; sin error SQL ni XSS reflejado	SEG	A
F-PUB-09	Publicar comentario como lector	Sesión de lector	Enviar comentario válido	Se envía a moderación o se publica según configuración	PE	A
F-PUB-10	Comentario vacío o excesivo	Límite 1000 caracteres	0, 1000 y 1001 caracteres	Vacío y 1001 rechazados; 1000 aceptado	VL	M
F-PUB-11	Anti-spam en comentarios	—	Envíos repetidos rápidos	Limitación de tasa o CAPTCHA accesible	SEG	M
F-PUB-12	Suscripción al boletín	—	Correo válido / inválido	Alta con confirmación; inválido rechazado	PE	M
F-PUB-13	Compartir en redes	—	Pulsar botones	Enlace correcto y metadatos Open Graph	PE	B
F-PUB-14	Feed RSS	—	Abrir /rss.xml	XML válido con últimas noticias	PE	M
F-PUB-15	Protección CSRF	Sesión activa	Enviar formulario sin token o con token inválido	Solicitud rechazada	SEG	A
F-PUB-16	Gestión de cookies	Primera visita	Aceptar / rechazar	Preferencia respetada y persistida	PE	M
2.3 CRITERIO 2: USABILIDAD

Se evalúa con base en las heurísticas de Nielsen, ISO 9241-11 (eficacia, eficiencia, satisfacción) y las subcaracterísticas de ISO/IEC 25010. Participantes recomendados: al menos 5 por perfil, incluyendo personas con discapacidad y adultos mayores.

ID	Escenario	Procedimiento	Criterio de aceptación	Métrica	Pri.
U-01	Reconocibilidad del propósito	Mostrar la portada 5 segundos y preguntar qué es el sitio	≥ 90 % identifica que es un sitio de noticias	% de aciertos	A
U-02	Navegación principal clara	Pedir encontrar una categoría específica	≥ 90 % éxito en ≤ 3 clics	Tasa de éxito, clics	A
U-03	Tarea: encontrar una noticia por búsqueda	Tarea guiada sin ayuda	≥ 90 % éxito; tiempo ≤ 30 s	Éxito, tiempo	A
U-04	Tarea del admin: publicar una noticia	Editor novato sin capacitación	≥ 80 % completa en ≤ 5 min sin ayuda	Éxito, tiempo, errores	A
U-05	Curva de aprendizaje	Repetir la tarea 3 veces	Tiempo disminuye ≥ 30 % entre 1.ª y 3.ª	Tiempo por iteración	M
U-06	Visibilidad del estado del sistema	Observar tras guardar, subir, buscar	Siempre hay retroalimentación (mensaje, indicador de carga) en ≤ 1 s	Observación	A
U-07	Prevención de errores	Intentar acciones destructivas	Confirmación en eliminaciones; datos del formulario preservados ante error	Lista de verificación	A
U-08	Mensajes de error comprensibles	Provocar 10 errores comunes	Mensajes en lenguaje claro, indican causa y solución, sin códigos técnicos	% de mensajes conformes	A
U-09	Consistencia y estándares	Auditar patrones (botones, iconos, ubicación)	0 inconsistencias de comportamiento entre pantallas	Lista de verificación	A
U-10	Responsividad (móvil, tablet, escritorio)	Probar 360, 768, 1280, 1920 px	Sin desplazamiento horizontal; todas las funciones disponibles	Revisión	A
U-11	Facilidad táctil	Prueba en móvil con pulgar	Objetivos ≥ 44×44 px con separación; formularios con teclado adecuado (type=email, inputmode)	Medición	A
U-12	Formularios usables	Revisar etiquetas, autocompletado, campos obligatorios	Etiqueta visible por campo; autocomplete correcto; obligatoriedad indicada de forma no solo cromática	Lista de verificación	A
U-13	Legibilidad del contenido	Evaluar tamaño, interlineado, longitud de línea	Texto base ≥ 16 px, interlineado ≥ 1.5, línea ≤ 80 caracteres	Medición	A
U-14	Ayuda y documentación del admin	Revisar ayudas contextuales	Tooltips y guía rápida disponibles en funciones complejas	Revisión	M
U-15	Migas de pan y orientación	Navegar 3 niveles	El usuario siempre sabe dónde está y cómo volver	Observación	M
U-16	Deshacer y control del usuario	Cancelar, volver, restaurar de papelera	Salidas de emergencia disponibles	Lista de verificación	M
U-17	Usabilidad para adultos mayores	Pruebas con usuarios ≥ 60 años	≥ 80 % éxito en tareas básicas	Éxito	A
U-18	Usabilidad para personas con discapacidad	Pruebas con usuarios con baja visión, ceguera, discapacidad motora y cognitiva	≥ 80 % éxito de tareas clave con sus tecnologías de apoyo	Éxito, SUS	A
U-19	Satisfacción global	Cuestionario SUS tras las tareas	SUS ≥ 80 (grado "excelente")	Puntuación SUS	A
U-20	Página 404 y estados vacíos	Provocar 404 y listas vacías	Mensaje útil con enlaces de retorno y búsqueda	Observación	M
2.4 CRITERIO 3: ACCESIBILIDAD (WCAG 2.2 AA + AAA selectivo)

Se organiza por discapacidad y se trazan los criterios de éxito de WCAG.

2.4.1 Discapacidad visual (ceguera, baja visión, daltonismo)
ID	WCAG	Caso de prueba	Método	Resultado esperado	Pri.
A-VIS-01	1.1.1	Todas las imágenes informativas tienen alt descriptivo; decorativas con alt=""	axe + revisión manual	0 imágenes sin alternativa	A
A-VIS-02	1.1.1	Campo "texto alternativo" obligatorio al subir imagen en el panel	Funcional	No se puede publicar imagen informativa sin alt	A
A-VIS-03	1.3.1 / 4.1.2	Estructura semántica: header, nav, main, article, footer; un solo h1; jerarquía de títulos sin saltos	Inspección + NVDA	Estructura correcta; regiones navegables	A
A-VIS-04	2.4.1	Enlace "Saltar al contenido" funcional	Teclado	Primer Tab lo muestra; salta a main	A
A-VIS-05	1.4.3	Contraste de texto ≥ 4.5:1 (3:1 en texto grande)	Colour Contrast Analyser	0 incumplimientos	A
A-VIS-06	1.4.6 (AAA)	Contraste ≥ 7:1 en texto de lectura de artículos	Colour Contrast Analyser	Cumple en cuerpo de noticia	M
A-VIS-07	1.4.11	Contraste ≥ 3:1 en bordes de campos, iconos y botones	Medición	Cumple	A
A-VIS-08	1.4.1	Información no transmitida solo por color (errores, enlaces, estados)	Simulador de daltonismo	Existe indicador adicional (icono, texto, subrayado)	A
A-VIS-09	1.4.4	Zoom de texto al 200 %	Navegador	Sin pérdida de contenido o funcionalidad	A
A-VIS-10	1.4.10	Reflujo a 320 px de ancho (equivale a zoom 400 %)	Navegador	Sin scroll horizontal en texto	A
A-VIS-11	1.4.12	Espaciado de texto aumentado (interlineado 1.5, párrafos 2×, letras 0.12em)	Bookmarklet	Sin recortes ni solapamientos	M
A-VIS-12	1.4.5	No se usan imágenes de texto (salvo logotipo)	Inspección	0 casos	M
A-VIS-13	1.4.13	Contenido emergente (tooltips, menús) descartable, persistente y que se puede apuntar	Manual	Cumple los tres requisitos	M
A-VIS-14	4.1.3	Mensajes de estado (guardado, error, resultados de búsqueda) anunciados	NVDA / VoiceOver	Se anuncian mediante role="status" o aria-live sin mover el foco	A
A-VIS-15	Tecnologías de asistencia	Tareas completas con NVDA+Firefox/Chrome, JAWS, VoiceOver (macOS/iOS), TalkBack	Prueba manual con script	Lectura de portada, noticia, búsqueda, login y publicación sin bloqueos	A
A-VIS-16	1.4.8 / modos	Modo de alto contraste de Windows y modo oscuro (prefers-color-scheme, forced-colors)	Sistema operativo	Interfaz legible y operable	M
2.4.2 Discapacidad auditiva
ID	WCAG	Caso de prueba	Método	Resultado esperado	Pri.
A-AUD-01	1.2.2	Videos incrustados con subtítulos sincronizados	Revisión manual	Subtítulos disponibles y precisos	A
A-AUD-02	1.2.1	Audio (podcast) con transcripción textual	Revisión	Transcripción accesible junto al reproductor	A
A-AUD-03	1.2.3 / 1.2.5	Audiodescripción o alternativa en vídeos con información visual	Revisión	Disponible	M
A-AUD-04	1.4.2	Audio con reproducción automática	Carga de página	Ninguno reproduce solo > 3 s sin control de pausa/volumen	A
A-AUD-05	1.2.4	Emisiones en vivo (si existen) con subtítulos	Revisión	Subtítulos en directo	B
A-AUD-06	3.3 / General	Alertas del sistema (errores, confirmaciones) no dependen únicamente de sonido	Silenciar el dispositivo	Alertas visuales siempre presentes	A
A-AUD-07	3.1.5 (AAA)	Lengua de señas o resumen en lenguaje sencillo para contenidos clave (opcional)	Revisión	Disponible o justificado	B
A-AUD-08	Reproductor	Controles del reproductor operables por teclado y etiquetados	Teclado + lector	Cumple	A
2.4.3 Discapacidad motora
ID	WCAG	Caso de prueba	Método	Resultado esperado	Pri.
A-MOT-01	2.1.1	Todas las funciones (portal y panel admin) operables solo con teclado	Teclado	100 % de tareas completables	A
A-MOT-02	2.1.2	Sin trampas de teclado (editor WYSIWYG, modales, reproductores)	Teclado	Siempre se puede salir con Tab/Esc	A
A-MOT-03	2.4.3	Orden de foco lógico	Tab	Orden coincide con el orden visual y de lectura	A
A-MOT-04	2.4.7 / 2.4.11	Foco visible y no oculto por barras fijas	Teclado	Indicador claro (contraste ≥ 3:1) y no tapado	A
A-MOT-05	2.5.8	Tamaño de objetivo ≥ 24×24 px (se exige 44×44 px como objetivo interno)	Medición	Cumple	A
A-MOT-06	2.5.1	Gestos complejos (arrastrar, pellizcar) con alternativa de un solo puntero	Dispositivo táctil	Existe alternativa simple	A
A-MOT-07	2.5.7	Arrastrar y soltar (ordenar noticias en admin, subir archivos) con alternativa sin arrastre	Manual	Botones o selector disponibles	A
A-MOT-08	2.5.2	Cancelación de puntero: acciones se activan al soltar, no al presionar	Ratón/táctil	Se puede cancelar	M
A-MOT-09	2.2.1	Tiempos de sesión ajustables o con aviso previo	Esperar expiración	Aviso con opción de extender (≥ 20 s para responder)	A
A-MOT-10	2.5.4	Funciones por movimiento (agitar) con alternativa	Móvil	Alternativa o no aplica	B
A-MOT-11	3.2.1 / 3.2.2	Sin cambios de contexto inesperados al enfocar o introducir datos	Manual	Comportamiento predecible	A
A-MOT-12	Entrada alternativa	Operación con control por voz (Dragon / Control por voz) y conmutador	Manual	Los nombres accesibles coinciden con el texto visible (2.5.3)	M
A-MOT-13	3.3.7	Evitar reintroducir información ya dada en un mismo proceso (entrada redundante)	Manual	Cumple	M
A-MOT-14	3.3.8	Autenticación accesible: sin pruebas cognitivas obligatorias; permite pegar contraseña y gestor de contraseñas	Manual	Cumple	A
2.4.4 Discapacidad cognitiva y de aprendizaje
ID	WCAG	Caso de prueba	Método	Resultado esperado	Pri.
A-COG-01	3.1.1 / 3.1.2	Idioma de página (lang="es") y de fragmentos en otro idioma	Inspección	Declarado correctamente	A
A-COG-02	3.1.3–3.1.5	Lenguaje claro: nivel de lectura adecuado; glosario de términos técnicos; siglas explicadas	Revisión (índice de legibilidad Fernández-Huerta)	Contenido de interfaz en lenguaje sencillo	M
A-COG-03	3.3.1 / 3.3.3	Errores identificados en texto, con sugerencias de corrección	Provocar errores	Descripción clara por campo	A
A-COG-04	3.3.2	Etiquetas e instrucciones visibles en formularios	Inspección	Todas presentes (no solo placeholder)	A
A-COG-05	3.3.4	Acciones críticas reversibles, verificables o confirmables	Funcional	Cumple en eliminaciones y publicaciones	A
A-COG-06	3.2.3 / 3.2.4	Navegación e identificación de componentes consistentes	Auditoría	Mismo orden y nombres en todas las páginas	A
A-COG-07	3.2.6	Ayuda consistente (contacto, ayuda) en la misma ubicación relativa	Auditoría	Cumple	M
A-COG-08	2.2.2	Contenido en movimiento (carruseles, ticker de noticias) pausable	Manual	Control de pausa disponible; sin autoplay obligatorio	A
A-COG-09	2.3.1	Sin destellos > 3 por segundo	Herramienta PEAT	0 incidencias	A
A-COG-10	2.2.6	Aviso de tiempo de inactividad que cause pérdida de datos	Manual	Se informa al usuario	M
A-COG-11	1.3.5	Propósito de campos identificado (autocomplete)	Inspección	Presente en nombre, correo, contraseña	M
A-COG-12	Cognitiva	Modo de lectura simplificado / sin distracciones en las noticias	Funcional	Disponible o justificado	M
A-COG-13	Cognitiva	Reducción de movimiento (prefers-reduced-motion) respetada	Configuración SO	Animaciones desactivadas o reducidas	A
A-COG-14	Cognitiva	Estructura de contenido en párrafos cortos, títulos descriptivos, listas	Revisión editorial	Cumple	M
2.4.5 Verificación técnica transversal
ID	Caso de prueba	Herramienta	Resultado esperado	Pri.
A-TEC-01	Análisis automatizado de todas las plantillas	axe-core, WAVE, Lighthouse, Pa11y en CI	0 violaciones críticas/serias; Lighthouse Accessibility = 100	A
A-TEC-02	HTML válido	W3C Validator	0 errores (IDs únicos, anidamiento correcto)	A
A-TEC-03	Uso correcto de ARIA (primera regla: usar HTML nativo)	axe + revisión	Sin roles redundantes ni conflictivos	A
A-TEC-04	Árbol de accesibilidad	DevTools	Nombre, rol y estado correctos en controles personalizados	A
A-TEC-05	Accesibilidad del panel admin y editor de contenido (ATAG 2.0)	Manual	El editor es accesible y ayuda a crear contenido accesible (alt obligatorio, jerarquía de títulos validada)	A
A-TEC-06	Orientación	Rotar dispositivo	Funciona en vertical y horizontal (1.3.4)	M
A-TEC-07	Declaración de accesibilidad	Revisión	Página con nivel de conformidad, contacto y canal de comentarios	M
A-TEC-08	CAPTCHA accesible	Manual	Alternativa no visual o sin CAPTCHA (honeypot, tiempo, tokens)	A
2.5 CRITERIO 4: ESTILO (diseño definido, coherente y atractivo)

Los criterios atractivo/coherente son parcialmente subjetivos, por lo que se combinan listas de verificación objetivas con evaluación por panel de expertos y encuesta de percepción (escala Likert 1–5; meta ≥ 4.3 de media).

ID	Aspecto	Caso de prueba / Criterio	Método	Criterio de aceptación	Pri.
E-01	Guía de estilo definida	Existe documento (paleta, tipografía, espaciado, iconografía, componentes) y se implementa	Revisión documental	Guía completa y trazable al CSS (variables / tokens de diseño)	A
E-02	Paleta de colores	Se usan solo los colores definidos (primario, secundario, neutros, estados)	Inspección CSS / auditoría	0 colores fuera de la paleta; contraste conforme a WCAG	A
E-03	Tipografía	Máximo 2 familias; escala tipográfica coherente; jerarquía clara (títulos, subtítulos, cuerpo)	Inspección	Cumple escala definida; fuentes con fallback	A
E-04	Coherencia entre páginas	Cabecera, pie, botones, formularios y tarjetas idénticos en todas las vistas	Comparación visual	0 inconsistencias	A
E-05	Coherencia entre portal y panel admin	Mismos tokens de diseño y componentes	Comparación	Misma identidad visual	M
E-06	Retícula y espaciado	Uso de rejilla (8 pt / 12 columnas); márgenes y alineaciones uniformes	Superposición de rejilla	Desviación ≤ 2 px	M
E-07	Jerarquía visual	La noticia principal destaca; flujo de lectura natural	Prueba de mirada (5 s) / mapa de calor	≥ 80 % identifica el elemento principal	A
E-08	Imágenes	Formato, proporción y calidad uniformes; sin distorsión ni pixelado; recorte consistente	Inspección	Cumple relación de aspecto definida	A
E-09	Estados de componentes	Normal, hover, foco, activo, deshabilitado y error definidos y coherentes	Inspección	Todos presentes y consistentes	A
E-10	Iconografía	Un único conjunto de iconos, tamaño y trazo uniformes	Inspección	Cumple	M
E-11	Responsividad visual	Diseño se adapta con elegancia en 360, 768, 1280 y 1920 px; sin elementos cortados	Capturas comparativas	Cumple en todos	A
E-12	Modo oscuro (si aplica)	Paleta alternativa coherente y con contraste adecuado	Comparación	Cumple	M
E-13	Microinteracciones y animaciones	Transiciones sutiles (150–300 ms), propósito claro, respetan prefers-reduced-motion	Revisión	Cumple	M
E-14	Impresión	Hoja de estilos de impresión para noticias (sin menús ni anuncios)	Vista previa de impresión	Legible y ordenada	B
E-15	Lenguaje y tono de interfaz	Textos de interfaz coherentes (voz, tratamiento, mayúsculas)	Revisión editorial	Sin variaciones	M
E-16	Calidad del código CSS	Convención de nombres (BEM), sin estilos en línea, linter sin errores	Stylelint	0 errores	M
E-17	Percepción estética	Encuesta a ≥ 20 usuarios (diseño moderno, ordenado, agradable, profesional)	Likert 1–5	Media ≥ 4.3	A
E-18	Evaluación experta	Panel de ≥ 3 diseñadores UI con rúbrica (originalidad, armonía, identidad)	Rúbrica 0–4	Puntuación ≥ 3.5	A
E-19	Consistencia entre navegadores	Renderizado equivalente en Chrome, Firefox, Safari y Edge	Pruebas visuales de regresión (Percy, BackstopJS)	Diferencia visual ≤ 1 %	A
E-20	Estilo vs. accesibilidad	Ningún elemento decorativo reduce legibilidad ni contraste	Revisión cruzada con A-VIS	0 conflictos	A
2.6 CRITERIO 5: EFICIENCIA

Se basa en la eficiencia de desempeño de ISO/IEC 25010 (comportamiento temporal, uso de recursos, capacidad). Se prueba en tres perfiles de dispositivo:

Perfil	Configuración de referencia
Gama alta	Escritorio moderno, fibra óptica
Gama media	Portátil/móvil promedio, 4G
Recursos limitados	Móvil de gama baja (2 GB RAM, CPU lenta), CPU ×4–6 más lenta (throttling), red 3G lenta / Slow 4G, PC antiguo
2.6.1 Rendimiento del cliente (front-end)
ID	Escenario	Procedimiento / Herramienta	Criterio de aceptación	Pri.
EF-01	LCP (Largest Contentful Paint)	Lighthouse / WebPageTest	≤ 2.5 s (gama media); ≤ 4 s (recursos limitados, red 3G)	A
EF-02	INP (Interaction to Next Paint)	Lighthouse / CrUX	≤ 200 ms	A
EF-03	CLS (Cumulative Layout Shift)	Lighthouse	≤ 0.1 (dimensiones fijas en imágenes y anuncios)	A
EF-04	FCP, TBT y Speed Index	Lighthouse	FCP ≤ 1.8 s; TBT ≤ 200 ms	M
EF-05	Puntuación global	Lighthouse (móvil con throttling)	Performance ≥ 90	A
EF-06	Peso total de página	DevTools Network	≤ 1 MB portada; ≤ 1.5 MB noticia (idealmente)	A
EF-07	Número de peticiones HTTP	DevTools	≤ 40 peticiones; recursos críticos priorizados	M
EF-08	Optimización de imágenes	Inspección	WebP/AVIF, srcset/sizes, loading="lazy" bajo el pliegue	A
EF-09	Minificación y compresión	Inspección de cabeceras	CSS/JS minificados; Gzip o Brotli activo	A
EF-10	Caché del navegador	Cabeceras	Cache-Control/ETag para estáticos; reutilización en 2.ª visita	A
EF-11	JavaScript mínimo	Cobertura de código (DevTools)	≤ 170 KB de JS comprimido; sin librerías no usadas; scripts defer/async	M
EF-12	Funcionamiento sin JS (mejora progresiva)	Deshabilitar JS	Contenido de lectura accesible	M
EF-13	Uso de CPU y memoria en cliente	Performance Monitor, Task Manager	Sin fugas de memoria tras 10 min de uso; CPU en reposo ≈ 0 %	A
EF-14	Consumo de batería y datos	Perfil en dispositivo real / modo "Ahorro de datos"	Respeta Save-Data; sin sondeos innecesarios; sin autoplay de vídeo	M
EF-15	Modo de datos reducido	Cabecera Save-Data / prefers-reduced-data	Imágenes de menor calidad o diferidas	B
EF-16	Fuentes web	Inspección	font-display: swap; subconjunto de caracteres; ≤ 2 familias	M
EF-17	Dispositivo de recursos limitados	Móvil real gama baja, 3G	Noticia legible e interactiva en ≤ 5 s	A
EF-18	Modo sin conexión	Service Worker (si se implementa)	Última noticia visitada disponible offline	B
2.6.2 Rendimiento del servidor (back-end PHP y BD)
ID	Escenario	Procedimiento / Herramienta	Criterio de aceptación	Pri.
EF-20	TTFB (Time to First Byte)	WebPageTest	≤ 600 ms (objetivo ≤ 200 ms con caché)	A
EF-21	Prueba de carga nominal	JMeter/k6: 100 usuarios concurrentes, 10 min	p95 ≤ 2 s; tasa de errores < 1 %	A
EF-22	Prueba de estrés	Incremento hasta el punto de quiebre	Se identifica el límite; degradación controlada (sin corrupción)	A
EF-23	Prueba de picos (noticia viral)	Salto de 50 a 1000 usuarios en 1 min	Sistema se mantiene o se recupera en < 2 min	A
EF-24	Prueba de resistencia (soak)	Carga moderada por 4–8 h	Sin fugas de memoria ni deterioro del tiempo de respuesta	M
EF-25	Consultas SQL eficientes	EXPLAIN, slow query log	Sin consultas N+1; índices en slug, categoría y fecha; consultas ≤ 100 ms	A
EF-26	Caché del servidor	OPcache, caché de página/fragmentos, Redis/Memcached	OPcache activo; razón de aciertos de caché ≥ 80 % en portada	A
EF-27	Uso de CPU/RAM del servidor	top, monitoreo	CPU < 70 %, RAM estable bajo carga nominal	A
EF-28	Paginación y volumen de datos	BD con 100 000 noticias	Listados y búsqueda con tiempos estables (≤ 1 s)	A
EF-29	Subida de archivos pesados	Imagen de 5 MB	Procesada sin agotar memory_limit ni max_execution_time; redimensionado optimizado	M
EF-30	Conexiones a BD	Monitoreo	Cierre/reutilización correcta; sin agotar max_connections	M
EF-31	Recuperación ante fallos	Reinicio de servicios / BD caída	Mensaje amigable; reanudación automática	M
EF-32	Escalabilidad	Aumentar recursos o instancias	Mejora proporcional del rendimiento	B
EF-33	Compresión y HTTP/2 o HTTP/3	Cabeceras	Activos	M
EF-34	Rendimiento del panel admin	Editor con artículo de 10 000 palabras y 50 imágenes	Edición fluida; guardado ≤ 3 s	M
2.7 Gestión del plan

Matriz de riesgos principal

Riesgo	Prob.	Impacto	Mitigación en el plan
XSS / inyección SQL	Media	Crítico	F-NOT-04, F-AUT-05, F-PUB-08, pruebas SEG
Contenido no accesible	Alta	Alto	Sección 2.4 completa, ATAG
Caída por pico de tráfico	Media	Alto	EF-21 a EF-24, caché
Pérdida de datos	Baja	Crítico	F-ADM-10, EF-31
Interfaz incoherente	Media	Medio	Sección 2.5

Estimación de referencia (equipo de 3 testers)

Actividad	Esfuerzo (persona-días)
Análisis y diseño de casos	6
Preparación de entorno y datos	3
Ejecución funcional	8
Usabilidad (con usuarios)	5
Accesibilidad (auto + manual + AT)	8
Estilo (revisión y encuestas)	3
Eficiencia (carga y front-end)	6
Reporte de defectos, retest y regresión	8
Informe de cierre	2
Total	≈ 49 (≈ 3.5 semanas calendario)

Métricas de reporte y cierre: cobertura de requisitos (meta 100 %), tasa de aprobación (≥ 95 %), densidad de defectos, DDE (≥ 90 %), violaciones WCAG (0 críticas), SUS (≥ 80), Lighthouse (Perf ≥ 90 / Acc = 100), p95 de respuesta y tasa de errores bajo carga.