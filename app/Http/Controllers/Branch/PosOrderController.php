<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\MenuPromotionRecommendation;
use App\Models\OrderDetail;
use App\Models\PosOrder;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Services\StockAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosOrderController extends Controller
{
    private function loadBranch($branchCode)
    {
        $companyCode = session('role.company.code');
        $company = Company::where('code', $companyCode)->firstOrFail();

        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Check active shift
        $shift = PosShift::where('cabang_resto_id', $branch->id)
            ->where('status', 'OPEN')
            ->first();

        return [$company, $branch, $shift];
    }

    public function index($branchCode)
    {
        [$company, $branch, $shift] = $this->loadBranch($branchCode);

        if (! $shift) {
            return redirect()
                ->route('branch.pos.shift.index', [$branchCode])
                ->with('error', 'Tidak dapat membuka POS karena shift belum dibuka.');
        }

        $companyCode = session('role.company.code');

        // ===============================
        // CART
        // ===============================
        $cart = session()->get('pos_cart', []);
        foreach ($cart as $key => $item) {
            $cart[$key]['note'] ??= null;
        }

        // ===============================
        // REKOMENDASI MENU (ID ONLY)
        // ===============================
        $recommendedProductIds = MenuPromotionRecommendation::whereDate('date', today())
            ->where('cabang_resto_id', $branch->id)
            ->pluck('product_id')
            ->toArray();

        $stockChecker = new StockAvailabilityService($branch->id);

        // ===============================
        // PRODUCTS
        // ===============================
        $products = Product::with('bomItems.item')
            ->where('company_id', $company->id)
            ->get()
            ->map(function ($product) use ($stockChecker, $recommendedProductIds) {

                $product->is_available = true;

                foreach ($product->bomItems as $bom) {
                    if (! $stockChecker->hasEnough($bom->item_id, $bom->qty_per_unit)) {
                        $product->is_available = false;
                        break;
                    }
                }

                // ⭐ rekomendasi TIDAK override stok
                $product->is_recommended = $product->is_available
                    && in_array($product->id, $recommendedProductIds);

                return $product;
            });

        // ===============================
        // BUNDLES (FILTER KETAT)
        // ===============================
        $bundles = ProductBundle::with('items.product.bomItems')
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->where(function ($q) use ($branch) {
                $q->whereNull('cabang_resto_id')
                    ->orWhere('cabang_resto_id', $branch->id);
            })
            ->get()
            ->filter(function ($bundle) use ($stockChecker) {

                foreach ($bundle->items as $bundleItem) {
                    foreach ($bundleItem->product->bomItems as $bom) {

                        $required = $bom->qty_per_unit * $bundleItem->qty;

                        if (! $stockChecker->hasEnough($bom->item_id, $required)) {
                            return false;
                        }
                    }
                }

                return true;
            })
            ->map(function ($bundle) {
                $bundle->is_available = true;

                return $bundle;
            })
            ->values();

        return view('branch.pos.order.index', compact(
            'companyCode',
            'branchCode',
            'branch',
            'shift',
            'cart',
            'products',
            'bundles'
        ));
    }

    public function addBundle(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $bundle = ProductBundle::with('items.product')
            ->findOrFail($request->bundle_id);

        $key = 'bundle_'.$bundle->id;

        if (! isset($cart[$key])) {
            $cart[$key] = [
                'id' => $bundle->id,
                'type' => 'BUNDLE',
                'name' => $bundle->name,
                'qty' => 1,
                'price' => $bundle->bundle_price,
                'subtotal' => $bundle->bundle_price,
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
            $cart[$key]['qty']++;
            $cart[$key]['subtotal'] =
                $cart[$key]['qty'] * $cart[$key]['price'];
        }

        session()->put('pos_cart', $cart);

        return back();
    }

    public function add(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $product = Product::findOrFail($request->product_id);
        $price = $product->base_price ?? 0;

        $key = 'product_'.$product->id;

        if (! isset($cart[$key])) {
            $cart[$key] = [
                'id' => $product->id,
                'type' => 'PRODUCT',
                'name' => $product->name,
                'qty' => 1,
                'price' => $price,
                'subtotal' => $price,
                'note' => null,
            ];
        } else {
            $cart[$key]['qty']++;
            $cart[$key]['subtotal'] = $cart[$key]['qty'] * $price;
        }

        session()->put('pos_cart', $cart);

        return back();
    }

    public function note(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $key = $request->cart_key;

        if (isset($cart[$key])) {
            $cart[$key]['note'] = $request->note;
        }

        session()->put('pos_cart', $cart);

        return back();
    }

    // ======================================
    // REMOVE ITEM FROM CART
    // ======================================
    public function remove(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $key = $request->cart_key;

        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        session()->put('pos_cart', $cart);

        return back();
    }

    public function updateNote(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $key = $request->cart_key;
        $note = $request->note;

        if (isset($cart[$key])) {
            $cart[$key]['note'] = $note;
        }

        session()->put('pos_cart', $cart);

        return response()->json(['success' => true]);
    }

    public function pay(Request $request, $branchCode)
    {
        [$company, $branch, $shift] = $this->loadBranch($branchCode);

        if (! $shift) {
            return back()->with('error', 'Shift tidak aktif.');
        }

        $cart = session()->get('pos_cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // ===============================
        // HITUNG TOTAL
        // ===============================
        $total = collect($cart)->sum(fn ($c) => $c['subtotal']);

        // ===============================
        // VALIDASI CASH
        // ===============================
        if ($request->payment_method === 'CASH') {
            $request->validate([
                'paid_amount' => 'required|numeric|min:0',
            ]);

            if ($request->paid_amount < $total) {
                return back()->with('error', 'Uang pelanggan kurang dari total pembayaran.');
            }
        }

        DB::beginTransaction();

        try {

            // ===============================
            // GENERATE ORDER NUMBER
            // ===============================
            $running = PosOrder::where('cabang_resto_id', $branch->id)->count() + 1;
            $orderNum = strtoupper($branch->code).'-'.date('Y').'-'.str_pad($running, 6, '0', STR_PAD_LEFT);

            // ===============================
            // 1️⃣ BUILD RECEIPT ITEMS (🔥 KUNCI)
            // ===============================
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
                        'items' => collect($item['items'])->map(function ($bi) {
                            return [
                                'product_id' => $bi['product_id'],
                                'name' => $bi['name'],
                                'qty' => $bi['qty'],
                            ];
                        })->toArray(),

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

            // ===============================
            // 2️⃣ SIMPAN ORDER (DENGAN RECEIPT)
            // ===============================
            $order = PosOrder::create([
                'cabang_resto_id' => $branch->id,
                'order_datetime' => now(),
                'status' => $request->payment_method === 'MIDTRANS' ? 'PENDING' : 'PAID',
                'order_number' => $orderNum,
                'cashier_id' => auth()->id(),
                'pos_shifts_id' => $shift->id,
                'table_no' => $request->table_no ?? null,

                // 🔥 INI WAJIB
                'receipt_items' => $receiptItems,
            ]);

            // ===============================
            // 3️⃣ SIMPAN ORDER DETAILS (INTERNAL)
            // ===============================
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
                    // ===== BUNDLE → EXPAND KE ISI =====
                    foreach ($item['items'] as $bi) {
                        OrderDetail::create([
                            'pos_order_id' => $order->id,
                            'products_id' => $bi['product_id'],
                            'qty' => $bi['qty'] * $item['qty'],
                            'price' => 0,
                            'discount_pct' => 100,
                            'note_line' => 'Bundle: '.$item['name'],
                        ]);
                    }
                }
            }

            // ===============================
            // 4️⃣ SIMPAN PEMBAYARAN
            // ===============================
            if ($request->payment_method === 'MIDTRANS') {

                $mid = $request->midtrans_result ?? [];

                DB::table('pos_payments')->insert([
                    'pos_order_id' => $order->id,
                    'method' => 'QRIS',
                    'amount' => $total,
                    'status' => 'PENDING',
                    'ref_number' => $mid['transaction_id'] ?? null,
                    'note' => 'QRIS pending approval',
                    'paid_at' => now(),
                ]);

            } else {

                $paid = $request->paid_amount;
                $change = $paid - $total;

                DB::table('pos_payments')->insert([
                    'pos_order_id' => $order->id,
                    'method' => 'CASH',
                    'amount' => $total,
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                    'status' => 'SUCCESS',
                    'note' => 'Cash payment',
                    'paid_at' => now(),
                ]);

            }

            // ===============================
            // 5️⃣ KURANGI STOK VIA BOM
            // ===============================
            $this->reduceBomStock($company, $branch, $order, $cart);

            // ===============================
            // 6️⃣ CLEAR CART + COMMIT
            // ===============================
            session()->forget('pos_cart');
            DB::commit();
            if ($request->payment_method === 'MIDTRANS') {
                return response()->json([
                    'redirect' => route('branch.pos.order.receipt', [
                        'branchCode' => $branchCode,
                        'order' => $order->id,
                    ]),
                ]);
            }

            return redirect()
                ->route('branch.pos.order.receipt', [
                    'branchCode' => $branchCode,
                    'order' => $order->id,
                ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function reduceBomStock($company, $branch, $order, $cart)
    {
        $warehouseIds = DB::table('warehouse')
            ->where('cabang_resto_id', $branch->id)
            ->pluck('id');

        foreach ($cart as $item) {

            if (($item['type'] ?? 'PRODUCT') === 'PRODUCT') {

                $this->consumeProductBom(
                    $company,
                    $branch,
                    $order,
                    $item['id'],
                    $item['qty'],
                    $warehouseIds
                );

            } else {

                foreach ($item['items'] as $bundleItem) {

                    $this->consumeProductBom(
                        $company,
                        $branch,
                        $order,
                        $bundleItem['product_id'],
                        $bundleItem['qty'] * $item['qty'],
                        $warehouseIds
                    );

                }
            }
        }
    }

    protected function consumeProductBom(
        $company,
        $branch,
        $order,
        int $productId,
        int $orderQty,
        $warehouseIds
    ) {
        $product = Product::with('bomItems.item')->findOrFail($productId);

        foreach ($product->bomItems as $bom) {

            $neededQty = $bom->qty_per_unit * $orderQty;

            $stocks = DB::table('stocks')
                ->where('item_id', $bom->item_id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->where('qty', '>', 0)
                ->orderByRaw('expired_at IS NULL ASC')
                ->orderBy('expired_at', 'ASC')
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $s) {
                if ($neededQty <= 0) {
                    break;
                }

                $take = min($s->qty, $neededQty);

                DB::table('stocks')->where('id', $s->id)->update([
                    'qty' => $s->qty - $take,
                ]);

                DB::table('stock_movements')->insert([
                    'company_id' => $company->id,
                    'warehouse_id' => $s->warehouse_id,
                    'stock_id' => $s->id,
                    'item_id' => $bom->item_id,
                    'created_by' => auth()->id(),
                    'type' => 'OUT',
                    'qty' => -$take,
                    'reference' => $order->order_number,
                    'notes' => 'POS Sale',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $neededQty -= $take;
            }

            if ($neededQty > 0) {
                DB::rollBack();
                throw new \Exception(
                    "Stok bahan {$bom->item->name} tidak mencukupi!"
                );
            }
        }
    }

    public function createMidtransPayment(Request $request, $branchCode)
    {
        [$company, $branch, $shift] = $this->loadBranch($branchCode);

        $cart = session()->get('pos_cart', []);
        $total = collect($cart)->sum('subtotal');

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'POS-'.time(),
                'gross_amount' => $total,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    public function receipt(string $branchCode, PosOrder $order)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        // pastikan order milik cabang ini
        abort_if($order->cabang_resto_id !== $branch->id, 403);

        $order->load('payments', 'details.product');
        $payment = $order->payments->first();

        return view('branch.pos.order.receipt', compact(
            'branchCode',
            'branch',
            'order', 'payment'
        ));
    }
}
