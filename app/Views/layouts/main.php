<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#174bdb">
    <title><?= e($title) ?> · <?= e($settings['site_name'] ?? 'Pulso') ?></title>
    <meta name="description" content="<?= e($article['excerpt'] ?? $settings['seo_description'] ?? 'Periodismo que conecta con tu mundo.') ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($article['excerpt'] ?? $settings['seo_description'] ?? '') ?>">
    <meta property="og:type" content="<?= isset($article) ? 'article' : 'website' ?>">
    <meta property="og:url" content="<?= e($GLOBALS['config']['url'] . (isset($article) ? '/noticia/' . $article['slug'] : '')) ?>">
    <meta property="og:image" content="<?= e($GLOBALS['config']['url'] . '/' . ($article['image_path'] ?? 'assets/news-1.webp')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/app.min.css')) ?>">
    <link rel="icon" href="<?= e(url('assets/icon.svg')) ?>" type="image/svg+xml">
    <link rel="manifest" href="<?= e(url('manifest.webmanifest')) ?>">
    <link rel="apple-touch-icon" href="<?= e(url('assets/icon-192.png')) ?>">
    <link rel="alternate" type="application/rss+xml" title="Noticias de Pulso" href="<?= e(url('rss.xml')) ?>">
    <script src="<?= e(url('assets/app.min.js')) ?>" defer></script>
</head>
<body<?= $user ? ' data-session-seconds="' . e($GLOBALS['config']['session_seconds']) . '"' : '' ?>>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="site-header">
    <div class="topline"><div class="container">Periodismo para entender tu mundo <span>Edición local · Perú</span></div></div>
    <div class="container masthead">
        <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e($settings['site_name'] ?? 'Pulso') ?> · Portada">
            <?php if (!empty($settings['logo'])): ?><img src="<?= e(url($settings['logo'])) ?>" width="160" height="64" alt="<?= e($settings['site_name']) ?>"><?php else: ?>
            <svg viewBox="0 0 40 40" width="40" height="40" aria-hidden="true"><path fill="currentColor" d="M0 0h40v40H0z"/><path fill="none" stroke="white" stroke-width="3" d="M5 22h7l4-10 7 19 4-9h8"/></svg><span><?= e($settings['site_name'] ?? 'Pulso') ?><small>La actualidad, con perspectiva</small></span><?php endif; ?>
        </a>
        <div class="header-actions"><button class="button button--quiet js-only" type="button" id="theme-toggle" aria-pressed="false">Modo oscuro</button>
        <?php if ($user): ?><?php if ($user['role'] !== 'lector'): ?><a class="button button--quiet" href="<?= e(url('admin')) ?>">Panel</a><?php endif; ?><form method="post" action="<?= e(url('logout')) ?>"><?= csrf() ?><button class="button button--quiet">Cerrar sesión</button></form><?php else: ?><a class="button button--quiet" href="<?= e(url('login')) ?>">Iniciar sesión</a><?php endif; ?></div>
    </div>
    <div class="navline"><div class="container navigation">
        <button class="button menu-toggle js-only" id="menu-toggle" aria-expanded="true" aria-controls="main-menu" type="button">Menú</button>
        <nav id="main-menu" aria-label="Navegación principal"><a href="<?= e(url()) ?>">Portada</a><?php foreach ($categories as $category): ?><a href="<?= e(url('categoria/' . $category['slug'])) ?>"><?= e($category['name']) ?></a><?php endforeach; ?></nav>
        <form class="search" action="<?= e(url('buscar')) ?>" method="get" role="search"><label class="sr-only" for="site-search">Buscar noticias</label><input id="site-search" type="search" name="q" placeholder="Buscar noticias" value="<?= e($q ?? '') ?>" maxlength="150" enterkeyhint="search"><button aria-label="Buscar noticias" type="submit"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/></svg></button></form>
    </div></div>
