<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\UnitConversion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Ambil kategori
        $bahanUtama = Category::where('name', 'Bahan Utama')->first();
        $bumbu = Category::where('name', 'Bumbu')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $pelengkap = Category::where('name', 'Pelengkap')->first();

        // ======================
        // SATUAN
        // ======================
        Satuan::insert([
            ['id' => 1, 'name' => 'Kilogram', 'code' => 'KG'],
            ['id' => 2, 'name' => 'Gram', 'code' => 'GR'],
            ['id' => 3, 'name' => 'Liter', 'code' => 'LTR'],
            ['id' => 4, 'name' => 'Pieces', 'code' => 'PCS'],
            ['id' => 5, 'name' => 'Pack', 'code' => 'PACK'],
            ['id' => 6, 'name' => 'Butir', 'code' => 'BTR'],
            ['id' => 7, 'name' => 'Milliliter', 'code' => 'ML'],
        ]);

        UnitConversion::insert([
            // BERAT
            ['from_satuan_id' => 1, 'to_satuan_id' => 2, 'factor' => 1000],   // KG → GR
            ['from_satuan_id' => 2, 'to_satuan_id' => 1, 'factor' => 0.001], // GR → KG

            // VOLUME
            ['from_satuan_id' => 3, 'to_satuan_id' => 7, 'factor' => 1000],
            ['from_satuan_id' => 7, 'to_satuan_id' => 3, 'factor' => 0.001],
        ]);

        // ======================
        // ITEM
        // ======================
        Item::insert([

            // ======================
            // BAHAN UTAMA
            // ======================
            [
                'company_id' => 1, 'category_id' => $bahanUtama->id, 'satuan_id' => 1, 'name' => 'Beras',
                'min_stock' => 50, 'max_stock' => 300, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $bahanUtama->id, 'satuan_id' => 1, 'name' => 'Ayam',
                'min_stock' => 20, 'max_stock' => 150, 'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $bahanUtama->id, 'satuan_id' => 6, 'name' => 'Telur Ayam',
                'min_stock' => 30, 'max_stock' => 200, 'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ======================
            // BUMBU
            // ======================
            [
                'company_id' => 1, 'category_id' => $bumbu->id, 'satuan_id' => 2, 'name' => 'Garam',
                'min_stock' => 5, 'max_stock' => 50, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Gula Pasir',
                'min_stock' => 10, 'max_stock' => 80, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Bawang Merah',
                'min_stock' => 10, 'max_stock' => 70, 'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $bumbu->id, 'satuan_id' => 1, 'name' => 'Bawang Putih',
                'min_stock' => 10, 'max_stock' => 70, 'forecast_enabled' => 1, 'mudah_rusak' => 1, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ======================
            // MINUMAN
            // ======================
            [
                'company_id' => 1, 'category_id' => $minuman->id, 'satuan_id' => 5, 'name' => 'Teh Celup',
                'min_stock' => 20, 'max_stock' => 150, 'forecast_enabled' => 0, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $minuman->id, 'satuan_id' => 5, 'name' => 'Kopi Bubuk',
                'min_stock' => 15, 'max_stock' => 100, 'forecast_enabled' => 0, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ======================
            // PELENGKAP
            // ======================
            [
                'company_id' => 1, 'category_id' => $pelengkap->id, 'satuan_id' => 3, 'name' => 'Minyak Goreng',
                'min_stock' => 20, 'max_stock' => 120, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $pelengkap->id, 'satuan_id' => 5, 'name' => 'Mie Kering',
                'min_stock' => 30, 'max_stock' => 200, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'company_id' => 1, 'category_id' => $pelengkap->id, 'satuan_id' => 3, 'name' => 'Sirup',
                'min_stock' => 2, 'max_stock' => 20, 'forecast_enabled' => 1, 'mudah_rusak' => 0, 'is_main_ingredient' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }
}
