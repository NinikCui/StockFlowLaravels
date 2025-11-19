<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StocksAdjustment;
use App\Models\StocksAdjustmentDetail;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class StockController extends Controller
{
    public function createIn($companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::where('id', $warehouseId)->firstOrFail();

        // Validasi tenant
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid untuk perusahaan ini.');
        }

        // Semua item milik company
        $items = Item::where('company_id', $company->id)
            ->with(['kategori', 'satuan'])
            ->orderBy('name')
            ->get();

        return view('company.warehouse.detail.partials.stock-in-create', [
            'companyCode' => $companyCode,
            'warehouse'   => $warehouse,
            'items'       => $items,
        ]);
    }

    public function storeIn(Request $request, $companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Tenant check
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        // Validasi form
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty'     => 'required|numeric|min:0.01',
            'notes'   => 'nullable|string|max:255',
        ]);

        // Cek stok eksisting
        $stock = Stock::where('warehouse_id', $warehouse->id)
            ->where('item_id', $data['item_id'])
            ->first();

        if (!$stock) {
            $stock = Stock::create([
                'company_id'   => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_id'      => $data['item_id'],
                'qty'          => 0,
            ]);
        }

        // Tambahkan stok
        $stock->qty += $data['qty'];
        $stock->save();

        // Buat movement log
        StockMovement::create([
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'created_by'   => auth()->id(),
            'item_id'      => $data['item_id'],
            'type'         => 'IN',
            'qty'          => $data['qty'],
            'notes'        => $data['notes'],
            'reference'    => 'Manual Stock In',
            
        ]);

        return redirect()->route('warehouse.show', [$companyCode, $warehouse->id])
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function storeAdjustment(Request $request, $companyCode, $warehouseId)
    {
        if ($request->after_qty == $request->prev_qty) {
            return back()->withErrors([
                'after_qty' => 'Qty penyesuaian tidak berubah.'
            ])->withInput();
        }
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $data = $request->validate([
            'stock_id'              => 'required|exists:stocks,id',
            'prev_qty'              => 'required|numeric',
            'after_qty'             => 'required|numeric',
            'categories_issues_id'  => 'required|exists:categories_issues,id',
            'note'                  => 'nullable|string|max:200',
        ]);

        $stock = Stock::findOrFail($data['stock_id']);

        // 1️⃣ Buat header adjustment
        $adj = StocksAdjustment::create([
            'warehouse_id'         => $warehouseId,
            'categories_issues_id' => $data['categories_issues_id'],
            'adjustment_date'      => now(),
            'status'               => 'DRAFT',
            'note'                 => $data['note'],
            'created_by'           => auth()->id(),
            'stockId'              => $stock->id,
        ]);

        // 2️⃣ Buat detail adjustment
        StocksAdjustmentDetail::create([
            'stocks_adjustmens_id' => $adj->id,
            'stocks_id'            => $stock->id,
            'prev_qty'             => $data['prev_qty'],
            'after_qty'            => $data['after_qty'],
        ]);

        // 3️⃣ Update stok utama
        $stock->qty = $data['after_qty'];
        $stock->save();

        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
    public function itemHistory($companyCode, $warehouseId, $itemId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $stock = Stock::where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->firstOrFail();

        $item = Item::findOrFail($itemId);

        $issueFilter = request('issue');

        // ================================
        // PENYESUAIAN
        // ================================
        $adjustments = StocksAdjustmentDetail::query()
            ->selectRaw("
                stocks_adjustmens.adjustment_date AS date,
                items.name AS item_name,
                categories_issues.name AS issue_name,
                prev_qty,
                after_qty,
                (after_qty - prev_qty) AS diff,
                stocks_adjustmens.note AS note,
                users.username AS created_by_name
            ")
            ->join('stocks_adjustmens', 'stocks_adjustmens.id', '=', 'stocks_adjustmens_detail.stocks_adjustmens_id')
            ->join('stocks', 'stocks.id', '=', 'stocks_adjustmens_detail.stocks_id')
            ->join('items', 'items.id', '=', 'stocks.item_id')
            ->join('categories_issues', 'categories_issues.id', '=', 'stocks_adjustmens.categories_issues_id')
            ->leftJoin('users', 'users.id', '=', 'stocks_adjustmens.created_by')
            ->where('stocks_adjustmens_detail.stocks_id', $stock->id);

        if (request('from')) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '>=', request('from'));
        }

        if (request('to')) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '<=', request('to'));
        }

        if (request('user')) {
            $adjustments->where('stocks_adjustmens.created_by', request('user'));
        }

        if ($issueFilter && !in_array($issueFilter, ['Stok Masuk', 'Stok Keluar'])) {
            $adjustments->where('categories_issues.name', $issueFilter);
        }


        // ================================
        // MOVEMENT
        // ================================
        $movements = StockMovement::query()
            ->selectRaw("
                stock_movements.created_at AS date,
                items.name AS item_name,
                CASE 
                    WHEN stock_movements.type='IN' THEN 'Stok Masuk'
                    WHEN stock_movements.type='OUT' THEN 'Stok Keluar'
                    ELSE stock_movements.type
                END AS issue_name,

                NULL AS prev_qty,
                NULL AS after_qty,

                CASE
                    WHEN stock_movements.type='IN' THEN qty
                    WHEN stock_movements.type='OUT' THEN -qty
                    ELSE qty
                END AS diff,

                stock_movements.notes AS note,
                users.username AS created_by_name
            ")
            ->join('items', 'items.id', '=', 'stock_movements.item_id')
            ->leftJoin('users', 'users.id', '=', 'stock_movements.created_by')
            ->where('stock_movements.item_id', $itemId)
            ->where('stock_movements.warehouse_id', $warehouseId);

        if (request('from')) {
            $movements->whereDate('stock_movements.created_at', '>=', request('from'));
        }

        if (request('to')) {
            $movements->whereDate('stock_movements.created_at', '<=', request('to'));
        }

        if (request('user')) {
            $movements->where('stock_movements.created_by', request('user'));
        }

        if ($issueFilter === 'Stok Masuk') {
            $movements->where('stock_movements.type', 'IN');
        } elseif ($issueFilter === 'Stok Keluar') {
            $movements->where('stock_movements.type', 'OUT');
        }


        // ================================
        // GABUNGKAN
        // ================================
        if ($issueFilter === 'Stok Masuk' || $issueFilter === 'Stok Keluar') {
            $history = $movements->get();
        } elseif ($issueFilter) {
            $history = $adjustments->get();
        } else {
            $history = collect()
                ->merge($movements->get())
                ->merge($adjustments->get());
        }

        $history = $history->sortByDesc('date')->values();


        // USERS
        $users = User::whereIn('id', function ($q) use ($itemId, $warehouseId) {
                $q->select('created_by')
                    ->from('stock_movements')
                    ->where('item_id', $itemId)
                    ->where('warehouse_id', $warehouseId);
            })
            ->orWhereIn('id', function ($q) use ($stock) {
                $q->select('created_by')
                    ->from('stocks_adjustmens')
                    ->join('stocks_adjustmens_detail', 'stocks_adjustmens.id', '=', 'stocks_adjustmens_detail.stocks_adjustmens_id')
                    ->where('stocks_adjustmens_detail.stocks_id', $stock->id);
            })
            ->get();

        $categoriesIssues = CategoriesIssues::all();

        return view('company.warehouse.detail.item-history', compact(
            'companyCode', 'warehouse', 'item',
            'history', 'users', 'categoriesIssues'
        ));
    }


    






}
