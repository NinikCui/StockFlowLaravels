<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $cabang = CabangResto::first();
            $user = User::first();

            $warehouses = Warehouse::where('cabang_resto_id', $cabang->id)->get();

            if ($warehouses->count() < 2) {
                return;
            }

            $fromWarehouse = $warehouses[0];
            $toWarehouse = $warehouses[1];

            $stock = Stock::where('warehouse_id', $fromWarehouse->id)
                ->where('qty', '>', 0)
                ->first();

            if (! $stock) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | KASUS 1: TRANSFER POSTED (BERHASIL)
            |--------------------------------------------------------------------------
            */
            $posted = InventoryTrans::create([
                'cabang_id_from' => 1,
                'cabang_id_to' => 2,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Transfer antar gudang (POSTED)',
                'reason' => 'Penataan ulang stok',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $posted->id,
                'items_id' => $stock->item_id,
                'qty' => 10,
                'note' => 'Transfer normal',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 2: TRANSFER DRAFT
            |--------------------------------------------------------------------------
            */
            $draft = InventoryTrans::create([
                'cabang_id_from' => 1,
                'cabang_id_to' => 2,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Transfer antar gudang (DRAFT)',
                'reason' => null,
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $draft->id,
                'items_id' => $stock->item_id,
                'qty' => 5,
                'note' => 'Menunggu konfirmasi',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 3: TRANSFER CANCELLED
            |--------------------------------------------------------------------------
            */
            $cancel = InventoryTrans::create([
                'cabang_id_from' => 1,
                'cabang_id_to' => 2,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'CANCELLED',
                'note' => 'Transfer antar gudang (CANCELLED)',
                'reason' => 'Kesalahan input',
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $cancel->id,
                'items_id' => $stock->item_id,
                'qty' => 8,
                'note' => 'Dibatalkan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 4: MULTI-ITEM TRANSFER
            |--------------------------------------------------------------------------
            */
            $multi = InventoryTrans::create([
                'cabang_id_from' => 1,
                'cabang_id_to' => 2,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REQUESTED',
                'note' => 'Transfer multi item',
                'reason' => 'Penyesuaian gudang',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            $stocks = Stock::where('warehouse_id', $fromWarehouse->id)
                ->where('qty', '>', 5)
                ->take(2)
                ->get();

            foreach ($stocks as $s) {
                InvenTransDetail::create([
                    'inven_trans_id' => $multi->id,
                    'items_id' => $s->item_id,
                    'qty' => 5,
                    'note' => 'Multi item transfer',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | KASUS 5: TRANSFER GAGAL (QTY > STOK)
            |--------------------------------------------------------------------------
            */
            $failed = InventoryTrans::create([
                'cabang_id_from' => 1,
                'cabang_id_to' => 2,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REJECTED',
                'note' => 'Transfer gagal',
                'reason' => 'Jumlah melebihi stok',
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $failed->id,
                'items_id' => $stock->item_id,
                'qty' => $stock->qty + 100,
                'note' => 'Simulasi gagal',
            ]);

            // dibalek
            $posted = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Transfer antar gudang (POSTED)',
                'reason' => 'Penataan ulang stok',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $posted->id,
                'items_id' => $stock->item_id,
                'qty' => 10,
                'note' => 'Transfer normal',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 2: TRANSFER DRAFT
            |--------------------------------------------------------------------------
            */
            $draft = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Transfer antar gudang (DRAFT)',
                'reason' => null,
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $draft->id,
                'items_id' => $stock->item_id,
                'qty' => 5,
                'note' => 'Menunggu konfirmasi',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 3: TRANSFER CANCELLED
            |--------------------------------------------------------------------------
            */
            $cancel = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'CANCELLED',
                'note' => 'Transfer antar gudang (CANCELLED)',
                'reason' => 'Kesalahan input',
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $cancel->id,
                'items_id' => $stock->item_id,
                'qty' => 8,
                'note' => 'Dibatalkan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASUS 4: MULTI-ITEM TRANSFER
            |--------------------------------------------------------------------------
            */
            $multi = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REQUESTED',
                'note' => 'Transfer multi item',
                'reason' => 'Penyesuaian gudang',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            $stocks = Stock::where('warehouse_id', $fromWarehouse->id)
                ->where('qty', '>', 5)
                ->take(2)
                ->get();

            foreach ($stocks as $s) {
                InvenTransDetail::create([
                    'inven_trans_id' => $multi->id,
                    'items_id' => $s->item_id,
                    'qty' => 5,
                    'note' => 'Multi item transfer',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | KASUS 5: TRANSFER GAGAL (QTY > STOK)
            |--------------------------------------------------------------------------
            */
            $failed = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REJECTED',
                'note' => 'Transfer gagal',
                'reason' => 'Jumlah melebihi stok',
                'created_by' => $user->id,
                'posted_at' => null,
            ]);

            InvenTransDetail::create([
                'inven_trans_id' => $failed->id,
                'items_id' => $stock->item_id,
                'qty' => $stock->qty + 100,
                'note' => 'Simulasi gagal',
            ]);
        });
    }
}
