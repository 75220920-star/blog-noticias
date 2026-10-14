<?php

declare(strict_types=1);

// El bootstrap explícito evita falsos negativos de is_readable bajo tokens
// restringidos de Windows; las aserciones siguen ejecutándose con PHPUnit.
require dirname(__DIR__) . '/app/Core/bootstrap.php';
require ROOT . '/vendor/autoload.php';
$arguments = ['phpunit', '--no-configuration', '--colors=always', '--testdox', ROOT . '/tests/unit'];
exit((new PHPUnit\TextUI\Application())->run($arguments));
