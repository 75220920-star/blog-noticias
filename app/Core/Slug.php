<?php

declare(strict_types=1);

namespace App\Core;

final class Slug
{
    public static function make(string $title): string
    {
        // iconv translitera de forma distinta en Windows y Linux: normalizamos español.
        $title = strtr($title, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n', 'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N']);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
        return substr(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($ascii ?: 'noticia')), '-'), 0, 140) ?: 'noticia';
    }

    public static function unique(string $title, callable $exists): string
    {
        $base = self::make($title);
        $slug = $base;
        for ($suffix = 2; $exists($slug); $suffix++) {
            $slug = $base . '-' . $suffix;
        }
        return $slug;
    }
}
