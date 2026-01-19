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

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER AKTIF – FRESH / FROZEN
        |--------------------------------------------------------------------------
        */
        Supplier::create([
            'company_id' => $company->id,
            'name' => 'PT Segar Abadi',
            'phone' => '081298765432',
            'email' => 'order@segarabadi.id',
            'address' => 'Malang',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER AKTIF – MINUMAN
        |--------------------------------------------------------------------------
        */
        Supplier::create([
            'company_id' => $company->id,
            'name' => 'UD Tirta Makmur',

            'phone' => '081233344455',
            'email' => 'admin@tirtamakmur.id',
            'address' => 'Sidoarjo',
            'is_active' => true,
        ]);

        Supplier::create([
            'company_id' => $company->id,
            'name' => 'CV Lama Jaya',

            'phone' => '081200000000',
            'email' => 'contact@lamajaya.id',
            'address' => 'Gresik',
            'is_active' => false,
        ]);

        Supplier::create([
            'company_id' => $company->id,
            'name' => 'PT Dummy Supplier',

            'phone' => '081299999999',
            'email' => 'dummy@supplier.id',
            'address' => 'Jakarta',
            'is_active' => true,
        ]);
    }
}
