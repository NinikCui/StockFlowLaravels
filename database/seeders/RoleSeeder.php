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

        // Mapping role → permission
        $rolePermissions = [

            'MANAGER' => Permission::all(),

            'KASIR' => Permission::whereIn('name', [
                'pos.manage',
                'report.view',
                'item.view',
                'category.view',
                'inventory.view',
            ])->get(),

            'GUDANG' => Permission::whereIn('name', [
                'inventory.view',
                'inventory.create',
                'inventory.adjust',
                'inventory.audit',

                'item.view',
                'supplier.view',
                'supplier.create',
                'supplier.update',

                'warehouse.view',
                'warehouse.create',
                'warehouse.update',

                'purchase.view',
                'purchase.create',
                'transfer.view',
                'transfer.create',
            ])->get(),

            'KITCHEN' => Permission::whereIn('name', [
                'item.view',
                'category.view',
                'inventory.view',
            ])->get(),

            'STAFF' => Permission::whereIn('name', [
                'item.view',
                'inventory.view',
            ])->get(),
        ];

        foreach ($cabangs as $cabang) {
            foreach ($rolePermissions as $roleName => $permissions) {

                $role = Role::firstOrCreate([
                    'company_id' => $company->id,
                    'cabang_resto_id' => $cabang->id,
                    'name' => $roleName.'_'.strtoupper($cabang->name),
                    'guard_name' => 'web',
                ], [
                    'code' => $roleName.'_'.$cabang->id,
                ]);

                $role->syncPermissions($permissions);
            }
        }
    }
}
