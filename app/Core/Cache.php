<?php

declare(strict_types=1);

namespace App\Core;

final class Cache
{
    public static function remember(string $key, callable $loader, int $ttl = 30): array
    {
        $file = ROOT . '/storage/cache/' . hash('sha256', $key) . '.json';
        if (is_file($file) && filemtime($file) + $ttl > time()) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                return $data;
            }
        }
        $data = $loader();
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
        return $data;
    }

    public static function clear(): void
    {
        foreach (glob(ROOT . '/storage/cache/*.json') ?: [] as $file) {
            unlink($file);
        }
    }
}
