<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CabangRestoSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        $cabangs = [
            'Cabang Surabaya',
            'Cabang Sidoarjo',
            'Cabang Gresik',
        ];

        foreach ($cabangs as $cabang) {
            CabangResto::create([
                'company_id' => $company->id,
                'name' => $cabang,
                'code' => strtoupper(str_replace(' ', '_', $cabang)),
                'address' => 'Jl. Example Address No. 123',
                'city' => 'Some City',
                'phone' => '081234567890',
                'is_active' => true,
            ]);
        }
    }
}
