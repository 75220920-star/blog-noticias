<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/Core/bootstrap.php';

if (!preg_match('/dbname=blog_noticias_test(?:;|$)/', $GLOBALS['config']['db']['dsn'])) {
    exit("Solo se permite ampliar blog_noticias_test.\n");
}
$target = filter_var($argv[1] ?? 500, FILTER_VALIDATE_INT, ['options' => ['min_range' => 60, 'max_range' => 100000]]);
if (!$target) {
    exit("Usa un volumen de 60 a 100000 noticias.\n");
}
$pdo = App\Core\DB::connection();
for ($i = 9; $i <= 20; $i++) {
    App\Core\DB::query('INSERT IGNORE INTO categories (id,name,slug) VALUES (?,?,?)', [$i, 'Sección de carga ' . $i, 'seccion-carga-' . $i]);
}
for ($i = 9; $i <= 50; $i++) {
    App\Core\DB::query('INSERT IGNORE INTO users (id,role_id,name,email,password_hash,email_verified_at) SELECT ?,3,?,?,password_hash,NOW() FROM users WHERE id=1', [$i, 'Lector de carga ' . $i, 'carga' . $i . '@example.test']);
}
$current = (int) App\Core\DB::query('SELECT COUNT(*) FROM articles')->fetchColumn();
for ($i = $current + 1; $i <= $target; $i++) {
    App\Core\DB::query("INSERT INTO articles (author_id,category_id,title,slug,content,excerpt,image_path,image_alt,status,published_at) SELECT author_id,?,CONCAT(title,' · carga ',?),?,content,excerpt,image_path,image_alt,'publicado',published_at FROM articles WHERE id=1", [($i % 20) + 1, $i, 'noticia-carga-' . $i]);
}
$comments = (int) App\Core\DB::query('SELECT COUNT(*) FROM comments')->fetchColumn();
for ($i = $comments + 1; $i <= 2000; $i++) {
    App\Core\DB::query("INSERT INTO comments (article_id,user_id,content,status) VALUES (?,?,'Comentario de carga para evaluación de capacidad.','aprobado')", [($i % $target) + 1, ($i % 48) + 3]);
}
App\Core\Cache::clear();
echo "Conjunto de carga: $target noticias, 20 categorías, 50 usuarios y al menos 2000 comentarios.\n";
