<?php

namespace App\Http\Controllers\Branch;

use app\http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\Stock;
use App\Models\StocksAdjustmentDetail;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchItemController extends Controller
{
    public function index($branchCode)
    {
        $companyId = session('role.company.id');
        $branchId = session('role.branch.id');
        $companyCode = session('role.company.code');
        $warehouseIds = Warehouse::where('cabang_resto_id', $branchId)->pluck('id');

        $items = Item::withSum(['stocks as total_qty' => function ($q) use ($warehouseIds) {
            $q->whereIn('warehouse_id', $warehouseIds);
        }], 'qty')->where('company_id', $companyId)->get();

        return view('branch.item.index', compact('items', 'branchCode', 'companyCode'));
    }

    public function show($branchCode, Item $item)
    {
        $companyId = session('role.company.id');
        $branchId = session('role.branch.id');

        $warehouseIds = Warehouse::where('cabang_resto_id', $branchId)
            ->pluck('id');

        $stocks = Stock::with('warehouse')
            ->where('item_id', $item->id)
            ->whereIn('warehouse_id', $warehouseIds)
            ->get();
        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)->orderBy('name')->get();

        return view('branch.item.show', compact(
            'categoriesIssues',
            'item',
            'stocks',
            'branchCode'
        ));
    }

    public function create()
    {
        $companyId = session('role.company.id');
        $branchId = session('role.branch.id');
        $companyCode = session('role.company.code');
        $branchCode = session('role.branch.code');

        return view('branch.item.create', [
            'branchCode' => $branchCode,
            'companyCode' => $companyCode,
            'kategori' => Category::where('company_id', $companyId)->get(),
            'satuan' => Satuan::where('company_id', $companyId)->get(),
        ]);

    }

    public function store(Request $r)
    {
        $branchId = session('role.branch.id');
        $companyId = session('role.company.id');
        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => 'required|exists:satuan,id',
        ]);

        Item::create([
            'company_id' => $companyId,
            'name' => $r->name,
            'category_id' => $r->category_id,
            'satuan_id' => $r->satuan_id,
        ]);

        return redirect()->route('branch.item.index', $branchId)
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function destroy($branchCode, $id)
    {
        $companyId = session('role.company.id');
        $item = Item::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return redirect()->route('branch.item.index', $branchCode)
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function edit($branchCode, $id)
    {
        $branchCode = session('role.branch.code');
        $companyId = session('role.company.id');
        Log::info($id);
        $item = Item::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();
        Log::info($item);

        return view('branch.item.edit', [
            'branchCode' => $branchCode,
            'item' => $item,
            'kategori' => Category::where('company_id', $companyId)->get(),
            'satuan' => Satuan::where('company_id', $companyId)->get(),
        ]);
    }

    public function update(Request $r, $branchCode, $id)
    {
        $companyId = session('role.company.id');
        $branchId = session('role.branch.id');
        $item = Item::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => 'required|exists:satuan,id',
        ]);

        $item->update([
            'name' => $r->name,
            'category_id' => $r->category_id,
            'satuan_id' => $r->satuan_id,
        ]);

        return redirect()->route('branch.item.index', $branchId)
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function editStock($branchCode, Item $item, Warehouse $warehouse)
    {
        $stock = Stock::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        return view('branch.item.edit-stock', compact(
            'item', 'warehouse', 'stock', 'branchCode'
        ));
    }

    public function itemHistoryByItem($branchCode, Item $item)
    {
        $companyId = session('role.company.id');

        // 1️⃣ Validasi Branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // 2️⃣ Ambil seluruh warehouse di cabang
        $warehouseIds = Warehouse::where('cabang_resto_id', $branch->id)
            ->pluck('id');

        // 3️⃣ Ambil seluruh stock_id milik item di cabang ini
        $stockIds = Stock::where('company_id', $companyId)
            ->where('item_id', $item->id)
            ->whereIn('warehouse_id', $warehouseIds)
            ->pluck('id');

        if ($stockIds->isEmpty()) {
            $history = collect();
            $users = collect();
            $categoriesIssues = CategoriesIssues::all();

            return view('branch.item.history', compact(
                'branchCode',
                'item',
                'history',
                'users',
                'categoriesIssues'
            ));
        }

        // 4️⃣ FILTERS
        $filterIssue = request('issue');
        $filterUser = request('user');
        $filterFrom = request('from');
        $filterTo = request('to');

        // 5️⃣ Ambil semua HISTORI dari seluruh stock_ids
        $adjustments = StocksAdjustmentDetail::query()
            ->selectRaw('
            sa.adjustment_date AS date,
            s.code AS stock_code,
            i.name AS item_name,
            ci.name AS issue_name,
            d.prev_qty,
            d.after_qty,
            (d.after_qty - d.prev_qty) AS diff,
            sa.note AS note,
            u.username AS created_by_name,
            w.name AS warehouse_name
        ')
            ->from('stocks_adjustmens_detail AS d')
            ->join('stocks_adjustmens AS sa', 'sa.id', '=', 'd.stocks_adjustmens_id')
            ->join('stocks AS s', 's.id', '=', 'd.stocks_id')
            ->join('items AS i', 'i.id', '=', 's.item_id')
            ->join('categories_issues AS ci', 'ci.id', '=', 'sa.categories_issues_id')
            ->join('warehouse AS w', 'w.id', '=', 's.warehouse_id')
            ->leftJoin('users AS u', 'u.id', '=', 'sa.created_by')
            ->whereIn('d.stocks_id', $stockIds)
            ->where('s.company_id', $companyId);

        // 6️⃣ APPLY FILTERS
        if ($filterFrom) {
            $adjustments->whereDate('sa.adjustment_date', '>=', $filterFrom);
        }

        if ($filterTo) {
            $adjustments->whereDate('sa.adjustment_date', '<=', $filterTo);
        }

        if ($filterUser) {
            $adjustments->where('sa.created_by', $filterUser);
        }

        if ($filterIssue) {
            $adjustments->where('ci.name', $filterIssue);
        }

        // 7️⃣ Hasil
        $history = $adjustments->orderBy('sa.adjustment_date', 'desc')->get();

        // 8️⃣ Ambil daftar user yang pernah adjust item ini
        $users = User::whereIn('id', function ($q) use ($stockIds) {
            $q->select('created_by')
                ->from('stocks_adjustmens AS sa')
                ->join('stocks_adjustmens_detail AS d', 'sa.id', '=', 'd.stocks_adjustmens_id')
                ->whereIn('d.stocks_id', $stockIds)
                ->whereNotNull('sa.created_by');
        })->get();

        $categoriesIssues = CategoriesIssues::all();

        return view('branch.item.history', compact(
            'branchCode',
            'item',
            'history',
            'users',
            'categoriesIssues'
        ));
    }

    public function updateStock(Request $request, $branchCode, Item $item, Warehouse $warehouse)
    {
        $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        Stock::updateOrCreate(
            [
                'item_id' => $item->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'qty' => $request->qty,
            ]
        );

        return redirect()->route('branch.item.show', [$branchCode, $item->id])
            ->with('success', 'Stok berhasil diperbarui.');
    }
}
