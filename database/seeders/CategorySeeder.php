<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Bahan Utama',
            'Bumbu',
            'Minuman',
            'Pelengkap',
        ] as $cat) {
            Category::create(['name' => $cat,
                'code' => strtoupper(str_replace(' ', '_', $cat)),
                'company_id' => 1]);
        }
    }
}
