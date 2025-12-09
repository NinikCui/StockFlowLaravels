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
        $products = Product::where('company_id', $company->id)->get();

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

    // ======================================
    // PAY → CREATE pos_order + order_detail
    // ======================================
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

        $total = collect($cart)->sum('subtotal');

        DB::beginTransaction();

        // Generate nomor order (SBY-2025-000001)
        $running = PosOrder::where('cabang_resto_id', $branch->id)->count() + 1;
        $orderNum = strtoupper($branch->code).'-'.date('Y').'-'.str_pad($running, 6, '0', STR_PAD_LEFT);

        // CREATE pos_order
        $order = PosOrder::create([
            'cabang_resto_id' => $branch->id,
            'order_datetime' => now(),
            'status' => 'OPEN',
            'order_number' => $orderNum,
            'cashier_id' => auth()->id(),
            'pos_shifts_id' => $shift->id,
            'table_no' => $request->table_no ?? null,
        ]);
        $notes = $request->notes ?? [];
        // CREATE order_detail
        foreach ($cart as $item) {

            OrderDetail::create([
                'pos_order_id' => $order->id,
                'products_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'discount_pct' => 0,
                'note_line' => $notes[$item['id']] ?? null,
            ]);
        }

        // CLEAR CART
        session()->forget('pos_cart');

        DB::commit();

        return redirect()
            ->route('branch.pos.order.index', [$branchCode])
            ->with('success', 'Order berhasil dibuat (status OPEN). Lanjutkan pembayaran!');
    }
}
