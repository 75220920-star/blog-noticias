<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function text(mixed $value, int $min, int $max): bool
    {
        return is_string($value) && mb_strlen(trim($value)) >= $min && mb_strlen(trim($value)) <= $max;
    }

    public static function email(mixed $email): bool
    {
        return is_string($email) && mb_strlen($email) <= 190 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function password(mixed $password): bool
    {
        return is_string($password) && mb_strlen($password) >= 8 && mb_strlen($password) <= 128
            && preg_match('/[A-Z]/', $password) && preg_match('/[0-9]/', $password);
    }

    public static function page(mixed $page, int $pages): int
    {
        $number = filter_var($page, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return min($number ?: 1, max(1, $pages));
    }

    public static function id(mixed $id): int
    {
        return (int) (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0);
    }
}
