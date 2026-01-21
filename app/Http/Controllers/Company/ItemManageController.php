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
        $monthsBack = 6; // contoh ambil 6 bulan histori terakhir
        $start = Carbon::now()->startOfMonth()->subMonths($monthsBack - 1);
        $end = Carbon::now()->endOfMonth();

        $rows = [];
        $overallActualByMonth = [];

        foreach ($branches as $branch) {

            $warehouseIds = Warehouse::where('cabang_resto_id', $branch->id)->pluck('id');

            $totalQty = DB::table('stocks')
                ->where('item_id', $item->id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->sum('qty');

            $history = DB::table('stock_movements as m')
                ->where('m.company_id', $companyId)
                ->where('m.item_id', $item->id)
                ->whereIn('m.warehouse_id', $warehouseIds)   // per cabang
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

                // kumpulkan untuk overall
                $overallActualByMonth[$ym] = ($overallActualByMonth[$ym] ?? 0) + $val;

                $cursor->addMonth();
            }

            $forecastNext = $ses->forecastNext($actuals, $alpha);

            $rows[] = [
                'branch' => $branch,
                'total_qty' => (float) $totalQty,
                'actuals_by_month' => array_combine($monthKeys, $actuals),
                'forecast_next' => $forecastNext,
            ];
        }

        // 3) forecast total seluruh cabang (gabungan)
        ksort($overallActualByMonth);
        $overallActuals = array_values($overallActualByMonth);
        $overallForecastNext = $ses->forecastNext($overallActuals, $alpha);

        // total stok gabungan
        $overallStockQty = array_sum(array_column($rows, 'total_qty'));

        $rowsFormatted = array_map(function ($r) {
            return [
                'branch' => $r['branch'],
                'total_qty' => $this->fmt($r['total_qty']),
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
                'actuals_by_month' => array_map(fn ($v) => $this->fmt($v), $overallActualByMonth),
                'forecast_next' => $overallForecastNext === null ? '-' : $this->fmt($overallForecastNext),
            ],
            'companyCode' => $companyCode,
        ]);
    }
}
