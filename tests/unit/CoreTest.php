<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Sanitizer;
use App\Core\Slug;
use App\Core\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class CoreTest extends TestCase
{
    #[TestDox('F-NOT-03: límites de 149, 150 y 151 caracteres')]
    public function testTitleBoundaries(): void
    {
        self::assertTrue(Validator::text(str_repeat('ñ', 149), 1, 150));
        self::assertTrue(Validator::text(str_repeat('ñ', 150), 1, 150));
        self::assertFalse(Validator::text(str_repeat('ñ', 151), 1, 150));
    }

    #[TestDox('F-NOT-02 F-PUB-10: campos vacíos, espacios y límite de comentario')]
    public function testCommentBoundaries(): void
    {
        self::assertFalse(Validator::text('', 1, 1000));
        self::assertFalse(Validator::text('  ', 1, 1000));
        self::assertTrue(Validator::text(str_repeat('a', 1000), 1, 1000));
        self::assertFalse(Validator::text(str_repeat('a', 1001), 1, 1000));
        self::assertFalse(Validator::text([], 1, 1000));
    }

    #[TestDox('F-AUT-12: política de contraseñas y almacenamiento seguro')]
    public function testPasswords(): void
    {
        foreach (['Abcd123', str_repeat('A', 128), 'abcdefgh9', 'Abcdefgh', str_repeat('A', 128) . '1'] as $password) {
            self::assertFalse(Validator::password($password));
        }
        self::assertTrue(Validator::password('Abcdefg9'));
        self::assertTrue(Validator::password(str_repeat('A', 127) . '1'));
        $hash = Auth::hash('Abcdefg9');
        self::assertTrue(password_verify('Abcdefg9', $hash));
        self::assertFalse(password_verify('otra', $hash));
    }

    #[TestDox('F-PUB-02: páginas inválidas y fuera de rango')]
    public function testPagination(): void
    {
        foreach ([0, -1, 'abc', [], '1 OR 1=1', '999999999999999999999'] as $page) {
            self::assertSame(1, Validator::page($page, 5));
        }
        self::assertSame(5, Validator::page(9999, 5));
        self::assertSame(3, Validator::page(3, 5));
    }

    #[TestDox('F-NOT-12 F-NOT-15: slug Unicode único')]
    public function testSlugs(): void
    {
        self::assertSame('limeno-y-cafe', Slug::make('Limeño y café'));
        self::assertSame('noticia', Slug::make('🎉'));
        self::assertSame('mi-noticia-3', Slug::unique('Mi noticia', fn ($slug) => in_array($slug, ['mi-noticia', 'mi-noticia-2'], true)));
    }

    public static function attacks(): array
    {
        return [
            ['<script>alert(1)</script><p onclick="alert(2)">Texto</p>'],
            ['<a href="javascript:alert(1)">Texto</a><img src=x onerror=alert(2)>'],
            ['<svg onload="alert(1)"><script>alert(2)</script></svg><p>Texto</p>'],
            ['<iframe src="https://evil.test"></iframe><p style="color:red">Texto</p>'],
        ];
    }

    #[DataProvider('attacks')]
    #[TestDox('F-NOT-04 F-PUB-08: lista blanca contra XSS')]
    public function testSanitizer(string $html): void
    {
        $clean = Sanitizer::html($html);
        self::assertStringNotContainsString('script', $clean);
        self::assertStringNotContainsString('onerror', $clean);
        self::assertStringNotContainsString('onclick', $clean);
        self::assertStringNotContainsString('style=', $clean);
        self::assertStringContainsString('Texto', $clean);
    }

    #[TestDox('A-TEC-05 A-VIS-03: editor valida jerarquía de encabezados')]
    public function testHeadings(): void
    {
        self::assertTrue(Sanitizer::headings('<h2>Uno</h2><h3>Dos</h3><h4>Tres</h4><h2>Otro</h2>'));
        self::assertFalse(Sanitizer::headings('<h3>Salto</h3>'));
        self::assertFalse(Sanitizer::headings('<h1>Segundo título principal</h1>'));
    }

    #[TestDox('F-PUB-15: CSRF rechaza tokens vacíos, distintos y arrays')]
    public function testCsrf(): void
    {
        $_SESSION = [];
        $token = Csrf::token();
        self::assertTrue(Csrf::valid($token));
        self::assertFalse(Csrf::valid(null));
        self::assertFalse(Csrf::valid([]));
        self::assertFalse(Csrf::valid('incorrecto'));
    }

    #[TestDox('F-ROL-02 F-ROL-03 F-ROL-04 F-ROL-05: matriz de permisos e IDOR')]
    public function testPermissions(): void
    {
        $article = ['author_id' => 2];
        self::assertTrue(Auth::canManage(['id' => 1, 'role' => 'administrador'], $article));
        self::assertTrue(Auth::canManage(['id' => 2, 'role' => 'editor'], $article));
        self::assertFalse(Auth::canManage(['id' => 3, 'role' => 'editor'], $article));
        self::assertFalse(Auth::canManage(['id' => 2, 'role' => 'lector'], $article));
        self::assertFalse(Auth::allowed('editor', ['administrador']));
    }

    #[TestDox('F-AUT-04 F-PUB-12: correo inválido y parámetros malformados')]
    public function testEmail(): void
    {
        self::assertTrue(Validator::email('lector@example.test'));
        self::assertFalse(Validator::email('no-es-correo'));
        self::assertFalse(Validator::email([]));
        self::assertSame(0, Validator::id('1 OR 1=1'));
    }
}
