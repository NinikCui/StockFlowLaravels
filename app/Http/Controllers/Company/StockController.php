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
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Tenant check
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, "Gudang bukan milik perusahaan ini.");
        }

        // Generate CODE
        $prefix = 'STK-' . strtoupper($warehouse->code) . '-';

        $last = Stock::where('warehouse_id', $warehouse->id)
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = $last
            ? intval(substr($last->code, -4)) + 1
            : 1;

        $generatedCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Ambil items untuk select
        $items = Item::where('company_id', $company->id)
            ->with('satuan')
            ->orderBy('name')
            ->get();

        return view('company.warehouse.detail.partials.stock-in-create', [
            'companyCode'    => $companyCode,
            'warehouse'      => $warehouse,
            'items'          => $items,
            'generatedCode'  => $generatedCode,
        ]);
    }

    public function storeIn(Request $request, $companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $data = $request->validate([
            'code'    => 'required|string|max:50|unique:stocks,code',
            'item_id' => 'required|exists:items,id',
            'qty'     => 'required|numeric|min:0.01',
            'notes'   => 'nullable|string|max:255',
        ]);

        // Buat stok BARU (tidak reuse stok existing)
        $stock = Stock::create([
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'item_id'      => $data['item_id'],
            'qty'          => $data['qty'],
            'code'         => $data['code'],
        ]);

        // Movement
        StockMovement::create([
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'created_by'   => auth()->id(),
             'item_id'      => $data['item_id'],
            'stock_id'      => $stock->id,
            'type'         => 'IN',
            'qty'          => $data['qty'],
            'notes'        => $data['notes'],
            'reference'    => 'Manual Stock In - ' . $data['code'],
        ]);

        return redirect()->route('warehouse.show', [$companyCode, $warehouse->id])
            ->with('success', 'Stok berhasil ditambahkan.');
    }


    
    public function storeAdjustment(Request $request, $companyCode, $warehouseId)
    {
        $request->validate([
            'stock_id'              => 'required|exists:stocks,id',
            'prev_qty'              => 'required|numeric',
            'after_qty'             => 'required|numeric',
            'categories_issues_id'  => 'required|exists:categories_issues,id',
            'note'                  => 'nullable|string|max:200',
        ]);

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

        $stock = Stock::where('id', $request->stock_id)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        // 1️⃣ Buat header adjustment
        $adj = StocksAdjustment::create([
            'warehouse_id'         => $warehouseId,
            'categories_issues_id' => $request->categories_issues_id,
            'adjustment_date'      => now(),
            'status'               => 'POSTED',
            'note'                 => $request->note,
            'created_by'           => auth()->id(),
        ]);

        // 2️⃣ Detail adjustment
        StocksAdjustmentDetail::create([
            'stocks_adjustmens_id' => $adj->id,
            'stocks_id'            => $stock->id,
            'prev_qty'             => $request->prev_qty,
            'after_qty'            => $request->after_qty,
        ]);

        // 3️⃣ Update stok utama
        $stock->qty = $request->after_qty;
        $stock->save();

        
        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
    
    public function itemHistory($companyCode, $warehouseId, $stockId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        // Ambil stok (per stok, bukan per item)
        $stock = Stock::with('item')
            ->where('id', $stockId)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        $item = $stock->item;

        $filterIssue = request('issue');
        $filterUser  = request('user');
        $filterFrom  = request('from');
        $filterTo    = request('to');

        // ==================================
        // PENYESUAIAN SAJA
        // ==================================
        $adjustments = StocksAdjustmentDetail::query()
            ->selectRaw("
                stocks_adjustmens.adjustment_date AS date,
                '{$stock->code}' AS stock_code,
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
            ->where('stocks_adjustmens_detail.stocks_id', $stockId);

        // FILTER DATE
        if ($filterFrom) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '>=', $filterFrom);
        }

        if ($filterTo) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '<=', $filterTo);
        }

        // FILTER USER
        if ($filterUser) {
            $adjustments->where('stocks_adjustmens.created_by', $filterUser);
        }

        // FILTER ISSUE (Penyesuaian saja)
        if ($filterIssue) {
            $adjustments->where('categories_issues.name', $filterIssue);
        }

        // FINAL RESULT
        $history = $adjustments->orderBy('stocks_adjustmens.adjustment_date', 'desc')->get();

        // LIST USER yg pernah melakukan Adjustment pada stok ini
        $users = User::whereIn('id', function ($q) use ($stockId) {
                $q->select('stocks_adjustmens.created_by')
                    ->from('stocks_adjustmens')
                    ->join(
                        'stocks_adjustmens_detail',
                        'stocks_adjustmens.id',
                        '=',
                        'stocks_adjustmens_detail.stocks_adjustmens_id'
                    )
                    ->where('stocks_adjustmens_detail.stocks_id', $stockId);
            })
            ->get();

        $categoriesIssues = CategoriesIssues::all();

        return view('company.warehouse.detail.item-history', compact(
            'companyCode', 'warehouse', 'stock', 'item',
            'history', 'users', 'categoriesIssues'
        ));
    }

    






}
