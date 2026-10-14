<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class Catalog
{
    public static function all(string $table): array
    {
        if (!in_array($table, ['categories', 'tags', 'roles'], true)) {
            throw new \InvalidArgumentException('Catálogo no permitido.');
        }
        return DB::query('SELECT * FROM ' . $table . ' ORDER BY name')->fetchAll();
    }

    public static function settings(): array
    {
        return DB::query('SELECT `key`, value FROM settings')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
