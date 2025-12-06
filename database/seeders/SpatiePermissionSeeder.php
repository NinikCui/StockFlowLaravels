<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // ============================
            // CABANG
            // ============================
            'branch.view',
            'branch.create',
            'branch.update',
            'branch.delete',

            // ============================
            // GUDANG
            // ============================
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
            'warehouse.delete',

            // ============================
            // ITEM
            // ============================
            'item.view',
            'item.create',
            'item.update',
            'item.delete',

            // ============================
            // CATEGORY
            // ============================
            'category.view',
            'category.create',
            'category.update',
            'category.delete',

            // ============================
            // INVENTORY
            // ============================
            'inventory.view',
            'inventory.transfer',
            'inventory.adjust',
            'inventory.audit',

            // ============================
            // SUPPLIER
            // ============================
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            // ============================
            // EMPLOYEE
            // ============================
            'employee.view',
            'employee.create',
            'employee.update',
            'employee.delete',

            // ============================
            // PURCHASE ORDER
            // ============================
            'purchase.view',
            'purchase.delete',
            'purchase.create',
            'purchase.update',

            // ============================
            // ROLE & PERMISSION MGMT
            // ============================
            'permission.view',
            'permission.update',
            'permission.delete',
            'permission.create',

            // ============================
            // ANALYTICS
            // ============================
            'analytics.inventory',
            'analytics.supplier',

            // ============================
            // SETTINGS
            // ============================
            'settings.general',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }
    }
}
