<?php

declare(strict_types=1);

// Genera exclusivamente archivos de demostración, nunca modifica una base de datos.
require dirname(__DIR__) . '/app/Core/bootstrap.php';

$topics = [
    'Actualidad' => ['Lima abre nuevos espacios peatonales para recuperar la vida de barrio', 'La biblioteca municipal amplía su horario para estudiantes', 'Vecinos organizan una red de apoyo durante la temporada de lluvias', 'El mercado de productores acerca alimentos frescos a la ciudad', 'Un programa de reciclaje puerta a puerta suma nuevos distritos', 'La feria de servicios públicos ofrece orientación gratuita', 'Jóvenes voluntarios recuperan un parque junto al río', 'El presupuesto participativo recibe propuestas de la comunidad'],
    'Tecnología' => ['Internet rural: las comunidades que construyen su propia conexión', 'Un laboratorio universitario desarrolla sensores de bajo consumo', 'Cómo proteger tus datos al cambiar de teléfono', 'La inteligencia artificial entra al aula con preguntas pendientes', 'Pequeños comercios prueban pagos digitales sin comisiones fijas', 'Una aplicación abierta ayuda a identificar rutas accesibles', 'Reparar antes de reemplazar: crece la comunidad de electrónica', 'La conectividad pública impulsa nuevos proyectos de aprendizaje'],
    'Cultura' => ['El museo de barrio abre sus puertas a nuevas historias', 'Una editorial independiente recupera relatos de la costa', 'El festival de cine reúne miradas de ocho regiones', 'Artesanas comparten técnicas que pasan de una generación a otra', 'La música vuelve a las plazas con conciertos gratuitos', 'Un archivo digital protege fotografías familiares', 'Teatro comunitario: historias contadas por sus protagonistas', 'La ruta de murales transforma los paseos del fin de semana'],
    'Ciencia' => ['Un observatorio invita a mirar el cielo lejos de las luces', 'Investigadores estudian cómo recuperar los suelos degradados', 'La ciencia ciudadana registra aves en los humedales', 'Nuevos materiales permiten almacenar energía con menos residuos', 'Una estación meteorológica acerca los datos a las escuelas', 'El laboratorio abierto conecta investigadores y agricultores', 'Estudiar el océano para anticipar cambios en la costa', 'Las semillas nativas encuentran un espacio de conservación'],
    'Economía' => ['Cooperativas locales encuentran nuevas rutas para sus productos', 'Ahorrar en familia empieza por conocer los gastos cotidianos', 'Emprendedoras comparten herramientas para vender en línea', 'El turismo de cercanía fortalece pequeños negocios', 'Los mercados barriales ensayan compras colectivas', 'Una incubadora acompaña proyectos de economía circular', 'Más formación financiera para los trabajadores independientes'],
    'Deportes' => ['El atletismo escolar descubre nuevas promesas regionales', 'Ciclistas proponen rutas seguras para entrenar en la ciudad', 'El campeonato de barrio celebra el juego limpio', 'Un club inclusivo acerca la natación a más familias', 'Las deportistas que abren camino en las ligas locales', 'Caminar en grupo combina actividad física y comunidad', 'Las escuelas recuperan espacios para jugar al aire libre'],
    'Medio ambiente' => ['Los humedales urbanos vuelven a reunir vida y comunidad', 'Restaurar un bosque requiere tiempo y participación local', 'Huertos escolares enseñan a cuidar el agua', 'El transporte compartido reduce viajes y emisiones', 'La costa ensaya una limpieza que empieza en los hogares', 'Una red de viveros protege plantas de la región', 'Aprender a separar residuos cambia la rutina del barrio'],
    'Bienestar' => ['Las redes de cuidado acompañan a las personas mayores', 'El descanso encuentra un lugar en la rutina de la ciudad', 'Cocinar juntos fortalece vínculos entre generaciones', 'Centros comunitarios ofrecen espacios de escucha', 'Pausas activas: una práctica sencilla en los espacios de trabajo', 'La lectura compartida acerca a familias y escuelas', 'Diseñar calles amables también mejora la vida cotidiana'],
];
$contexts = [
    'Actualidad' => ['Los espacios de encuentro ayudan a construir confianza entre vecinos. La iniciativa reúne a organizaciones locales, comerciantes y estudiantes en una agenda compartida.', 'Las actividades se organizarán por etapas para que cada barrio pueda adaptar la propuesta a sus necesidades. Los participantes piden que la información sobre horarios y resultados permanezca abierta.'],
    'Tecnología' => ['La propuesta prioriza herramientas fáciles de mantener y equipos que consumen poca energía. El equipo considera esencial que las personas puedan comprender cómo se utilizan sus datos.', 'Durante el piloto se recogerán experiencias de usuarios con teléfonos antiguos y conexiones lentas. Los resultados servirán para mejorar la accesibilidad antes de ampliar el servicio.'],
    'Cultura' => ['El proyecto reúne memoria, creación y participación. Sus organizadores buscan que el público conozca las historias que suelen quedar fuera de las grandes salas de exhibición.', 'La programación incluye encuentros con creadores y actividades de acceso libre. El trabajo con escuelas permitirá acercar los materiales a nuevas generaciones.'],
    'Ciencia' => ['El trabajo combina observación de campo y análisis de datos. Los investigadores explican que las conclusiones deben contrastarse con mediciones en distintas épocas del año.', 'Las escuelas y organizaciones interesadas podrán participar en talleres de divulgación. Compartir los métodos permitirá que otras comunidades repitan las observaciones.'],
    'Economía' => ['Los pequeños negocios necesitan información clara para tomar decisiones. La iniciativa busca reducir costos y mejorar la colaboración sin perder la identidad de cada emprendimiento.', 'Los participantes compararán resultados durante las próximas semanas. El acompañamiento incluye planificación, registro de gastos y orientación para evaluar los riesgos.'],
    'Deportes' => ['La participación importa tanto como el resultado. Entrenadores y familias impulsan espacios donde el respeto y la inclusión acompañan el desarrollo de habilidades.', 'La organización propone horarios compatibles con los estudios y el trabajo. También se revisarán las condiciones de acceso para que más personas puedan sumarse.'],
    'Medio ambiente' => ['Las soluciones ambientales necesitan continuidad y conocimiento del territorio. La propuesta conecta a vecinos con especialistas para cuidar los recursos que comparten.', 'Los avances se registrarán con indicadores sencillos y observaciones periódicas. La educación comunitaria será una parte central de cada etapa del proyecto.'],
    'Bienestar' => ['El bienestar también depende de los vínculos y de los espacios que habitamos. La propuesta ofrece actividades sencillas que las familias pueden adaptar a sus rutinas.', 'Los equipos locales recogerán sugerencias para ajustar horarios y formatos. La participación es voluntaria y no sustituye la atención profesional cuando se necesita.'],
];
$quote = fn (string $text): string => "'" . str_replace(["\\", "'"], ["\\\\", "''"], $text) . "'";
$sql = "USE blog_noticias;\nSET NAMES utf8mb4;\nSTART TRANSACTION;\nINSERT INTO roles VALUES (1,'administrador'),(2,'editor'),(3,'lector');\n";
$hash = App\Core\Auth::hash('DemoNoticias2026!');
$names = ['Administración Pulso', 'Ana Torres', 'Diego Rojas', 'Lucía Mendoza', 'Carlos Quispe', 'María Castillo', 'José Salas', 'Elena Vargas'];
$emails = ['admin', 'editor1', 'editor2', 'lector1', 'lector2', 'lector3', 'lector4', 'lector5'];
foreach ($names as $i => $name) {
    $role = $i === 0 ? 1 : ($i < 3 ? 2 : 3);
    $sql .= 'INSERT INTO users (id,role_id,name,email,password_hash,email_verified_at) VALUES (' . ($i + 1) . ',' . $role . ',' . $quote($name) . ',' . $quote($emails[$i] . '@pulso.test') . ',' . $quote($hash) . ",NOW());\n";
}
$tags = ['Comunidad', 'Innovación', 'Perú', 'Educación', 'Sostenibilidad', 'Ciudad', 'Ideas', 'Participación'];
foreach ($tags as $i => $tag) {
    $sql .= 'INSERT INTO tags VALUES (' . ($i + 1) . ',' . $quote($tag) . ',' . $quote(App\Core\Slug::make($tag)) . ");\n";
}
$id = 0;
$categoryId = 0;
foreach ($topics as $category => $titles) {
    $categoryId++;
    $sql .= 'INSERT INTO categories VALUES (' . $categoryId . ',' . $quote($category) . ',' . $quote(App\Core\Slug::make($category)) . ");\n";
    foreach ($titles as $title) {
        $id++;
        $content = '<p>' . $contexts[$category][0] . '</p><h2>Una iniciativa que conecta</h2><p>' . $contexts[$category][1] . '</p><h2>La mirada de la comunidad</h2><p>En este reportaje de demostración sobre «' . $title . '», las voces locales recuerdan que escuchar es el primer paso para construir acuerdos.</p><blockquote><p>Los cambios duraderos se construyen con participación y una evaluación transparente.</p></blockquote><h2>Qué viene después</h2><p>Los próximos encuentros permitirán revisar los avances y recoger propuestas. Esta noticia es un ejemplo ficticio para probar el portal; no describe un anuncio oficial.</p>';
        $status = $id <= 54 ? 'publicado' : ($id <= 56 ? 'borrador' : ($id <= 58 ? 'revision' : 'archivado'));
        $date = 'DATE_SUB(NOW(), INTERVAL ' . ($id * 2) . ' HOUR)';
        $excerpt = mb_substr($contexts[$category][0], 0, 200);
        $alt = 'Ilustración editorial de ' . mb_strtolower($category) . ' con paisaje y espacios de encuentro';
        $sql .= 'INSERT INTO articles (id,author_id,category_id,title,slug,content,excerpt,image_path,image_alt,status,featured,views,published_at) VALUES (' . implode(',', [$id, $id % 2 ? 2 : 3, $categoryId, $quote($title), $quote(App\Core\Slug::make($title)), $quote($content), $quote($excerpt), $quote('assets/news-' . $categoryId . '.webp'), $quote($alt), $quote($status), $id === 1 ? 1 : 0, 150 + ($id * 137) % 1400, $date]) . ");\n";
        $sql .= 'INSERT INTO article_tag VALUES (' . $id . ',' . ($categoryId) . '),(' . $id . ',' . ($categoryId % 8 + 1) . ");\n";
    }
}
$messages = ['Gracias por explicar el contexto. Me gustaría conocer cómo participar en los próximos encuentros.', 'La colaboración entre barrios puede ayudar a que estas iniciativas duren más tiempo.', 'Sería útil publicar los resultados de cada etapa para que podamos seguir el avance.', 'Me interesa la perspectiva de las escuelas. Compartir materiales abiertos facilita el aprendizaje.', 'Estas historias muestran el trabajo que ya ocurre en nuestras comunidades. Gracias por darles espacio.'];
for ($i = 1; $i <= 100; $i++) {
    $sql .= 'INSERT INTO comments (article_id,user_id,content,status,created_at) VALUES (' . (($i - 1) % 54 + 1) . ',' . (($i - 1) % 5 + 4) . ',' . $quote($messages[$i % 5]) . ',' . $quote($i % 4 === 0 ? 'pendiente' : 'aprobado') . ',DATE_SUB(NOW(), INTERVAL ' . ($i + 2) . " MINUTE));\n";
}
$sql .= "INSERT INTO settings (`key`,value) VALUES ('site_name','Pulso'),('seo_description','Historias, ideas y voces para entender el presente. Noticias de actualidad, cultura, tecnología y comunidad.'),('contact_email','contacto@example.test'),('logo',''),('social_facebook',''),('social_x','');\nCOMMIT;\n";
file_put_contents(ROOT . '/database/seed.sql', $sql);
if (!extension_loaded('gd')) {
    fwrite(STDERR, "Semilla generada. Activa GD para generar las ilustraciones.\n");
    exit(1);
}
// Ilustraciones vectoriales dibujadas con GD; no se descargan medios de terceros.
$palettes = [[174, 201, 205], [154, 182, 229], [226, 183, 152], [147, 179, 210], [182, 192, 162], [171, 189, 213], [153, 189, 170], [207, 182, 198]];
foreach ($palettes as $index => [$r, $g, $b]) {
    $image = imagecreatetruecolor(1200, 675);
    for ($y = 0; $y < 675; $y++) {
        $fade = $y / 675;
        $color = imagecolorallocate($image, (int) ($r * (1 - $fade * 0.25)), (int) ($g * (1 - $fade * 0.25)), (int) ($b * (1 - $fade * 0.25)));
        imageline($image, 0, $y, 1200, $y, $color);
    }
    $sun = imagecolorallocate($image, 247, 230, 186);
    imagefilledellipse($image, 900, 155, 170, 170, $sun);
    $mountain = imagecolorallocate($image, (int) ($r * 0.58), (int) ($g * 0.68), (int) ($b * 0.72));
    imagefilledpolygon($image, [0, 490, 280, 180 + $index * 12, 640, 530, 940, 320, 1200, 480, 1200, 675, 0, 675], $mountain);
    $ground = imagecolorallocate($image, 33, 68 + $index * 4, 73 + $index * 5);
    imagefilledpolygon($image, [0, 560, 450, 430, 800, 580, 1200, 460, 1200, 675, 0, 675], $ground);
    $building = imagecolorallocate($image, 232, 224, 203);
    $roof = imagecolorallocate($image, 63, 79, 97);
    for ($j = 0; $j < 4; $j++) {
        $x = 140 + $j * 140;
        $height = 105 + ($j + $index) % 3 * 28;
        imagefilledrectangle($image, $x, 560 - $height, $x + 105, 560, $building);
        imagefilledrectangle($image, $x - 8, 551 - $height, $x + 113, 560 - $height, $roof);
        for ($w = 0; $w < 3; $w++) {
            imagefilledrectangle($image, $x + 12 + $w * 30, 575 - $height, $x + 24 + $w * 30, 600 - $height, $roof);
        }
    }
    imagewebp($image, ROOT . '/public/assets/news-' . ($index + 1) . '.webp', 78);
    $small = imagescale($image, 480, 270);
    imagewebp($small, ROOT . '/public/assets/news-' . ($index + 1) . '-480.webp', 55);
    imagedestroy($small);
    imagedestroy($image);
}
foreach ([192, 512] as $size) {
    $icon = imagecreatetruecolor($size, $size);
    $blue = imagecolorallocate($icon, 23, 75, 219);
    $white = imagecolorallocate($icon, 255, 255, 255);
    imagefill($icon, 0, 0, $blue);
    imagesetthickness($icon, (int) ($size / 16));
    $points = [0.18, 0.54, 0.33, 0.54, 0.41, 0.31, 0.59, 0.76, 0.68, 0.54, 0.82, 0.54];
    for ($j = 0; $j < 10; $j += 2) {
        imageline($icon, (int) ($points[$j] * $size), (int) ($points[$j + 1] * $size), (int) ($points[$j + 2] * $size), (int) ($points[$j + 3] * $size), $white);
    }
    imagepng($icon, ROOT . '/public/assets/icon-' . $size . '.png');
    imagedestroy($icon);
}
echo "Generados seed.sql (8 usuarios, 8 categorías, 60 noticias, 100 comentarios) e imágenes WebP.\n";
