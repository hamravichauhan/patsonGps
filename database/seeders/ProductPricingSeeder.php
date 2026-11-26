<?php

namespace Database\Seeders;

use App\Models\ProductCatalog;
use Illuminate\Database\Seeder;

class ProductPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds exact MRP, Units per Box, and Minimum Order Quantities from Client's Price List.
     * Deactivates duplicate pack sizes (e.g. 500ml can vs bottle) and unlisted bulk sizes (5L cans, 35L drums).
     */
    public function run(): void
    {
        // 1. KOKUM SQUASH: Only 1 LTR (₹200, Box 12) & 500ML Bottle (₹150, Box 12)
        ProductCatalog::where('product_name', 'Kokum Squash')->where('size', '500ml Bottle')
            ->update(['mrp' => 150.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'Kokum Squash')->where('size', '1 Litre Bottle')
            ->update(['mrp' => 200.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        // Deactivate duplicate 500ml Can, 700ml, 5L Can, 35L Drum
        ProductCatalog::where('product_name', 'Kokum Squash')
            ->whereIn('size', ['500ml Can', '700ml Bottle', '5 Litre Can', '35 Litre Drum'])
            ->update(['is_active' => 0]);

        // 2. KOKUM AGAL: Only 1 LTR (₹150, Box 12) & 500ML Bottle (₹120, Box 15)
        ProductCatalog::where('product_name', 'like', '%Kokum Agal%')->where('size', '500ml Bottle')
            ->update(['mrp' => 120.00, 'units_per_box' => 15, 'min_order_qty' => 15, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Kokum Agal%')->where('size', '1 Litre Bottle')
            ->update(['mrp' => 150.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        // Deactivate duplicate 500ml Can, 700ml, 5L Can, 35L Drum
        ProductCatalog::where('product_name', 'like', '%Kokum Agal%')
            ->whereIn('size', ['500ml Can', '700ml Bottle', '5 Litre Can', '35 Litre Drum'])
            ->update(['is_active' => 0]);

        // 3. KOKAM JUICE WITH WHOLE FRUIT: 1 LTR (₹250, Box 12) & 500ML (₹150, Box 12)
        ProductCatalog::where('product_name', 'like', '%Kokum Juice with Whole%')->where('size', '500ml Bottle')
            ->update(['mrp' => 150.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Kokum Juice with Whole%')->where('size', '1 Litre Bottle')
            ->update(['mrp' => 250.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 4. SWEET KOKAM: Only 100GM (₹120, Min 25)
        ProductCatalog::where('product_name', 'like', '%Sweet Kokam%')->where('size', '100g Pack')
            ->update(['mrp' => 120.00, 'units_per_box' => 25, 'min_order_qty' => 25, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Sweet Kokam%')->where('size', '200g Pack')
            ->update(['is_active' => 0]);

        // 5. KOKAM SOAL (DRY KOKAM): 250gm min pack (₹500, Box 12)
        ProductCatalog::where('product_name', 'like', '%Kokum Sol%')->where('id', 17)
            ->update(['size' => '250g Pack', 'mrp' => 500.00, 'units_per_box' => 12, 'min_order_qty' => 1, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Kokum Sol%')->where('id', 18)
            ->update(['is_active' => 0]);

        // 6. ALL JAMS: 500GM (₹200, Box 12) & 1 KG (₹280, Box 12)
        ProductCatalog::where('category', 'like', '%Jam%')->where('size', '500g Jar')
            ->update(['mrp' => 200.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        ProductCatalog::where('category', 'like', '%Jam%')->where('size', '1kg Jar')
            ->update(['mrp' => 280.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'Orange Marmalade')->where('size', '500g Jar')
            ->update(['mrp' => 200.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 7. MIX DRY FRUIT HONEY 500GM (₹400, Box 12)
        ProductCatalog::where('product_name', 'like', '%Mix Dry Fruits with Honey%')
            ->update(['size' => '500g Jar', 'mrp' => 400.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        // Plain dry fruit packs without honey remain inactive
        ProductCatalog::where('product_name', 'like', '%Mix Dry Fruits Pack%')->update(['is_active' => 0]);
        ProductCatalog::where('product_name', 'like', '%Badam%')->update(['is_active' => 0]);

        // 8. SQUASHES: Standard 700ML Bottle (₹200, Box 12)
        $mixSquashes = [
            'Alphonso Mango Squash',
            'Raw Mango Squash',
            'Orange Squash',
            'Pineapple Squash',
            'Lime Ginger Squash',
            'Mixed Fruit Squash',
            'Amala Squash',
            'Pure Amla Juice',
        ];
        foreach ($mixSquashes as $sq) {
            ProductCatalog::where('product_name', $sq)
                ->where('size', '!=', '5 Litre Can')
                ->update(['size' => '700ml Bottle', 'mrp' => 200.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
            // Deactivate 5L Cans
            ProductCatalog::where('product_name', $sq)->where('size', '5 Litre Can')
                ->update(['is_active' => 0]);
        }
        ProductCatalog::where('product_name', 'Karela Jamun Amla Juice')->update(['is_active' => 0]);

        // 9. PASSION FRUIT SQUASH 700ML (₹250, Box 12)
        ProductCatalog::where('product_name', 'like', '%Passion%')
            ->update(['size' => '700ml Bottle', 'mrp' => 250.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 10. KESAR ELAICHI SYRUP 700ML (₹250, Box 12)
        ProductCatalog::where('product_name', 'like', '%Kesar Elaichi%')->where('size', '700ml Bottle')
            ->update(['mrp' => 250.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Kesar Elaichi%')
            ->whereIn('size', ['200ml Bottle', '500ml Bottle'])
            ->update(['is_active' => 0]);

        // 11. APPETIZER (PACHAK) 500ML (₹150, Box 15)
        ProductCatalog::where('product_name', 'like', '%Appetizer%')->where('id', 62)
            ->update(['size' => '500ml Bottle', 'mrp' => 150.00, 'units_per_box' => 15, 'min_order_qty' => 15, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Appetizer%')->where('id', 63)
            ->update(['is_active' => 0]); // 5L Can inactive

        // 12. JAMUN CRUSH 700ML (₹300, Box 12)
        ProductCatalog::where('product_name', 'like', '%Jamun%')->where('id', 43)
            ->update(['product_name' => 'Jamun Crush', 'size' => '700ml Bottle', 'mrp' => 300.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 13. FRUIT SYRUP MOJITO 700ML (₹250, Box 12)
        ProductCatalog::where('product_name', 'like', '%Mojito%')
            ->update(['size' => '700ml Bottle', 'mrp' => 250.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 14. BLUE CURACAO SYRUP 700ML (₹250, Box 12)
        ProductCatalog::where('product_name', 'like', '%Blue Curacao%')
            ->update(['size' => '700ml Bottle', 'mrp' => 250.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);

        // 15. OTHER 700ML SYRUPS (₹200, Box 12)
        $syrups700 = [
            'Rose Falooda Syrup',
            'Kala Khatta Syrup',
            'Paan Shot Syrup',
            'Lime Syrup',
            'Raspberry Syrup',
            'Zeera Cordial',
        ];
        foreach ($syrups700 as $s) {
            ProductCatalog::where('product_name', $s)
                ->update(['size' => '700ml Bottle', 'mrp' => 200.00, 'units_per_box' => 12, 'min_order_qty' => 12, 'is_active' => 1]);
        }

        // 16. JELLIES: min 10 units
        // Chocolate Jellies (100g = ₹75, 200g = ₹125, 400g = ₹175)
        ProductCatalog::where('product_name', 'like', '%Novelty Jellies%')
            ->update(['size' => '100g Pack', 'mrp' => 75.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Purse Pack%')
            ->update(['size' => '200g Pack', 'mrp' => 125.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'like', '%Assorted Box%')
            ->update(['size' => '400g Box', 'mrp' => 175.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);
        // Cube Jellies (100g = ₹75, 200g = ₹125, 300g = ₹175)
        ProductCatalog::where('product_name', 'Cub Jelly')->where('size', 'like', '%100g%')
            ->update(['size' => '100g Cup', 'mrp' => 75.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'Cub Jelly')->where('size', 'like', '%200g%')
            ->update(['size' => '200g Cup', 'mrp' => 125.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);
        ProductCatalog::where('product_name', 'Cub Jelly')->where('size', 'like', '%300g%')
            ->update(['size' => '300g Cup', 'mrp' => 175.00, 'units_per_box' => 10, 'min_order_qty' => 10, 'is_active' => 1]);

        // 17. DEACTIVATE UNLISTED / TEST ITEMS
        ProductCatalog::where('product_name', 'like', '%red chilly s ravi%')->update(['is_active' => 0]);
        ProductCatalog::where('product_name', 'like', '%Kiwi Crush%')->update(['is_active' => 0]);
        ProductCatalog::whereIn('product_name', [
            'Tomato Ketchup',
            'Tomato Sauce',
            'Green Chilli Sauce',
            'Red Chilli Sauce',
            'Synthetic White Vinegar',
        ])->update(['is_active' => 0]);
    }
}
