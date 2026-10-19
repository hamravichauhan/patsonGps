<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Convert an uploaded image to WebP format and save to target directory.
     *
     * @param  UploadedFile  $file
     * @param  string  $subfolder  Subfolder within images/ and storage/app/public/ (e.g. 'products')
     * @param  int  $quality  Compression quality (1-100, default 82)
     * @param  string|null  $customPrefix  Optional filename prefix
     * @param  bool  $removeBg  Whether to automatically remove studio background
     * @param  int  $tolerance  Color distance tolerance for background removal (default 34)
     * @param  int  $feather  Edge alpha feathering width (default 12)
     * @return string  The generated WebP filename (e.g. 'kokum-syrup-a1b2c3d4.webp')
     */
    public static function uploadAsWebp(
        UploadedFile $file,
        string $subfolder = 'products',
        int $quality = 82,
        ?string $customPrefix = null,
        bool $removeBg = false,
        int $tolerance = 34,
        int $feather = 12
    ): string {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $cleanBase = Str::slug($customPrefix ?: $originalName);
        if (empty($cleanBase)) {
            $cleanBase = 'img';
        }

        $filename = $cleanBase . '-' . Str::random(8) . '.webp';

        // Target storage destinations:
        // 1. public/images/{subfolder} (legacy direct public path used by storefront)
        // 2. storage/app/public/{subfolder} (Laravel public disk storage)
        $publicDirPath = public_path('images/' . trim($subfolder, '/'));
        $storageDirPath = storage_path('app/public/' . trim($subfolder, '/'));

        if (!File::isDirectory($publicDirPath)) {
            File::makeDirectory($publicDirPath, 0755, true, true);
        }
        if (!File::isDirectory($storageDirPath)) {
            File::makeDirectory($storageDirPath, 0755, true, true);
        }

        $targetPublicFile = $publicDirPath . '/' . $filename;
        $targetStorageFile = $storageDirPath . '/' . $filename;

        $tempPath = $file->getRealPath();

        // Convert the image to WebP
        $converted = self::processAndSaveWebp($tempPath, $targetPublicFile, $quality);

        if ($converted) {
            // Optional: Auto-remove studio product background if requested
            if ($removeBg) {
                try {
                    self::removeBackground($targetPublicFile, $targetPublicFile, $tolerance, $feather);
                } catch (\Throwable $bgError) {
                    Log::warning('Automatic background removal failed: ' . $bgError->getMessage());
                }
            }

            // Mirror to storage path for Laravel public disk compatibility
            try {
                File::copy($targetPublicFile, $targetStorageFile);
            } catch (\Throwable $e) {
                Log::warning('Failed mirroring WebP image to storage disk: ' . $e->getMessage());
            }

            return $filename;
        }

        // Fallback: If WebP conversion failed (e.g. unsupported raw format), save using standard storage
        Log::warning('WebP conversion failed for ' . $file->getClientOriginalName() . ', falling back to original upload.');
        $fallbackName = $file->hashName();
        $file->move($publicDirPath, $fallbackName);
        @copy($publicDirPath . '/' . $fallbackName, $storageDirPath . '/' . $fallbackName);

        return $fallbackName;
    }

    /**
     * Process an image file from disk and convert it to WebP.
     *
     * @param  string  $sourcePath  Absolute source file path
     * @param  string  $destinationPath  Absolute destination file path (.webp)
     * @param  int  $quality  Compression quality (1-100)
     * @return bool  True if successful, false otherwise
     */
    public static function processAndSaveWebp(string $sourcePath, string $destinationPath, int $quality = 82): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        // Check if GD extension with WebP is available
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            Log::error('GD extension or imagewebp() is not available on this server.');
            return false;
        }

        $image = null;
        $sourceData = @file_get_contents($sourcePath);
        if ($sourceData === false) {
            return false;
        }

        // Attempt to create image resource from string (handles JPEG, PNG, GIF, WebP, BMP)
        $image = @imagecreatefromstring($sourceData);

        if (!$image) {
            // Try specific loaders if imagecreatefromstring failed
            $info = @getimagesize($sourcePath);
            $mime = $info['mime'] ?? '';

            switch ($mime) {
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $image = @imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($sourcePath);
                    break;
                case 'image/bmp':
                case 'image/x-ms-bmp':
                    if (function_exists('imagecreatefrombmp')) {
                        $image = @imagecreatefrombmp($sourcePath);
                    }
                    break;
                case 'image/avif':
                    if (function_exists('imagecreatefromavif')) {
                        $image = @imagecreatefromavif($sourcePath);
                    }
                    break;
            }
        }

        if (!$image) {
            return false;
        }

        // Auto-orient based on EXIF if available (JPEG images from smartphones)
        if (function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($sourcePath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore EXIF read errors
            }
        }

        // Handle transparency for PNG / GIF / WebP images
        if (function_exists('imagepalettetotruecolor')) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);
        imagesavealpha($image, true);

        // Ensure destination directory exists
        $destDir = dirname($destinationPath);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true, true);
        }

        // Save as WebP
        $success = imagewebp($image, $destinationPath, max(1, min(100, $quality)));
        if (PHP_VERSION_ID < 80500) {
            @imagedestroy($image);
        }

        return $success;
    }

    /**
     * Convert an existing file on disk in place or to a new WebP path.
     *
     * @param  string  $filePath
     * @param  int  $quality
     * @param  bool  $deleteOriginal
     * @return string|null  The new .webp path if successful
     */
    public static function convertFileToWebp(string $filePath, int $quality = 82, bool $deleteOriginal = false): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension === 'webp') {
            return $filePath;
        }

        $webpPath = pathinfo($filePath, PATHINFO_DIRNAME) . '/' . pathinfo($filePath, PATHINFO_FILENAME) . '.webp';

        if (self::processAndSaveWebp($filePath, $webpPath, $quality)) {
            if ($deleteOriginal && $webpPath !== $filePath) {
                @unlink($filePath);
            }
            return $webpPath;
        }

        return null;
    }

    /**
     * Check if an image already has transparent corners.
     *
     * @param  string  $filePath
     * @return bool
     */
    public static function hasTransparentCorners(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $sourceData = @file_get_contents($filePath);
        if ($sourceData === false) {
            return false;
        }

        $src = @imagecreatefromstring($sourceData);
        if (!$src) {
            return false;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        if ($w < 6 || $h < 6) {
            if (PHP_VERSION_ID < 80500) @imagedestroy($src);
            return false;
        }

        $corners = [
            [2, 2],
            [$w - 3, 2],
            [2, $h - 3],
            [$w - 3, $h - 3],
        ];

        $transparentCount = 0;
        foreach ($corners as [$cx, $cy]) {
            $rgba = imagecolorat($src, $cx, $cy);
            $alpha = ($rgba >> 24) & 0x7F;
            if ($alpha >= 100) {
                $transparentCount++;
            }
        }

        if (PHP_VERSION_ID < 80500) {
            @imagedestroy($src);
        }

        return $transparentCount === count($corners);
    }

    /**
     * Remove solid/gradient studio backdrops and output a clean transparent WebP.
     * Uses perimeter boundary sampling, Euclidean color distance, BFS flood-fill,
     * and soft alpha feathering to keep product contents and labels 100% sharp.
     *
     * @param  string  $sourcePath  Source image file
     * @param  string|null  $destinationPath  Destination WebP path (defaults to overwriting source)
     * @param  int  $tolerance  Color distance sensitivity (default 34)
     * @param  int  $feather  Soft edge feathering in alpha units (default 12)
     * @param  int  $maxDimension  Max width/height constraint
     * @return bool  True if successful, false otherwise
     */
    public static function removeBackground(
        string $sourcePath,
        ?string $destinationPath = null,
        int $tolerance = 34,
        int $feather = 12,
        int $maxDimension = 1600
    ): bool {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $destinationPath = $destinationPath ?: $sourcePath;

        $sourceData = @file_get_contents($sourcePath);
        if ($sourceData === false) {
            return false;
        }

        $src = @imagecreatefromstring($sourceData);
        if (!$src) {
            return false;
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // Scale down high-resolution images for memory safety and fast web performance
        if ($w > $maxDimension || $h > $maxDimension) {
            $scale = min($maxDimension / $w, $maxDimension / $h);
            $newW = (int) round($w * $scale);
            $newH = (int) round($h * $scale);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefill($resized, 0, 0, $transparent);
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
            if (PHP_VERSION_ID < 80500) {
                @imagedestroy($src);
            }
            $src = $resized;
            $w = $newW;
            $h = $newH;
        }

        // 1. Sample boundary perimeter points to learn background color profile
        $samplePoints = [
            [2, 2], [$w - 3, 2], [2, $h - 3], [$w - 3, $h - 3],
            [(int)($w / 2), 2], [(int)($w / 2), $h - 3],
            [2, (int)($h / 2)], [$w - 3, (int)($h / 2)],
            [(int)($w / 4), 2], [(int)(3 * $w / 4), 2],
            [(int)($w / 4), $h - 3], [(int)(3 * $w / 4), $h - 3],
        ];

        $sampleColors = [];
        $rSum = 0; $gSum = 0; $bSum = 0; $count = 0;

        foreach ($samplePoints as [$px, $py]) {
            $rgb = imagecolorat($src, $px, $py);
            $a = ($rgb >> 24) & 0x7F;
            // Skip if already transparent
            if ($a >= 100) {
                continue;
            }
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $rSum += $r;
            $gSum += $g;
            $bSum += $b;
            $sampleColors[] = [$r, $g, $b];
            $count++;
        }

        // If all boundary sample points are already transparent, image is already ready!
        if ($count === 0) {
            imagealphablending($src, false);
            imagesavealpha($src, true);
            $destDir = dirname($destinationPath);
            if (!File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true, true);
            }
            imagewebp($src, $destinationPath, 90);
            if (PHP_VERSION_ID < 80500) {
                @imagedestroy($src);
            }
            return true;
        }

        $avgR = $rSum / $count;
        $avgG = $gSum / $count;
        $avgB = $bSum / $count;

        // Euclidean distance function in RGB color space
        $calcDist = function (int $r, int $g, int $b) use ($avgR, $avgG, $avgB, $sampleColors): float {
            $dMin = sqrt(($r - $avgR) ** 2 + ($g - $avgG) ** 2 + ($b - $avgB) ** 2);
            foreach ($sampleColors as [$sr, $sg, $sb]) {
                $d = sqrt(($r - $sr) ** 2 + ($g - $sg) ** 2 + ($b - $sb) ** 2);
                if ($d < $dMin) {
                    $dMin = $d;
                }
            }
            return $dMin;
        };

        // 2. BFS flood fill from all perimeter boundaries
        $totalPixels = $w * $h;
        $visited = new \SplFixedArray($totalPixels);
        $queue = new \SplQueue();

        // Enqueue top and bottom boundaries
        for ($x = 0; $x < $w; $x++) {
            $idxTop = $x;
            $idxBottom = ($h - 1) * $w + $x;
            $queue->enqueue($idxTop);
            $visited[$idxTop] = 1;
            $queue->enqueue($idxBottom);
            $visited[$idxBottom] = 1;
        }

        // Enqueue left and right boundaries
        for ($y = 1; $y < $h - 1; $y++) {
            $idxLeft = $y * $w;
            $idxRight = $y * $w + ($w - 1);
            $queue->enqueue($idxLeft);
            $visited[$idxLeft] = 1;
            $queue->enqueue($idxRight);
            $visited[$idxRight] = 1;
        }

        // bgMask: 0 = solid foreground, 127 = fully transparent, 1-126 = soft feather
        $bgMask = new \SplFixedArray($totalPixels);

        while (!$queue->isEmpty()) {
            $curr = $queue->dequeue();
            $cx = $curr % $w;
            $cy = (int)($curr / $w);

            $color = imagecolorat($src, $cx, $cy);
            $a = ($color >> 24) & 0x7F;

            if ($a >= 110) {
                $bgMask[$curr] = 127;
            } else {
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                $dist = $calcDist($r, $g, $b);

                if ($dist <= $tolerance) {
                    $bgMask[$curr] = 127;
                    // Spread outward to 4 connected neighbors
                    if ($cx > 0 && !$visited[$curr - 1]) {
                        $visited[$curr - 1] = 1;
                        $queue->enqueue($curr - 1);
                    }
                    if ($cx < $w - 1 && !$visited[$curr + 1]) {
                        $visited[$curr + 1] = 1;
                        $queue->enqueue($curr + 1);
                    }
                    if ($cy > 0 && !$visited[$curr - $w]) {
                        $visited[$curr - $w] = 1;
                        $queue->enqueue($curr - $w);
                    }
                    if ($cy < $h - 1 && !$visited[$curr + $w]) {
                        $visited[$curr + $w] = 1;
                        $queue->enqueue($curr + $w);
                    }
                } elseif ($dist < ($tolerance + $feather)) {
                    // Soft alpha anti-aliasing edge
                    $ratio = ($tolerance + $feather - $dist) / $feather;
                    $bgMask[$curr] = (int) round($ratio * 127);
                }
            }
        }

        unset($visited, $queue);

        // 3. Render transparent output canvas
        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $idx = $y * $w + $x;
                $alpha = $bgMask[$idx] ?? 0;
                $color = imagecolorat($src, $x, $y);
                $origA = ($color >> 24) & 0x7F;
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                $finalA = max($origA, $alpha);

                if ($finalA >= 127) {
                    imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, 0, 0, 0, 127));
                } elseif ($finalA > 0) {
                    imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $r, $g, $b, $finalA));
                } else {
                    imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $r, $g, $b, 0));
                }
            }
        }

        unset($bgMask);

        // 4. Atomically write to destination
        $destDir = dirname($destinationPath);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true, true);
        }

        $tmpFile = $destinationPath . '.tmp.' . uniqid() . '.webp';
        $saved = imagewebp($out, $tmpFile, 92);

        if (PHP_VERSION_ID < 80500) {
            @imagedestroy($src);
            @imagedestroy($out);
        }

        if ($saved && file_exists($tmpFile)) {
            rename($tmpFile, $destinationPath);
            return true;
        }

        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
        }

        return false;
    }
}

