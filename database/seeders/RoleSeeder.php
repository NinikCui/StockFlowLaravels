<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $cabangs = CabangResto::all();

        foreach ($cabangs as $cabang) {

            // MANAGER
            $manager = Role::create([
                'company_id' => $company->id,
                'cabang_resto_id' => $cabang->id,
                'code' => 'MANAGER_'.$cabang->name,
                'name' => 'MANAGER_'.strtoupper($cabang->name),
                'guard_name' => 'web',
            ]);

            $manager->syncPermissions(Permission::all());

        }

    }
}
