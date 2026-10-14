<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler, array $roles = []): void
    {
        $this->routes[] = [$method, $path, $handler, $roles];
    }

    public function dispatch(): void
    {
        $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
        $base = rtrim($GLOBALS['config']['base_path'], '/');
        if ($base && !str_starts_with($path, $base . '/') && $path !== $base) {
            View::error(404, 'La dirección no existe. Vuelve a la portada o usa el buscador.');
            return;
        }
        $path = '/' . trim(substr($path, strlen($base)), '/');
        foreach ($this->routes as [$method, $pattern, $handler, $roles]) {
            $parts = preg_split('/(\{[a-z]+\})/', $pattern, -1, PREG_SPLIT_DELIM_CAPTURE);
            $regex = implode('', array_map(
                fn ($part) => preg_match('/^\{[a-z]+\}$/', $part) ? '([^/]+)' : preg_quote($part, '~'),
                $parts
            ));
            if ($_SERVER['REQUEST_METHOD'] !== $method || !preg_match('~^' . $regex . '$~D', $path, $matches)) {
                continue;
            }
            $user = Auth::user();
            if ($roles && !$user) {
                \flash('Inicia sesión para continuar.');
                \redirect('login');
            }
            if ($roles && !Auth::allowed($user['role'], $roles)) {
                View::error(403, 'Tu cuenta no tiene permiso para esta acción.');
                return;
            }
            if ($method === 'POST' && !Csrf::valid($_POST['csrf'] ?? null)) {
                View::error(403, 'El formulario caducó. Recarga la página y vuelve a enviarlo.');
                return;
            }
            // Las visitas humanas renuevan la sesión; no existe sondeo periódico.
            if ($user) {
                $_SESSION['last_active'] = time();
            }
            array_shift($matches);
            $handler(...$matches);
            return;
        }
        View::error(404, 'La dirección no existe. Vuelve a la portada o usa el buscador.');
    }
}
