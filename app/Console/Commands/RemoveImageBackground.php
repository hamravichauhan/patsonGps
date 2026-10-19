<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveImageBackground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:remove-bg
                            {--dir=images/products : Relative directory inside public/ to scan}
                            {--file= : Process a single specific image file}
                            {--all : Process all product images in the directory}
                            {--tolerance=34 : Euclidean color distance threshold (default 34)}
                            {--feather=12 : Alpha edge feathering width (default 12)}
                            {--backup : Create a .bak backup file before overwriting}
                            {--force : Reprocess even if image already has transparent corners}
                            {--max-dim=1600 : Maximum dimension before scaling down}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove studio photography backgrounds from product images, converting them to clean transparent WebP';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $dirOption = $this->option('dir');
        $singleFile = $this->option('file');
        $all = (bool) $this->option('all');
        $tolerance = (int) $this->option('tolerance');
        $feather = (int) $this->option('feather');
        $backup = (bool) $this->option('backup');
        $force = (bool) $this->option('force');
        $maxDim = (int) $this->option('max-dim');

        $filesToProcess = [];

        if (!empty($singleFile)) {
            $absPath = file_exists($singleFile) ? $singleFile : public_path($singleFile);
            if (!file_exists($absPath)) {
                $this->error("Specified file not found: {$singleFile}");
                return 1;
            }
            $filesToProcess[] = $absPath;
        } else {
            $targetDir = public_path($dirOption);
            if (!File::isDirectory($targetDir)) {
                $this->error("Directory does not exist: {$targetDir}");
                return 1;
            }

            if (!$all) {
                $this->warn("Please specify --all to process all images in {$dirOption}, or --file=path/to/image.webp");
                return 0;
            }

            $allFiles = File::files($targetDir);
            foreach ($allFiles as $splFile) {
                $ext = strtolower($splFile->getExtension());
                if (in_array($ext, ['webp', 'png', 'jpg', 'jpeg'])) {
                    $filesToProcess[] = $splFile->getRealPath();
                }
            }
        }

        if (empty($filesToProcess)) {
            $this->info("No images found to process.");
            return 0;
        }

        $total = count($filesToProcess);
        $this->info("Analyzing {$total} image(s) for background removal...");
        $this->line("Parameters: Tolerance={$tolerance}, Feather={$feather}, MaxDimension={$maxDim}px");

        $storageProductsDir = storage_path('app/public/' . trim(str_replace('images/', '', $dirOption), '/'));

        $processedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $startTime = microtime(true);

        foreach ($filesToProcess as $filePath) {
            // Check if already transparent
            if (!$force && ImageService::hasTransparentCorners($filePath)) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            // Create backup if requested
            if ($backup) {
                $bakPath = $filePath . '.bak';
                if (!file_exists($bakPath)) {
                    @copy($filePath, $bakPath);
                }
            }

            // Perform background removal
            $success = ImageService::removeBackground($filePath, $filePath, $tolerance, $feather, $maxDim);

            if ($success) {
                $processedCount++;

                // Mirror to Laravel storage disk if storage folder exists
                if (File::isDirectory($storageProductsDir)) {
                    $storageDst = $storageProductsDir . '/' . basename($filePath);
                    @copy($filePath, $storageDst);
                }
            } else {
                $failedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $elapsed = round(microtime(true) - $startTime, 2);

        $this->info("=== Background Removal Summary ===");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Examined', $total],
                ['Processed (Background Removed)', $processedCount],
                ['Skipped (Already Transparent)', $skippedCount],
                ['Failed', $failedCount],
                ['Total Time', "{$elapsed} seconds"],
            ]
        );

        $this->info("✓ Background removal completed successfully!");

        return 0;
    }
}
