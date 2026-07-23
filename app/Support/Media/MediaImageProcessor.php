<?php

namespace App\Support\Media;

use RuntimeException;

class MediaImageProcessor
{
    private const MAX_DIMENSION = 2400;

    private const THUMB_SIZE = 320;

    private const JPEG_QUALITY = 82;

    /**
     * @return array{width: int|null, height: int|null}
     */
    public function optimize(string $absolutePath): array
    {
        if (! $this->isSupportedImage($absolutePath)) {
            return ['width' => null, 'height' => null];
        }

        $image = $this->loadImage($absolutePath);
        [$width, $height] = $this->dimensions($image);

        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            $image = $this->resizeToFit($image, $width, $height, self::MAX_DIMENSION);
            [$width, $height] = $this->dimensions($image);
        }

        $this->saveImage($image, $absolutePath);
        imagedestroy($image);

        return ['width' => $width, 'height' => $height];
    }

    /**
     * @return array{width: int, height: int}
     */
    public function crop(string $absolutePath, int $x, int $y, int $width, int $height): array
    {
        $source = $this->loadImage($absolutePath);
        [$sourceWidth, $sourceHeight] = $this->dimensions($source);

        $x = max(0, min($x, $sourceWidth - 1));
        $y = max(0, min($y, $sourceHeight - 1));
        $width = max(1, min($width, $sourceWidth - $x));
        $height = max(1, min($height, $sourceHeight - $y));

        $cropped = imagecrop($source, [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
        ]);

        imagedestroy($source);

        if ($cropped === false) {
            throw new RuntimeException('Unable to crop image.');
        }

        $this->saveImage($cropped, $absolutePath);
        imagedestroy($cropped);

        return ['width' => $width, 'height' => $height];
    }

    public function createThumbnail(string $sourcePath, string $destinationPath): void
    {
        if (! $this->isSupportedImage($sourcePath)) {
            return;
        }

        $source = $this->loadImage($sourcePath);
        [$width, $height] = $this->dimensions($source);
        $thumb = $this->coverCrop($source, $width, $height, self::THUMB_SIZE, self::THUMB_SIZE);

        $directory = dirname($destinationPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->saveImage($thumb, $destinationPath, forceJpeg: true);
        imagedestroy($source);
        imagedestroy($thumb);
    }

    /**
     * @return array{width: int|null, height: int|null}
     */
    public function dimensionsForPath(string $absolutePath): array
    {
        if (! $this->isSupportedImage($absolutePath)) {
            return ['width' => null, 'height' => null];
        }

        $size = getimagesize($absolutePath);

        return [
            'width' => $size[0] ?? null,
            'height' => $size[1] ?? null,
        ];
    }

    private function isSupportedImage(string $absolutePath): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $mime = mime_content_type($absolutePath) ?: '';

        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);
    }

    /**
     * @return \GdImage
     */
    private function loadImage(string $absolutePath)
    {
        $mime = mime_content_type($absolutePath) ?: '';

        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($absolutePath),
            'image/png' => imagecreatefrompng($absolutePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($absolutePath) : false,
            'image/gif' => imagecreatefromgif($absolutePath),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unable to load image for processing.');
        }

        return $image;
    }

    /**
     * @param  \GdImage  $image
     * @return array{0: int, 1: int}
     */
    private function dimensions($image): array
    {
        return [imagesx($image), imagesy($image)];
    }

    /**
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private function resizeToFit($image, int $width, int $height, int $maxDimension)
    {
        $ratio = min($maxDimension / $width, $maxDimension / $height);
        $targetWidth = (int) round($width * $ratio);
        $targetHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }

    /**
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private function coverCrop($image, int $width, int $height, int $targetWidth, int $targetHeight)
    {
        $ratio = max($targetWidth / $width, $targetHeight / $height);
        $scaledWidth = (int) round($width * $ratio);
        $scaledHeight = (int) round($height * $ratio);
        $x = (int) round(($scaledWidth - $targetWidth) / 2);
        $y = (int) round(($scaledHeight - $targetHeight) / 2);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $scaled = imagecreatetruecolor($scaledWidth, $scaledHeight);
        imagecopyresampled($scaled, $image, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $width, $height);
        imagecopy($canvas, $scaled, 0, 0, $x, $y, $targetWidth, $targetHeight);
        imagedestroy($scaled);

        return $canvas;
    }

    /**
     * @param  \GdImage  $image
     */
    private function saveImage($image, string $absolutePath, bool $forceJpeg = false): void
    {
        $mime = $forceJpeg ? 'image/jpeg' : (mime_content_type($absolutePath) ?: 'image/jpeg');

        $saved = match ($mime) {
            'image/png' => imagepng($image, $absolutePath, 6),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, $absolutePath, self::JPEG_QUALITY) : imagejpeg($image, $absolutePath, self::JPEG_QUALITY),
            'image/gif' => imagegif($image, $absolutePath),
            default => imagejpeg($image, $absolutePath, self::JPEG_QUALITY),
        };

        if (! $saved) {
            throw new RuntimeException('Unable to save processed image.');
        }
    }
}
