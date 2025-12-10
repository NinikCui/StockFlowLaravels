<?php

namespace App\Http\Controllers\Branch;

use app\http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\Stock;
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

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $warehouseIds = Warehouse::where('cabang_resto_id', $branch->id)->pluck('id');

        $stocks = Stock::with('warehouse')
            ->where('company_id', $companyId)
            ->where('item_id', $item->id)
            ->whereIn('warehouse_id', $warehouseIds)
            ->get();

        if ($stocks->isEmpty()) {
            return view('branch.item.history', [
                'branchCode' => $branchCode,
                'item' => $item,
                'history' => collect(),
                'users' => collect(),
                'categoriesIssues' => CategoriesIssues::where('company_id', $companyId)->get(),
            ]);
        }

        // 4️⃣ Kumpulkan seluruh stock IDs
        $stockIds = $stocks->pluck('id');

        // 5️⃣ Ambil FILTERS
        $filterIssue = request('issue');
        $filterUser = request('user');
        $filterFrom = request('from');
        $filterTo = request('to');

        $history = collect();

        foreach ($stocks as $stock) {

            // Ambil history universal dari masing-masing stock
            $stockHistory = collect()
                ->merge($stock->historyAdjustments())
                ->merge($stock->historyMovements());

            $stockHistory = $stockHistory->map(function ($h) use ($stock) {
                $h->stock_code = $stock->code;
                $h->warehouse_name = $stock->warehouse->name;

                return $h;
            });

            $history = $history->merge($stockHistory);
        }
        if ($filterFrom) {
            $history = $history->where('date', '>=', $filterFrom);
        }
        if ($filterTo) {
            $history = $history->where('date', '<=', $filterTo);
        }
        if ($filterUser) {
            $history = $history->where('user', $filterUser);
        }
        if ($filterIssue) {
            $history = $history->where('issue_name', $filterIssue);
        }

        // Sort descending by date
        $history = $history->sortByDesc('date')->values();

        // Ambil user dari histori
        $users = User::whereIn('username', $history->pluck('user')->filter())->get();

        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)->get();

        return view('branch.item.history', compact(
            'branchCode', 'item', 'history', 'users', 'categoriesIssues'
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