</header>
<div class="container <?= $admin ? 'admin-shell' : '' ?>">
<?php if ($admin): ?>
<aside class="admin-sidebar"><button class="button button--quiet js-only" id="admin-toggle" aria-expanded="true" aria-controls="admin-menu">Secciones del panel</button><nav id="admin-menu" aria-label="Administración"><a href="<?= e(url('admin')) ?>">Resumen</a><a href="<?= e(url('admin/noticias')) ?>">Mis noticias</a><a href="<?= e(url('admin/noticias/nueva')) ?>">Crear noticia</a><?php if ($user['role'] === 'administrador'): ?><?php foreach (['categorias' => 'Categorías', 'etiquetas' => 'Etiquetas', 'usuarios' => 'Usuarios', 'comentarios' => 'Comentarios', 'configuracion' => 'Configuración', 'auditoria' => 'Auditoría'] as $route => $label): ?><a href="<?= e(url('admin/' . $route)) ?>"><?= e($label) ?></a><?php endforeach; ?><?php endif; ?></nav><p class="muted"><?= e($user['name']) ?><br><?= e(ucfirst($user['role'])) ?></p></aside>
<?php endif; ?>
<main id="contenido" tabindex="-1">
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert" role="status"><?= e($_SESSION['flash']) ?></div><?php unset($_SESSION['flash']); endif; ?>
    <?php if ($admin): ?><nav class="breadcrumbs" aria-label="Migas de pan"><a href="<?= e(url()) ?>">Portada</a><span>/</span><a href="<?= e(url('admin')) ?>">Panel</a><span>/</span><span><?= e($title) ?></span></nav><?php endif; ?>
    <?= $body ?>
</main></div>
<footer class="site-footer"><div class="container footer-grid"><div><strong><?= e($settings['site_name'] ?? 'Pulso') ?></strong><p>Información que importa.<br>Una mirada abierta a tu mundo.</p></div><nav aria-label="Enlaces del pie"><a href="<?= e(url('accesibilidad')) ?>">Declaración de accesibilidad</a><a href="<?= e(url('rss.xml')) ?>">Feed RSS</a><a href="<?= e(url('registro')) ?>">Crear cuenta</a><a href="mailto:<?= e($settings['contact_email'] ?? 'contacto@example.test') ?>">Contacto y ayuda</a><?php foreach (['social_facebook' => 'Facebook', 'social_x' => 'X'] as $key => $label): ?><?php if (!empty($settings[$key])): ?><a href="<?= e($settings[$key]) ?>" rel="noopener noreferrer"><?= e($label) ?></a><?php endif; ?><?php endforeach; ?></nav><p class="muted">© <?= date('Y') ?> Pulso.<br>Datos e ilustraciones de demostración.</p></div></footer>
<?php if (!isset($_COOKIE['cookie_choice'])): ?><aside class="cookies container" aria-label="Preferencias de cookies"><p>Usamos cookies necesarias para la sesión y la seguridad. No incluimos rastreadores. Puedes guardar tu preferencia.</p><form method="post" action="<?= e(url('cookies')) ?>"><?= csrf() ?><button class="button" name="choice" value="aceptar">Aceptar</button><button class="button button--quiet" name="choice" value="rechazar">Rechazar opcionales</button></form></aside><?php endif; ?>
<?php if ($user): ?><dialog id="session-dialog" aria-labelledby="session-title"><h2 id="session-title">Tu sesión está por caducar</h2><p>Tienes al menos 60 segundos para extenderla. Guarda tus cambios antes de salir.</p><form method="post" action="<?= e(url('sesion/extender')) ?>"><?= csrf() ?><button class="button" type="submit">Extender sesión</button><button class="button button--quiet" type="button" data-close-dialog>Volver al formulario</button></form><p id="session-status" role="status"></p></dialog><?php endif; ?>
<dialog id="confirm-dialog" aria-labelledby="confirm-title"><h2 id="confirm-title">Confirmar eliminación</h2><p>Esta acción cambia los datos. Las noticias y los usuarios pueden restaurarse desde la papelera.</p><div class="actions"><button class="button button--danger" type="button" id="confirm-submit">Confirmar eliminación</button><button class="button button--quiet" type="button" data-close-dialog>Cancelar</button></div></dialog>
</body>
</html>
