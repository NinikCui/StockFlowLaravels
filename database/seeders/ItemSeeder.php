<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori
        $bahanUtama = Category::where('name', 'Bahan Utama')->first();
        $bumbu = Category::where('name', 'Bumbu')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $pelengkap = Category::where('name', 'Pelengkap')->first();

        // ======================
        // SATUAN
        // ======================
        Satuan::insert([
            ['id' => 1, 'company_id' => 1, 'name' => 'Kilogram', 'code' => 'KG'],
            ['id' => 2, 'company_id' => 1, 'name' => 'Gram', 'code' => 'GR'],
            ['id' => 3, 'company_id' => 1, 'name' => 'Liter', 'code' => 'LTR'],
            ['id' => 4, 'company_id' => 1, 'name' => 'Pieces', 'code' => 'PCS'],
            ['id' => 5, 'company_id' => 1, 'name' => 'Pack', 'code' => 'PACK'],
            ['id' => 6, 'company_id' => 1, 'name' => 'Butir', 'code' => 'BTR'],
        ]);

        // ======================
        // ITEM
        // ======================
        Item::insert([

            // ======================
            // BAHAN UTAMA
            // ======================
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Beras', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Ayam', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Daging Sapi', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Ikan Fillet', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Telur Ayam', 'satuan_id' => 6],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Tahu', 'satuan_id' => 4],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Tempe', 'satuan_id' => 4],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Kentang', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bahanUtama->id, 'name' => 'Wortel', 'satuan_id' => 1],

            // ======================
            // BUMBU
            // ======================
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Garam', 'satuan_id' => 2],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Gula Pasir', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Merica Bubuk', 'satuan_id' => 2],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Ketumbar Bubuk', 'satuan_id' => 2],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Kaldu Bubuk', 'satuan_id' => 2],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Bawang Merah', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Bawang Putih', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Cabai Merah', 'satuan_id' => 1],
            ['company_id' => 1, 'category_id' => $bumbu->id, 'name' => 'Cabai Rawit', 'satuan_id' => 1],

            // ======================
            // MINUMAN
            // ======================
            ['company_id' => 1, 'category_id' => $minuman->id, 'name' => 'Air Mineral Galon', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $minuman->id, 'name' => 'Teh Celup', 'satuan_id' => 5],
            ['company_id' => 1, 'category_id' => $minuman->id, 'name' => 'Kopi Bubuk', 'satuan_id' => 5],
            ['company_id' => 1, 'category_id' => $minuman->id, 'name' => 'Susu Cair', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $minuman->id, 'name' => 'Sirup', 'satuan_id' => 3],

            // ======================
            // PELENGKAP
            // ======================
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Minyak Goreng', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Kecap Manis', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Kecap Asin', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Saus Sambal', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Saus Tomat', 'satuan_id' => 3],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Mie Kering', 'satuan_id' => 5],
            ['company_id' => 1, 'category_id' => $pelengkap->id, 'name' => 'Keju', 'satuan_id' => 1],
        ]);
    }
}
