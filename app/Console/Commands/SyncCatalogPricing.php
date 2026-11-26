<?php

namespace App\Console\Commands;

use Database\Seeders\ProductPricingSeeder;
use Illuminate\Console\Command;

class SyncCatalogPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:sync-mrp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product MRP, units per box, and minimum order quantities from the official price list';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Syncing catalog MRP and packaging configurations...');
        $seeder = new ProductPricingSeeder();
        $seeder->run();
        $this->info('Catalog MRP and packaging rules successfully synced!');
        return Command::SUCCESS;
    }
}
