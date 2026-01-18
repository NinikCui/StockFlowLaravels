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

            /* ===============================
             * BASIC KPI
             * =============================== */
            $totalItemsInBranch = Stock::whereIn('warehouse_id', $warehouseIds)
                ->distinct('item_id')
                ->count('item_id');

            $lowStockItems = Stock::whereIn('stocks.warehouse_id', $warehouseIds)
                ->join('items', 'items.id', '=', 'stocks.item_id')
                ->where(function ($q) {
                    $q->where('stocks.qty', '=', 0)
                        ->orWhereRaw('stocks.qty <= COALESCE(items.min_stock, 0)');
                })
                ->distinct('stocks.item_id')
                ->count('stocks.item_id');

            /* ===============================
             * REQUEST BULAN INI
             * =============================== */
            $validStatus = ['REQUESTED', 'APPROVED', 'IN_TRANSIT', 'RECEIVED'];

            $incomingRequests = InventoryTrans::where('cabang_id_to', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->whereIn('status', $validStatus)
                ->count();

            $outgoingRequests = InventoryTrans::where('cabang_id_from', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->whereIn('status', $validStatus)
                ->count();

            /* ===============================
             * PURCHASE ORDER
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
             * TOP ITEMS (FAST MOVING)
             * =============================== */
            $topItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
                ->whereHas('header', function ($q) use ($branch, $validStatus) {
                    $q->where('cabang_id_from', $branch->id)
                        ->whereIn('status', $validStatus);
                })
                ->groupBy('items_id')
                ->with('item:id,name')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row) => [
                    'name' => $row->item->name ?? 'Unknown',
                    'qty' => (float) $row->total,
                ]);

            /* ===============================
             * RECENT ACTIVITIES
             * =============================== */
            $recentActivities = InventoryTrans::with(['cabangFrom', 'cabangTo'])
                ->whereIn('status', $validStatus)
                ->where(function ($q) use ($branch) {
                    $q->where('cabang_id_from', $branch->id)
                        ->orWhere('cabang_id_to', $branch->id);
                })
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->map(function ($row) use ($branch) {
                    return [
                        'type' => $row->cabang_id_to == $branch->id ? 'in' : 'out',
                        'from' => $row->cabangFrom->name ?? '-',
                        'to' => $row->cabangTo->name ?? '-',
                        'time' => optional($row->created_at)->format('d M Y H:i'),
                        'note' => $row->reason ?? 'Transfer Stok',
                    ];
                });

            /* ===============================
             * RETURN DATA (TANPA TREND)
             * =============================== */
            return [
                'totalItemsInBranch' => $totalItemsInBranch,
                'lowStockItems' => $lowStockItems,

                'incomingRequests' => $incomingRequests,
                'outgoingRequests' => $outgoingRequests,

                'purchaseOrders' => $purchaseOrders,
                'receivedGoods' => $receivedGoods,

                'topItems' => $topItems,
                'recentActivities' => $recentActivities,
            ];
        });
    }
}
