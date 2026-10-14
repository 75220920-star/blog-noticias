<div class="auth-card"><p class="eyebrow">TU CUENTA EN PULSO</p><h1><?= e($title) ?></h1><?php errorSummary($errors ?? []); ?>
<?php $action = match ($mode) { 'restablecer' => 'restablecer/' . $token, 'confirmar' => 'confirmar/' . $token, 'subscribe' => 'boletin', 'confirm-subscribe' => 'boletin/confirmar/' . $token, default => $mode }; ?>
<form method="post" action="<?= e(url($action)) ?>"><?= csrf() ?>
<?php if ($mode === 'registro'): ?><?php input('name', $data, 'Nombre', 'text', true, 'name'); ?><?php endif; ?>
<?php if (in_array($mode, ['login', 'registro', 'recuperar', 'subscribe'], true)): ?><?php input('email', $data ?? [], 'Correo electrónico', 'email', true, 'email'); ?><?php endif; ?>
<?php if (in_array($mode, ['login', 'registro', 'restablecer'], true)): ?><?php input('password', $data ?? [], 'Contraseña', 'password', true, $mode === 'login' ? 'current-password' : 'new-password'); ?><?php if ($mode !== 'login'): ?><p class="muted">Entre 8 y 128 caracteres, una mayúscula y un número. Permite pegar y utilizar un gestor de contraseñas.</p><?php endif; ?><?php endif; ?>
<?php if (in_array($mode, ['confirmar', 'confirm-subscribe'], true)): ?><p>Confirma para completar el proceso. El enlace caduca en 24 horas y solo puede usarse una vez.</p><?php endif; ?>
<button class="button" type="submit"><?= e(match ($mode) { 'login' => 'Iniciar sesión', 'registro' => 'Crear cuenta', 'recuperar' => 'Solicitar enlace', 'restablecer' => 'Guardar contraseña', 'subscribe' => 'Suscribirme', default => 'Confirmar' }) ?></button></form>
<nav class="auth-links" aria-label="Opciones de cuenta"><a href="<?= e(url('login')) ?>">Iniciar sesión</a><a href="<?= e(url('registro')) ?>">Crear cuenta</a><a href="<?= e(url('recuperar')) ?>">Olvidé mi contraseña</a></nav></div>
