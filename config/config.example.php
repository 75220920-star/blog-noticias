<?php

declare(strict_types=1);

return [
    'name' => 'Pulso · Noticias',
    'base_path' => getenv('BLOG_BASE_PATH') !== false ? getenv('BLOG_BASE_PATH') : '/blog-noticias/public',
    'url' => getenv('BLOG_URL') ?: 'http://localhost/blog-noticias/public',
    'debug' => getenv('BLOG_DEBUG') === '1',
    'timezone' => 'America/Lima',
    'session_seconds' => 1800,
    'db' => [
        'dsn' => getenv('BLOG_DB_DSN') ?: 'mysql:host=127.0.0.1;port=3306;dbname=blog_noticias;charset=utf8mb4',
        'user' => getenv('BLOG_DB_USER') !== false ? getenv('BLOG_DB_USER') : 'root',
        'password' => getenv('BLOG_DB_PASSWORD') ?: '',
    ],
];
