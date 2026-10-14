<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file(dirname(__DIR__, 2) . '/public' . $path)) {
    return false;
}
require dirname(__DIR__, 2) . '/public/index.php';
