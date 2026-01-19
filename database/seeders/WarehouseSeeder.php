<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        WarehouseType::insert([
            ['company_id' => 1, 'id' => 1, 'name' => 'Bahan Kering'],
            ['company_id' => 1, 'id' => 2, 'name' => 'Fresh / Frozen'],
            ['company_id' => 1, 'id' => 3, 'name' => 'Minuman'],
        ]);
        foreach (CabangResto::all() as $cabang) {

            // Gudang Bahan Kering
            Warehouse::create([
                'cabang_resto_id' => $cabang->id,
                'name' => 'Gudang Bahan - '.$cabang->name,
                'code' => 'WH-BHN-'.$cabang->id,
                'warehouse_type_id' => 1, // bahan kering
            ]);

            // Gudang Fresh / Frozen
            Warehouse::create([
                'cabang_resto_id' => $cabang->id,
                'name' => 'Gudang Fresh - '.$cabang->name,
                'code' => 'WH-FRS-'.$cabang->id,
                'warehouse_type_id' => 2, // fresh / frozen
            ]);

            // Gudang Minuman
            Warehouse::create([
                'cabang_resto_id' => $cabang->id,
                'name' => 'Gudang Minuman - '.$cabang->name,
                'code' => 'WH-MNM-'.$cabang->id,
                'warehouse_type_id' => 3, // minuman
            ]);
        }
    }
}
