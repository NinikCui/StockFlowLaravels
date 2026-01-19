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
        $utama = Category::where('name', 'Bahan Utama')->first();
        Satuan::create(['company_id' => 1, 'id' => 1, 'name' => 'kg', 'code' => 'KG'], [
            'company_id' => 1, 'id' => 2, 'name' => 'liter', 'code' => 'LTR',
        ]);
        Item::insert([
            [
                'company_id' => 1,
                'category_id' => $utama->id,
                'name' => 'Beras',
                'satuan_id' => 1,
            ],
            [
                'company_id' => 1,
                'category_id' => $utama->id,
                'name' => 'Ayam',
                'satuan_id' => 1,
            ],
        ]);
    }
}
