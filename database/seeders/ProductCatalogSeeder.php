<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_catalog')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = [
            // 1. KOKUM & AGAL RANGE
            [
                'category' => 'Kokum & Agal',
                'name' => 'Kokum Squash',
                'description' => 'Authentic Goan Kokum Squash prepared with pure kokum juice, cumin, black salt, and natural spices. Aids digestion and body cooling.',
                'image' => 'kokum-squash.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '500ml Bottle', 'price' => 40.00],
                    ['size' => '500ml Can', 'price' => 50.00],
                    ['size' => '700ml Bottle', 'price' => 60.00],
                    ['size' => '1 Litre Bottle', 'price' => 75.00],
                    ['size' => '5 Litre Can', 'price' => 300.00],
                    ['size' => '35 Litre Drum', 'price' => 2450.00],
                ]
            ],
            [
                'category' => 'Kokum & Agal',
                'name' => 'Kokum Agal (Pure Extract)',
                'description' => '100% pure unsweetened Goan Kokum extract with natural salt. Essential for Sol Kadhi, fish curries, sambar, and traditional coastal recipes.',
                'image' => 'kokum-agal.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '500ml Bottle', 'price' => 35.00],
                    ['size' => '500ml Can', 'price' => 40.00],
                    ['size' => '700ml Bottle', 'price' => 50.00],
                    ['size' => '1 Litre Bottle', 'price' => 75.00],
                    ['size' => '5 Litre Can', 'price' => 300.00],
                    ['size' => '35 Litre Drum', 'price' => 2450.00],
                ]
            ],
            [
                'category' => 'Kokum & Agal',
                'name' => 'Kokum Juice with Whole Fruits',
                'description' => 'Natural refreshing Kokum drink infused with authentic sun-ripened fruit rinds.',
                'image' => 'kokum-juice-whole-fruits.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500ml Bottle', 'price' => 45.00],
                    ['size' => '1 Litre Bottle', 'price' => 80.00],
                ]
            ],
            [
                'category' => 'Kokum & Agal',
                'name' => 'Sweet Kokam',
                'description' => 'Sun-dried Kokum rinds soaked in rich sugar syrup. A delightful sweet and tangy digestive mouth freshener.',
                'image' => 'sweet-kokam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '100g Pack', 'price' => 40.00],
                    ['size' => '200g Pack', 'price' => 75.00],
                ]
            ],
            [
                'category' => 'Kokum & Agal',
                'name' => 'Kokum Sol (Raw / Regular)',
                'description' => 'Handpicked sun-dried kokum petals for traditional Goan fish curries and medicinal decoctions.',
                'image' => 'kokum-sol.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => 'Standard Pack', 'price' => 50.00],
                ]
            ],
            [
                'category' => 'Kokum & Agal',
                'name' => 'Kokum Sol (First Quality)',
                'description' => 'Premium grade dark whole kokum rinds with maximum natural acidity and tart flavor.',
                'image' => 'kokum-sol.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => 'Standard Pack', 'price' => 70.00],
                ]
            ],

            // 2. CASHEW & FRUIT JAMS
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Mango Jam',
                'description' => 'Signature blend of sweet Konkan mangoes and crunchy roasted cashew nuts. Rich, nutty, and 100% vegetarian.',
                'image' => 'cashew-mango-jam.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 120.00],
                    ['size' => '1kg Jar', 'price' => 220.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Strawberry Jam',
                'description' => 'Slow-cooked juicy strawberries blended with whole crunchy cashews.',
                'image' => 'cashew-strawberry-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 120.00],
                    ['size' => '1kg Jar', 'price' => 220.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Pineapple Jam',
                'description' => 'Tropical delight combining natural pineapple chunks with roasted cashews.',
                'image' => 'cashew-pineapple-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 120.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Litchi Jam',
                'description' => 'Aromatic litchi pulp loaded with crunchy roasted cashew pieces.',
                'image' => 'cashew-litchi-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 125.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Chocolate Jam',
                'description' => 'Rich cocoa spread loaded with nutritious cashew nuts for breakfasts and desserts.',
                'image' => 'cashew-chocolate-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 140.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Cashew Vanilla Jam',
                'description' => 'Delicate sweet vanilla cream jam infused with fine cashew crunch.',
                'image' => 'cashew-vanilla-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 130.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Mango + Strawberry Jam',
                'description' => 'Dual-fruit delight combining Alphonso mango and fresh strawberries.',
                'image' => 'mango-strawberry-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1kg Jar', 'price' => 190.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Mango + Pineapple Jam',
                'description' => 'Tangy-sweet fruit conserve made from real mangoes and pineapples.',
                'image' => 'mango-pineapple-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 100.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Pineapple + Strawberry Jam',
                'description' => 'Wholesome breakfast jam crafted with natural strawberries and pineapples.',
                'image' => 'pineapple-strawberry-jam.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1kg Jar', 'price' => 190.00],
                ]
            ],
            [
                'category' => 'Cashew & Fruit Jams',
                'name' => 'Orange Marmalade',
                'description' => 'Classic bitter-sweet orange conserve with natural fruit peel shreds.',
                'image' => 'orange-marmalade.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Jar', 'price' => 110.00],
                ]
            ],

            // 3. SQUASHES & HEALTH JUICES
            [
                'category' => 'Squashes & Juices',
                'name' => 'Alphonso Mango Squash',
                'description' => 'Rich concentrate prepared from ripe Goan Alphonso mangoes.',
                'image' => 'mango-squash.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 85.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Raw Mango Squash',
                'description' => 'Refreshing raw green mango cooler with cumin and cooling spices.',
                'image' => 'raw-mango-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 80.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Orange Squash',
                'description' => 'Zesty citrus squash packed with Vitamin C for instant refreshment.',
                'image' => 'orange-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 75.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Pineapple Squash',
                'description' => 'Tropical squash made from real pineapple pulp. Ideal for mocktails and party coolers.',
                'image' => 'pineapple-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 75.00],
                    ['size' => '5 Litre Can', 'price' => 320.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Passion Fruit Squash',
                'description' => 'Burst of exotic tropical flavor crafted from pure passion fruit pulp.',
                'image' => 'passion-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 95.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Lime Ginger Squash',
                'description' => 'Digestive citrus tonic made with freshly pressed lemons and fresh ginger root.',
                'image' => 'lime-ginger-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 75.00],
                    ['size' => '5 Litre Can', 'price' => 310.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Mixed Fruit Squash',
                'description' => 'Wholesome multi-fruit concentrate blending citrus, mango, and tropical juices.',
                'image' => 'mixed-fruit-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 65.00],
                    ['size' => '5 Litre Can', 'price' => 310.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Pure Amla Juice',
                'description' => '100% pure Indian Gooseberry juice with zero added sugar.',
                'image' => 'amala-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 110.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Amala Squash',
                'description' => 'Sweetened Amla and ginger concentrate for daily energy and digestion.',
                'image' => 'amala-squash.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 85.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Jamun Juice',
                'description' => 'Pure Black Plum juice for blood sugar management and detox.',
                'image' => 'jamun-juice.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 120.00],
                ]
            ],
            [
                'category' => 'Squashes & Juices',
                'name' => 'Karela Jamun Amla Juice',
                'description' => 'Diabetic-friendly Ayurvedic wellness formula combining Bitter Gourd, Jamun, and Amla.',
                'image' => 'karela-jamun-amla-juice.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 135.00],
                ]
            ],

            // 4. SYRUPS & CRUSHES
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Kesar Elaichi Syrup',
                'description' => 'Royal dessert syrup infused with saffron strands and aromatic cardamom.',
                'image' => 'kesar-elaichi-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '200ml Bottle', 'price' => 50.00],
                    ['size' => '500ml Bottle', 'price' => 95.00],
                    ['size' => '700ml Bottle', 'price' => 130.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Rose Falooda Syrup',
                'description' => 'Classic aromatic pink rose syrup for milkshakes, lassi, and falooda.',
                'image' => 'rose-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 85.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Red Rose Syrup',
                'description' => 'Intense red rose syrup for desserts, mocktails, and milk drinks.',
                'image' => 'rose-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 85.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Kala Khatta Syrup',
                'description' => 'Authentic tangy-sweet Kala Khatta fruit syrup for golas and soda mocktails.',
                'image' => 'kala-khatta-syrup.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 75.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Paan Shot Syrup',
                'description' => 'Refreshing Betel Leaf crush with natural gulkand and digestive spices.',
                'image' => 'paan-shot-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 90.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Lime Syrup',
                'description' => 'Sweet and tangy lemon syrup for instant soda and water coolers.',
                'image' => 'lime-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 70.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Blue Curacao Syrup',
                'description' => 'Vibrant citrus-infused blue mixer for mocktails and party coolers.',
                'image' => 'blue-curacao-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 95.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Raspberry Syrup',
                'description' => 'Sweet-tart berry syrup for sodas, cocktails, and dessert drizzles.',
                'image' => 'raspberry-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 80.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Mojito Mint Syrup',
                'description' => 'Crisp garden mint and lime concentrate for instant virgin mojitos.',
                'image' => 'mojito-mint-syrup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 90.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Strawberry Whole Fruit Crush',
                'description' => 'Real strawberry crush packed with whole fruit pieces.',
                'image' => 'strawberry-crush.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Bottle', 'price' => 85.00],
                    ['size' => '1 Litre Bottle', 'price' => 150.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Kiwi Crush',
                'description' => 'Tangy kiwi pulp with natural seeds for gourmet beverages.',
                'image' => 'kiwi-crush.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 160.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Litchi Crush',
                'description' => 'Juicy litchi fruit concentrate for mocktails and ice creams.',
                'image' => 'litchi-crush.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 155.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Keshar Kulfi Crush',
                'description' => 'Traditional saffron and mawa flavor crush for milkshakes.',
                'image' => 'keshar-kulfi-crush.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 170.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Zeera Cordial',
                'description' => 'Digestive cumin and lemon drink concentrate.',
                'image' => 'zeera-cordial.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 75.00],
                ]
            ],
            [
                'category' => 'Syrups & Crushes',
                'name' => 'Appetizer (Pachak) Crush',
                'description' => 'Traditional Goan digestive pachak made with lime, ginger, amla, cumin, and black pepper.',
                'image' => 'appetizer-crush.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '1 Litre Bottle', 'price' => 85.00],
                    ['size' => '5 Litre Can', 'price' => 350.00],
                ]
            ],

            // 5. DRY FRUITS & HONEY
            [
                'category' => 'Dry Fruits & Honey',
                'name' => 'Mix Dry Fruits with Honey',
                'description' => 'Almonds, cashews, pistachios, walnuts, dates, and figs in pure forest honey.',
                'image' => 'mix-dry-fruits-honey.webp',
                'is_featured' => 1,
                'variants' => [
                    ['size' => '250g Jar', 'price' => 180.00],
                ]
            ],
            [
                'category' => 'Dry Fruits & Honey',
                'name' => 'Mix Dry Fruits Pack',
                'description' => 'Handpicked premium mixed nuts for wholesome daily snacking.',
                'image' => 'mix-dry-fruits.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '200g Pack', 'price' => 150.00],
                    ['size' => '250g Pack', 'price' => 185.00],
                    ['size' => '400g Pack', 'price' => 290.00],
                    ['size' => '500g Pack', 'price' => 360.00],
                ]
            ],
            [
                'category' => 'Dry Fruits & Honey',
                'name' => 'Badam / Almonds',
                'description' => 'Crisp, premium grade California almonds packed for freshness.',
                'image' => 'badam-almonds.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Pack', 'price' => 420.00],
                ]
            ],

            // 6. NOVELTY JELLIES
            [
                'category' => 'Jellies & Confectionery',
                'name' => 'Novelty Jellies (Bat / Spider / Bucket / Tiffin)',
                'description' => 'Fun shaped fruit jellies in assorted flavours loved by children.',
                'image' => 'novelty-jellies.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '100g Pack', 'price' => 30.00],
                ]
            ],
            [
                'category' => 'Jellies & Confectionery',
                'name' => 'Purse Pack Jelly',
                'description' => 'Assorted soft fruit jelly candies in an attractive purse gift pack.',
                'image' => 'purse-jelly.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '200g Pack', 'price' => 60.00],
                ]
            ],
            [
                'category' => 'Jellies & Confectionery',
                'name' => 'Assorted Box Jelly',
                'description' => 'Premium assorted fruit jelly bites in a family gift box.',
                'image' => 'assorted-box-jelly.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '400g Box', 'price' => 120.00],
                ]
            ],
            [
                'category' => 'Jellies & Confectionery',
                'name' => 'Cub Jelly',
                'description' => 'Classic mini fruit jelly cups with natural fruit juices.',
                'image' => 'cup-jelly.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '100g Cup', 'price' => 25.00],
                    ['size' => '200g Cup', 'price' => 45.00],
                    ['size' => '300g Cup', 'price' => 65.00],
                ]
            ],

            // 7. CULINARY SAUCES & VINEGAR
            [
                'category' => 'Culinary & Sauces',
                'name' => 'Tomato Ketchup',
                'description' => 'Rich and thick tomato ketchup made with ripe red tomatoes.',
                'image' => 'tomato-ketchup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Bottle', 'price' => 55.00],
                ]
            ],
            [
                'category' => 'Culinary & Sauces',
                'name' => 'Tomato Sauce',
                'description' => 'All-purpose zesty tomato table sauce for snacks and cooking.',
                'image' => 'tomato-ketchup.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '1kg Bottle / Pouch', 'price' => 90.00],
                ]
            ],
            [
                'category' => 'Culinary & Sauces',
                'name' => 'Green Chilli Sauce',
                'description' => 'Spicy and tangy green pepper sauce for cooking and snacks.',
                'image' => 'green-chilli-sauce.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Bottle', 'price' => 50.00],
                ]
            ],
            [
                'category' => 'Culinary & Sauces',
                'name' => 'Red Chilli Sauce',
                'description' => 'Fiery red chilli condiment prepared with hot peppers and vinegar.',
                'image' => 'red-chilli-sauce.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '500g Bottle', 'price' => 50.00],
                ]
            ],
            [
                'category' => 'Culinary & Sauces',
                'name' => 'Synthetic White Vinegar',
                'description' => 'Multi-purpose food grade vinegar for pickling, marinades, and Goan vindaloo gravies.',
                'image' => 'white-vinegar.webp',
                'is_featured' => 0,
                'variants' => [
                    ['size' => '700ml Bottle', 'price' => 35.00],
                ]
            ],
        ];

        foreach ($products as $p) {
            $slug = Str::slug($p['name']);

            foreach ($p['variants'] as $v) {
                DB::table('product_catalog')->insert([
                    'category' => $p['category'],
                    'product_name' => $p['name'],
                    'slug' => $slug,
                    'description' => $p['description'],
                    'size' => $v['size'],
                    'price' => $v['price'],
                    'image' => $p['image'],
                    'is_featured' => $p['is_featured'],
                    'in_stock' => 1,
                    'is_active' => 1,
                ]);
            }
        }
    }
}