# Guía de estilo · Pulso

Portal editorial en español: información clara, espacios amplios y una portada con jerarquía visible. Datos e ilustraciones de demostración identificados en el pie.

| Token CSS | Uso | Claro | Oscuro |
|---|---|---|---|
| `--primary` | Acciones, enlaces, identidad | `#174bdb` | `#adc5ff` |
| `--secondary` | Categoría y éxito | `#00634e` | `#9fe4d2` |
| `--ink` | Texto de lectura | `#172334` | `#f4f7fb` |
| `--canvas` | Fondo | `#f5f6f8` | `#0d1622` |
| `--paper` | Tarjetas y formularios | `#ffffff` | `#152233` |
| `--muted` | Texto secundario | `#536174` | `#becada` |
| `--line` | Bordes de controles | `#778397` | `#9aa9bc` |
| `--danger` | Errores con texto y bordes | `#b02132` | `#ffb4be` |
| `--focus` | Foco de 3 px | `#9e3900` | `#ffd092` |

Una pila de fuentes del sistema, sin peticiones de fuentes. Texto base 16 px, lectura 18 px e interlineado 1.75. Títulos fluidos con `clamp`; ancho del texto 75 caracteres. Retícula de 12 columnas y espaciado principal de 8, 16, 24, 32 y 48 px; las medidas menores se reservan para bordes y detalles. Tarjetas 16:9 con WebP original dibujado con GD. Iconos geométricos SVG inline con nombre accesible en el control.

Clases de bloque descriptivas (`news-card`, `site-header`, `article-body`) y modificadores BEM (`button--quiet`, `alert--error`). Estados hover, activo, foco, deshabilitado y error comparten tokens. Foco exterior visible; el header no es fijo. El estado de una noticia se escribe, no se comunica solo por color.

El menú se despliega con botón y Escape; sin JS sus enlaces siguen presentes. Las tablas usan desplazamiento interno enfocable. El diseño se amplía en 360, 480, 768, 1024 y 1280 px. Áreas seguras de iOS con `env`. El botón de modo oscuro conserva la elección local, y sin elección se respeta el sistema. Animaciones de 180 ms y desplazamiento de 1 px al activar un botón; se desactivan con reducción de movimiento. La impresión muestra el artículo, su autor, su fecha y su imagen.

El contraste se verifica en ambas paletas con axe y con una medición adicional de ≥ 7:1 en `.article-body`; las evaluaciones manuales y perceptuales figuran en el plan original. Esta guía define la implementación; no sustituye una evaluación de WCAG completa.
