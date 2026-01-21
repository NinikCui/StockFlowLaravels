<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Item;
use App\Models\Stock;
use App\Models\UnitConversion;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class itemManageController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('role.company.id');

        $branchId = $request->get('branch_id');

        $branches = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $warehouseIds = Warehouse::whereIn(
            'cabang_resto_id',
            $branchId
                ? [$branchId]
                : $branches->pluck('id')
        )->pluck('id');

        $items = Item::withSum([
            'stocks as total_qty' => function ($q) use ($warehouseIds) {
                $q->whereIn('warehouse_id', $warehouseIds);
            },
        ], 'qty')
            ->with('satuan') // penting
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        // 🔥 siapkan conversion PER SATUAN (siap pakai Blade)
        $unitConversions = UnitConversion::with('toSatuan')
            ->where('is_active', true)
            ->get()
            ->groupBy('from_satuan_id');

        return view('company.itemmanage.index', [
            'items' => $items,
            'branches' => $branches,
            'selectedBranch' => $branchId,
            'companyCode' => session('role.company.code'),
            'unitConversions' => $unitConversions,
        ]);
    }

    public function history(Request $request, $companyCode, Item $item)
    {
        $companyId = session('role.company.id');

        abort_if($item->company_id !== $companyId, 403);

        // =========================
        // FILTER INPUT
        // =========================
        $filterBranch = $request->get('branch');
        $filterWarehouse = $request->get('warehouse');
        $filterUser = $request->get('user');
        $filterIssue = $request->get('issue');
        $filterFrom = $request->get('from');
        $filterTo = $request->get('to');

        // =========================
        // AMBIL STOCK ITEM (LINTAS CABANG)
        // =========================
        $stocks = Stock::with(['warehouse.cabangResto'])
            ->where('company_id', $companyId)
            ->where('item_id', $item->id)
            ->when($filterBranch, function ($q) use ($filterBranch) {
                $q->whereHas('warehouse', function ($qq) use ($filterBranch) {
                    $qq->where('cabang_resto_id', $filterBranch);
                });
            })

            ->when($filterWarehouse, function ($q) use ($filterWarehouse) {
                $q->where('warehouse_id', $filterWarehouse);
            })
            ->get();

        $history = collect();

        foreach ($stocks as $stock) {

            $stockHistory = collect()
                ->merge($stock->historyAdjustments())
                ->merge($stock->historyMovements());

            $stockHistory = $stockHistory->map(function ($h) use ($stock) {
                $h->stock_code = $stock->code;
                $h->warehouse_name = $stock->warehouse->name;
                $h->cabang_name = $stock->warehouse->cabangResto->name ?? '-';
                $h->cabang_id = $stock->warehouse->cabangResto->id ?? null;

                return $h;
            });

            $history = $history->merge($stockHistory);
        }

        // =========================
        // FILTER COLLECTION
        // =========================
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

        // =========================
        // DATA UNTUK FILTER
        // =========================
        $branches = CabangResto::where('company_id', $companyId)->orderBy('name')->get();

        $warehouses = Warehouse::whereIn(
            'cabang_resto_id',
            $filterBranch
                ? [$filterBranch]
                : $branches->pluck('id')
        )->orderBy('name')->get();

        $users = User::whereIn(
            'username',
            $history->pluck('user')->filter()->unique()
        )->get();

        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
        $companyCodes = session('role.company.codeUrl');

        return view('company.itemmanage.history', compact(
            'item',
            'history',
            'branches',
            'warehouses',
            'users',
            'companyCodes',
            'categoriesIssues'
        ));
    }
}
