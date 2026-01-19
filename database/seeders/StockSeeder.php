<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        foreach (Warehouse::all() as $warehouse) {

            $items = Item::inRandomOrder()->take(3)->get();

            foreach ($items as $item) {

                // stok normal
                Stock::create([
                    'code' => 'STK-'.strtoupper(Str::random(6)),
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id,
                    'item_id' => $item->id,
                    'qty' => rand(20, 100),
                    'expired_at' => Carbon::now()->addDays(rand(7, 180)),
                ]);

                // multi-batch (FEFO)
                Stock::create([
                    'code' => 'STK-'.strtoupper(Str::random(6)),
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id,
                    'item_id' => $item->id,
                    'qty' => rand(5, 30),
                    'expired_at' => Carbon::now()->addDays(rand(3, 30)),
                ]);
            }

            // stok habis
            Stock::create([
                'code' => 'STK-ZERO-'.strtoupper(Str::random(4)),
                'company_id' => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_id' => Item::inRandomOrder()->first()->id,
                'qty' => 0,
                'expired_at' => Carbon::now()->addDays(30),
            ]);

            // stok expired
            Stock::create([
                'code' => 'STK-EXP-'.strtoupper(Str::random(4)),
                'company_id' => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_id' => Item::inRandomOrder()->first()->id,
                'qty' => rand(5, 20),
                'expired_at' => Carbon::now()->subDays(2),
            ]);
        }
    }
}
