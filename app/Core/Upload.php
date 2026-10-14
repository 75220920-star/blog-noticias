<?php

declare(strict_types=1);

namespace App\Core;

final class Upload
{
    public static function image(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
            throw new \RuntimeException('La imagen supera 5 MB o no se pudo recibir. Elige un archivo más pequeño.');
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        if (!isset($allowed[$extension]) || $mime !== $allowed[$extension] || !is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Formato no permitido. Usa una imagen JPG, PNG o WebP real.');
        }
        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            throw new \RuntimeException('Activa la extensión GD en PHP para procesar imágenes.');
        }
        $size = getimagesize($file['tmp_name']);
        if (!$size || $size[0] * $size[1] > 12000000) {
            throw new \RuntimeException('La imagen supera 12 megapíxeles. Reduce sus dimensiones antes de subirla.');
        }
        $source = imagecreatefromstring(file_get_contents($file['tmp_name']));
        if (!$source) {
            throw new \RuntimeException('No se puede leer esta imagen. Exporta otra copia e inténtalo de nuevo.');
        }
        $name = bin2hex(random_bytes(16));
        foreach ([1200, 480] as $width) {
            $height = (int) round($width * 9 / 16);
            $target = imagecreatetruecolor($width, $height);
            $ratio = max($width / $size[0], $height / $size[1]);
            $cropW = (int) round($width / $ratio);
            $cropH = (int) round($height / $ratio);
            imagecopyresampled($target, $source, 0, 0, (int) (($size[0] - $cropW) / 2), (int) (($size[1] - $cropH) / 2), $width, $height, $cropW, $cropH);
            $path = ROOT . '/public/uploads/' . $name . ($width === 480 ? '-480' : '') . '.webp';
            if (!imagewebp($target, $path, $width === 480 ? 55 : 78)) {
                throw new \RuntimeException('No se pudo guardar la imagen. Revisa los permisos de uploads.');
            }
            imagedestroy($target);
        }
        imagedestroy($source);
        return 'uploads/' . $name . '.webp';
    }
}
