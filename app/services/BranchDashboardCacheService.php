<?php

namespace App\Services;

use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\PoReceive;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class BranchDashboardCacheService
{
    public function getBranchDashboard($branch, $warehouseIds)
    {
        $cacheKey = "branch_dashboard_{$branch->id}";

        return Cache::remember($cacheKey, 60, function () use ($branch, $warehouseIds) {

            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();

            $totalItemsInBranch = Stock::whereIn('warehouse_id', $warehouseIds)
                ->distinct('item_id')
                ->count('item_id');

            /* ===============================
             * LOW STOCK ITEMS (DISTINCT)
             * =============================== */
            $lowStockItems = Stock::whereIn('warehouse_id', $warehouseIds)
                ->join('items', 'items.id', '=', 'stocks.item_id')
                ->whereColumn('stocks.qty', '<=', 'items.min_stock')
                ->distinct('stocks.item_id')
                ->count('stocks.item_id');

            /* ===============================
             * INCOMING / OUTGOING REQUEST THIS MONTH
             * exclude DRAFT / CANCEL / REJECT
             * =============================== */
            $validStatus = ['REQUESTED', 'APPROVED', 'IN_TRANSIT', 'RECEIVED'];

            $incoming = InventoryTrans::where('cabang_id_to', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->whereIn('status', $validStatus)
                ->count();

            $outgoing = InventoryTrans::where('cabang_id_from', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->whereIn('status', $validStatus)
                ->count();

            /* ===============================
             * PURCHASE ORDER SUMMARY
             * =============================== */
            $purchaseOrders = PurchaseOrder::where('cabang_resto_id', $branch->id)
                ->whereBetween('po_date', [$start, $end])
                ->count();

            $receivedGoods = PoReceive::whereHas('purchaseOrder', function ($q) use ($branch) {
                $q->where('cabang_resto_id', $branch->id);
            })
                ->whereBetween('received_at', [$start, $end])
                ->count();

            /* ===============================
             * TOP ITEMS (EAGER LOAD)
             * =============================== */
            $topItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
                ->whereHas('header', function ($q) use ($branch) {
                    $q->where('cabang_id_from', $branch->id);
                })
                ->groupBy('items_id')
                ->with('item:id,name')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row) => [
                    'name' => $row->item->name ?? 'Unknown',
                    'code' => '-',
                    'qty' => $row->total,
                ]);

            /* ===============================
             * RECENT ACTIVITIES
             * =============================== */
            $recentActivities = InventoryTrans::with(['cabangFrom', 'cabangTo'])
                ->where(function ($q) use ($branch) {
                    $q->where('cabang_id_from', $branch->id)
                        ->orWhere('cabang_id_to', $branch->id);
                })
                ->whereIn('status', $validStatus)
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->map(function ($row) use ($branch) {
                    return [
                        'type' => $row->cabang_id_to == $branch->id ? 'in' : 'out',
                        'description' => $row->reason ?? 'Aktivitas Stok',
                        'from' => $row->cabangFrom->name ?? null,
                        'to' => $row->cabangTo->name ?? null,
                        'time' => $row->created_at?->format('d M Y H:i'),
                    ];
                });

            /* ===============================
             * STOCK TREND (6 BULAN TERAKHIR)
             * Menggunakan updated_at > created_at
             * =============================== */
            $stockTrend = Stock::selectRaw("
                DATE_FORMAT(updated_at, '%Y-%m') AS month,
                SUM(qty) AS total
            ")
                ->whereIn('warehouse_id', $warehouseIds)
                ->where('updated_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            /* ===============================
             * REQUEST TREND (12 BULAN)
             * =============================== */
            $requestTrend = InventoryTrans::selectRaw("
                DATE_FORMAT(trans_date, '%Y-%m') AS month,
                SUM(CASE WHEN cabang_id_to = {$branch->id} THEN 1 ELSE 0 END) AS incoming,
                SUM(CASE WHEN cabang_id_from = {$branch->id} THEN 1 ELSE 0 END) AS outgoing
            ")
                ->whereIn('status', $validStatus)
                ->where('trans_date', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return [
                'totalItemsInBranch' => $totalItemsInBranch,
                'lowStockItems' => $lowStockItems,

                'incomingRequests' => $incoming,
                'outgoingRequests' => $outgoing,

                'purchaseOrders' => $purchaseOrders,
                'receivedGoods' => $receivedGoods,

                'topItems' => $topItems,
                'recentActivities' => $recentActivities,

                'stockTrendLabels' => $stockTrend->pluck('month'),
                'stockTrendData' => $stockTrend->pluck('total'),

                'requestLabels' => $requestTrend->pluck('month'),
                'requestInData' => $requestTrend->pluck('incoming'),
                'requestOutData' => $requestTrend->pluck('outgoing'),
            ];
        });
    }
}
