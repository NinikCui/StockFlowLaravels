<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BomSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = 1;

        // ================= ITEM =================
        $items = Item::where('company_id', $companyId)
            ->pluck('id', 'name');

        // ================= PRODUCT =================
        $products = Product::where('company_id', $companyId)
            ->pluck('id', 'code');

        // helper aman
        $pid = fn ($code) => $products[$code] ?? null;
        $iid = fn ($name) => $items[$name] ?? null;

        $boms = [

            // ================= NASI =================
            ['MENU-NG',  'Beras', 0.15],
            ['MENU-NG',  'Telur Ayam', 1],
            ['MENU-NG',  'Minyak Goreng', 0.02],
            ['MENU-NG',  'Garam', 3],

            ['MENU-NGA', 'Beras', 0.15],
            ['MENU-NGA', 'Ayam', 0.1],
            ['MENU-NGA', 'Minyak Goreng', 0.02],

            ['MENU-NGT', 'Beras', 0.15],
            ['MENU-NGT', 'Telur Ayam', 2],
            ['MENU-NGT', 'Minyak Goreng', 0.02],

            ['MENU-NAG', 'Beras', 0.15],
            ['MENU-NAG', 'Ayam', 0.15],

            ['MENU-NAB', 'Beras', 0.15],
            ['MENU-NAB', 'Ayam', 0.15],

            // ================= MIE =================
            ['MENU-MG',  'Mie Kering', 1],
            ['MENU-MG',  'Telur Ayam', 1],

            ['MENU-MGA', 'Mie Kering', 1],
            ['MENU-MGA', 'Ayam', 0.1],

            ['MENU-MGT', 'Mie Kering', 1],
            ['MENU-MGT', 'Telur Ayam', 2],

            // ================= MINUMAN =================
            ['MENU-ETM', 'Teh Celup', 1],
            ['MENU-ETM', 'Gula Pasir', 0.02],

            ['MENU-TH',  'Teh Celup', 1],
            ['MENU-TH',  'Gula Pasir', 0.01],

            ['MENU-KH',  'Kopi Bubuk', 0.015],
            ['MENU-KH',  'Gula Pasir', 0.01],

            ['MENU-EK',  'Kopi Bubuk', 0.015],
            ['MENU-EK',  'Gula Pasir', 0.015],

            ['MENU-ES',  'Sirup', 0.05],
        ];

        foreach ($boms as [$code, $itemName, $qty]) {
            if (! $pid($code) || ! $iid($itemName)) {
                continue; // skip aman kalau data belum ada
            }

            DB::table('boms')->insert([
                'company_id' => $companyId,
                'product_id' => $pid($code),
                'item_id' => $iid($itemName),
                'qty_per_unit' => $qty,
            ]);
        }
    }
}
