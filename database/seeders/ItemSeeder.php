<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\UnitConversion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = 1;

        DB::transaction(function () use ($now, $companyId) {

            // Ambil kategori
            $bahanUtama = Category::where('name', 'Bahan Utama')->firstOrFail();
            $bumbu = Category::where('name', 'Bumbu')->firstOrFail();
            $minuman = Category::where('name', 'Minuman')->firstOrFail();
            $pelengkap = Category::where('name', 'Pelengkap')->firstOrFail();

            // ======================
            // SATUAN (aman re-run)
            // ======================
            Satuan::upsert([
                ['id' => 1, 'name' => 'Kilogram',   'code' => 'KG',   'company_id' => null],
                ['id' => 2, 'name' => 'Gram',       'code' => 'GR',   'company_id' => null],
                ['id' => 3, 'name' => 'Liter',      'code' => 'LTR',  'company_id' => null],
                ['id' => 4, 'name' => 'Pieces',     'code' => 'PCS',  'company_id' => null],
                ['id' => 5, 'name' => 'Pack',       'code' => 'PACK', 'company_id' => null],
                ['id' => 6, 'name' => 'Butir',      'code' => 'BTR',  'company_id' => null],
                ['id' => 7, 'name' => 'Milliliter', 'code' => 'ML',   'company_id' => null],
            ], ['id'], ['name', 'code', 'company_id']);

            // ======================
            // UNIT CONVERSION (aman re-run)
            // ======================
            UnitConversion::upsert([
                // BERAT
                ['from_satuan_id' => 1, 'to_satuan_id' => 2, 'factor' => 1000],   // KG → GR
                ['from_satuan_id' => 2, 'to_satuan_id' => 1, 'factor' => 0.001], // GR → KG

                // VOLUME
                ['from_satuan_id' => 3, 'to_satuan_id' => 7, 'factor' => 1000],   // LTR → ML
                ['from_satuan_id' => 7, 'to_satuan_id' => 3, 'factor' => 0.001],  // ML → LTR
            ], ['from_satuan_id', 'to_satuan_id'], ['factor']);

            // ======================
            // CABANG LIST (company 1)
            // ======================
            $branchIds = CabangResto::where('company_id', $companyId)->pluck('id');
            $hasBranches = $branchIds->isNotEmpty();

            /**
             * ======================
             * MIN / MAX STOCK PER ITEM
             * ======================
             * Catatan:
             * - Nilai ini dalam satuan dasar item (mengikuti satuan_id pada item)
             * - Bisa kamu ubah sesuai logika bisnismu.
             */
            $minMaxMap = [
                // BAHAN UTAMA
                'Beras' => ['min' => 20,    'max' => 100],  // KG
                'Ayam' => ['min' => 5,     'max' => 30],   // KG
                'Telur Ayam' => ['min' => 30,    'max' => 200],  // BTR

                // BUMBU
                'Garam' => ['min' => 1000,  'max' => 5000], // GR
                'Gula Pasir' => ['min' => 5,     'max' => 25],   // KG
                'Bawang Merah' => ['min' => 2,     'max' => 12],   // KG
                'Bawang Putih' => ['min' => 2,     'max' => 12],   // KG

                // MINUMAN
                'Teh Celup' => ['min' => 5,     'max' => 40],   // PACK
                'Kopi Bubuk' => ['min' => 5,     'max' => 30],   // PACK

                // PELENGKAP
                'Minyak Goreng' => ['min' => 10,   'max' => 60],   // LTR
                'Mie Kering' => ['min' => 20,   'max' => 100],  // PACK
                'Sirup' => ['min' => 5,    'max' => 30],   // LTR
            ];

            // ======================
            // ITEM DATA
            // ======================
            $itemsData = [
                // BAHAN UTAMA
                ['company_id' => $companyId, 'category_id' => $bahanUtama->id, 'satuan_id' => 1, 'name' => 'Beras',      'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 1],
                ['company_id' => $companyId, 'category_id' => $bahanUtama->id, 'satuan_id' => 1, 'name' => 'Ayam',       'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 1],
                ['company_id' => $companyId, 'category_id' => $bahanUtama->id, 'satuan_id' => 6, 'name' => 'Telur Ayam', 'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 1],

                // BUMBU
                ['company_id' => $companyId, 'category_id' => $bumbu->id, 'satuan_id' => 2, 'name' => 'Garam',         'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],
                ['company_id' => $companyId, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Gula Pasir',    'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],
                ['company_id' => $companyId, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Bawang Merah',  'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 0],
                ['company_id' => $companyId, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Bawang Putih',  'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 0],

                // MINUMAN
                ['company_id' => $companyId, 'category_id' => $minuman->id, 'satuan_id' => 5, 'name' => 'Teh Celup',   'forecast_enabled' => 0, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],
                ['company_id' => $companyId, 'category_id' => $minuman->id, 'satuan_id' => 5, 'name' => 'Kopi Bubuk',  'forecast_enabled' => 0, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],

                // PELENGKAP
                ['company_id' => $companyId, 'category_id' => $pelengkap->id, 'satuan_id' => 3, 'name' => 'Minyak Goreng', 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],
                ['company_id' => $companyId, 'category_id' => $pelengkap->id, 'satuan_id' => 5, 'name' => 'Mie Kering',    'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 1],
                ['company_id' => $companyId, 'category_id' => $pelengkap->id, 'satuan_id' => 3, 'name' => 'Sirup',         'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0],
            ];

            // ======================
            // CREATE / UPDATE ITEM + AUTO MIN/MAX STOCK PER CABANG
            // ======================
            foreach ($itemsData as $data) {
                $itemName = $data['name'];

                $item = Item::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'name' => $itemName,
                    ],
                    array_merge($data, [
                        'updated_at' => $now,
                        'created_at' => $now,
                    ])
                );

                if (! $hasBranches) {
                    continue;
                }

                $min = $minMaxMap[$itemName]['min'] ?? 0;
                $max = $minMaxMap[$itemName]['max'] ?? 0;

                // guard biar gak kebalik
                if ($max < $min) {
                    [$min, $max] = [$max, $min];
                }

                $rows = $branchIds->map(function ($branchId) use ($companyId, $item, $min, $max, $now) {
                    return [
                        'company_id' => $companyId,
                        'cabang_resto_id' => $branchId,
                        'item_id' => $item->id,
                        'min_stock' => $min,
                        'max_stock' => $max,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->toArray();

                DB::table('item_branch_min_stocks')->upsert(
                    $rows,
                    ['company_id', 'cabang_resto_id', 'item_id'],
                    ['min_stock', 'max_stock', 'updated_at']
                );
            }
        });
    }
}
