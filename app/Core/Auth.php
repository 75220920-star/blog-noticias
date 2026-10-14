<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function hash(string $password): string
    {
        // Argon2id permite 128 caracteres sin el truncamiento de bcrypt.
        return password_hash($password, PASSWORD_ARGON2ID, ['memory_cost' => 32768, 'time_cost' => 3, 'threads' => 1]);
    }

    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        if (time() - ($_SESSION['last_active'] ?? 0) >= $GLOBALS['config']['session_seconds']) {
            self::logout();
            return null;
        }
        $user = DB::query('SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=? AND u.active=1 AND u.deleted_at IS NULL AND u.email_verified_at IS NOT NULL', [$_SESSION['user_id']])->fetch();
        if (!$user) {
            self::logout();
            return null;
        }
        return $user;
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = DB::query('SELECT * FROM users WHERE email=? AND deleted_at IS NULL', [$email])->fetch();
        $dummy = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';
        $valid = password_verify($password, $user['password_hash'] ?? $dummy);
        if (!$user || ($user['locked_until'] && strtotime($user['locked_until']) > time())) {
            return false;
        }
        if (!$valid || !$user['active'] || !$user['email_verified_at']) {
            DB::query('UPDATE users SET failed_attempts=failed_attempts+1, locked_until=IF(failed_attempts>=5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), locked_until) WHERE id=?', [$user['id']]);
            return false;
        }
        DB::query('UPDATE users SET failed_attempts=0, locked_until=NULL WHERE id=?', [$user['id']]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['last_active'] = time();
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        DB::audit('inicio_sesion', 'users', (int) $user['id']);
        return true;
    }

    public static function canManage(array $user, array $article): bool
    {
        return $user['role'] === 'administrador' || ($user['role'] === 'editor' && (int) $article['author_id'] === (int) $user['id']);
    }

    public static function allowed(string $role, array $roles): bool
    {
        return in_array($role, $roles, true);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
