<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/Core/bootstrap.php';

if (!preg_match('/dbname=blog_noticias_test(?:;|$)/', $GLOBALS['config']['db']['dsn'])) {
    fwrite(STDERR, "Las pruebas solo pueden modificar blog_noticias_test.\n");
    exit(1);
}
$action = $argv[1] ?? '';
if ($action === 'reset') {
    $config = $GLOBALS['config']['db'];
    $dsn = preg_replace('/;dbname=[^;]+/', '', $config['dsn']);
    $pdo = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('DROP DATABASE IF EXISTS blog_noticias_test');
    $schema = str_replace('blog_noticias', 'blog_noticias_test', file_get_contents(ROOT . '/database/schema.sql'));
    $pdo->exec($schema);
    $pdo->exec("SET time_zone = '-05:00'");
    $pdo->exec(str_replace('USE blog_noticias;', 'USE blog_noticias_test;', file_get_contents(ROOT . '/database/seed.sql')));
    App\Core\Cache::clear();
    echo "Base de pruebas reiniciada.\n";
} elseif ($action === 'query') {
    $sql = base64_decode($argv[2] ?? '', true);
    $params = json_decode(base64_decode($argv[3] ?? 'W10='), true);
    echo json_encode(App\Core\DB::query($sql, $params)->fetchAll(), JSON_UNESCAPED_UNICODE);
} elseif ($action === 'mail') {
    $lines = file(ROOT . '/storage/logs/mail.log', FILE_IGNORE_NEW_LINES) ?: [];
    $found = [];
    foreach ($lines as $line) {
        $mail = json_decode($line, true);
        if (($mail['to'] ?? '') === ($argv[2] ?? '')) {
            $found = $mail;
        }
    }
    echo json_encode($found);
}
