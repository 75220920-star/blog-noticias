<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/bootstrap.php';

// Ejecutar cada minuto con el Programador de tareas o cron. La consulta pública
// comprueba la fecha directamente, por lo que no depende de que el cron se ejecute.
App\Core\Cache::clear();
$count = App\Core\DB::query("SELECT COUNT(*) FROM articles WHERE status='publicado' AND published_at<=NOW() AND deleted_at IS NULL")->fetchColumn();
echo "Caché invalidada. Noticias publicadas: $count\n";
