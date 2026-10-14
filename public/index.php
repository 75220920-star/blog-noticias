<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\PortalController;
use App\Core\Auth;
use App\Core\Router;
use App\Core\View;

$router = new Router();
$portal = new PortalController();
$account = new AuthController();
$admin = new AdminController();
$staff = ['administrador', 'editor'];
$administrator = ['administrador'];
$router->add('GET', '/', [$portal, 'home']);
$router->add('GET', '/noticias', fn () => $portal->listing());
$router->add('GET', '/buscar', fn () => $portal->listing('buscar'));
$router->add('GET', '/categoria/{slug}', fn ($slug) => $portal->listing('categoria', $slug));
$router->add('GET', '/etiqueta/{slug}', fn ($slug) => $portal->listing('etiqueta', $slug));
$router->add('GET', '/noticia/{slug}', [$portal, 'article']);
$router->add('POST', '/noticia/{slug}/comentar', [$portal, 'comment'], ['lector']);
$router->add('GET', '/rss.xml', [$portal, 'rss']);
$router->add('GET', '/accesibilidad', fn () => View::render('portal/accessibility', ['title' => 'Declaración de accesibilidad']));
$router->add('POST', '/boletin', [$portal, 'subscribe']);
$router->add('GET', '/boletin/confirmar/{token}', [$portal, 'confirmSubscription']);
$router->add('POST', '/boletin/confirmar/{token}', [$portal, 'confirmSubscription']);
$router->add('POST', '/cookies', [$portal, 'cookies']);
foreach (['login', 'registro', 'recuperar'] as $mode) {
    $router->add('GET', '/' . $mode, fn () => $account->form($mode));
}
$router->add('POST', '/login', [$account, 'login']);
$router->add('POST', '/registro', [$account, 'register']);
$router->add('POST', '/recuperar', [$account, 'recover']);
$router->add('GET', '/restablecer/{token}', fn ($token) => $account->form('restablecer', [], [], $token));
$router->add('POST', '/restablecer/{token}', [$account, 'reset']);
$router->add('GET', '/confirmar/{token}', [$account, 'confirm']);
$router->add('POST', '/confirmar/{token}', [$account, 'confirm']);
$router->add('POST', '/logout', function (): void {
    Auth::logout();
    flash('Sesión cerrada.');
    redirect('login');
});
$router->add('POST', '/sesion/extender', function (): void {
    $_SESSION['last_active'] = time();
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['extended' => true, 'seconds' => $GLOBALS['config']['session_seconds']]);
        return;
    }
    flash('Sesión extendida durante 30 minutos. Tu borrador local se puede recuperar en el editor.');
    redirect(Auth::user()['role'] === 'lector' ? '' : 'admin/noticias');
}, ['lector', 'editor', 'administrador']);
$router->add('GET', '/admin', [$admin, 'dashboard'], $staff);
$router->add('GET', '/admin/noticias', [$admin, 'articles'], $staff);
$router->add('GET', '/admin/noticias/nueva', fn () => $admin->articleForm(), $staff);
$router->add('POST', '/admin/noticias/nueva', fn () => $admin->saveArticle(), $staff);
$router->add('GET', '/admin/noticias/{id}/editar', [$admin, 'articleForm'], $staff);
$router->add('POST', '/admin/noticias/{id}/editar', [$admin, 'saveArticle'], $staff);
foreach (['eliminar', 'restaurar'] as $action) {
    $router->add('POST', '/admin/noticias/{id}/' . $action, fn ($id) => $admin->articleAction($id, $action), $staff);
    $router->add('POST', '/admin/usuarios/{id}/' . $action, fn ($id) => $admin->userAction($id, $action), $administrator);
}
foreach (['categorias', 'etiquetas'] as $kind) {
    $router->add('GET', '/admin/' . $kind, fn () => $admin->catalog($kind), $administrator);
    $router->add('POST', '/admin/' . $kind, fn () => $admin->saveCatalog($kind), $administrator);
    $router->add('POST', '/admin/' . $kind . '/{id}/eliminar', fn ($id) => $admin->deleteCatalog($kind, $id), $administrator);
}
$router->add('GET', '/admin/usuarios', [$admin, 'users'], $administrator);
$router->add('GET', '/admin/usuarios/nuevo', fn () => $admin->userForm(), $administrator);
$router->add('POST', '/admin/usuarios/nuevo', fn () => $admin->saveUser(), $administrator);
$router->add('GET', '/admin/usuarios/{id}/editar', [$admin, 'userForm'], $administrator);
$router->add('POST', '/admin/usuarios/{id}/editar', [$admin, 'saveUser'], $administrator);
$router->add('GET', '/admin/comentarios', [$admin, 'comments'], $administrator);
$router->add('POST', '/admin/comentarios/{id}', [$admin, 'moderate'], $administrator);
$router->add('GET', '/admin/configuracion', [$admin, 'settings'], $administrator);
$router->add('POST', '/admin/configuracion', [$admin, 'saveSettings'], $administrator);
$router->add('GET', '/admin/auditoria', [$admin, 'audit'], $administrator);

try {
    $router->dispatch();
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    // Esta respuesta no consulta la BD: también funciona cuando MySQL falla.
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Servicio temporalmente no disponible</title><link rel="stylesheet" href="' . e(url('assets/app.min.css')) . '"></head><body><a class="skip-link" href="#contenido">Saltar al contenido</a><main class="container prose" id="contenido"><h1>Estamos recuperando el servicio</h1><p>No se pudo completar la solicitud. Espera un momento y vuelve a intentarlo.</p><a class="button" href="' . e(url()) . '">Volver a intentar</a>';
    if ($GLOBALS['config']['debug']) {
        echo '<pre>' . e($exception->getMessage()) . '</pre>';
    }
    echo '</main></body></html>';
}
