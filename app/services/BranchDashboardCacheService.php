<?php

namespace App\Services;

use App\Models\InventoryTrans;
use App\Models\Item;
use App\Models\POReceive;
use App\Models\PosOrder;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BranchDashboardCacheService
{
    private $branchId;

    public function getDashboardData($branchId)
    {
        $this->branchId = $branchId;

        return Cache::remember("branch_dashboard_{$branchId}", 300, function () {
            return [
                // KPI Cards
                'totalItemsInBranch' => $this->getTotalItems(),
                'safeStockItems' => $this->getSafeStockItems(),
                'criticalItems' => $this->getCriticalStockItems(),
                'nearCriticalItems' => $this->getNearCriticalStockItems(),
                'outOfStockItems' => $this->getOutOfStockItems(),
                'expiringSoonItems' => $this->getExpiringSoonItems(),

                // Transaction KPIs
                'incomingRequests' => $this->getIncomingRequestsCount(),
                'outgoingRequests' => $this->getOutgoingRequestsCount(),
                'purchaseOrders' => $this->getPurchaseOrdersCount(),
                'receivedGoods' => $this->getReceivedGoodsCount(),

                // POS & Sales
                'todaySales' => $this->getTodaySales(),
                'todayOrders' => $this->getTodayOrdersCount(),
                'monthSales' => $this->getMonthSales(),
                'monthOrders' => $this->getMonthOrdersCount(),

                // Charts
                'chartStock' => $this->getStockDistribution(),
                'chartExpiry' => $this->getExpiryChart(),
                'chartSales' => $this->getSalesChart(),

                // Lists
                'topItems' => $this->getTopSellingItems(),
                'fastMovingItems' => $this->getFastMovingItems(),
                'criticalStockList' => $this->getCriticalStockList(),
                'expiringItems' => $this->getExpiringItemsList(),
                'recentActivities' => $this->getRecentActivities(),
            ];
        });
    }

    // ==================== ITEM & STOCK STATUS ====================

    protected function getTotalItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->distinct('item_id')
            ->count('item_id');
    }

    protected function getSafeStockItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->whereHas('item', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM stocks WHERE item_id = items.id AND warehouse_id IN (SELECT id FROM warehouse WHERE cabang_resto_id = ?)) > min_stock * 1.5', [$this->branchId]);
            })
            ->distinct('item_id')
            ->count('item_id');
    }

    protected function getCriticalStockItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->whereHas('item', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM stocks WHERE item_id = items.id AND warehouse_id IN (SELECT id FROM warehouse WHERE cabang_resto_id = ?)) <= min_stock', [$this->branchId]);
            })
            ->distinct('item_id')
            ->count('item_id');
    }

    protected function getNearCriticalStockItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->whereHas('item', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM stocks WHERE item_id = items.id AND warehouse_id IN (SELECT id FROM warehouse WHERE cabang_resto_id = ?)) > min_stock AND (SELECT COALESCE(SUM(qty), 0) FROM stocks WHERE item_id = items.id AND warehouse_id IN (SELECT id FROM warehouse WHERE cabang_resto_id = ?)) <= min_stock * 1.5', [$this->branchId, $this->branchId]);
            })
            ->distinct('item_id')
            ->count('item_id');
    }

    protected function getOutOfStockItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->whereHas('item', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM stocks WHERE item_id = items.id AND warehouse_id IN (SELECT id FROM warehouse WHERE cabang_resto_id = ?)) = 0', [$this->branchId]);
            })
            ->distinct('item_id')
            ->count('item_id');
    }

    protected function getExpiringSoonItems()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where('expired_at', '<=', now()->addDays(7))
            ->where('expired_at', '>', now())
            ->where('qty', '>', 0)
            ->count();
    }

    // ==================== TRANSACTIONS ====================

    protected function getIncomingRequestsCount()
    {
        return InventoryTrans::where('cabang_id_to', $this->branchId)
            ->whereMonth('trans_date', now()->month)
            ->whereYear('trans_date', now()->year)
            ->whereIn('status', ['REQUESTED', 'IN_TRANSIT'])
            ->count();
    }

    protected function getOutgoingRequestsCount()
    {
        return InventoryTrans::where('cabang_id_from', $this->branchId)
            ->whereMonth('trans_date', now()->month)
            ->whereYear('trans_date', now()->year)
            ->whereIn('status', ['REQUESTED', 'IN_TRANSIT'])
            ->count();
    }

    protected function getPurchaseOrdersCount()
    {
        return PurchaseOrder::where('cabang_resto_id', $this->branchId)
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->whereIn('status', ['DRAFT', 'APPROVED'])
            ->count();
    }

    protected function getReceivedGoodsCount()
    {
        return POReceive::whereHas('purchaseOrder', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->count();
    }

    // ==================== POS & SALES ====================

    protected function getTodaySales()
    {
        return PosOrder::join('pos_payments', 'pos_payments.pos_order_id', '=', 'pos_order.id')
            ->where('pos_order.cabang_resto_id', $this->branchId)
            ->whereDate('pos_order.order_datetime', now())
            ->where('pos_order.status', 'PAID')
            ->sum('pos_payments.amount');
    }

    protected function getTodayOrdersCount()
    {
        return PosOrder::where('cabang_resto_id', $this->branchId)
            ->whereDate('order_datetime', now())
            ->count();
    }

    protected function getMonthSales()
    {
        return PosOrder::join('pos_payments', 'pos_payments.pos_order_id', '=', 'pos_order.id')
            ->where('pos_order.cabang_resto_id', $this->branchId)
            ->whereMonth('pos_order.order_datetime', now()->month)
            ->whereYear('pos_order.order_datetime', now()->year)
            ->where('pos_order.status', 'PAID')
            ->sum('pos_payments.amount');
    }

    protected function getMonthOrdersCount()
    {
        return PosOrder::where('cabang_resto_id', $this->branchId)
            ->whereMonth('order_datetime', now()->month)
            ->whereYear('order_datetime', now()->year)
            ->count();
    }

    // ==================== CHARTS ====================

    protected function getStockDistribution()
    {
        $safe = $this->getSafeStockItems();
        $critical = $this->getCriticalStockItems();
        $nearCritical = $this->getNearCriticalStockItems();
        $outOfStock = $this->getOutOfStockItems();

        return [
            'labels' => ['Aman', 'Mendekati Minimum', 'Kritis', 'Habis'],
            'data' => [$safe, $nearCritical, $critical, $outOfStock],
            'colors' => ['#10B981', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    protected function getExpiryChart()
    {
        $expired = Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where('expired_at', '<', now())
            ->where('qty', '>', 0)
            ->count();

        $expiringSoon = $this->getExpiringSoonItems();

        $safe = Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where(function ($q) {
                $q->where('expired_at', '>', now()->addDays(7))
                    ->orWhereNull('expired_at');
            })
            ->where('qty', '>', 0)
            ->count();

        return [
            'labels' => ['Aman', 'Segera Kadaluarsa (≤7 hari)', 'Kadaluarsa'],
            'data' => [$safe, $expiringSoon, $expired],
            'colors' => ['#10B981', '#F59E0B', '#EF4444'],
        ];
    }

    protected function getSalesChart()
    {
        $days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $sales = PosOrder::join(
                'pos_payments',
                'pos_payments.pos_order_id',
                '=',
                'pos_order.id'
            )
                ->where('pos_order.cabang_resto_id', $this->branchId)
                ->whereDate('pos_order.order_datetime', $date->toDateString())
                ->where('pos_order.status', 'PAID')
                ->sum('pos_payments.amount');

            $days->push([
                'date' => $date->format('d M'),
                'amount' => (float) $sales,
            ]);
        }

        return [
            'labels' => $days->pluck('date')->toArray(),
            'data' => $days->pluck('amount')->toArray(),
        ];
    }

    // ==================== TOP LISTS ====================

    protected function getTopSellingItems()
    {
        return StockMovement::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where('type', 'OUT')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select('item_id', DB::raw('ABS(SUM(qty)) as total_qty'))
            ->with('item:id,name')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name ?? 'N/A',
                    'qty' => round($item->total_qty, 2),
                ];
            });
    }

    protected function getFastMovingItems()
    {
        return StockMovement::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where('type', 'OUT')
            ->where('created_at', '>=', now()->subDays(7))
            ->select('item_id', DB::raw('ABS(SUM(qty)) as total_qty'))
            ->with('item:id,name')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name ?? 'N/A',
                    'qty' => round($item->total_qty, 2),
                ];
            });
    }

    protected function getCriticalStockList()
    {
        return Item::whereHas('stocks.warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->with(['satuan:id,code'])
            ->get()
            ->map(function ($item) {
                $totalQty = Stock::where('item_id', $item->id)
                    ->whereHas('warehouse', function ($q) {
                        $q->where('cabang_resto_id', $this->branchId);
                    })
                    ->sum('qty');

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'current_qty' => round($totalQty, 2),
                    'minimum_qty' => $item->min_stock,
                    'unit' => $item->satuan->code ?? '',
                    'status' => $totalQty <= $item->min_stock ? 'critical' : 'near_critical',
                ];
            })
            ->filter(function ($item) {
                return $item['current_qty'] <= $item['minimum_qty'] * 1.5;
            })
            ->sortBy('current_qty')
            ->take(10)
            ->values();
    }

    protected function getExpiringItemsList()
    {
        return Stock::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->where('expired_at', '<=', now()->addDays(7))
            ->where('expired_at', '>', now())
            ->where('qty', '>', 0)
            ->with(['item:id,name', 'item.satuan:id,code', 'warehouse:id,name'])
            ->orderBy('expired_at')
            ->limit(10)
            ->get()
            ->map(function ($stock) {
                return [
                    'item_name' => $stock->item->name ?? 'N/A',
                    'warehouse' => $stock->warehouse->name ?? 'N/A',
                    'qty' => round($stock->qty, 2),
                    'unit' => $stock->item->satuan->code ?? '',
                    'expired_at' => Carbon::parse($stock->expired_at)->format('d M Y'),
                    'days_left' => now()->diffInDays($stock->expired_at),
                ];
            });
    }

    // ==================== RECENT ACTIVITIES ====================

    protected function getRecentActivities()
    {
        $activities = collect();

        // Stock Movements
        $movements = StockMovement::whereHas('warehouse', function ($q) {
            $q->where('cabang_resto_id', $this->branchId);
        })
            ->with(['item:id,name', 'warehouse:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($movement) {
                return [
                    'type' => $movement->type === 'IN' ? 'stock_in' : 'stock_out',
                    'item' => $movement->item->name ?? 'N/A',
                    'warehouse' => $movement->warehouse->name ?? 'N/A',
                    'qty' => abs(round($movement->qty, 2)),
                    'reference' => $movement->reference_number,
                    'note' => $movement->note ?? ($movement->type === 'IN' ? 'Stok Masuk' : 'Stok Keluar'),
                    'time' => Carbon::parse($movement->created_at)->diffForHumans(),
                    'timestamp' => $movement->created_at,
                ];
            });

        // Transfers
        $transfers = InventoryTrans::where(function ($q) {
            $q->where('cabang_id_from', $this->branchId)
                ->orWhere('cabang_id_to', $this->branchId);
        })
            ->with(['cabangFrom:id,name', 'cabangTo:id,name'])
            ->orderByDesc('posted_at')
            ->limit(10)
            ->get()
            ->map(function ($transfer) {
                $isIncoming = $transfer->cabang_id_to === $this->branchId;

                return [
                    'type' => $isIncoming ? 'transfer_in' : 'transfer_out',
                    'from' => $transfer->cabangFrom->name ?? 'N/A',
                    'to' => $transfer->cabangTo->name ?? 'N/A',
                    'reference' => $transfer->trans_code,
                    'note' => $transfer->note ?? 'Transfer Stok',
                    'status' => $transfer->status,
                    'time' => Carbon::parse($transfer->created_at)->diffForHumans(),
                    'timestamp' => $transfer->created_at,
                ];
            });

        return $activities
            ->merge($movements)
            ->merge($transfers)
            ->sortByDesc('timestamp')
            ->take(15)
            ->values();
    }
}
