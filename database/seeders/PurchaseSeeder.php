<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = CabangResto::first();
        $warehouse = Warehouse::where('cabang_resto_id', $cabang->id)->first();
        $supplier = Supplier::where('is_active', true)->first();
        $user = User::first();

        /*
        |--------------------------------------------------------------------------
        | KASUS 1: PO DRAFT
        |--------------------------------------------------------------------------
        */
        DB::table('purchase_order')->insert([
            'cabang_resto_id' => $cabang->id,
            'warehouse_id' => $warehouse->id,
            'suppliers_id' => $supplier->id,
            'po_date' => Carbon::now(),
            'status' => 'DRAFT',
            'note' => 'PO masih draft',
            'ontime' => 0,
            'po_number' => null,
            'expected_delivery_date' => Carbon::now()->addDays(3),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | KASUS 2: PO APPROVED
        |--------------------------------------------------------------------------
        */
        DB::table('purchase_order')->insert([
            'cabang_resto_id' => $cabang->id,
            'warehouse_id' => $warehouse->id,
            'suppliers_id' => $supplier->id,
            'po_date' => Carbon::now()->subDays(2),
            'status' => 'APPROVED',
            'note' => 'PO sudah disetujui',
            'ontime' => 1,
            'po_number' => 'PO-'.strtoupper(Str::random(6)),
            'expected_delivery_date' => Carbon::now()->addDays(1),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | KASUS 3: PO RECEIVED (SELESAI)
        |--------------------------------------------------------------------------
        */
        DB::table('purchase_order')->insert([
            'cabang_resto_id' => $cabang->id,
            'warehouse_id' => $warehouse->id,
            'suppliers_id' => $supplier->id,
            'po_date' => Carbon::now()->subDays(5),
            'status' => 'APPROVED',
            'note' => 'Barang sudah diterima',
            'ontime' => 1,
            'po_number' => 'PO-'.strtoupper(Str::random(6)),
            'expected_delivery_date' => Carbon::now()->subDays(2),
            'delivered_date' => Carbon::now()->subDays(2),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | KASUS 4: PO CANCELLED
        |--------------------------------------------------------------------------
        */
        DB::table('purchase_order')->insert([
            'cabang_resto_id' => $cabang->id,
            'warehouse_id' => $warehouse->id,
            'suppliers_id' => $supplier->id,
            'po_date' => Carbon::now()->subDays(1),
            'status' => 'CANCELLED',
            'note' => 'PO dibatalkan karena stok cukup',
            'ontime' => 0,
            'po_number' => null,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
