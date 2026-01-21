<?php

namespace App\Http\Controllers\Branch;

use app\http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Category;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\Stock;
use App\Models\UnitConversion;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\SesForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchItemController extends Controller
{
    private function getAvailableSatuan($companyId)
    {
        return Satuan::whereNull('company_id')
            ->orWhere('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function index($branchCode, SesForecastService $ses)
    {
        $companyId = session('role.company.id');
        $branchId = session('role.branch.id');
        $companyCode = session('role.company.code');

        $warehouseIds = Warehouse::where('cabang_resto_id', $branchId)
            ->pluck('id');

        $items = Item::query()
            ->select([
                'items.*',
                DB::raw('COALESCE(ib.min_stock, 0) as min_stock'),
                DB::raw('COALESCE(ib.max_stock, 0) as max_stock'),
            ])
            ->leftJoin('item_branch_min_stocks as ib', function ($join) use ($companyId, $branchId) {
                $join->on('ib.item_id', '=', 'items.id')
                    ->where('ib.company_id', '=', $companyId)
                    ->where('ib.cabang_resto_id', '=', $branchId);
            })
            ->with('satuan')
            ->withSum(['stocks as total_qty' => function ($q) use ($warehouseIds) {
                $q->whereIn('warehouse_id', $warehouseIds);
            }], 'qty')
            ->where('items.company_id', $companyId)
            ->get();

        foreach ($items as $item) {
            $stokSekarang = $item->total_qty ?? 0;
            $minStock = (float) ($item->min_stock ?? 0);
            $warningThreshold = ceil($minStock * 1.2);

            $item->is_low_stock = $minStock > 0 && $stokSekarang < $minStock;
            $item->is_near_low_stock = $minStock > 0 && $stokSekarang >= $minStock && $stokSekarang <= $warningThreshold;

            $item->predicted_usage = 0;
            $item->recommended_restock = 0;

            if ($item->forecast_enabled && ($item->is_low_stock || $item->is_near_low_stock)) {
                $alpha = 0.3;
                $monthsBack = 6;
                $start = Carbon::now()->startOfMonth()->subMonths($monthsBack - 1);
                $end = Carbon::now()->endOfMonth();

                $history = DB::table('stock_movements as m')
                    ->where('m.company_id', $companyId)
                    ->where('m.item_id', $item->id)
                    ->whereIn('m.warehouse_id', $warehouseIds)   // ✅ penting: scope cabang
                    ->where('m.type', 'OUT')
                    ->whereBetween('m.created_at', [$start, $end])
                    ->selectRaw("DATE_FORMAT(m.created_at, '%Y-%m') as ym, SUM(ABS(m.qty)) as total_out")
                    ->groupBy('ym')
                    ->orderBy('ym')
                    ->pluck('total_out', 'ym');  // ['2025-08'=>10,'2025-09'=>7,...]

                $actuals = [];
                $cursor = $start->copy()->startOfMonth();
                while ($cursor <= $end) {
                    $ym = $cursor->format('Y-m');
                    $actuals[] = (float) ($history[$ym] ?? 0);
                    $cursor->addMonth();
                }

                $item->predicted_usage = $ses->forecastNext($actuals, $alpha) ?? 0;
                $item->recommended_restock = $item->is_low_stock
                ? max(1, ($minStock - $stokSekarang) + (int) ceil($item->predicted_usage))
                : max(1, (int) ceil($item->predicted_usage));
            }

            $exp = DB::table('stocks')
                ->where('item_id', $item->id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->whereNotNull('expired_at')
                ->orderBy('expired_at', 'asc')
                ->first();

            if ($exp) {
                $expDate = Carbon::parse($exp->expired_at);
                $item->days_to_expire = now()->diffInDays($expDate, false);
            } else {
                $item->days_to_expire = null;
            }
        }

        // 🔥 conversion universal
        $unitConversions = UnitConversion::with('toSatuan')
            ->where('is_active', true)
            ->get()
            ->groupBy('from_satuan_id');

        return view('branch.item.index', compact(
            'items',
            'branchCode',
            'companyCode',
            'unitConversions'
        ));
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

        // 🔥 Tambah hitung days_to_expire
        foreach ($stocks as $s) {
            if ($s->expired_at) {
                $s->days_to_expire = now()->diffInDays($s->expired_at, false);
            } else {
                $s->days_to_expire = null;
            }
        }

        $categoriesIssues = CategoriesIssues::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

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
            'satuan' => $this->getAvailableSatuan($companyId),

        ]);

    }

    public function store(Request $r, $branchCode)
    {
        $branchId = session('role.branch.id');
        $companyId = session('role.company.id');

        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => [
                'required',
                Rule::exists('satuan', 'id')->where(function ($q) use ($companyId) {
                    $q->whereNull('company_id')->orWhere('company_id', $companyId);
                }),
            ],
            'is_main_ingredient' => 'nullable|boolean',

            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0|gte:min_stock',

            'forecast_enabled' => 'nullable|boolean',
        ]);

        \DB::transaction(function () use ($r, $companyId, $branchId) {

            // 1) create item (company-level)
            $item = Item::create([
                'company_id' => $companyId,
                'name' => $r->name,
                'category_id' => $r->category_id,
                'satuan_id' => $r->satuan_id,
                'is_main_ingredient' => $r->has('is_main_ingredient') ? 1 : 0,

                'forecast_enabled' => $r->has('forecast_enabled') ? 1 : 0,
            ]);

            // 2) simpan setting min/max khusus cabang
            DB::table('item_branch_min_stocks')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'cabang_resto_id' => $branchId,
                    'item_id' => $item->id,
                ],
                [
                    'min_stock' => (int) $r->min_stock,
                    'max_stock' => (int) $r->max_stock,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        });

        return redirect()->route('branch.item.index', $branchCode)
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function destroy($branchCode, $id)
    {
        $companyId = session('role.company.id');

        $item = Item::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        DB::transaction(function () use ($companyId, $item) {
            DB::table('item_branch_min_stocks')
                ->where('company_id', $companyId)
                ->where('item_id', $item->id)
                ->delete();

            $item->delete();
        });

        return redirect()->route('branch.item.index', $branchCode)
            ->with('success', 'Item berhasil dihapus');
    }

    public function edit($branchCode, $id)
    {
        $branchId = session('role.branch.id');
        $companyId = session('role.company.id');

        $item = Item::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        $setting = DB::table('item_branch_min_stocks')
            ->where('company_id', $companyId)
            ->where('cabang_resto_id', $branchId)
            ->where('item_id', $item->id)
            ->first();

        $item->min_stock = $setting->min_stock ?? 0;
        $item->max_stock = $setting->max_stock ?? 0;

        return view('branch.item.edit', [
            'branchCode' => $branchCode,
            'item' => $item,
            'kategori' => Category::where('company_id', $companyId)->get(),
            'satuan' => $this->getAvailableSatuan($companyId),
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
            'satuan_id' => [
                'required',
                Rule::exists('satuan', 'id')->where(function ($q) use ($companyId) {
                    $q->whereNull('company_id')->orWhere('company_id', $companyId);
                }),
            ],
            'is_main_ingredient' => 'nullable|boolean',

            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0|gte:min_stock',

            'forecast_enabled' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($r, $item, $companyId, $branchId) {

            $item->update([
                'name' => $r->name,
                'category_id' => $r->category_id,
                'satuan_id' => $r->satuan_id,
                'is_main_ingredient' => $r->has('is_main_ingredient') ? 1 : 0,
                'forecast_enabled' => $r->has('forecast_enabled') ? 1 : 0,
            ]);

            DB::table('item_branch_min_stocks')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'cabang_resto_id' => $branchId,
                    'item_id' => $item->id,
                ],
                [
                    'min_stock' => (int) $r->min_stock,
                    'max_stock' => (int) $r->max_stock,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        });

        return redirect()->route('branch.item.index', $branchCode)
            ->with('success', 'Item berhasil diperbarui');
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
