<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierItemSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $items = Item::all();

        if ($suppliers->isEmpty() || $items->isEmpty()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | KASUS 1: SUPPLIER UTAMA → BANYAK ITEM
        |--------------------------------------------------------------------------
        */
        $mainSupplier = $suppliers->first();

        foreach ($items->take(4) as $item) {
            DB::table('suppliers_item')->insert([
                'suppliers_id' => $mainSupplier->id,
                'items_id' => $item->id,
                'price' => rand(10000, 50000),
                'min_order_qty' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KASUS 2: SUPPLIER ALTERNATIF
        |--------------------------------------------------------------------------
        */
        foreach ($suppliers->skip(1)->take(2) as $supplier) {
            foreach ($items->random(2) as $item) {
                DB::table('suppliers_item')->insert([
                    'suppliers_id' => $supplier->id,
                    'items_id' => $item->id,
                    'price' => rand(12000, 60000),
                    'min_order_qty' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | KASUS 4: ITEM DENGAN MULTI SUPPLIER
        |--------------------------------------------------------------------------
        */
        $item = $items->first();

        foreach ($suppliers->take(3) as $supplier) {
            DB::table('suppliers_item')->insert([
                'suppliers_id' => $supplier->id,
                'items_id' => $item->id,
                'min_order_qty' => 10,
                'price' => rand(10000, 55000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
