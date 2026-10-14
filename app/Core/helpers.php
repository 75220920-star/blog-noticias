<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return rtrim($GLOBALS['config']['base_path'], '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit;
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

function csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(App\Core\Csrf::token()) . '">';
}

function input(string $name, array $data, string $label, string $type = 'text', bool $required = true, string $autocomplete = 'off'): void
{
    $errors = $GLOBALS['form_errors'] ?? [];
    $error = $errors[$name] ?? '';
    $value = $type === 'password' ? '' : (string) ($data[$name] ?? '');
    echo '<div class="field"><label for="' . e($name) . '">' . e($label) . ($required ? ' (obligatorio)' : ' (opcional)') . '</label>';
    echo '<input id="' . e($name) . '" name="' . e($name) . '" type="' . e($type) . '" value="' . e($value) . '" autocomplete="' . e($autocomplete) . '" enterkeyhint="next"';
    echo $required ? ' required' : '';
    echo $type === 'email' ? ' inputmode="email"' : '';
    echo isset(['title' => 150, 'image_alt' => 255, 'name' => 100, 'email' => 190, 'site_name' => 60, 'seo_description' => 160][$name])
        ? ' maxlength="' . ['title' => 150, 'image_alt' => 255, 'name' => 100, 'email' => 190, 'site_name' => 60, 'seo_description' => 160][$name] . '"' : '';
    echo $error ? ' aria-invalid="true" aria-describedby="error-' . e($name) . '"' : '';
    echo '>';
    if ($error) {
        echo '<p class="field-error" id="error-' . e($name) . '">' . e($error) . '</p>';
    }
    echo '</div>';
}

function errorSummary(array $errors): void
{
    $GLOBALS['form_errors'] = $errors;
    if (!$errors) {
        return;
    }
    echo '<div class="alert alert--error" id="errors" tabindex="-1" role="alert"><h2>Revisa estos datos antes de continuar</h2><ul>';
    foreach ($errors as $field => $message) {
        echo '<li><a href="#' . e($field) . '">' . e($message) . '</a></li>';
    }
    echo '</ul></div>';
}

function pagination(array $result, string $path, array $params = []): void
{
    if ($result['pages'] < 2) {
        return;
    }
    echo '<nav class="pagination" aria-label="Paginación">';
    for ($page = max(1, $result['page'] - 2); $page <= min($result['pages'], $result['page'] + 2); $page++) {
        $params['page'] = $page;
        echo '<a class="button button--quiet" href="' . e(url($path) . '?' . http_build_query($params)) . '"' . ($page === $result['page'] ? ' aria-current="page"' : '') . '> <span class="sr-only">Página </span>' . $page . '</a>';
    }
    echo '</nav>';
}

function articleImage(array $article, bool $priority = false): void
{
    $path = $article['image_path'] ?: 'assets/news-1.webp';
    $small = preg_replace('/\.webp$/', '-480.webp', $path);
    $saveData = ($_SERVER['HTTP_SAVE_DATA'] ?? '') === 'on';
    $srcset = url($small) . ' 480w' . ($saveData ? '' : ', ' . url($path) . ' 1200w');
    echo '<img src="' . e(url($saveData ? $small : $path)) . '" srcset="' . e($srcset) . '" sizes="' . ($saveData ? '320px' : '(max-width: 767px) 100vw, (max-width: 1023px) 50vw, 800px') . '" width="1200" height="675" alt="' . e($article['image_alt']) . '" ' . ($priority ? 'fetchpriority="high"' : 'loading="lazy"') . ' decoding="async">';
}

function highlighted(string $text, string $query): string
{
    if ($query === '') {
        return e($text);
    }
    $parts = preg_split('/(' . preg_quote($query, '/') . ')/iu', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    return implode('', array_map(fn ($part) => mb_strtolower($part) === mb_strtolower($query) ? '<mark>' . e($part) . '</mark>' : e($part), $parts ?: [$text]));
}
