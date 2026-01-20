<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('products')->insert([

            // ================= NASI =================
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Nasi Goreng', 'code' => 'MENU-NG', 'base_price' => 25000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Nasi Goreng Ayam', 'code' => 'MENU-NGA', 'base_price' => 28000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Nasi Goreng Telur', 'code' => 'MENU-NGT', 'base_price' => 23000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Nasi Ayam Goreng', 'code' => 'MENU-NAG', 'base_price' => 30000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Nasi Ayam Bakar', 'code' => 'MENU-NAB', 'base_price' => 32000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ================= MIE =================
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Mie Goreng', 'code' => 'MENU-MG', 'base_price' => 23000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Mie Goreng Ayam', 'code' => 'MENU-MGA', 'base_price' => 26000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Mie Goreng Telur', 'code' => 'MENU-MGT', 'base_price' => 24000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ================= SAYUR =================
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Capcay', 'code' => 'MENU-CAP', 'base_price' => 22000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Tumis Kangkung', 'code' => 'MENU-TK', 'base_price' => 18000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ================= GORENGAN =================
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Tahu Goreng', 'code' => 'MENU-TG', 'base_price' => 12000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 1, 'name' => 'Tempe Goreng', 'code' => 'MENU-TMG', 'base_price' => 12000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ================= MINUMAN =================
            ['company_id' => 1, 'category_id' => 2, 'name' => 'Es Teh Manis', 'code' => 'MENU-ETM', 'base_price' => 8000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 2, 'name' => 'Teh Hangat', 'code' => 'MENU-TH', 'base_price' => 7000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 2, 'name' => 'Kopi Hitam', 'code' => 'MENU-KH', 'base_price' => 12000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 2, 'name' => 'Es Kopi', 'code' => 'MENU-EK', 'base_price' => 15000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'category_id' => 2, 'name' => 'Es Sirup', 'code' => 'MENU-ES', 'base_price' => 10000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
