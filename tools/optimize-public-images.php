<?php

declare(strict_types=1);

if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
    fwrite(STDERR, "The GD extension with WebP support is required.\n");
    exit(1);
}

$publicPath = dirname(__DIR__).DIRECTORY_SEPARATOR.'public';
$outputRoot = $publicPath.DIRECTORY_SEPARATOR.'generated'.DIRECTORY_SEPARATOR.'optimized';
$sourceDirectories = [
    'bckgrndHome',
    'bglogo',
    'brochure',
    'home-assets',
];
$sourceFiles = [$publicPath.DIRECTORY_SEPARATOR.'logo.png'];

foreach ($sourceDirectories as $relativeDirectory) {
    $directory = $publicPath.DIRECTORY_SEPARATOR.$relativeDirectory;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (! $file instanceof SplFileInfo || ! $file->isFile()) {
            continue;
        }

        if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $sourceFiles[] = $file->getPathname();
        }
    }
}

$written = 0;
$skipped = 0;
$originalBytes = 0;
$optimizedBytes = 0;

foreach (array_unique($sourceFiles) as $sourcePath) {
    $relativePath = ltrim(str_replace('\\', '/', substr($sourcePath, strlen($publicPath))), '/');
    $outputRelativePath = preg_replace('/\.(?:jpe?g|png|webp)$/i', '.webp', $relativePath);

    if (! is_string($outputRelativePath)) {
        continue;
    }

    $outputPath = $outputRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $outputRelativePath);

    if (is_file($outputPath) && filemtime($outputPath) >= filemtime($sourcePath)) {
        $skipped++;
        $originalBytes += filesize($sourcePath) ?: 0;
        $optimizedBytes += filesize($outputPath) ?: 0;

        continue;
    }

    $image = loadImage($sourcePath);

    if (! $image instanceof GdImage) {
        fwrite(STDERR, "Skipped unreadable image: {$relativePath}\n");

        continue;
    }

    $image = applyExifOrientation($image, $sourcePath);
    [$maxWidth, $maxHeight, $quality] = outputProfile($relativePath);
    $width = imagesx($image);
    $height = imagesy($image);
    $scale = min(1, $maxWidth / $width, $maxHeight / $height);
    $targetWidth = max(1, (int) round($width * $scale));
    $targetHeight = max(1, (int) round($height * $scale));

    $outputImage = imagecreatetruecolor($targetWidth, $targetHeight);
    imagealphablending($outputImage, false);
    imagesavealpha($outputImage, true);
    $transparent = imagecolorallocatealpha($outputImage, 0, 0, 0, 127);
    imagefilledrectangle($outputImage, 0, 0, $targetWidth, $targetHeight, $transparent);
    imagecopyresampled(
        $outputImage,
        $image,
        0,
        0,
        0,
        0,
        $targetWidth,
        $targetHeight,
        $width,
        $height,
    );

    if (! is_dir(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0775, true);
    }

    if (! imagewebp($outputImage, $outputPath, $quality)) {
        fwrite(STDERR, "Failed to write optimized image: {$outputRelativePath}\n");
        imagedestroy($outputImage);
        imagedestroy($image);

        continue;
    }

    imagedestroy($outputImage);
    imagedestroy($image);

    $sourceBytes = filesize($sourcePath) ?: 0;
    $outputBytes = filesize($outputPath) ?: 0;

    if ($outputBytes >= $sourceBytes) {
        unlink($outputPath);
        $skipped++;

        continue;
    }

    $written++;
    $originalBytes += $sourceBytes;
    $optimizedBytes += $outputBytes;
}

printf(
    "Optimized %d images; reused/skipped %d. Selected assets: %.1f MB -> %.1f MB.\n",
    $written,
    $skipped,
    $originalBytes / 1048576,
    $optimizedBytes / 1048576,
);

function loadImage(string $path): GdImage|false
{
    return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        'webp' => @imagecreatefromwebp($path),
        default => false,
    };
}

function applyExifOrientation(GdImage $image, string $path): GdImage
{
    if (! function_exists('exif_read_data') || ! in_array(
        strtolower(pathinfo($path, PATHINFO_EXTENSION)),
        ['jpg', 'jpeg'],
        true,
    )) {
        return $image;
    }

    $exif = @exif_read_data($path);
    $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

    if (in_array($orientation, [2, 4, 5, 7], true)) {
        imageflip($image, in_array($orientation, [2, 5, 7], true) ? IMG_FLIP_HORIZONTAL : IMG_FLIP_VERTICAL);
    }

    $angle = match ($orientation) {
        3, 4 => 180,
        5, 6 => -90,
        7, 8 => 90,
        default => 0,
    };

    if ($angle === 0) {
        return $image;
    }

    $rotated = imagerotate($image, $angle, 0);

    if (! $rotated instanceof GdImage) {
        return $image;
    }

    imagedestroy($image);

    return $rotated;
}

/**
 * @return array{int, int, int}
 */
function outputProfile(string $relativePath): array
{
    if (str_starts_with($relativePath, 'bglogo/') || $relativePath === 'logo.png') {
        return [720, 720, 84];
    }

    if (str_starts_with($relativePath, 'brochure/')) {
        return [1800, 2400, 84];
    }

    return [1920, 1920, 80];
}
