<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            // CABANG
            'branch.view',
            'branch.create',
            'branch.update',
            'branch.activate',
            'branch.deactivate',

            // GUDANG
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
            'warehouse.delete',

            // ITEM
            'item.view',
            'item.create',
            'item.update',
            'item.delete',

            // INVENTORY
            'inventory.view',
            'inventory.transfer',
            'inventory.adjust',
            'inventory.audit',

            // PEGAWAI
            'employee.view',
            'employee.create',
            'employee.update',
            'employee.activate',
            'employee.deactivate',

            // PERMISSION ADMIN
            'permission.manage',

            // SUPPLIER
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            // PURCHASE ORDER
            'purchase.view',
            'purchase.create',
            'purchase.update',
            'purchase.approve',

            // ANALYTICS
            'analytics.inventory',
            'analytics.supplier',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate([
                'name' => $p,
                'guard_name' => 'web',
            ]);
        }
    }
}
