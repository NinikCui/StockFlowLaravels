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

        /* ===============================
         * CABANG
         * ===============================*/
        $branches = CabangResto::where('company_id', $companyId)->get();
        $branchIds = $branches->pluck('id');

        /* ===============================
         * BASIC KPI
         * ===============================*/
        $totalBranches = $branches->count();
        $totalSuppliers = Supplier::where('company_id', $companyId)->count();
        $totalItems = Item::where('company_id', $companyId)->count();
        $totalEmployees = 0;

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        /* ===============================
         * REQUEST BULAN INI
         * ===============================*/
        $requestMonth = InventoryTrans::whereIn('cabang_id_to', $branchIds)
            ->whereBetween('trans_date', [$start, $end])
            ->where('status', '!=', 'DRAFT')
            ->count();

        /* ===============================
         * PURCHASE ORDER BULAN INI
         * ===============================*/
        $poMonth = PurchaseOrder::whereIn('cabang_resto_id', $branchIds)
            ->whereBetween('po_date', [$start, $end])
            ->count();

        /* ===============================
         * HEATMAP TRANSFER ANTAR CABANG
         * ===============================*/
        $heatmap = [];
        foreach ($branches as $from) {
            foreach ($branches as $to) {
                $heatmap[$from->name][$to->name] = InventoryTrans::where('cabang_id_from', $from->id)
                    ->where('cabang_id_to', $to->id)
                    ->where('status', '!=', 'DRAFT')
                    ->count();
            }
        }

        /* ===============================
         * FAST MOVING ITEMS (BULAN INI)
         * ===============================*/
        $fastItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
            ->whereHas('header', function ($q) use ($branchIds, $start, $end) {
                $q->whereIn('cabang_id_to', $branchIds)
                    ->whereBetween('trans_date', [$start, $end])
                    ->where('status', '!=', 'DRAFT');
            })
            ->groupBy('items_id')
            ->with('item:id,name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /* ===============================
         * LATEST REQUEST
         * ===============================*/
        $latestRequest = InventoryTrans::with(['cabangFrom', 'cabangTo'])
            ->whereIn('cabang_id_to', $branchIds)
            ->where('status', '!=', 'DRAFT')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* ===============================
         * LATEST PO
         * ===============================*/
        $latestPO = PurchaseOrder::with('cabangResto')
            ->whereIn('cabang_resto_id', $branchIds)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* ===============================
         * LATEST RECEIVE
         * ===============================*/
        $latestReceive = PoReceive::with(['purchaseOrder.cabangResto'])
            ->whereHas('purchaseOrder', fn ($q) => $q->whereIn('cabang_resto_id', $branchIds)
            )
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* ===============================
         * RETURN VIEW
         * ===============================*/
        return view('company.dashboard', compact(
            'companyCode',
            'totalBranches',
            'totalSuppliers',
            'totalItems',
            'totalEmployees',
            'requestMonth',
            'poMonth',
            'heatmap',
            'fastItems',
            'latestRequest',
            'latestPO',
            'latestReceive'
        ));
    }
}
