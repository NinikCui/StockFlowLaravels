<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBundleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('product_bundles')->insert([

            [
                'company_id' => 1,
                'cabang_resto_id' => 1,
                'name' => 'Paket Hemat Nasi',
                'bundle_price' => 35000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => 1,
                'cabang_resto_id' => 1,
                'name' => 'Paket Kenyang',
                'bundle_price' => 45000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => 1,
                'cabang_resto_id' => 1,
                'name' => 'Paket Nongkrong',
                'bundle_price' => 30000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
