<?php

namespace Database\Seeders;

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

            $user = User::first();

            $fromBranch = 1;
            $toBranch = 2;

            $fromWarehouses = Warehouse::where('cabang_resto_id', $fromBranch)->get();
            $toWarehouses = Warehouse::where('cabang_resto_id', $toBranch)->get();

            if ($fromWarehouses->isEmpty() || $toWarehouses->isEmpty()) {
                return;
            }

            $fromWarehouse = $fromWarehouses->first();

            // ======================================================
            // AMBIL STOCK DENGAN ITEM BERBEDA
            // ======================================================
            $uniqueStocks = Stock::where('warehouse_id', $fromWarehouse->id)
                ->where('qty', '>', 10)
                ->get()
                ->unique('item_id')
                ->values();

            if ($uniqueStocks->count() < 3) {
                return;
            }

            /*
            |======================================================
            | 1. REQUESTED (BELUM DIKIRIM)
            |======================================================
            */
            $requested = InventoryTrans::create([
                'cabang_id_from' => $fromBranch,
                'cabang_id_to' => $toBranch,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REQUESTED',
                'note' => 'Request awal multi item',
                'reason' => 'Kebutuhan operasional',
                'created_by' => $user->id,
            ]);

            foreach ($uniqueStocks->take(3) as $s) {
                InvenTransDetail::create([
                    'inven_trans_id' => $requested->id,
                    'items_id' => $s->item_id,
                    'qty' => rand(5, 10),
                    'sended' => null,
                    'note' => 'Belum diproses',
                ]);
            }

            /*
            |======================================================
            | 2. APPROVED + PARTIAL SEND
            |======================================================
            */
            $approved = InventoryTrans::create([
                'cabang_id_from' => $fromBranch,
                'cabang_id_to' => $toBranch,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'APPROVED',
                'note' => 'Approve dengan partial send',
                'reason' => 'Stok terbatas',
                'created_by' => $user->id,
            ]);

            foreach ($uniqueStocks->slice(1, 3) as $s) {
                $qty = rand(6, 10);
                $sent = rand(3, $qty - 1);

                InvenTransDetail::create([
                    'inven_trans_id' => $approved->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $sent,
                    'note' => 'Partial send',
                ]);
            }

            /*
            |======================================================
            | 3. IN TRANSIT (FULL SEND)
            |======================================================
            */
            $inTransit = InventoryTrans::create([
                'cabang_id_from' => $fromBranch,
                'cabang_id_to' => $toBranch,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Barang sedang dikirim',
                'reason' => 'Pengiriman normal',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            foreach ($uniqueStocks->slice(2, 3) as $s) {
                $qty = rand(4, 8);

                InvenTransDetail::create([
                    'inven_trans_id' => $inTransit->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $qty,
                    'note' => 'Full send',
                ]);
            }

            /*
            |======================================================
            | 4. RECEIVED
            |======================================================
            */
            $received = InventoryTrans::create([
                'cabang_id_from' => $fromBranch,
                'cabang_id_to' => $toBranch,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'RECEIVED',
                'note' => 'Barang sudah diterima',
                'reason' => 'Selesai',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            foreach ($uniqueStocks->slice(3, 2) as $s) {
                $qty = rand(3, 6);

                InvenTransDetail::create([
                    'inven_trans_id' => $received->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $qty,
                    'note' => 'Sudah diterima',
                ]);
            }

            /*
            |======================================================
            | 5. REJECTED
            |======================================================
            */
            $rejected = InventoryTrans::create([
                'cabang_id_from' => $fromBranch,
                'cabang_id_to' => $toBranch,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REJECTED',
                'note' => 'Request ditolak',
                'reason' => 'Qty melebihi stok',
                'created_by' => $user->id,
            ]);

            $s = $uniqueStocks->random();

            InvenTransDetail::create([
                'inven_trans_id' => $rejected->id,
                'items_id' => $s->item_id,
                'qty' => $s->qty + 50,
                'sended' => null,
                'note' => 'Simulasi gagal',
            ]);

            /*
            |======================================================
            | 1. REQUESTED (BELUM DIKIRIM)
            |======================================================
            */
            $requested = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REQUESTED',
                'note' => 'Request awal multi item',
                'reason' => 'Kebutuhan operasional',
                'created_by' => $user->id,
            ]);

            foreach ($uniqueStocks->take(3) as $s) {
                InvenTransDetail::create([
                    'inven_trans_id' => $requested->id,
                    'items_id' => $s->item_id,
                    'qty' => rand(5, 10),
                    'sended' => null,
                    'note' => 'Belum diproses',
                ]);
            }

            /*
            |======================================================
            | 2. APPROVED + PARTIAL SEND
            |======================================================
            */
            $approved = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'APPROVED',
                'note' => 'Approve dengan partial send',
                'reason' => 'Stok terbatas',
                'created_by' => $user->id,
            ]);

            foreach ($uniqueStocks->slice(1, 3) as $s) {
                $qty = rand(6, 10);
                $sent = rand(3, $qty - 1);

                InvenTransDetail::create([
                    'inven_trans_id' => $approved->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $sent,
                    'note' => 'Partial send',
                ]);
            }

            /*
            |======================================================
            | 3. IN TRANSIT (FULL SEND)
            |======================================================
            */
            $inTransit = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'IN_TRANSIT',
                'note' => 'Barang sedang dikirim',
                'reason' => 'Pengiriman normal',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            foreach ($uniqueStocks->slice(2, 3) as $s) {
                $qty = rand(4, 8);

                InvenTransDetail::create([
                    'inven_trans_id' => $inTransit->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $qty,
                    'note' => 'Full send',
                ]);
            }

            /*
            |======================================================
            | 4. RECEIVED
            |======================================================
            */
            $received = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'RECEIVED',
                'note' => 'Barang sudah diterima',
                'reason' => 'Selesai',
                'created_by' => $user->id,
                'posted_at' => Carbon::now(),
            ]);

            foreach ($uniqueStocks->slice(3, 2) as $s) {
                $qty = rand(3, 6);

                InvenTransDetail::create([
                    'inven_trans_id' => $received->id,
                    'items_id' => $s->item_id,
                    'qty' => $qty,
                    'sended' => $qty,
                    'note' => 'Sudah diterima',
                ]);
            }

            /*
            |======================================================
            | 5. REJECTED
            |======================================================
            */
            $rejected = InventoryTrans::create([
                'cabang_id_from' => 2,
                'cabang_id_to' => 1,
                'trans_number' => 'TRG-'.strtoupper(Str::random(6)),
                'trans_date' => Carbon::now(),
                'status' => 'REJECTED',
                'note' => 'Request ditolak',
                'reason' => 'Qty melebihi stok',
                'created_by' => $user->id,
            ]);

            $s = $uniqueStocks->random();

            InvenTransDetail::create([
                'inven_trans_id' => $rejected->id,
                'items_id' => $s->item_id,
                'qty' => $s->qty + 50,
                'sended' => null,
                'note' => 'Simulasi gagal',
            ]);
        });

    }
}
