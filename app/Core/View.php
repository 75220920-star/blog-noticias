<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);
        $user = Auth::user();
        $settings = \App\Models\Catalog::settings();
        $categories = \App\Models\Catalog::all('categories');
        $title ??= $settings['site_name'] ?? 'Pulso · Noticias';
        $admin ??= str_starts_with($template, 'admin/');
        ob_start();
        require ROOT . '/app/Views/' . $template . '.php';
        $body = ob_get_clean();
        require ROOT . '/app/Views/layouts/main.php';
    }

    public static function error(int $status, string $message): void
    {
        self::render('portal/error', ['title' => $status === 404 ? 'Página no encontrada' : 'Acceso no permitido', 'message' => $message], $status);
    }
}
