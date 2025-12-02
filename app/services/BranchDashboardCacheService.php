<?php

namespace App\Services;

use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Item;
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
                ->count();

            $totalStockValue = Stock::whereIn('warehouse_id', $warehouseIds)
                ->join('suppliers_item', 'suppliers_item.items_id', '=', 'stocks.item_id')
                ->selectRaw('SUM(stocks.qty * suppliers_item.price) AS total')
                ->value('total');

            $lowStockItems = Stock::whereIn('warehouse_id', $warehouseIds)
                ->join('items', 'items.id', '=', 'stocks.item_id')
                ->whereColumn('stocks.qty', '<=', 'items.min_stock')
                ->count();

            $incoming = InventoryTrans::where('cabang_id_to', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->count();

            $outgoing = InventoryTrans::where('cabang_id_from', $branch->id)
                ->whereBetween('trans_date', [$start, $end])
                ->count();

            $purchaseOrders = PurchaseOrder::where('cabang_resto_id', $branch->id)
                ->whereBetween('po_date', [$start, $end])
                ->count();

            $receivedGoods = PoReceive::whereHas('purchaseOrder', function ($q) use ($branch) {
                $q->where('cabang_resto_id', $branch->id);
            })
                ->whereBetween('received_at', [$start, $end])
                ->count();

            $topItems = InvenTransDetail::selectRaw('items_id, SUM(qty) AS total')
                ->whereHas('header', function ($q) use ($branch) {
                    $q->where('cabang_id_from', $branch->id);
                })
                ->groupBy('items_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $item = Item::find($row->items_id);

                    return [
                        'name' => $item->name ?? 'Unknown',
                        'code' => $item->code ?? '-',
                        'qty' => $row->total,
                    ];
                });

            $recentActivities = InventoryTrans::where(function ($q) use ($branch) {
                $q->where('cabang_id_from', $branch->id)
                    ->orWhere('cabang_id_to', $branch->id);
            })
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->map(function ($row) use ($branch) {

                    $type =
                        $row->cabang_id_to == $branch->id ? 'in' :
                        ($row->cabang_id_from == $branch->id ? 'out' : 'other');

                    return [
                        'type' => $type,
                        'description' => $row->reason ?? 'Aktivitas Stok',
                        'time' => $row->created_at?->format('d M Y H:i'),
                    ];
                });

            $stockTrend = Stock::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                SUM(qty) AS total
            ")
                ->whereIn('warehouse_id', $warehouseIds)
                ->groupBy('month')
                ->orderBy('month')
                ->limit(6)
                ->get();

            $stockTrendLabels = $stockTrend->pluck('month');
            $stockTrendData = $stockTrend->pluck('total');

            $requestTrend = InventoryTrans::selectRaw("
                DATE_FORMAT(trans_date, '%Y-%m') AS month,
                SUM(CASE WHEN cabang_id_to = {$branch->id} THEN 1 END) AS incoming,
                SUM(CASE WHEN cabang_id_from = {$branch->id} THEN 1 END) AS outgoing
            ")
                ->groupBy('month')
                ->orderBy('month')
                ->limit(12)
                ->get();

            return [
                'totalItemsInBranch' => $totalItemsInBranch,
                'totalStockValue' => $totalStockValue,
                'lowStockItems' => $lowStockItems,

                'incomingRequests' => $incoming,
                'outgoingRequests' => $outgoing,

                'purchaseOrders' => $purchaseOrders,
                'receivedGoods' => $receivedGoods,

                'topItems' => $topItems,
                'recentActivities' => $recentActivities,

                'stockTrendLabels' => $stockTrendLabels,
                'stockTrendData' => $stockTrendData,

                'requestLabels' => $requestTrend->pluck('month'),
                'requestInData' => $requestTrend->pluck('incoming'),
                'requestOutData' => $requestTrend->pluck('outgoing'),
            ];
        });
    }
}
