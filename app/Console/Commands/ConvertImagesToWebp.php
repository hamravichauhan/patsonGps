<?php

namespace App\Console\Commands;

use App\Models\ProductCatalog;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert-webp 
                            {--dir=images/products : Relative directory inside public/ to scan}
                            {--quality=82 : WebP quality factor (1-100)}
                            {--delete-original : Delete original non-webp file after conversion}
                            {--update-db : Update product_catalog database records with the new .webp names}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan image directories and convert existing JPEG/PNG/BMP files to WebP format';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dirOption = $this->option('dir');
        $targetDir = public_path($dirOption);
        $quality = (int) $this->option('quality');
        $deleteOriginal = (bool) $this->option('delete-original');
        $updateDb = (bool) $this->option('update-db');

        if (!File::isDirectory($targetDir)) {
            $this->error("Directory does not exist: {$targetDir}");
            return 1;
        }

        $this->info("Scanning: {$targetDir} for non-WebP images...");

        $allFiles = File::files($targetDir);
        $convertible = [];
        $validExts = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];

        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, $validExts)) {
                $convertible[] = $file;
            }
        }

        if (empty($convertible)) {
            $this->info("No convertible images (JPEG, PNG, GIF, BMP) found in {$targetDir}. All images are already WebP!");
            return 0;
        }

        $this->info("Found " . count($convertible) . " images to convert.");
        $bar = $this->output->createProgressBar(count($convertible));
        $bar->start();

        $convertedCount = 0;
        $failedCount = 0;
        $nameMap = []; // old basename => new basename

        foreach ($convertible as $splFile) {
            $sourcePath = $splFile->getRealPath();
            $oldBasename = $splFile->getFilename();
            $newBasename = pathinfo($oldBasename, PATHINFO_FILENAME) . '.webp';
            $destPath = $splFile->getPath() . '/' . $newBasename;

            if (ImageService::processAndSaveWebp($sourcePath, $destPath, $quality)) {
                $convertedCount++;
                $nameMap[$oldBasename] = $newBasename;

                if ($deleteOriginal && $destPath !== $sourcePath) {
                    @unlink($sourcePath);
                }
            } else {
                $failedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Conversion Complete: {$convertedCount} converted, {$failedCount} failed.");

        if ($updateDb && !empty($nameMap)) {
            $this->info("Updating database product_catalog records...");
            $products = ProductCatalog::all();
            $dbUpdated = 0;

            foreach ($products as $prod) {
                $changed = false;

                // Update primary image
                if (!empty($prod->image) && isset($nameMap[basename($prod->image)])) {
                    $prod->image = $nameMap[basename($prod->image)];
                    $changed = true;
                }

                // Update gallery images
                if (is_array($prod->images)) {
                    $newGallery = [];
                    foreach ($prod->images as $img) {
                        $base = basename($img);
                        $newGallery[] = $nameMap[$base] ?? $img;
                        if (isset($nameMap[$base])) {
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $prod->images = $newGallery;
                    }
                }

                if ($changed) {
                    $prod->save();
                    $dbUpdated++;
                }
            }

            $this->info("✓ Updated {$dbUpdated} database catalog products with new .webp references.");
        }

        return 0;
    }
}
