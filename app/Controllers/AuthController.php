<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Validator;
use App\Core\View;

final class AuthController
{
    public static function localMail(string $email, string $subject, string $path): void
    {
        // Transporte local explícito; este archivo privado contiene enlaces de un solo uso.
        $message = json_encode(['date' => date(DATE_ATOM), 'to' => $email, 'subject' => $subject, 'url' => $GLOBALS['config']['url'] . '/' . $path], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents(ROOT . '/storage/logs/mail.log', $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function form(string $mode, array $errors = [], array $data = [], string $token = ''): void
    {
        $titles = ['login' => 'Iniciar sesión', 'registro' => 'Crear cuenta de lector', 'recuperar' => 'Recuperar contraseña', 'restablecer' => 'Crear nueva contraseña', 'confirmar' => 'Confirmar cuenta'];
        View::render('auth/form', compact('mode', 'errors', 'data', 'token') + ['title' => $titles[$mode]]);
    }

    public function login(): void
    {
        $errors = [];
        if (!Validator::email($_POST['email'] ?? '')) {
            $errors['email'] = 'Escribe un correo electrónico válido.';
        }
        if (!Validator::text($_POST['password'] ?? '', 1, 128)) {
            $errors['password'] = 'Escribe tu contraseña.';
        }
        if (!$errors && !Auth::attempt($_POST['email'], $_POST['password'])) {
            $errors['password'] = 'Credenciales inválidas. Revisa tus datos o espera 15 minutos si hubo varios intentos.';
        }
        if ($errors) {
            $this->form('login', $errors, $_POST);
            return;
        }
        flash('Sesión iniciada correctamente.');
        redirect(Auth::user()['role'] === 'lector' ? '' : 'admin');
    }

    public function register(): void
    {
        $errors = [];
        if (!Validator::text($_POST['name'] ?? '', 2, 100)) {
            $errors['name'] = 'Escribe tu nombre entre 2 y 100 caracteres.';
        }
        if (!Validator::email($_POST['email'] ?? '')) {
            $errors['email'] = 'Escribe un correo válido.';
        } elseif (DB::query('SELECT id FROM users WHERE email=?', [$_POST['email']])->fetch()) {
            $errors['email'] = 'Este correo ya está registrado. Inicia sesión o recupera tu contraseña.';
        }
        if (!Validator::password($_POST['password'] ?? '')) {
            $errors['password'] = 'Usa entre 8 y 128 caracteres, al menos una mayúscula y un número.';
        }
        if ($errors) {
            $this->form('registro', $errors, $_POST);
            return;
        }
        $token = bin2hex(random_bytes(32));
        // El rol enviado por el navegador nunca se utiliza en el registro público.
        DB::query('INSERT INTO users (role_id,name,email,password_hash,verification_hash,verification_expires) VALUES (3,?,?,?,?,DATE_ADD(NOW(),INTERVAL 1 DAY))', [trim($_POST['name']), $_POST['email'], Auth::hash($_POST['password']), hash('sha256', $token)]);
        self::localMail($_POST['email'], 'Confirma tu cuenta de lector', 'confirmar/' . $token);
        flash('Cuenta creada. Confirma tu correo antes de iniciar sesión; en local consulta storage/logs/mail.log.');
        redirect('login');
    }

    public function confirm(string $token): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->form('confirmar', [], [], $token);
            return;
        }
        $updated = DB::query('UPDATE users SET email_verified_at=NOW(),verification_hash=NULL WHERE verification_hash=? AND verification_expires>NOW() AND email_verified_at IS NULL', [hash('sha256', $token)])->rowCount();
        flash($updated ? 'Correo confirmado. Ya puedes iniciar sesión.' : 'El enlace ya se usó o caducó. Solicita ayuda al administrador.');
        redirect('login');
    }

    public function recover(): void
    {
        if (!Validator::email($_POST['email'] ?? '')) {
            $this->form('recuperar', ['email' => 'Escribe un correo válido.'], $_POST);
            return;
        }
        // Evita generar enlaces en cada pulsación y no revela si existe la cuenta.
        $user = DB::query('SELECT id FROM users WHERE email=? AND active=1 AND deleted_at IS NULL', [$_POST['email']])->fetch();
        if ($user && time() - ($_SESSION['recovery_at'] ?? 0) >= 60) {
            $token = bin2hex(random_bytes(32));
            DB::query('UPDATE password_resets SET used_at=NOW() WHERE user_id=? AND used_at IS NULL', [$user['id']]);
            DB::query('INSERT INTO password_resets (user_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL 30 MINUTE))', [$user['id'], hash('sha256', $token)]);
            self::localMail($_POST['email'], 'Restablece tu contraseña', 'restablecer/' . $token);
            $_SESSION['recovery_at'] = time();
        }
        flash('Si el correo está registrado, recibirás un enlace válido durante 30 minutos. En local consulta storage/logs/mail.log.');
        redirect('login');
    }

    public function reset(string $token): void
    {
        if (!Validator::password($_POST['password'] ?? '')) {
            $this->form('restablecer', ['password' => 'Usa entre 8 y 128 caracteres, una mayúscula y un número.'], [], $token);
            return;
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        $reset = DB::query('SELECT * FROM password_resets WHERE token_hash=? AND used_at IS NULL AND expires_at>NOW() FOR UPDATE', [hash('sha256', $token)])->fetch();
        if (!$reset) {
            $pdo->rollBack();
            $this->form('restablecer', ['password' => 'Este enlace caducó o ya se usó. Solicita uno nuevo.'], [], $token);
            return;
        }
        DB::query('UPDATE users SET password_hash=?, failed_attempts=0,locked_until=NULL WHERE id=?', [Auth::hash($_POST['password']), $reset['user_id']]);
        DB::query('UPDATE password_resets SET used_at=NOW() WHERE user_id=? AND used_at IS NULL', [$reset['user_id']]);
        DB::audit('restablecer_clave', 'users', (int) $reset['user_id']);
        $pdo->commit();
        Auth::logout();
        flash('Contraseña actualizada. Inicia sesión con tu nueva contraseña.');
        redirect('login');
    }
}
