<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Company;
use App\Models\OrderDetail;
use App\Models\PosOrder;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\ProductBundle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PosLast2MonthsSeeder extends Seeder
{
    public function run(): void
    {
        $cashierId = DB::table('users')->value('id');
        if (! $cashierId) {
            return;
        }

        // cabang 1 & 2
        $branch1 = CabangResto::find(1);
        $branch2 = CabangResto::find(2);
        if (! $branch1 || ! $branch2) {
            return;
        }

        $company = Company::find($branch1->company_id);
        if (! $company) {
            return;
        }

        $hasExpiredAtInMovement = Schema::hasColumn('stock_movements', 'expired_at');

        $start = now()->subMonthsNoOverflow(2)->startOfMonth()->startOfDay(); // contoh: 1 Nov / 1 Des
        $end = now()->endOfDay();

        $branches = collect([$branch1, $branch2]);

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {

            // 70% hari ada shift
            if (rand(1, 100) > 70) {
                continue;
            }

            foreach ($branches as $branch) {

                DB::transaction(function () use ($company, $branch, $day, $cashierId, $hasExpiredAtInMovement) {

                    // =========================
                    // 1) SHIFT (OPEN -> CLOSED) 1 HARI YANG SAMA
                    // =========================
                    $baseDate = $day->copy()->startOfDay();

                    // OPEN: 09:00 - 11:59
                    $openedAt = $baseDate->copy()
                        ->addHours(rand(9, 11))
                        ->addMinutes(rand(0, 59))
                        ->addSeconds(rand(0, 59));

                    // CLOSE: 18:00 - 22:59 (tanggal sama)
                    $closedAt = $baseDate->copy()
                        ->addHours(rand(18, 22))
                        ->addMinutes(rand(0, 59))
                        ->addSeconds(rand(0, 59));

                    // guard: kalau somehow closed <= open
                    if ($closedAt->lte($openedAt)) {
                        $closedAt = $openedAt->copy()->addHours(rand(8, 12));
                    }

                    $openingCash = rand(100_000, 500_000);

                    $shift = PosShift::create([
                        'cabang_resto_id' => $branch->id,
                        'opened_by' => $cashierId,
                        'opened_at' => $openedAt,
                        'opening_cash' => $openingCash,
                        'status' => 'OPEN',

                    ]);

                    // =========================
                    // Data Pendukung
                    // =========================
                    $warehouseIds = DB::table('warehouse')
                        ->where('cabang_resto_id', $branch->id)
                        ->pluck('id');

                    if ($warehouseIds->isEmpty()) {
                        $shift->update([
                            'closed_by' => $cashierId,
                            'closed_at' => $closedAt,
                            'closing_cash' => $openingCash,
                            'status' => 'CLOSED',
                        ]);

                        return;
                    }

                    $products = Product::with('bomItems.item')
                        ->where('company_id', $company->id)
                        ->get();

                    $bundles = ProductBundle::with('items.product.bomItems')
                        ->where('company_id', $company->id)
                        ->where('is_active', true)
                        ->where(function ($q) use ($branch) {
                            $q->whereNull('cabang_resto_id')
                                ->orWhere('cabang_resto_id', $branch->id);
                        })
                        ->get();

                    if ($products->isEmpty() && $bundles->isEmpty()) {
                        $shift->update([
                            'closed_by' => $cashierId,
                            'closed_at' => $closedAt,
                            'closing_cash' => $openingCash,
                            'status' => 'CLOSED',
                        ]);

                        return;
                    }

                    // =========================
                    // 2) ORDERS DALAM SHIFT
                    // =========================
                    $orderCount = rand(3, 12);
                    $totalSales = 0;

                    for ($i = 0; $i < $orderCount; $i++) {

                        // orderTime random di dalam rentang open-close
                        $minTs = $openedAt->copy()->addMinutes(5)->timestamp;
                        $maxTs = $closedAt->copy()->subMinutes(5)->timestamp;

                        if ($maxTs <= $minTs) {
                            $orderTime = $openedAt->copy()->addMinutes(10);
                        } else {
                            $orderTime = Carbon::createFromTimestamp(rand($minTs, $maxTs));
                        }

                        // running number per cabang
                        $running = PosOrder::where('cabang_resto_id', $branch->id)->count() + 1;
                        $orderNum = strtoupper($branch->code).'-'.$orderTime->format('Y').'-'.str_pad($running, 6, '0', STR_PAD_LEFT);

                        // cart campur product/bundle
                        $cart = $this->buildCart($products, $bundles);
                        if (empty($cart)) {
                            continue;
                        }

                        $total = collect($cart)->sum(fn ($c) => $c['subtotal']);
                        if ($total <= 0) {
                            continue;
                        }

                        // kalau stok gak cukup, skip order
                        if (! $this->validateCartStockEnough($cart, $warehouseIds)) {
                            continue;
                        }

                        $receiptItems = $this->buildReceiptItems($cart);

                        $order = PosOrder::create([
                            'cabang_resto_id' => $branch->id,
                            'order_datetime' => $orderTime,
                            'status' => 'PAID',
                            'order_number' => $orderNum,
                            'cashier_id' => $cashierId,
                            'pos_shifts_id' => $shift->id,
                            'table_no' => rand(1, 15),
                            'receipt_items' => $receiptItems,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);

                        // details
                        foreach ($cart as $item) {

                            if (($item['type'] ?? 'PRODUCT') === 'PRODUCT') {
                                OrderDetail::create([
                                    'pos_order_id' => $order->id,
                                    'products_id' => $item['id'],
                                    'qty' => $item['qty'],
                                    'price' => $item['price'],
                                    'discount_pct' => 0,
                                    'note_line' => $item['note'] ?? null,
                                ]);
                            } else {
                                foreach ($item['items'] as $bi) {
                                    OrderDetail::create([
                                        'pos_order_id' => $order->id,
                                        'products_id' => $bi['product_id'],
                                        'qty' => $bi['qty'] * $item['qty'],
                                        'price' => 0,
                                        'discount_pct' => 100,
                                        'note_line' => 'Bundle: '.$item['name'],
                                        'created_at' => $orderTime,
                                    ]);
                                }
                            }
                        }

                        // payment CASH
                        $paid = $total + rand(0, 50_000);

                        DB::table('pos_payments')->insert([
                            'pos_order_id' => $order->id,
                            'method' => 'CASH',
                            'amount' => $total,
                            'paid_amount' => $paid,
                            'change_amount' => $paid - $total,
                            'status' => 'SUCCESS',
                            'note' => 'Cash payment (Seeder)',
                            'paid_at' => $orderTime,
                        ]);

                        // consume stok via BOM + stock_movements (OUT qty negatif)
                        $this->consumeCartBomAndInsertMovements(
                            $company->id,
                            $warehouseIds,
                            $orderNum,
                            $cashierId,
                            $orderTime,
                            $cart,
                            $hasExpiredAtInMovement
                        );

                        $totalSales += $total;
                    }

                    // =========================
                    // 3) CLOSE SHIFT
                    // =========================
                    $expectedCash = $openingCash + $totalSales;
                    $closingCash = max(0, $expectedCash + rand(-20_000, 20_000));

                    $shift->update([
                        'closed_by' => $cashierId,
                        'closed_at' => $closedAt,
                        'closing_cash' => $closingCash,
                        'status' => 'CLOSED',
                        'note' => 'Seeder auto close',
                    ]);
                });
            }
        }
    }

    // =========================================================
    // CART BUILDER
    // =========================================================
    private function buildCart($products, $bundles): array
    {
        $cart = [];
        $cartCount = rand(1, 4);

        for ($i = 0; $i < $cartCount; $i++) {

            $chooseBundle = (! $bundles->isEmpty()) && rand(1, 100) <= 25;

            if ($chooseBundle) {
                $bundle = $bundles->random();
                $key = 'bundle_'.$bundle->id;

                $qty = rand(1, 2);
                $price = (float) ($bundle->bundle_price ?? 0);

                $cart[$key] = [
                    'id' => $bundle->id,
                    'type' => 'BUNDLE',
                    'name' => $bundle->name,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                    'items' => $bundle->items->map(function ($i) {
                        return [
                            'product_id' => $i->product_id,
                            'name' => $i->product->name,
                            'qty' => $i->qty,
                        ];
                    })->toArray(),
                    'note' => null,
                ];
            } else {
                if ($products->isEmpty()) {
                    continue;
                }

                $product = $products->random();
                $key = 'product_'.$product->id;

                $qty = rand(1, 3);
                $price = (float) ($product->base_price ?? 0);

                if (! isset($cart[$key])) {
                    $cart[$key] = [
                        'id' => $product->id,
                        'type' => 'PRODUCT',
                        'name' => $product->name,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $qty * $price,
                        'note' => null,
                    ];
                } else {
                    $cart[$key]['qty'] += $qty;
                    $cart[$key]['subtotal'] = $cart[$key]['qty'] * $price;
                }
            }
        }

        return array_values($cart);
    }

    private function buildReceiptItems(array $cart): array
    {
        $receiptItems = [];

        foreach ($cart as $item) {
            if (($item['type'] ?? 'PRODUCT') === 'BUNDLE') {
                $receiptItems[] = [
                    'type' => 'BUNDLE',
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                    'items' => collect($item['items'])->map(fn ($bi) => [
                        'product_id' => $bi['product_id'],
                        'name' => $bi['name'],
                        'qty' => $bi['qty'],
                    ])->toArray(),
                ];
            } else {
                $receiptItems[] = [
                    'type' => 'PRODUCT',
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                ];
            }
        }

        return $receiptItems;
    }

    // =========================================================
    // VALIDASI STOK CUKUP (TOTAL PER ITEM_ID)
    // =========================================================
    private function validateCartStockEnough(array $cart, $warehouseIds): bool
    {
        $needs = [];

        foreach ($cart as $c) {
            if (($c['type'] ?? 'PRODUCT') === 'PRODUCT') {
                $boms = DB::table('boms')
                    ->where('product_id', $c['id'])
                    ->get(['item_id', 'qty_per_unit']);

                foreach ($boms as $bom) {
                    $needs[$bom->item_id] = ($needs[$bom->item_id] ?? 0) + ($bom->qty_per_unit * (int) $c['qty']);
                }
            } else {
                foreach ($c['items'] as $bi) {
                    $boms = DB::table('boms')
                        ->where('product_id', $bi['product_id'])
                        ->get(['item_id', 'qty_per_unit']);

                    foreach ($boms as $bom) {
                        $needs[$bom->item_id] = ($needs[$bom->item_id] ?? 0) + ($bom->qty_per_unit * (int) ($bi['qty'] * $c['qty']));
                    }
                }
            }
        }

        foreach ($needs as $itemId => $needQty) {
            $available = (float) DB::table('stocks')
                ->where('item_id', $itemId)
                ->whereIn('warehouse_id', $warehouseIds)
                ->whereNull('deleted_at')
                ->sum('qty');

            if ($available + 1e-9 < (float) $needQty) {
                return false;
            }
        }

        return true;
    }

    // =========================================================
    // CONSUME FIFO (expired_at) + INSERT STOCK MOVEMENTS
    // =========================================================
    private function consumeCartBomAndInsertMovements(
        int $companyId,
        $warehouseIds,
        string $reference,
        int $cashierId,
        Carbon $time,
        array $cart,
        bool $hasExpiredAtInMovement
    ): void {
        foreach ($cart as $c) {
            if (($c['type'] ?? 'PRODUCT') === 'PRODUCT') {
                $this->consumeProductBom($companyId, $warehouseIds, $reference, $cashierId, $time, $c['id'], (int) $c['qty'], $hasExpiredAtInMovement);
            } else {
                foreach ($c['items'] as $bi) {
                    $this->consumeProductBom($companyId, $warehouseIds, $reference, $cashierId, $time, $bi['product_id'], (int) ($bi['qty'] * $c['qty']), $hasExpiredAtInMovement);
                }
            }
        }
    }

    private function consumeProductBom(
        int $companyId,
        $warehouseIds,
        string $reference,
        int $cashierId,
        Carbon $time,
        int $productId,
        int $orderQty,
        bool $hasExpiredAtInMovement
    ): void {
        $boms = DB::table('boms')
            ->where('product_id', $productId)
            ->get(['item_id', 'qty_per_unit']);

        foreach ($boms as $bom) {

            $neededQty = (float) ($bom->qty_per_unit * $orderQty);

            $stocks = DB::table('stocks')
                ->where('item_id', $bom->item_id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->whereNull('deleted_at')
                ->where('qty', '>', 0)
                ->orderByRaw('expired_at IS NULL ASC')
                ->orderBy('expired_at', 'ASC')
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $s) {
                if ($neededQty <= 0) {
                    break;
                }

                $take = min((float) $s->qty, $neededQty);

                DB::table('stocks')->where('id', $s->id)->update([
                    'qty' => (float) $s->qty - $take,
                    'updated_at' => $time,
                ]);

                $movement = [
                    'company_id' => $companyId,
                    'warehouse_id' => $s->warehouse_id,
                    'stock_id' => $s->id,
                    'item_id' => $bom->item_id,
                    'created_by' => $cashierId,
                    'type' => 'OUT',
                    'qty' => -$take,
                    'reference' => $reference,
                    'notes' => 'POS Sale (Seeder)',
                    'created_at' => $time,
                    'updated_at' => $time,
                ];

                if ($hasExpiredAtInMovement) {
                    $movement['expired_at'] = $s->expired_at ?? null;
                }

                DB::table('stock_movements')->insert($movement);

                $neededQty -= $take;
            }

            if ($neededQty > 0) {
                throw new \Exception("Seeder POS: stok bahan item_id {$bom->item_id} tidak mencukupi.");
            }
        }
    }
}
