<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceivingSeeder extends Seeder
{
    public function run(): void
    {
        $po = DB::table('purchase_order')
            ->where('status', 'APPROVED')
            ->first();

        if (! $po) {
            return;
        }

        $warehouse = Warehouse::find($po->warehouse_id);
        $user = User::first();

        /*
        |--------------------------------------------------------------------------
        | HEADER RECEIVING
        |--------------------------------------------------------------------------
        */
        $receiveId = DB::table('po_receive')->insertGetId([
            'purchase_order_id' => $po->id,
            'warehouse_id' => $warehouse->id,
            'received_by' => $user->id,
            'received_at' => Carbon::now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | DETAIL RECEIVING
        |--------------------------------------------------------------------------
        */
        $details = DB::table('po_detail')
            ->where('purchase_order_id', $po->id)
            ->get();

        foreach ($details as $detail) {

            // qty PO
            $orderedQty = (int) $detail->qty_ordered;

            // simulasi parsial (80%)
            $qtyReceived = (int) floor($orderedQty * 0.8);
            $qtyReturned = $orderedQty - $qtyReceived;

            DB::table('po_receive_detail')->insert([
                'po_receive_id' => $receiveId,
                'po_detail_id' => $detail->id,
                'item_id' => $detail->items_id,
                'qty_received' => $qtyReceived,
                'qty_returned' => $qtyReturned,
                'note' => $qtyReturned > 0
                    ? 'Sebagian barang dikembalikan'
                    : 'Barang diterima sesuai PO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | TAMBAH STOK KE GUDANG
            |--------------------------------------------------------------------------
            */
            DB::table('stocks')->insert([
                'code' => 'STK-'.strtoupper(uniqid()),
                'company_id' => $po->company_id ?? 1, // fallback aman
                'warehouse_id' => $warehouse->id,
                'item_id' => $detail->items_id,
                'qty' => $qtyReceived * ($detail->conversion_to_stock ?? 1),
                'expired_at' => Carbon::now()->addDays(rand(30, 180)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS PO
        |--------------------------------------------------------------------------
        */
        DB::table('purchase_order')
            ->where('id', $po->id)
            ->update([
                'status' => 'RECEIVED',
            ]);
    }
}
