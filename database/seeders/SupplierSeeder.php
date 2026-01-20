<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER AKTIF – BAHAN UTAMA
        |--------------------------------------------------------------------------
        */
        Supplier::create([
            'company_id' => $company->id,
            'name' => 'CV Sumber Pangan Nusantara',
            'phone' => '081234567890',
            'email' => 'sales@sumberpangan.id',
            'address' => 'Surabaya',
            'is_active' => true,
        ]);

    }
}
