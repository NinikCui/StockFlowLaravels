<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\OrderDetail;
use App\Models\PosOrder;
use App\Models\PosShift;
use App\Models\Product;
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

    // ======================================
    // POS SCREEN
    // ======================================
    public function index($branchCode)
    {
        [$company, $branch, $shift] = $this->loadBranch($branchCode);

        if (! $shift) {
            return redirect()
                ->route('branch.pos.shift.index', [$branchCode])
                ->with('error', 'Tidak dapat membuka POS karena shift belum dibuka.');
        }

        $companyCode = session('role.company.code');
        $cart = session()->get('pos_cart', []);
        foreach ($cart as $key => $item) {
            if (! array_key_exists('note', $item)) {
                $cart[$key]['note'] = null;
            }
        }
        $products = Product::with('bomItems.item')
            ->where('company_id', $company->id)
            ->get()
            ->map(function ($product) {

                $product->is_available = $product->isStockAvailableForOne();

                return $product;
            });

        return view('branch.pos.order.index', compact(
            'companyCode', 'branchCode', 'branch', 'shift', 'cart', 'products'
        ));
    }

    // ======================================
    // ADD ITEM KE CART (SESSION)
    // ======================================
    public function add(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $product = Product::findOrFail($request->product_id);
        $price = $product->base_price ?? 0;

        if (! isset($cart[$product->id])) {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $price,
                'subtotal' => $price, 'note' => null,
            ];
        } else {
            $cart[$product->id]['qty']++;
            $cart[$product->id]['subtotal'] = $cart[$product->id]['qty'] * $price;
        }

        session()->put('pos_cart', $cart);

        return back();
    }

    public function note(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['note'] = $request->note;
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

        unset($cart[$request->product_id]);

        session()->put('pos_cart', $cart);

        return back();
    }

    public function updateNote(Request $request, $branchCode)
    {
        $cart = session()->get('pos_cart', []);

        $productId = $request->product_id;
        $note = $request->note;

        if (isset($cart[$productId])) {
            $cart[$productId]['note'] = $note;
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

        // Hitung total
        $total = collect($cart)->sum(fn ($c) => $c['subtotal']);

        // Validasi cash payment
        if ($request->payment_method === 'CASH') {
            $request->validate([
                'paid_amount' => 'required|numeric|min:0',
            ]);

            if ($request->paid_amount < $total) {
                return back()->with('error', 'Uang pelanggan kurang dari total pembayaran.');
            }
        }

        DB::beginTransaction();

        // Generate nomor order
        $running = PosOrder::where('cabang_resto_id', $branch->id)->count() + 1;
        $orderNum = strtoupper($branch->code).'-'.date('Y').'-'.str_pad($running, 6, '0', STR_PAD_LEFT);

        // ===============================
        // 1) Simpan order
        // ===============================
        $order = PosOrder::create([
            'cabang_resto_id' => $branch->id,
            'order_datetime' => now(),
            'status' => $request->payment_method === 'MIDTRANS' ? 'PENDING' : 'PAID',
            'order_number' => $orderNum,
            'cashier_id' => auth()->id(),
            'pos_shifts_id' => $shift->id,
            'table_no' => $request->table_no ?? null,
        ]);

        // ===============================
        // 2) Simpan detail order
        // ===============================
        foreach ($cart as $item) {
            OrderDetail::create([
                'pos_order_id' => $order->id,
                'products_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'discount_pct' => 0,
                'note_line' => $item['note'] ?? null,
            ]);
        }

        // ===============================
        // 3) Simpan pembayaran
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

            $change = $request->paid_amount - $total;

            DB::table('pos_payments')->insert([
                'pos_order_id' => $order->id,
                'method' => 'CASH',
                'amount' => $total,
                'status' => 'SUCCESS',
                'ref_number' => null,
                'note' => 'Cash payment; Change: '.$change,
                'paid_at' => now(),
            ]);
        }

        // ===============================
        // 4) Kurangi stok (FEFO) via BOM
        // ===============================
        $this->reduceBomStock($company, $branch, $order, $cart);

        // ===============================
        // 5) Hapus cart
        // ===============================
        session()->forget('pos_cart');

        DB::commit();

        return redirect()
            ->route('branch.pos.order.index', [$branchCode])
            ->with('success', "Order {$orderNum} berhasil dibuat!");
    }

    protected function reduceBomStock($company, $branch, $order, $cart)
    {
        $warehouseIds = DB::table('warehouse')
            ->where('cabang_resto_id', $branch->id)
            ->pluck('id');

        foreach ($cart as $item) {

            $product = Product::with('bomItems')->find($item['id']);

            foreach ($product->bomItems as $bom) {

                $neededQty = $bom->qty_per_unit * $item['qty'];

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
                    throw new \Exception("Stok bahan {$bom->item->name} tidak mencukupi!");
                }
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
}
