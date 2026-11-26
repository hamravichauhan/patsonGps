<?php

namespace Database\Seeders;

use App\Models\ProductCatalog;
use Illuminate\Database\Seeder;

class ProductPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds exact MRP, Units per Box, and Minimum Order Quantities from Client's Price List.
     */
    public function run(): void
    {
        $products = ProductCatalog::all();

        foreach ($products as $p) {
            $name = strtolower($p->product_name);
            $size = strtolower($p->size);
            $mrp = null;
            $unitsPerBox = $p->units_per_box ?: 12;
            $minOrderQty = 1;

            // 1. KOKAM JUICE WITH WHOLE FRUIT
            if (str_contains($name, 'kokum juice') || str_contains($name, 'kokam juice')) {
                if (str_contains($size, '1 litre') || str_contains($size, '1l')) {
                    $mrp = 250.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '500ml')) {
                    $mrp = 150.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                }
            }

            // 2. KOKAM SQUASH
            elseif ($name === 'kokum squash' || $name === 'kokam squash') {
                if (str_contains($size, '1 litre') || str_contains($size, '1l')) {
                    $mrp = 200.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '500ml')) {
                    $mrp = 150.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '700ml')) {
                    $mrp = 200.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '5 litre') || str_contains($size, '5l')) {
                    $mrp = 600.00;
                    $unitsPerBox = 4;
                    $minOrderQty = 1;
                } elseif (str_contains($size, '35 litre')) {
                    $mrp = 3500.00;
                    $unitsPerBox = 1;
                    $minOrderQty = 1;
                }
            }

            // 3. KOKAM AGAL (PURE EXTRACT)
            elseif (str_contains($name, 'kokum agal') || str_contains($name, 'kokam agal')) {
                if (str_contains($size, '1 litre') || str_contains($size, '1l')) {
                    $mrp = 150.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '500ml')) {
                    $mrp = 120.00;
                    $unitsPerBox = 15; // Sheet specifies 15
                    $minOrderQty = 15;
                } elseif (str_contains($size, '700ml')) {
                    $mrp = 150.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '5 litre') || str_contains($size, '5l')) {
                    $mrp = 600.00;
                    $unitsPerBox = 4;
                    $minOrderQty = 1;
                } elseif (str_contains($size, '35 litre')) {
                    $mrp = 3500.00;
                    $unitsPerBox = 1;
                    $minOrderQty = 1;
                }
            }

            // 4. JAMS (ALL 500G & ALL 1KG)
            elseif (str_contains($name, 'jam') || str_contains($name, 'marmalade')) {
                if (str_contains($size, '1kg')) {
                    $mrp = 280.00; // ALL 1 KG JAM = ₹280
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '500g')) {
                    $mrp = 200.00; // ALL 500GM JAM = ₹200
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                }
            }

            // 5. MIX DRY FRUIT HONEY
            elseif (str_contains($name, 'dry fruit') && str_contains($name, 'honey')) {
                if (str_contains($size, '500g') || str_contains($size, '500')) {
                    $mrp = 400.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } else {
                    $mrp = 220.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                }
            }

            // 6. SWEET KOKAM
            elseif (str_contains($name, 'sweet kokam') || str_contains($name, 'sweet kokum')) {
                if (str_contains($size, '100g')) {
                    $mrp = 120.00;
                    $minOrderQty = 25; // customize (25) min
                    $unitsPerBox = 25;
                } else {
                    $mrp = 220.00;
                    $minOrderQty = 15;
                    $unitsPerBox = 15;
                }
            }

            // 7. KOKAM SOAL (DRY KOKAM)
            elseif (str_contains($name, 'kokum sol') || str_contains($name, 'kokam soal') || str_contains($name, 'dry kokam')) {
                $mrp = 500.00;
                $minOrderQty = 1; // 250gm min pack
                $unitsPerBox = 12;
            }

            // 8. KESAR ELAICHI SYRUP 700ML
            elseif (str_contains($name, 'kesar elaichi')) {
                if (str_contains($size, '700ml')) {
                    $mrp = 250.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } elseif (str_contains($size, '500ml')) {
                    $mrp = 180.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } else {
                    $mrp = 90.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                }
            }

            // 9. APPETIZER (PACHAK)
            elseif (str_contains($name, 'appetizer') || str_contains($name, 'pachak')) {
                if (str_contains($size, '500ml')) {
                    $mrp = 150.00;
                    $unitsPerBox = 15; // Sheet specifies 15
                    $minOrderQty = 15;
                } elseif (str_contains($size, '1 litre')) {
                    $mrp = 220.00;
                    $unitsPerBox = 12;
                    $minOrderQty = 12;
                } else {
                    $mrp = 650.00;
                    $unitsPerBox = 4;
                    $minOrderQty = 1;
                }
            }

            // 10. JELLIES (NOVELTY / PURSE / ASSORTED / CUBE)
            elseif (str_contains($name, 'jelly') || str_contains($name, 'cub jelly')) {
                if (str_contains($size, '100g') || str_contains($size, '100')) {
                    $mrp = 75.00;
                    $minOrderQty = 10; // min (10)
                    $unitsPerBox = 10;
                } elseif (str_contains($size, '200g') || str_contains($size, '200')) {
                    $mrp = 125.00;
                    $minOrderQty = 10;
                    $unitsPerBox = 10;
                } elseif (str_contains($size, '300g') || str_contains($size, '300')) {
                    $mrp = 175.00;
                    $minOrderQty = 10;
                    $unitsPerBox = 10;
                } elseif (str_contains($size, '400g') || str_contains($size, '400')) {
                    $mrp = 175.00;
                    $minOrderQty = 10;
                    $unitsPerBox = 10;
                } else {
                    $mrp = 100.00;
                    $minOrderQty = 10;
                    $unitsPerBox = 10;
                }
            }

            // 11. JAMUN CRUSH / JUICE 700ML
            elseif (str_contains($name, 'jamun')) {
                $mrp = 300.00;
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // 12. FRUIT SYRUP MOJITO 700ML
            elseif (str_contains($name, 'mojito')) {
                $mrp = 250.00;
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // 13. PASSION FRUIT SQUASH 700ML
            elseif (str_contains($name, 'passion')) {
                $mrp = 250.00;
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // 14. BLUE CURACAO SYRUP 700ML
            elseif (str_contains($name, 'blue curacao') || str_contains($name, 'blue caracao')) {
                $mrp = 250.00;
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // 15. ALL MIX 700ML SQUASHES & SYRUPS
            elseif (str_contains($size, '700ml')) {
                $mrp = 200.00; // ALL MIX 700ML SQUASHS = ₹200
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // 16. SQUASHES & JUICES (1 LITRE)
            elseif (str_contains($size, '1 litre') && (str_contains($name, 'squash') || str_contains($name, 'juice'))) {
                $mrp = 200.00;
                $unitsPerBox = 12;
                $minOrderQty = 12;
            }

            // FALLBACK FOR OTHER PRODUCTS (Default reasonable MRP: 1.8x - 2.0x wholesale)
            if ($mrp === null) {
                $calculated = round(((float)$p->price * 1.8) / 5) * 5;
                $mrp = max($calculated, (float)$p->price + 20);
            }

            $p->mrp = $mrp;
            $p->units_per_box = $unitsPerBox;
            $p->min_order_qty = $minOrderQty;
            $p->save();
        }
    }
}
