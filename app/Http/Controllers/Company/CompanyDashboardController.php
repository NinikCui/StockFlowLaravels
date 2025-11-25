<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Item;
use App\Models\PoReceive;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Carbon;

class CompanyDashboardController extends Controller
{
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        $totalBranches = CabangResto::where('company_id', $companyId)->count();
        $totalSuppliers = Supplier::where('company_id', $companyId)->count();
        $totalItems = Item::where('company_id', $companyId)->count();
        $totalEmployees = 0;

        // Semua gudang perusahaan
        $warehouses = Warehouse::whereHas('cabangResto',
            fn ($q) => $q->where('company_id', $companyId)
        )->get();

        $warehouseIds = $warehouses->pluck('id');

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $requestMonth = InventoryTrans::whereIn('warehouse_id_from', $warehouseIds)
            ->whereBetween('trans_date', [$start, $end])
            ->count();

        $poMonth = PurchaseOrder::whereHas('cabangResto',
            fn ($q) => $q->where('company_id', $companyId)
        )
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $receivedMonth = PoReceive::whereHas('purchaseOrder.cabangResto',
            fn ($q) => $q->where('company_id', $companyId)
        )
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $requestTrend = InventoryTrans::selectRaw("
                DATE_FORMAT(trans_date, '%Y-%m') AS month,
                COUNT(*) AS total
            ")
            ->whereIn('warehouse_id_from', $warehouseIds)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $poTrend = PurchaseOrder::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COUNT(*) AS total
            ")
            ->whereHas('cabangResto', fn ($q) => $q->where('company_id', $companyId))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $avgPerMonth = $requestTrend->avg() ?? 0;

        $branches = CabangResto::with('warehouses')
            ->where('company_id', $companyId)
            ->get();

        $heatmap = [];

        foreach ($branches as $from) {
            foreach ($branches as $to) {
                if (! isset($heatmap[$from->name])) {
                    $heatmap[$from->name] = [];
                }

                // jika cabang belum punya gudang
                if (! $from->warehouse || ! $to->warehouse) {
                    $heatmap[$from->name][$to->name] = 0;

                    continue;
                }

                $heatmap[$from->name][$to->name] = InventoryTrans::where('warehouse_id_from', $from->warehouse->id)
                    ->where('warehouse_id_to', $to->warehouse->id)
                    ->count();
            }
        }

        $fastItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
            ->whereHas('header', function ($q) use ($warehouseIds) {
                $q->whereIn('warehouse_id_from', $warehouseIds);
            })
            ->groupBy('items_id')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'items_id');

        $latestRequest = InventoryTrans::with(['warehouseFrom.cabangResto', 'warehouseTo.cabangResto'])
            ->whereIn('warehouse_id_from', $warehouseIds)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $latestPO = PurchaseOrder::with('cabangResto')
            ->whereHas('cabangResto', fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $latestReceive = PoReceive::with(['purchaseOrder.cabangResto'])
            ->whereHas('purchaseOrder.cabangResto', fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('company.dashboard', compact(
            'companyCode',
            'totalBranches',
            'totalSuppliers',
            'totalItems',
            'totalEmployees',
            'requestMonth',
            'poMonth',
            'receivedMonth',
            'avgPerMonth',
            'requestTrend',
            'poTrend',
            'heatmap',
            'fastItems',
            'latestRequest',
            'latestPO',
            'latestReceive'
        ));
    }
}
