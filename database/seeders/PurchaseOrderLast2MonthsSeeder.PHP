<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PurchaseOrderLast2MonthsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (! $user) {
            return;
        }

        $branches = CabangResto::all();
        if ($branches->isEmpty()) {
            return;
        }

        $companyId = $branches->first()->company_id ?? null;

        $items = $companyId
            ? Item::where('company_id', $companyId)->get()
            : Item::all();

        if ($items->isEmpty()) {
            return;
        }

        $suppliers = $companyId
            ? Supplier::where('company_id', $companyId)->where('is_active', true)->get()
            : Supplier::where('is_active', true)->get();

        if ($suppliers->isEmpty()) {
            return;
        }

        $start = Carbon::now()->subMonths(2)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $hasCompanyIdOnPO = Schema::hasColumn('purchase_order', 'company_id');

        DB::transaction(function () use (
            $branches, $items, $suppliers, $user, $companyId,
            $start, $end, $hasCompanyIdOnPO
        ) {
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

                foreach ($branches as $branch) {

                    $warehouse = Warehouse::where('cabang_resto_id', $branch->id)
                        ->inRandomOrder()
                        ->first();

                    if (! $warehouse) {
                        continue;
                    }

                    $supplierCandidates = $suppliers->filter(function ($s) use ($branch) {
                        return is_null($s->cabang_resto_id) || $s->cabang_resto_id == $branch->id;
                    })->values();

                    $supplier = $supplierCandidates->isNotEmpty()
                        ? $supplierCandidates->random()
                        : $suppliers->random();

                    // 0-2 PO per hari per cabang
                    $poCount = rand(0, 2);

                    for ($i = 0; $i < $poCount; $i++) {

                        $poDate = $date->copy()->setTime(rand(8, 16), rand(0, 59));
                        $daysAgo = Carbon::now()->diffInDays($poDate);

                        // kamu boleh tambah PARTIAL biar datanya lebih kaya
                        $statusPool = $daysAgo >= 14
                            ? ['RECEIVED', 'RECEIVED', 'APPROVED', 'PARTIAL', 'CANCELLED', 'DRAFT']
                            : ($daysAgo >= 5
                                ? ['APPROVED', 'APPROVED', 'PARTIAL', 'CANCELLED', 'DRAFT']
                                : ['DRAFT', 'DRAFT', 'APPROVED', 'CANCELLED']);

                        $status = $statusPool[array_rand($statusPool)];

                        // =========================
                        // ✅ RULE YANG KAMU MINTA
                        // =========================
                        // "request & lain-lain sebelum diterima" = sebelum di-acc -> DRAFT (dan opsional CANCELLED)
                        if (in_array($status, ['DRAFT', 'CANCELLED', 'APPROVED'])) {
                            $expected = Carbon::today()->setTime(0, 0, 0);
                        } else {
                            $expected = $poDate->copy()->addDays(rand(1, 5))->setTime(0, 0, 0);
                        }

                        $poNumber = 'PO-'.strtoupper($branch->code).'-'.$poDate->format('YmdHis').'-'.strtoupper(Str::random(3));

                        $deliveredAt = null;
                        $ontime = 0;

                        if (in_array($status, ['RECEIVED'])) {
                            $deliveredAt = $expected->copy()
                                ->addDays(rand(-1, 2))
                                ->setTime(rand(9, 17), rand(0, 59));

                            if ($deliveredAt->lt($poDate)) {
                                $deliveredAt = $poDate->copy()->addDays(1)->setTime(rand(9, 17), rand(0, 59));
                            }

                            $ontime = $deliveredAt->lte($expected) ? 1 : 0;
                        }

                        // =========================
                        // INSERT PURCHASE ORDER
                        // =========================
                        $poPayload = [
                            'cabang_resto_id' => $branch->id,
                            'warehouse_id' => $warehouse->id,
                            'suppliers_id' => $supplier->id,
                            'po_date' => $poDate,
                            'expected_delivery_date' => $expected,
                            'delivered_date' => $deliveredAt,
                            'status' => $status,
                            'note' => "Seeder PO ({$status}) - ".$poDate->toDateString(),
                            'ontime' => $ontime,
                            'po_number' => $poNumber,
                            'created_by' => $user->id,
                            'created_at' => $poDate,
                            'updated_at' => $deliveredAt ?? $poDate,
                        ];

                        if ($hasCompanyIdOnPO) {
                            $poPayload['company_id'] = $branch->company_id ?? $companyId;
                        }

                        $poId = DB::table('purchase_order')->insertGetId($poPayload);

                        // =========================
                        // INSERT PO DETAIL (2-5 item)
                        // =========================
                        $detailCount = rand(2, 5);
                        $selectedItems = $items->random(min($detailCount, $items->count()));

                        $detailRows = [];

                        foreach ($selectedItems as $it) {
                            $qtyOrdered = rand(5, 60);
                            $unitPrice = rand(8000, 70000);

                            $detailId = DB::table('po_detail')->insertGetId([
                                'purchase_order_id' => $poId,
                                'items_id' => $it->id,
                                'qty_ordered' => $qtyOrdered,
                                'unit_price' => $unitPrice,
                                'quality' => rand(3, 5),
                                'conversion_to_stock' => 1,
                                'discount_pct' => rand(0, 10),
                                'note_line' => 'Seeder line',
                                'created_at' => $poDate,
                                'updated_at' => $poDate,
                            ]);

                            $detailRows[] = [
                                'id' => $detailId,
                                'item_id' => $it->id,
                                'qty_ordered' => (float) $qtyOrdered,
                            ];
                        }

                        // =======================================================
                        // RECEIVED / PARTIAL:
                        // buat po_receive + movement
                        // =======================================================
                        if ($status !== 'RECEIVED') {
                            continue;
                        }

                        $receiveHeaderId = DB::table('po_receive')->insertGetId([
                            'purchase_order_id' => $poId,
                            'warehouse_id' => $warehouse->id,
                            'received_by' => $user->id,
                            'received_at' => $deliveredAt,
                            'created_at' => $deliveredAt,
                            'updated_at' => $deliveredAt,
                        ]);

                        foreach ($detailRows as $d) {

                            $qtyOrdered = $d['qty_ordered'];

                            // RECEIVED: terima semua, kadang return 10%
                            $qtyReturned = (rand(0, 100) < 10) ? round($qtyOrdered * 0.1, 2) : 0;
                            $qtyReceived = max(0, $qtyOrdered - $qtyReturned);

                            $expiredAt = $deliveredAt->copy()->addDays(rand(7, 90))->toDateString();

                            DB::table('po_receive_detail')->insert([
                                'po_receive_id' => $receiveHeaderId,
                                'po_detail_id' => $d['id'],
                                'item_id' => $d['item_id'],
                                'qty_received' => $qtyReceived,
                                'qty_returned' => $qtyReturned,
                                'created_at' => $deliveredAt,
                                'updated_at' => $deliveredAt,
                            ]);

                            if ($qtyReceived <= 0) {
                                continue;
                            }

                            $stockCode = $this->nextStockCode($warehouse->id, $warehouse->code);

                            $stockId = DB::table('stocks')->insertGetId([
                                'code' => $stockCode,
                                'company_id' => $branch->company_id ?? $companyId,
                                'warehouse_id' => $warehouse->id,
                                'item_id' => $d['item_id'],
                                'qty' => $qtyReceived,
                                'expired_at' => $expiredAt,
                                'deleted_at' => null,
                                'created_at' => $deliveredAt,
                                'updated_at' => $deliveredAt,
                            ]);

                            DB::table('stock_movements')->insert([
                                'company_id' => $branch->company_id ?? $companyId,
                                'warehouse_id' => $warehouse->id,
                                'stock_id' => $stockId,
                                'item_id' => $d['item_id'],
                                'created_by' => $user->id,
                                'type' => 'IN',
                                'qty' => $qtyReceived,
                                'expired_at' => $expiredAt,
                                'reference' => $poNumber ? "PO#{$poNumber}" : "PO#{$poId}",
                                'notes' => 'Receive PO (Seeder)',
                                'created_at' => $deliveredAt,
                                'updated_at' => $deliveredAt,
                            ]);
                        }
                    }
                }
            }
        });
    }

    private function nextStockCode(int $warehouseId, string $warehouseCode): string
    {
        $prefix = 'STK-'.strtoupper($warehouseCode).'-';

        $lastCode = DB::table('stocks')
            ->where('warehouse_id', $warehouseId)
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('code');

        $next = $lastCode ? (int) substr($lastCode, -4) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
