<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StockTransferLast2MonthsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $user = User::first();
            if (! $user) {
                return;
            }

            $branch1 = CabangResto::find(1);
            $branch2 = CabangResto::find(2);
            if (! $branch1 || ! $branch2) {
                return;
            }

            $companyId = $branch1->company_id;

            $fromWarehouses1 = Warehouse::where('cabang_resto_id', $branch1->id)->get();
            $fromWarehouses2 = Warehouse::where('cabang_resto_id', $branch2->id)->get();
            if ($fromWarehouses1->isEmpty() || $fromWarehouses2->isEmpty()) {
                return;
            }

            $hasPostedAt = Schema::hasColumn('inventory_trans', 'posted_at');
            $hasReceivedAt = Schema::hasColumn('inventory_trans', 'received_at');
            $hasSended = Schema::hasColumn('inven_trans_detail', 'sended');

            $start = Carbon::now()->subMonths(2)->startOfDay();
            $end = Carbon::now()->endOfDay();

            for ($day = $start->copy(); $day->lte($end); $day->addDays(rand(2, 4))) {

                $createCount = rand(0, 2);

                for ($k = 0; $k < $createCount; $k++) {

                    $direction = rand(0, 1);
                    $fromBranch = $direction === 0 ? $branch1 : $branch2;
                    $toBranch = $direction === 0 ? $branch2 : $branch1;

                    $fromWarehouses = $direction === 0 ? $fromWarehouses1 : $fromWarehouses2;
                    $toWarehouses = $direction === 0 ? $fromWarehouses2 : $fromWarehouses1;

                    $fromWarehouse = $fromWarehouses->random();
                    $toWarehouse = $toWarehouses->random();

                    $stocks = Stock::where('warehouse_id', $fromWarehouse->id)
                        ->whereNull('deleted_at')
                        ->where('qty', '>', 5)
                        ->get()
                        ->unique('item_id')
                        ->values();

                    if ($stocks->count() < 1) {
                        continue;
                    }

                    $take = min($stocks->count(), rand(1, 3));
                    $selectedStocks = $stocks->random($take);

                    $transDate = $day->copy()->setTime(rand(8, 17), rand(0, 59));
                    $daysAgo = Carbon::now()->diffInDays($transDate);

                    $statusPool = $daysAgo >= 14
                        ? ['RECEIVED', 'RECEIVED', 'IN_TRANSIT', 'APPROVED', 'REQUESTED', 'REJECTED']
                        : ($daysAgo >= 5
                            ? ['IN_TRANSIT', 'APPROVED', 'REQUESTED', 'REJECTED']
                            : ['REQUESTED', 'REQUESTED', 'APPROVED', 'REJECTED']);

                    $status = $statusPool[array_rand($statusPool)];

                    $transNumber = 'TRG-'.$transDate->format('YmdHis').'-'.strtoupper(Str::random(4));

                    // ✅ Header: posted_at hanya untuk IN_TRANSIT / RECEIVED
                    $headerPayload = [
                        'cabang_id_from' => $fromBranch->id,
                        'cabang_id_to' => $toBranch->id,
                        'trans_number' => $transNumber,
                        'trans_date' => $transDate->toDateString(),
                        'status' => $status,
                        'reason' => 'MATERIAL_REQUEST',
                        'note' => "Seeder transfer {$fromBranch->id} -> {$toBranch->id} ({$status})",
                        'created_by' => $user->id,
                        'posted_at' => $transDate,
                        'updated_at' => $transDate,
                    ];

                    if ($hasPostedAt) {
                        $headerPayload['posted_at'] = in_array($status, ['IN_TRANSIT', 'RECEIVED'])
                            ? $transDate->copy()->addHours(rand(0, 6))
                            : null;
                    }

                    if ($hasReceivedAt) {
                        $headerPayload['received_at'] = $status === 'RECEIVED'
                            ? $transDate->copy()->addDays(rand(1, 3))
                            : null;
                    }

                    $trans = InventoryTrans::create($headerPayload);

                    foreach ($selectedStocks as $s) {

                        $maxSend = max(1, (float) $s->qty - 1);
                        $qtyRequested = round(min($maxSend, rand(1, 10)), 4);

                        // ✅ sended = qty yang benar-benar dikirim
                        // selaras controller:
                        // - REQUESTED/REJECTED: belum ada pengiriman => null
                        // - APPROVED/IN_TRANSIT/RECEIVED: sudah ada pengiriman => sama dengan qtyRequested (full send)
                        $qtySended = in_array($status, ['APPROVED', 'IN_TRANSIT', 'RECEIVED'])
                            ? $qtyRequested
                            : null;

                        $detailPayload = [
                            'inven_trans_id' => $trans->id,
                            'items_id' => $s->item_id,
                            'qty' => $qtyRequested,
                            'note' => 'Seeder line',
                        ];

                        if ($hasSended) {
                            $detailPayload['sended'] = $qtySended;
                        }

                        InvenTransDetail::create($detailPayload);

                        // ✅ Kalau belum ada pengiriman, jangan buat movement
                        if (! in_array($status, ['APPROVED', 'IN_TRANSIT', 'RECEIVED'])) {
                            continue;
                        }

                        // ✅ OUT pakai qty terkirim (sended), bukan qty request kalau suatu saat partial
                        $sent = (float) ($qtySended ?? 0);
                        if ($sent <= 0) {
                            continue;
                        }

                        $stockLocked = Stock::where('id', $s->id)
                            ->whereNull('deleted_at')
                            ->lockForUpdate()
                            ->first();

                        if (! $stockLocked || $stockLocked->qty < $sent) {
                            continue;
                        }

                        $stockLocked->qty = $stockLocked->qty - $sent;
                        $stockLocked->save();

                        // ✅ expired_at wajib dan OUT qty negatif (sesuai controller approve())
                        StockMovement::create([
                            'company_id' => $companyId,
                            'warehouse_id' => $stockLocked->warehouse_id,
                            'stock_id' => $stockLocked->id,
                            'item_id' => $stockLocked->item_id,
                            'created_by' => $user->id,
                            'type' => 'OUT',
                            'qty' => -$sent,
                            'reference' => "Transfer Send #{$transNumber}",
                            'notes' => "Send to branch {$toBranch->name}",
                            'created_at' => $transDate,
                            'updated_at' => $transDate,
                        ]);

                        // ✅ IN hanya saat RECEIVED (selaras controller receive())
                        if ($status !== 'RECEIVED') {
                            continue;
                        }

                        $expiredAt = $stockLocked->expired_at
                            ? Carbon::parse($stockLocked->expired_at)->toDateString()
                            : Carbon::now()->addDays(rand(14, 90))->toDateString();

                        $newCode = $this->nextStockCode($toWarehouse->id, $toWarehouse->code);

                        $newStock = Stock::create([
                            'company_id' => $companyId,
                            'warehouse_id' => $toWarehouse->id,
                            'item_id' => $stockLocked->item_id,
                            'qty' => $sent,
                            'code' => $newCode,
                            'expired_at' => $expiredAt,
                            'created_at' => $transDate,
                            'updated_at' => $transDate,
                        ]);

                        StockMovement::create([
                            'company_id' => $companyId,
                            'warehouse_id' => $toWarehouse->id,
                            'stock_id' => $newStock->id,
                            'item_id' => $newStock->item_id,
                            'created_by' => $user->id,
                            'type' => 'IN',
                            'qty' => $sent,
                            'reference' => "Transfer Receive #{$transNumber}",
                            'notes' => 'Receive from inter-branch transfer (Seeder)',
                            'created_at' => $transDate,
                            'updated_at' => $transDate,
                        ]);
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
