<?php

namespace Tests\Unit;

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    public function test_can_convert_uploaded_image_to_webp(): void
    {
        $img = imagecreatetruecolor(50, 50);
        $blue = imagecolorallocate($img, 0, 0, 255);
        imagefill($img, 0, 0, $blue);

        $tmpFile = sys_get_temp_dir() . '/test_unit_' . uniqid() . '.png';
        imagepng($img, $tmpFile);

        $uploaded = new UploadedFile($tmpFile, 'test-unit-sample.png', 'image/png', null, true);

        $savedFilename = ImageService::uploadAsWebp($uploaded, 'products', 80, 'unit-test');

        $this->assertStringEndsWith('.webp', $savedFilename);
        $this->assertFileExists(public_path('images/products/' . $savedFilename));

        // Verify it is indeed WebP format
        $mime = mime_content_type(public_path('images/products/' . $savedFilename));
        $this->assertEquals('image/webp', $mime);

        // Cleanup
        @unlink(public_path('images/products/' . $savedFilename));
        @unlink(storage_path('app/public/products/' . $savedFilename));
        @unlink($tmpFile);
    }

    public function test_can_detect_transparent_corners(): void
    {
        // 1. Solid image
        $solid = imagecreatetruecolor(40, 40);
        $gray = imagecolorallocate($solid, 160, 160, 160);
        imagefill($solid, 0, 0, $gray);
        $solidPath = sys_get_temp_dir() . '/solid_' . uniqid() . '.webp';
        imagewebp($solid, $solidPath);
        imagedestroy($solid);

        $this->assertFalse(ImageService::hasTransparentCorners($solidPath));

        // 2. Transparent image
        $trans = imagecreatetruecolor(40, 40);
        imagealphablending($trans, false);
        imagesavealpha($trans, true);
        $alpha = imagecolorallocatealpha($trans, 0, 0, 0, 127);
        imagefill($trans, 0, 0, $alpha);
        $transPath = sys_get_temp_dir() . '/trans_' . uniqid() . '.webp';
        imagewebp($trans, $transPath);
        imagedestroy($trans);

        $this->assertTrue(ImageService::hasTransparentCorners($transPath));

        @unlink($solidPath);
        @unlink($transPath);
    }

    public function test_can_remove_solid_background_and_make_corners_transparent(): void
    {
        // Create an image with solid gray background and a red circle in the center
        $img = imagecreatetruecolor(60, 60);
        $bg = imagecolorallocate($img, 150, 150, 150);
        $red = imagecolorallocate($img, 220, 30, 30);
        imagefill($img, 0, 0, $bg);
        imagefilledellipse($img, 30, 30, 30, 30, $red);

        $tmpFile = sys_get_temp_dir() . '/product_sample_' . uniqid() . '.webp';
        imagewebp($img, $tmpFile);
        imagedestroy($img);

        $this->assertFalse(ImageService::hasTransparentCorners($tmpFile));

        // Remove background
        $success = ImageService::removeBackground($tmpFile, $tmpFile, 30, 8);
        $this->assertTrue($success);
        $this->assertTrue(ImageService::hasTransparentCorners($tmpFile));

        // Verify the center circle remains red (not transparent)
        $out = imagecreatefromwebp($tmpFile);
        $centerColor = imagecolorat($out, 30, 30);
        $centerAlpha = ($centerColor >> 24) & 0x7F;
        $centerRed = ($centerColor >> 16) & 0xFF;
        imagedestroy($out);

        $this->assertEquals(0, $centerAlpha, 'Center circle should remain opaque');
        $this->assertGreaterThan(180, $centerRed, 'Center circle should retain red color');

        @unlink($tmpFile);
    }
}

