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
use Illuminate\Support\Carbon;

class CompanyDashboardController extends Controller
{
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        /* =======================================
         * BASIC KPI
         * ======================================= */
        $totalBranches = CabangResto::where('company_id', $companyId)->count();
        $totalSuppliers = Supplier::where('company_id', $companyId)->count();
        $totalItems = Item::where('company_id', $companyId)->count();
        $totalEmployees = 0; // nanti bisa diisi

        $branches = CabangResto::where('company_id', $companyId)->get();
        $branchIds = $branches->pluck('id');

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        /* =======================================
         * REQUEST (CABANG) - THIS MONTH
         * ======================================= */
        $requestMonth = InventoryTrans::whereIn('cabang_id_from', $branchIds)
            ->whereBetween('trans_date', [$start, $end])
            ->count();

        /* =======================================
         * PURCHASE ORDER - THIS MONTH
         * ======================================= */
        $poMonth = PurchaseOrder::whereIn('cabang_resto_id', $branchIds)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $receivedMonth = PoReceive::whereHas('purchaseOrder', function ($q) use ($branchIds) {
            $q->whereIn('cabang_resto_id', $branchIds);
        })
            ->whereBetween('created_at', [$start, $end])
            ->count();

        /* =======================================
         * REQUEST TREND (12 BULAN) - CABANG
         * ======================================= */
        $requestTrend = InventoryTrans::selectRaw("
                DATE_FORMAT(trans_date, '%Y-%m') AS month,
                COUNT(*) AS total
            ")
            ->whereIn('cabang_id_from', $branchIds)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        /* =======================================
         * PO TREND (12 BULAN)
         * ======================================= */
        $poTrend = PurchaseOrder::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COUNT(*) AS total
            ")
            ->whereIn('cabang_resto_id', $branchIds)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $avgPerMonth = $requestTrend->avg() ?? 0;

        /* =======================================
         * HEATMAP TRANSFER ANTAR CABANG
         * ======================================= */
        $heatmap = [];

        foreach ($branches as $from) {
            foreach ($branches as $to) {

                if (! isset($heatmap[$from->name])) {
                    $heatmap[$from->name] = [];
                }

                $heatmap[$from->name][$to->name] = InventoryTrans::where('cabang_id_from', $from->id)
                    ->where('cabang_id_to', $to->id)
                    ->count();
            }
        }

        /* =======================================
         * FAST MOVING ITEMS (TOP 10)
         * ======================================= */
        $fastItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
            ->whereHas('header', function ($q) use ($branchIds) {
                $q->whereIn('cabang_id_from', $branchIds);
            })
            ->groupBy('items_id')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'items_id');

        /* =======================================
         * LATEST REQUEST (CABANG)
         * ======================================= */
        $latestRequest = InventoryTrans::with(['cabangFrom', 'cabangTo'])
            ->whereIn('cabang_id_from', $branchIds)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* =======================================
         * LATEST PO
         * ======================================= */
        $latestPO = PurchaseOrder::with('cabangResto')
            ->whereIn('cabang_resto_id', $branchIds)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* =======================================
         * LATEST RECEIVE
         * ======================================= */
        $latestReceive = PoReceive::with(['purchaseOrder.cabangResto'])
            ->whereHas('purchaseOrder', fn ($q) => $q->whereIn('cabang_resto_id', $branchIds))
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* =======================================
         * RETURN VIEW
         * ======================================= */
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
