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
use App\Services\SesForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    private function fmt($n, int $dec = 2): string
    {
        return rtrim(rtrim(number_format((float) $n, $dec, '.', ''), '0'), '.');
    }

    public function detail($companyCode, Item $item, SesForecastService $ses)
    {
        $companyId = session('role.company.id');
        abort_unless($item->company_id == $companyId, 404);

        $branches = CabangResto::where('company_id', $companyId)->get();

        $alpha = 0.3;
        $monthsBack = 6;
        $start = Carbon::now()->startOfMonth()->subMonths($monthsBack - 1);
        $end = Carbon::now()->endOfMonth();

        $rows = [];
        $overallActualByMonth = [];

        // ✅ ambil min+max biar tabel bisa tampil max_stock juga (optional)
        $minMaxByBranch = DB::table('item_branch_min_stocks')
            ->where('company_id', $companyId)
            ->where('item_id', $item->id)
            ->get(['cabang_resto_id', 'min_stock', 'max_stock'])
            ->keyBy('cabang_resto_id');

        foreach ($branches as $branch) {

            $warehouseIds = Warehouse::where('cabang_resto_id', $branch->id)->pluck('id');

            $totalQty = (float) DB::table('stocks')
                ->where('item_id', $item->id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->sum('qty');

            $minStock = (float) ($minMaxByBranch[$branch->id]->min_stock ?? 0);
            $maxStock = (float) ($minMaxByBranch[$branch->id]->max_stock ?? 0);

            $warningThreshold = $minStock * 1.2;

            // ✅ aman: minStock harus > 0
            $isLow = $minStock > 0 && $totalQty < $minStock;
            $isNear = $minStock > 0 && $totalQty >= $minStock && $totalQty <= $warningThreshold;

            $history = DB::table('stock_movements as m')
                ->where('m.company_id', $companyId)
                ->where('m.item_id', $item->id)
                ->whereIn('m.warehouse_id', $warehouseIds)
                ->where('m.type', 'OUT')
                ->whereBetween('m.created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(m.created_at, '%Y-%m') as ym, SUM(ABS(m.qty)) as total_out")
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total_out', 'ym');

            $actuals = [];
            $monthKeys = [];
            $cursor = $start->copy()->startOfMonth();

            while ($cursor <= $end) {
                $ym = $cursor->format('Y-m');
                $val = (float) ($history[$ym] ?? 0);

                $actuals[] = $val;
                $monthKeys[] = $ym;

                // ✅ akumulasi untuk overall forecast
                $overallActualByMonth[$ym] = ($overallActualByMonth[$ym] ?? 0) + $val;

                $cursor->addMonth();
            }

            $forecastNext = $ses->forecastNext($actuals, $alpha);
            $forecastInt = (int) ceil($forecastNext ?? 0);

            // ✅ recommended sama persis seperti branch
            $recommendedRestock = 0;
            if ($isLow) {
                $recommendedRestock = max(1, ($minStock - $totalQty) + $forecastInt);
            } elseif ($isNear) {
                $recommendedRestock = max(0, $forecastInt);
            }

            $rows[] = [
                'branch' => $branch,
                'total_qty' => $totalQty,
                'min_stock' => $minStock,
                'max_stock' => $maxStock,
                'is_low_stock' => $isLow,
                'is_near_low_stock' => $isNear,
                'recommended_restock' => $recommendedRestock,
                'actuals_by_month' => array_combine($monthKeys, $actuals),
                'forecast_next' => $forecastNext,
            ];
        }

        // =========================
        // OVERALL (ALL CABANG)
        // =========================
        ksort($overallActualByMonth);

        $overallForecastNext = $ses->forecastNext(array_values($overallActualByMonth), $alpha);
        $overallForecastInt = (int) ceil($overallForecastNext ?? 0);

        $overallStockQty = array_sum(array_column($rows, 'total_qty'));
        $overallMinStock = array_sum(array_column($rows, 'min_stock'));
        $overallMaxStock = array_sum(array_column($rows, 'max_stock'));

        $overallWarningThreshold = $overallMinStock * 1.2;
        $overallIsLow = $overallMinStock > 0 && $overallStockQty < $overallMinStock;
        $overallIsNear = $overallMinStock > 0 && $overallStockQty >= $overallMinStock && $overallStockQty <= $overallWarningThreshold;

        $overallRecommendedRestock = 0;
        if ($overallIsLow) {
            $overallRecommendedRestock = max(1, ($overallMinStock - $overallStockQty) + $overallForecastInt);
        } elseif ($overallIsNear) {
            $overallRecommendedRestock = max(0, $overallForecastInt);
        }

        $rowsFormatted = array_map(function ($r) {
            return [
                'branch' => $r['branch'],
                'total_qty' => $this->fmt($r['total_qty']),
                'min_stock' => $this->fmt($r['min_stock']),
                'max_stock' => $this->fmt($r['max_stock']),
                'is_low_stock' => $r['is_low_stock'],
                'is_near_low_stock' => $r['is_near_low_stock'],
                'recommended_restock' => $this->fmt($r['recommended_restock']),
                'forecast_next' => $r['forecast_next'] === null ? '-' : $this->fmt($r['forecast_next']),
                'actuals_by_month' => array_map(fn ($v) => $this->fmt($v), $r['actuals_by_month']),
            ];
        }, $rows);

        return view('company.itemmanage.detail', [
            'item' => $item,
            'rows' => $rowsFormatted,
            'alpha' => $alpha,
            'monthsBack' => $monthsBack,
            'overall' => [
                'stock_qty' => $this->fmt($overallStockQty),
                'min_stock' => $this->fmt($overallMinStock),
                'max_stock' => $this->fmt($overallMaxStock),

                'is_low_stock' => $overallIsLow,
                'is_near_low_stock' => $overallIsNear,
                'recommended_restock' => $this->fmt($overallRecommendedRestock),

                'actuals_by_month' => array_map(fn ($v) => $this->fmt($v), $overallActualByMonth),

                'forecast_next' => $overallForecastNext === null ? '-' : $this->fmt($overallForecastNext),
            ],
            'companyCode' => $companyCode,
        ]);
    }
}
