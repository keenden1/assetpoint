<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * The full BW pricelist: category => [SKU prefix, [[name, SRP], ...]].
     *
     * Each sheet section is its own category so duplicate names (e.g. the
     * same cake in 7-in, rectangle, and bundle versions) stay distinct.
     */
    private const CATALOG = [
        'LOAVES' => ['LOAVES', [
            ['PULLMAN LOAF', 82],
            ['UBE LOAF', 73],
            ['MONGO LOAF', 60],
            ['RAISIN LOAF', 60],
            ['JUMBO LOAF', 89],
            ['WHOLE WHEAT LOAF', 71],
            ['SAKTO LOAF', 59],
        ]],
        'BREADS' => ['BREADS', [
            ['CHEESY ENSAYMADA', 45],
            ['UBE ENSAYMADA', 49],
            ['DULCE ENSAYMADA', 49],
            ['ASADO BUN', 39],
            ['WHOLE WHEAT TUNA BUN', 39],
            ['PANDESAL PACK (10 PCS)', 49],
            ['CHEESE BREAD PACK (8 PCS)', 69],
            ['UBE CHEESE PANDESAL (8 PCS)', 135],
            ['CHOCOLATE CROISSANT', 69],
            ['ASADO ROLL', 199],
            ['NEW: CINNAMON BREAD STICKS', 100],
            ['NEW: CINNAMON ROLLS', 79],
        ]],
        'BW DONUT' => ['DONUT', [
            ['CHOCO BUTTERNUT', 29],
            ['CHOCO LAVA', 29],
        ]],
        'PASALUBONG' => ['PASAL', [
            ['EGGNOG', 89],
            ['FUDGE BROWNIES', 84],
            ['CARAMEL BARS', 84],
            ['CHEESE BREAD CHIPS', 69],
            ['GARLIC BREAD CHIPS', 69],
            ['TOASTED BREAD', 55],
            ['ORIGINAL PIAYA', 69],
            ['CHOCO CHIPS', 49],
            ['MIXED BREAD CHIPS', 49],
            ['SHORT BREAD COOKIES', 39],
            ['BUTTER TOAST BIG', 39],
            ['REVEL BITES (PC.)', 18],
            ['REVEL BITES (BOX) 8 PCS', 129],
            ['BOAT TART (PC)', 19],
            ['BOAT TART (BOX) 15 PCS', 249],
            ['GARLIC NUTS', 95],
            ['BUTTER BISCUITS', 99],
            ['BUTTER BISCUITS PP', 28],
            ['SPECIAL BISCOCHO', 55],
        ]],
        'LOAF PLUS SECTION' => ['LOAFPLUS', [
            ['CHEESE WHIZ', 94],
            ['MAYO', 99],
            ['PEANUT BUTTER', 85],
        ]],
        'HOT BEVERAGE' => ['HOTBEV', [
            ['COFFEE AMERICANO | 8 OZ', 27],
            ['COFFEE CAPPUCINO | 8 OZ', 27],
            ['COFFEE CHOCOLATE | 8 OZ', 27],
        ]],
        'CHINESE DELICACIES' => ['CHIDEL', [
            ['HOPIA BABOY', 55],
            ['HOPIA RED MONGO', 55],
            ['HOPIA HAPON', 55],
            ['HOPIA MONGO', 55],
            ['HOPIA UBE', 55],
            ['HOPIA PASTILLAS', 40],
            ['PINEAPPLE CAKE (SINGLE)', 29],
            ['PINEAPPLE CAKE (BOX)', 379],
        ]],
        'PASTRIES' => ['PASTRY', [
            ['EGG PIE', 35],
            ['UBE EGG PIE', 40],
            ['CHOCO EGG PIE', 40],
            ['EGG PIE LITE', 40],
            ['BANANA SLICE', 25],
            ['BUTTER CUP', 25],
            ['YEMA CAKE', 25],
            ['CHAM CHAM CAKE', 23],
            ['SPECIAL MAMON', 36],
            ['ORIGINAL BROWNIES', 32],
            ['GOLDEN MINI MAMON', 35],
            ['MINI MACAROONS', 39],
            ['TIGER ROLL', 45],
        ]],
        'SNACKS ON THE GO' => ['SNACKS', [
            ['HAM AND CHEESE', 32],
            ['CHICKEN EMPANADA', 32],
        ]],
        'BOTTLED DRINKS' => ['DRINKS', [
            ['COKE 1.5L', 85],
        ]],
        'ROUND CAKES (7 IN)' => ['RCAKE7', [
            ['FAMOUS MOCHA CAKE', 359],
            ['DOUBLE CHOCO CAKE', 439],
            ['DELISH VANILLA CAKE', 399],
            ['DUO CAKE UBE-YEMA CAKE', 449],
            ['HEAVENLY CARAMEL', 429],
            ['COOKIE DREAM CAKE', 549],
            ['LEMON CRÈME CAKE', 395],
        ]],
        'SAKTO CAKES (4 IN)' => ['SAKTO4', [
            ['RED VELVET SAKTO CAKE', 286],
            ['CARROT SAKTO CAKE', 286],
            ['BLUEBERRY SAKTO CAKE', 286],
            ['CHOCOLATE SAKTO CAKE', 286],
        ]],
        'CHILLED BITES' => ['CHILLED', [
            ['CUSTARD CAKE', 189],
            ['CREAMY YEMA CAKE', 185],
            ['FLANTASTIC LECHE FLAN, SPECIAL', 119],
            ['SINFUL SLICE', 87],
            ['SANS RIVAL SLICE', 87],
            ['MINI LECHE FLAN', 29],
        ]],
        'RECTANGLE CAKES (8X11 IN)' => ['RCAKE811', [
            ['FAMOUS MOCHA CAKE', 549],
            ['DOUBLE CHOCO CAKE', 639],
            ['DUO CAKE UBE-YEMA CAKE', 629],
        ]],
        'ROLL CAKES' => ['ROLL', [
            ['SUPREME CHOCOLATE ROLL', 369],
            ['BRAZO DE MERCEDEZ - HALF ROLL', 239],
        ]],
        'FBC ROUND' => ['FBCR', [
            ['FAMOUS MOCHA CAKE', 490],
            ['DOUBLE CHOCO CAKE', 565],
            ['DELISH VANILLA CAKE', 399],
            ['DUO CAKE UBE-YEMA CAKE', 579],
            ['HEAVENLY CARAMEL', 617],
            ['COOKIE DREAM CAKE', 674],
            ['LEMON CRÈME CAKE', 583],
        ]],
        'FBC RECTANGULAR' => ['FBCRT', [
            ['FAMOUS MOCHA CAKE, 8X11"', 680],
            ['DOUBLE CHOCO CAKE, 8X11"', 765],
            ['DUO CAKE UBE-YEMA CAKE', 760],
        ]],
        'REGULAR PROMO - BREADS' => ['PROMO', [
            ['PULLMAN BUNDLE', 109],
            ['JUMBO LOAF BUNDLE', 139],
            ['TRIO - CLASSIC', 169],
            ['TRIO - DOUBLE UBE', 179],
            ['HEALTHY BUNDLE', 139],
        ]],
    ];

    /**
     * Sync the catalog above into the database.
     *
     * Idempotent: upserts by SKU, then removes any product whose SKU is no
     * longer in the catalog (stale sample/renamed rows).
     */
    public function run(): void
    {
        $keptSkus = [];

        foreach (self::CATALOG as $categoryName => [$prefix, $products]) {
            $category = Category::updateOrCreate(['name' => $categoryName]);

            foreach ($products as $index => [$name, $price]) {
                $sku = sprintf('%s-%03d', $prefix, $index + 1);
                $keptSkus[] = $sku;

                Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'price' => $price,
                        'status' => 'active',
                    ],
                );
            }
        }

        Product::whereNotIn('sku', $keptSkus)->delete();
    }
}
