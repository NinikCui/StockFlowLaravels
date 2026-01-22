<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpatiePermissionSeeder::class,
            CompanyWithOwnerSeeder::class,
            CabangRestoSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            WarehouseSeeder::class,

            SupplierSeeder::class,
            SupplierItemSeeder::class,
            StockSeeder::class,
            // StockTransferSeeder::class,
            ProductSeeder::class,
            BomSeeder::class,
            ProductBundleSeeder::class,
            ProductBundleItemSeeder::class,
            CategoryIssueSeeder::class,
            PurchaseOrderLast2MonthsSeeder::class,
            StockTransferLast2MonthsSeeder::class, PosLast2MonthsSeeder::class,
        ]);
    }
}
