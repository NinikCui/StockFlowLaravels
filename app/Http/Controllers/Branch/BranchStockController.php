<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StocksAdjustment;
use App\Models\StocksAdjustmentDetail;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchStockController extends Controller
{
    public function index($branchCode, Request $request)
    {
        // Ambil dari session
        $companyId = session('role.company.id');
        $companyCode = session('role.company.code');

        // Pastikan branch milik company
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Filters
        $search = $request->get('q');
        $categoryId = $request->get('category');
        $warehouseId = $request->get('warehouse');

        // Query stok
        $stocks = Stock::query()
            ->with([
                'item.kategori',
                'item.satuan',
                'warehouse',
            ])
            ->where('company_id', $companyId)

            // Filter branch → via warehouse
            ->whereHas('warehouse', function ($q) use ($branch) {
                $q->where('cabang_resto_id', $branch->id);
            })

            // Filter gudang
            ->when($warehouseId, function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })

            // Filter search item
            ->when($search, function ($q) use ($search) {
                $q->whereHas('item', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%$search%")
                        ->orWhere('code', 'like', "%$search%");
                });
            })

            // Filter kategori
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('item', function ($qq) use ($categoryId) {
                    $qq->where('category_id', $categoryId);
                });
            })

            ->orderBy('item_id')
            ->paginate(20);

        $stocks->getCollection()->transform(function ($stock) {
            if ($stock->expired_at) {
                // FALSE = boleh menghasilkan angka negatif jika sudah lewat
                $stock->days_to_expire = now()->diffInDays($stock->expired_at, false);

                // dibulatkan agar tidak mengeluarkan desimal
                $stock->days_to_expire = (int) ceil($stock->days_to_expire);
            } else {
                $stock->days_to_expire = null;
            }

            return $stock;
        });

        // Gudang berdasarkan cabang
        $warehouses = Warehouse::where('cabang_resto_id', $branch->id)
            ->orderBy('name')
            ->get();

        // Kategori item
        $categories = Category::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        // Issue categories (untuk modal adjust)
        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.stock.index', [
            'companyCode' => strtolower($companyCode),
            'branchCode' => $branchCode,
            'branch' => $branch,
            'stocks' => $stocks,
            'categoriesIssues' => $categoriesIssues,
            'categories' => $categories,
            'warehouses' => $warehouses,

            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedWarehouse' => $warehouseId,
        ]);
    }

    public function adjustStore(Request $request, $branchCode)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'prev_qty' => 'required|numeric',
            'after_qty' => 'required|numeric',
            'categories_issues_id' => 'required|exists:categories_issues,id',
            'note' => 'nullable|string|max:200',
        ]);

        if ($request->after_qty == $request->prev_qty) {
            return back()->withErrors([
                'after_qty' => 'Qty penyesuaian tidak berubah.',
            ])->withInput();
        }

        $companyId = session('role.company.id');

        // Validasi branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil stok (stock harus milik company dan cabang melalui warehouse)
        $stock = Stock::with('warehouse')
            ->where('company_id', $companyId)
            ->where('id', $request->stock_id)
            ->firstOrFail();

        // Pastikan gudang yg memuat stok ini milik cabang tersebut
        if ($stock->warehouse->cabang_resto_id !== $branch->id) {
            abort(403, 'Gudang tidak valid untuk cabang ini.');
        }

        $warehouseId = $stock->warehouse_id;

        DB::transaction(function () use ($request, $stock, $warehouseId) {

            // 1️⃣ HEADER ADJUSTMENT
            $adj = StocksAdjustment::create([
                'warehouse_id' => $warehouseId,
                'categories_issues_id' => $request->categories_issues_id,
                'adjustment_date' => now(),
                'status' => 'POSTED',
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            // 2️⃣ DETAIL ADJUSTMENT
            StocksAdjustmentDetail::create([
                'stocks_adjustmens_id' => $adj->id,
                'stocks_id' => $stock->id,
                'prev_qty' => $request->prev_qty,
                'after_qty' => $request->after_qty,
            ]);

            // 3️⃣ UPDATE QTY STOCK
            $stock->update([
                'qty' => $request->after_qty,
            ]);
        });

        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }

    public function itemHistory($branchCode, Stock $stock)
    {
        $companyId = session('role.company.id');

        // Validasi branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        if ($stock->company_id !== $companyId) {
            abort(403);
        }
        if ($stock->warehouse->cabang_resto_id !== $branch->id) {
            abort(403);
        }

        $item = $stock->item;

        // Ambil semua history, tidak pakai UNION SQL
        $history = collect()
            ->merge($stock->historyAdjustments())
            ->merge($stock->historyMovements())
            ->sortByDesc('date')
            ->values();

        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)->get();
        $users = User::whereIn('id', function ($q) use ($stock) {
            $q->select('created_by')
                ->from('stocks_adjustmens AS sa')
                ->join('stocks_adjustmens_detail AS d', 'sa.id', '=', 'd.stocks_adjustmens_id')
                ->where('d.stocks_id', $stock->id)
                ->whereNotNull('sa.created_by');
        })
            ->get();

        return view('branch.stock.history', compact(
            'branchCode', 'stock', 'item', 'history', 'categoriesIssues', 'users'
        ));
    }

    public function createStockIn($branchCode)
    {
        $companyId = session('role.company.id');

        // Validasi branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil semua gudang milik branch
        $warehouses = Warehouse::where('cabang_resto_id', $branch->id)
            ->orderBy('name')
            ->get();

        // Ambil items milik company
        $items = Item::where('company_id', $companyId)
            ->with('satuan')
            ->orderBy('name')
            ->get();

        return view('branch.stock.create', [
            'branchCode' => $branchCode,
            'branch' => $branch,
            'warehouses' => $warehouses,
            'items' => $items,
        ]);
    }

    public function storeStockIn(Request $request, $branchCode)
    {
        $companyId = session('role.company.id');

        // Validasi branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|numeric|min:0.01',
            'expired_at' => 'required|date|after:today',
            'notes' => 'nullable|string|max:255',
        ], [
            'warehouse_id.required' => 'Gudang wajib dipilih.',
            'item_id.required' => 'Item wajib dipilih.',
            'qty.required' => 'Jumlah stok wajib diisi.',
            'expired_at.required' => 'Expired date wajib diisi.',
            'expired_at.after' => 'Expired date harus lebih besar dari hari ini.',
        ]);

        // Pastikan warehouse milik branch
        $warehouse = Warehouse::where('id', $data['warehouse_id'])
            ->where('cabang_resto_id', $branch->id)
            ->firstOrFail();

        // Generate kode stok
        $prefix = 'STK-'.strtoupper($warehouse->code).'-';

        $last = Stock::where('warehouse_id', $warehouse->id)
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = $last
            ? intval(substr($last->code, -4)) + 1
            : 1;

        $generatedCode = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // SIMPAN STOCK
        $stock = Stock::create([
            'company_id' => $companyId,
            'warehouse_id' => $warehouse->id,
            'item_id' => $data['item_id'],
            'qty' => $data['qty'],
            'code' => $generatedCode,
            'expired_at' => $data['expired_at'], // ⬅️ TAMBAH
        ]);

        // STOCK MOVEMENT
        StockMovement::create([
            'company_id' => $companyId,
            'warehouse_id' => $warehouse->id,
            'created_by' => auth()->id(),
            'item_id' => $data['item_id'],
            'stock_id' => $stock->id,
            'type' => 'IN',
            'qty' => $data['qty'],
            'expired_at' => $data['expired_at'], // ⬅️ TAMBAH
            'notes' => $data['notes'],
            'reference' => 'Branch Stock In - '.$generatedCode,
        ]);

        return redirect()
            ->route('branch.stock.index', $branchCode)
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function destroy($branchCode, Stock $stock)
    {
        $companyId = session('role.company.id');

        // Pastikan stok ini milik company yang benar
        if ($stock->company_id !== $companyId) {
            abort(403, 'Stok tidak valid.');
        }

        if ($stock->qty > 0) {
            return back()->with('error', 'Stok tidak bisa dihapus karena masih memiliki jumlah.');
        }

        $stock->delete();

        return back()->with('success', 'Stok berhasil dihapus.');
    }
}
