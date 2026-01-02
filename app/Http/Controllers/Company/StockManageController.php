<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;

class StockManageController extends Controller
{
    /**
     * ===============================
     * INDEX – LIST STOK (COMPANY)
     * ===============================
     */
    public function index(Request $request)
    {
        $companyId = session('role.company.id');

        // ===============================
        // FILTERS
        // ===============================
        $search = $request->get('q');
        $branchId = $request->get('branch');
        $warehouseId = $request->get('warehouse');
        $itemId = $request->get('item');
        $categoryId = $request->get('category');

        // ===============================
        // DATA FILTER DROPDOWN
        // ===============================
        $branches = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $items = Item::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $categories = Category::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        // ===============================
        // QUERY STOCK
        // ===============================
        $stocks = Stock::query()
            ->with([
                'item.kategori',
                'item.satuan',
                'warehouse.cabangResto',
            ])
            ->where('company_id', $companyId)

            // Filter cabang
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('warehouse', function ($qq) use ($branchId) {
                    $qq->where('cabang_resto_id', $branchId);
                });
            })

            // Filter item
            ->when($itemId, function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            })

            // Search item
            ->when($search, function ($q) use ($search) {
                $q->whereHas('item', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%$search%")
                        ->orWhere('code', 'like', "%$search%");
                });
            })

            // Filter kategori item
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('item', function ($qq) use ($categoryId) {
                    $qq->where('category_id', $categoryId);
                });
            })

            ->orderBy('item_id')
            ->paginate(25);

        // ===============================
        // HITUNG DAYS TO EXPIRE
        // ===============================
        $stocks->getCollection()->transform(function ($stock) {
            if ($stock->expired_at) {
                $stock->days_to_expire = (int) ceil(
                    now()->diffInDays($stock->expired_at, false)
                );
            } else {
                $stock->days_to_expire = null;
            }

            return $stock;
        });

        return view('company.stockmanage.index', [
            'stocks' => $stocks,

            // filters
            'branches' => $branches,
            'items' => $items,
            'categories' => $categories,

            // selected
            'search' => $search,
            'selectedBranch' => $branchId,
            'selectedWarehouse' => $warehouseId,
            'selectedItem' => $itemId,
            'selectedCategory' => $categoryId,
        ]);
    }

    /**
     * ===============================
     * HISTORY – PER STOCK (COMPANY)
     * ===============================
     */
    public function history($companyCode, Stock $stock, Request $request)
    {
        $companyId = session('role.company.id');

        abort_if($stock->company_id !== $companyId, 403);

        $item = $stock->item;

        // ===============================
        // FILTER
        // ===============================
        $filterUser = $request->get('user');
        $filterIssue = $request->get('issue');
        $filterFrom = $request->get('from');
        $filterTo = $request->get('to');

        // ===============================
        // AMBIL HISTORY
        // ===============================
        $history = collect()
            ->merge($stock->historyAdjustments())
            ->merge($stock->historyMovements());

        // Filter collection
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

        $history = $history->sortByDesc('date')->values();

        // ===============================
        // DATA FILTER DROPDOWN
        // ===============================
        $users = User::whereIn(
            'username',
            $history->pluck('user')->filter()->unique()
        )->get();

        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('company.stockmanage.history', compact(
            'stock',
            'item',
            'history',
            'users',
            'categoriesIssues'
        ));
    }
}
