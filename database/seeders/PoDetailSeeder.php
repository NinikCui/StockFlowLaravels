<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoDetailSeeder extends Seeder
{
    public function run(): void
    {
        $purchaseOrders = DB::table('purchase_order')->get();
        $items = Item::all();

        if ($purchaseOrders->isEmpty() || $items->isEmpty()) {
            return;
        }

        foreach ($purchaseOrders as $po) {

            $itemCount = $items->count();
            $take = min($itemCount, rand(2, 3));

            $selectedItems = $items->random($take);

            foreach ($selectedItems as $item) {

                DB::table('po_detail')->insert([
                    'purchase_order_id' => $po->id,
                    'items_id' => $item->id,
                    'qty_ordered' => rand(10, 50),
                    'unit_price' => rand(10000, 50000),
                    'quality' => 5, // skala 1-5
                    'conversion_to_stock' => 1, // 1:1 ke stok
                    'discount_pct' => rand(0, 10),
                    'note_line' => 'Item sesuai standar',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
