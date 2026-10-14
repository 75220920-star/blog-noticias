<?php

declare(strict_types=1);

// Reiniciar antes de que Playwright compruebe disponibilidad de /login.
$argv[1] = 'reset';
require __DIR__ . '/db.php';
passthru(escapeshellarg(PHP_BINARY) . ' -d extension=gd -d upload_max_filesize=5M -d post_max_size=8M -S 127.0.0.1:8088 -t public tests/support/router.php', $exitCode);
exit($exitCode);
