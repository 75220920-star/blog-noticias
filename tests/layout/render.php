<?php

declare(strict_types=1);

// Ensayo de presentación con datos de la semilla; no sustituye E2E con MySQL.
require dirname(__DIR__, 2) . '/app/Core/bootstrap.php';
$_SESSION = [];
$GLOBALS['config']['base_path'] = 'file:///' . str_replace('\\', '/', ROOT) . '/public';
$settings = ['site_name' => 'Pulso', 'contact_email' => 'contacto@example.test'];
$names = ['Actualidad', 'Tecnología', 'Cultura', 'Ciencia', 'Economía', 'Deportes', 'Medio ambiente', 'Bienestar'];
$categories = [];
foreach ($names as $i => $name) {
    $categories[] = ['id' => $i + 1, 'name' => $name, 'slug' => App\Core\Slug::make($name)];
}
preg_match_all('/INSERT INTO articles .*? VALUES \((.*)\);/u', file_get_contents(ROOT . '/database/seed.sql'), $matches);
$items = [];
foreach (array_slice($matches[1], 0, 9) as $row) {
    $values = str_getcsv($row, ',', "'", '\\');
    $category = $categories[(int) $values[2] - 1];
    $items[] = ['id' => $values[0], 'author_id' => $values[1], 'category_id' => $values[2], 'title' => $values[3], 'slug' => $values[4], 'content' => $values[5], 'excerpt' => $values[6], 'image_path' => $values[7], 'image_alt' => $values[8], 'status' => $values[9], 'featured' => $values[10], 'views' => $values[11], 'published_at' => '2026-10-13 12:00:00', 'updated_at' => '2026-10-13 12:00:00.000000', 'deleted_at' => null, 'author' => 'Ana Torres', 'category' => $category['name'], 'category_slug' => $category['slug']];
}
if (!is_dir(ROOT . '/work/layout')) {
    mkdir(ROOT . '/work/layout', 0775, true);
}
foreach (['home', 'article', 'editor', 'table'] as $page) {
    $title = 'Vista de presentación';
    $admin = in_array($page, ['editor', 'table'], true);
    $user = $admin ? ['id' => 1, 'name' => 'Administración', 'role' => 'administrador'] : null;
    $result = ['items' => $items, 'total' => 60, 'page' => 1, 'pages' => 7];
    $errors = [];
    $data = $page === 'editor' ? $items[0] : [];
    $id = '1';
    $tags = [['id' => 1, 'name' => 'Comunidad', 'slug' => 'comunidad']];
    $selectedTags = [1];
    $filters = ['q' => '', 'status' => '', 'trash' => false];
    $related = array_slice($items, 1, 3);
    $comments = [];
    $featured = $items[0];
    $popular = array_slice($items, 1, 4);
    unset($article);
    if ($page === 'article') {
        $article = $items[0];
    }
    ob_start();
    require ROOT . '/app/Views/' . match ($page) { 'home' => 'portal/home.php', 'article' => 'portal/article.php', 'editor' => 'admin/article-form.php', 'table' => 'admin/articles.php' };
    $body = ob_get_clean();
    ob_start();
    require ROOT . '/app/Views/layouts/main.php';
    file_put_contents(ROOT . '/work/layout/' . $page . '.html', ob_get_clean());
}
echo "Plantillas reales renderizadas con datos de prueba para revisión de presentación.\n";
