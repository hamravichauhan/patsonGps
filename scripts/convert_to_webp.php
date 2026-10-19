<?php
/**
 * Standalone Image to WebP Converter Script
 *
 * Usage:
 *   php scripts/convert_to_webp.php [path-to-directory-or-file] [--quality=82] [--delete-original]
 *
 * Example:
 *   php scripts/convert_to_webp.php public/images/Products
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
    fwrite(STDERR, "Error: PHP GD extension with WebP support is required to run this script.\n");
    exit(1);
}

// Parse command line arguments
$target = $argv[1] ?? __DIR__ . '/../public/images/Products';
$quality = 82;
$deleteOriginal = false;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--quality=')) {
        $quality = (int) substr($arg, 10);
    }
    if ($arg === '--delete-original') {
        $deleteOriginal = true;
    }
}

$target = realpath($target);
if (!$target || (!is_dir($target) && !is_file($target))) {
    fwrite(STDERR, "Error: Specified path does not exist: {$argv[1]}\n");
    exit(1);
}

function convertImageToWebp($sourcePath, $quality = 82, $deleteOriginal = false) {
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    if ($ext === 'webp') {
        return null; // Already webp
    }

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'gif'])) {
        return null;
    }

    $raw = @file_get_contents($sourcePath);
    if ($raw === false) {
        return false;
    }

    $img = @imagecreatefromstring($raw);
    if (!$img) {
        return false;
    }

    // Auto-rotate if EXIF orientation exists
    if (function_exists('exif_read_data')) {
        try {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $img = imagerotate($img, 180, 0);
                        break;
                    case 6:
                        $img = imagerotate($img, -90, 0);
                        break;
                    case 8:
                        $img = imagerotate($img, 90, 0);
                        break;
                }
            }
        } catch (Throwable $e) {}
    }

    // Preserve transparency
    if (function_exists('imagepalettetotruecolor')) {
        imagepalettetotruecolor($img);
    }
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $destination = pathinfo($sourcePath, PATHINFO_DIRNAME) . '/' . pathinfo($sourcePath, PATHINFO_FILENAME) . '.webp';
    $saved = imagewebp($img, $destination, max(1, min(100, $quality)));
    if (PHP_VERSION_ID < 80500) {
        @imagedestroy($img);
    }

    if ($saved) {
        $origSize = filesize($sourcePath);
        $newSize = filesize($destination);
        $savedPct = round((($origSize - $newSize) / max(1, $origSize)) * 100, 1);
        echo "✓ Converted: " . basename($sourcePath) . " -> " . basename($destination) . " (" . round($origSize/1024) . "KB to " . round($newSize/1024) . "KB, saved {$savedPct}%)\n";

        if ($deleteOriginal && $destination !== $sourcePath) {
            @unlink($sourcePath);
        }
        return true;
    }

    return false;
}

echo "=== PatSons WebP Converter ===\n";
echo "Target: {$target}\n";
echo "Quality: {$quality}\n";
echo "Delete Originals: " . ($deleteOriginal ? 'YES' : 'NO') . "\n";
echo "---------------------------------\n";

$converted = 0;
$skipped = 0;
$failed = 0;

if (is_file($target)) {
    $res = convertImageToWebp($target, $quality, $deleteOriginal);
    if ($res === true) $converted++;
    elseif ($res === false) $failed++;
    else $skipped++;
} else {
    $files = scandir($target);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..', '.DS_Store'])) continue;
        $filePath = $target . '/' . $file;
        if (is_file($filePath)) {
            $res = convertImageToWebp($filePath, $quality, $deleteOriginal);
            if ($res === true) $converted++;
            elseif ($res === false) $failed++;
            else $skipped++;
        }
    }
}

echo "---------------------------------\n";
echo "Done! Converted: {$converted}, Skipped (Already WebP or non-image): {$skipped}, Failed: {$failed}\n";
