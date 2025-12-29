<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SpatiePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // ============================
            // BRANCH
            // ============================
            'branch.view',
            'branch.create',
            'branch.update',
            'branch.delete',

            // ============================
            // WAREHOUSE
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
            'inventory.create',
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
            'purchase.create',
            'purchase.update',
            'purchase.delete',

            // ============================
            // ROLE & PERMISSION MANAGEMENT
            // ============================
            'permission.view',
            'permission.create',
            'permission.update',
            'permission.delete',

            // ============================
            // TRANSFER
            // ============================
            'transfer.view',
            'transfer.create',
            'transfer.update',
            'transfer.delete',

            // ============================
            // ANALYTICS
            // ============================
            'analytics.inventory',
            'analytics.supplier',

            // ============================
            // REPORT
            // ============================
            'report.view',

            // ============================
            // SETTINGS (company context)
            // ============================
            'settings.general',

            // ============================
            // POS (branch context)
            // ============================
            'pos.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
