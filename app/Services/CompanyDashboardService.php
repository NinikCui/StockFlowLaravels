<?php

namespace App\Services;

use App\Models\CabangResto;
use App\Models\Company;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Item;
use App\Models\POReceive;
use App\Models\PosOrder;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompanyDashboardService
{
    protected int $companyId;

    protected Company $company;

    /** @var \Illuminate\Support\Collection<int,int> */
    protected $branchIds;

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
        $this->company = Company::findOrFail($companyId);
        $this->branchIds = CabangResto::where('company_id', $companyId)->pluck('id');
    }

    public function getDashboardData(): array
    {
        return Cache::remember("company_dashboard_{$this->companyId}", 300, function () {

            $companyStockTotalsByItem = $this->getCompanyStockTotalsByItem();
            $companyMinRequiredByItem = $this->getCompanyMinRequiredByItem();

            return [

                // ================= STOCK STATUS (ALL BRANCHES) =================
                'criticalItemsCompany' => $this->getCriticalItemsAcrossCompany($companyStockTotalsByItem, $companyMinRequiredByItem),
                'expiringItemsCompany' => $this->getExpiringItemsAcrossCompany(),
                'lowStockBranches' => $this->getLowStockBranches(),

                // ================= TRANSACTIONS =================
                'monthlyRequests' => $this->getMonthlyRequests(),
                'monthlyPurchaseOrders' => $this->getMonthlyPurchaseOrders(),
                'monthlyReceives' => $this->getMonthlyReceives(),
                'pendingTransfers' => $this->getPendingTransfers(),

                // ================= SALES PERFORMANCE =================
                'totalSalesToday' => $this->getTotalSalesToday(),
                'totalSalesMonth' => $this->getTotalSalesMonth(),
                'topPerformingBranch' => $this->getTopPerformingBranch(),
                'branchSalesComparison' => $this->getBranchSalesComparison(),

                // ================= CHARTS =================
                'chartBranchStock' => $this->getBranchStockComparison(),
                'chartPurchaseTrend' => $this->getPurchaseTrendChart(),
                'chartSalesTrend' => $this->getSalesTrendChart(),

                // ================= LISTS =================
                'fastMovingItems' => $this->getFastMovingItems(),
                'branchPerformance' => $this->getBranchPerformanceList(),
                'recentActivities' => $this->getRecentActivities(),
                'transferHeatmap' => $this->getTransferHeatmap(),
            ];
        });
    }

    protected function getCompanyStockTotalsByItem(): array
    {
        return DB::table('stocks as s')
            ->join('warehouse as w', 'w.id', '=', 's.warehouse_id')
            ->whereIn('w.cabang_resto_id', $this->branchIds)
            ->select('s.item_id', DB::raw('COALESCE(SUM(s.qty),0) as total_qty'))
            ->groupBy('s.item_id')
            ->pluck('total_qty', 'item_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }

    protected function getCompanyMinRequiredByItem(): array
    {
        $mins = DB::table('item_branch_min_stocks')
            ->where('company_id', $this->companyId)
            ->whereIn('cabang_resto_id', $this->branchIds)
            ->select('item_id', DB::raw('COALESCE(SUM(min_stock),0) as required_min'))
            ->groupBy('item_id')
            ->pluck('required_min', 'item_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        // pastikan semua item company punya key (kalau belum diset, default 0)
        $itemIds = Item::where('company_id', $this->companyId)->pluck('id')->toArray();
        foreach ($itemIds as $itemId) {
            $mins[$itemId] = $mins[$itemId] ?? 0.0;
        }

        return $mins;
    }

    protected function getBranchMinRequiredByItem(int $branchId): array
    {
        $mins = DB::table('item_branch_min_stocks')
            ->where('company_id', $this->companyId)
            ->where('cabang_resto_id', $branchId)
            ->pluck('min_stock', 'item_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        $itemIds = Item::where('company_id', $this->companyId)->pluck('id')->toArray();
        foreach ($itemIds as $itemId) {
            $mins[$itemId] = $mins[$itemId] ?? 0.0;
        }

        return $mins;
    }

    // ==================== STOCK STATUS ====================

    protected function getCriticalItemsAcrossCompany(array $totalsByItem, array $minRequiredByItem): int
    {
        $critical = 0;

        foreach ($minRequiredByItem as $itemId => $requiredMin) {
            $totalQty = (float) ($totalsByItem[$itemId] ?? 0);
            if ($totalQty <= (float) $requiredMin) {
                $critical++;
            }
        }

        return $critical;
    }

    protected function getExpiringItemsAcrossCompany(): int
    {
        // ✅ sesuai update: expired_at
        return Stock::whereHas('warehouse', function ($q) {
            $q->whereIn('cabang_resto_id', $this->branchIds);
        })
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now()->addDays(7))
            ->where('expired_at', '>', now())
            ->where('qty', '>', 0)
            ->count();
    }

    protected function getLowStockBranches(): int
    {
        $branches = CabangResto::where('company_id', $this->companyId)->get();
        $lowCount = 0;

        foreach ($branches as $branch) {

            $totalsByItem = DB::table('stocks as s')
                ->join('warehouse as w', 'w.id', '=', 's.warehouse_id')
                ->where('w.cabang_resto_id', $branch->id)
                ->select('s.item_id', DB::raw('COALESCE(SUM(s.qty),0) as total_qty'))
                ->groupBy('s.item_id')
                ->pluck('total_qty', 'item_id')
                ->map(fn ($v) => (float) $v)
                ->toArray();

            $minsByItem = $this->getBranchMinRequiredByItem($branch->id);

            $hasCritical = false;
            foreach ($minsByItem as $itemId => $min) {
                $qty = (float) ($totalsByItem[$itemId] ?? 0);
                if ($qty <= (float) $min) {
                    $hasCritical = true;
                    break;
                }
            }

            if ($hasCritical) {
                $lowCount++;
            }
        }

        return $lowCount;
    }

    // ==================== TRANSACTIONS ====================

    protected function getMonthlyRequests(): int
    {
        return InventoryTrans::whereIn('cabang_id_to', $this->branchIds)
            ->whereMonth('trans_date', now()->month)
            ->whereYear('trans_date', now()->year)
            ->count();
    }

    protected function getMonthlyPurchaseOrders(): int
    {
        return PurchaseOrder::whereIn('cabang_resto_id', $this->branchIds)
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->count();
    }

    protected function getMonthlyReceives(): int
    {
        return POReceive::whereHas('purchaseOrder', function ($q) {
            $q->whereIn('cabang_resto_id', $this->branchIds);
        })
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->count();
    }

    protected function getPendingTransfers(): int
    {
        return InventoryTrans::whereIn('cabang_id_to', $this->branchIds)
            ->whereIn('status', ['REQUESTED', 'IN_TRANSIT'])
            ->count();
    }

    // ==================== SALES PERFORMANCE ====================

    protected function getTotalSalesToday(): float
    {
        return (float) DB::table('pos_payments as pp')
            ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
            ->whereIn('po.cabang_resto_id', $this->branchIds)
            ->whereDate('po.order_datetime', now()->toDateString())
            ->where('po.status', 'PAID')
            ->sum('pp.amount');
    }

    protected function getTotalSalesMonth(): float
    {
        return (float) DB::table('pos_payments as pp')
            ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
            ->whereIn('po.cabang_resto_id', $this->branchIds)
            ->whereMonth('po.order_datetime', now()->month)
            ->whereYear('po.order_datetime', now()->year)
            ->where('po.status', 'PAID')
            ->sum('pp.amount');
    }

    protected function getTopPerformingBranch(): array
    {
        $top = DB::table('pos_payments as pp')
            ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
            ->join('cabang_resto as cr', 'cr.id', '=', 'po.cabang_resto_id')
            ->whereIn('po.cabang_resto_id', $this->branchIds)
            ->whereMonth('po.order_datetime', now()->month)
            ->whereYear('po.order_datetime', now()->year)
            ->where('po.status', 'PAID')
            ->select('po.cabang_resto_id', 'cr.name', DB::raw('SUM(pp.amount) as total_sales'))
            ->groupBy('po.cabang_resto_id', 'cr.name')
            ->orderByDesc('total_sales')
            ->first();

        return [
            'name' => $top->name ?? 'N/A',
            'sales' => (float) ($top->total_sales ?? 0),
        ];
    }

    protected function getBranchSalesComparison()
    {
        return DB::table('pos_payments as pp')
            ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
            ->join('cabang_resto as cr', 'cr.id', '=', 'po.cabang_resto_id')
            ->whereIn('po.cabang_resto_id', $this->branchIds)
            ->whereMonth('po.order_datetime', now()->month)
            ->whereYear('po.order_datetime', now()->year)
            ->where('po.status', 'PAID')
            ->select('po.cabang_resto_id', 'cr.name', DB::raw('SUM(pp.amount) as total_sales'))
            ->groupBy('po.cabang_resto_id', 'cr.name')
            ->orderByDesc('total_sales')
            ->get()
            ->map(fn ($r) => [
                'branch' => $r->name,
                'sales' => (float) $r->total_sales,
            ]);
    }

    // ==================== CHARTS ====================

    protected function getBranchStockComparison(): array
    {
        $branches = CabangResto::where('company_id', $this->companyId)->get();

        $data = $branches->map(function ($branch) {
            $totalItems = Stock::whereHas('warehouse', function ($q) use ($branch) {
                $q->where('cabang_resto_id', $branch->id);
            })
                ->distinct('item_id')
                ->count('item_id');

            return [
                'branch' => $branch->name,
                'items' => $totalItems,
            ];
        });

        return [
            'labels' => $data->pluck('branch')->toArray(),
            'data' => $data->pluck('items')->toArray(),
            'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
        ];
    }

    protected function getInterBranchTransferChart(): array
    {
        $thisMonth = InventoryTrans::whereIn('cabang_id_from', $this->branchIds)
            ->whereMonth('trans_date', now()->month)
            ->whereYear('trans_date', now()->year)
            ->count();

        $lastMonth = InventoryTrans::whereIn('cabang_id_from', $this->branchIds)
            ->whereMonth('trans_date', now()->subMonth()->month)
            ->whereYear('trans_date', now()->subMonth()->year)
            ->count();

        return [
            'labels' => ['Bulan Lalu', 'Bulan Ini'],
            'data' => [$lastMonth, $thisMonth],
            'colors' => ['#94A3B8', '#3B82F6'],
        ];
    }

    protected function getPurchaseTrendChart(): array
    {
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = PurchaseOrder::whereIn('cabang_resto_id', $this->branchIds)
                ->whereMonth('po_date', $date->month)
                ->whereYear('po_date', $date->year)
                ->count();

            $months->push([
                'month' => $date->format('M'),
                'count' => $count,
            ]);
        }

        return [
            'labels' => $months->pluck('month')->toArray(),
            'data' => $months->pluck('count')->toArray(),
        ];
    }

    protected function getSalesTrendChart(): array
    {
        $days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $sales = (float) DB::table('pos_payments as pp')
                ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
                ->whereIn('po.cabang_resto_id', $this->branchIds)
                ->whereDate('po.order_datetime', $date->toDateString())
                ->where('po.status', 'PAID')
                ->sum('pp.amount');

            $days->push([
                'date' => $date->format('d M'),
                'amount' => $sales,
            ]);
        }

        return [
            'labels' => $days->pluck('date')->toArray(),
            'data' => $days->pluck('amount')->toArray(),
        ];
    }

    protected function getSupplierPerformanceChart(): array
    {
        $suppliers = PurchaseOrder::select('suppliers_id', DB::raw('COUNT(*) as total_orders'))
            ->whereIn('cabang_resto_id', $this->branchIds)
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->groupBy('suppliers_id')
            ->with('supplier:id,name')
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get();

        return [
            'labels' => $suppliers->pluck('supplier.name')->toArray(),
            'data' => $suppliers->pluck('total_orders')->toArray(),
            'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
        ];
    }

    // ==================== LISTS ====================

    protected function getFastMovingItems()
    {
        return InvenTransDetail::select('items_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('header', function ($q) {
                $q->whereIn('cabang_id_to', $this->branchIds)
                    ->whereMonth('trans_date', now()->month)
                    ->whereYear('trans_date', now()->year)
                    ->where('status', '!=', 'DRAFT');
            })
            ->with('item:id,name')
            ->groupBy('items_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($detail) {
                return [
                    'name' => $detail->item->name ?? 'N/A',
                    'qty' => round((float) $detail->total_qty, 2),
                ];
            });
    }

    protected function getTopSuppliers()
    {
        return PurchaseOrder::select('suppliers_id', DB::raw('COUNT(*) as total_orders'))
            ->whereIn('cabang_resto_id', $this->branchIds)
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->with('supplier:id,name,phone')
            ->groupBy('suppliers_id')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get()
            ->map(function ($po) {
                return [
                    'name' => $po->supplier->name ?? 'N/A',
                    'contact' => $po->supplier->contact ?? '-',
                    'orders' => (int) $po->total_orders,
                ];
            });
    }

    protected function getBranchPerformanceList()
    {
        $branches = CabangResto::where('company_id', $this->companyId)->get();

        return $branches->map(function ($branch) {

            $sales = (float) DB::table('pos_payments as pp')
                ->join('pos_order as po', 'po.id', '=', 'pp.pos_order_id')
                ->where('po.cabang_resto_id', $branch->id)
                ->whereMonth('po.order_datetime', now()->month)
                ->whereYear('po.order_datetime', now()->year)
                ->where('po.status', 'PAID')
                ->sum('pp.amount');

            $orders = (int) PosOrder::where('cabang_resto_id', $branch->id)
                ->whereMonth('order_datetime', now()->month)
                ->whereYear('order_datetime', now()->year)
                ->count();

            $totalsByItem = DB::table('stocks as s')
                ->join('warehouse as w', 'w.id', '=', 's.warehouse_id')
                ->where('w.cabang_resto_id', $branch->id)
                ->select('s.item_id', DB::raw('COALESCE(SUM(s.qty),0) as total_qty'))
                ->groupBy('s.item_id')
                ->pluck('total_qty', 'item_id')
                ->map(fn ($v) => (float) $v)
                ->toArray();

            $minsByItem = $this->getBranchMinRequiredByItem($branch->id);

            $criticalItems = 0;
            foreach ($minsByItem as $itemId => $min) {
                $qty = (float) ($totalsByItem[$itemId] ?? 0);
                if ($qty <= (float) $min) {
                    $criticalItems++;
                }
            }

            return [
                'name' => $branch->name,
                'sales' => $sales,
                'orders' => $orders,
                'critical_items' => $criticalItems,
                'status' => $criticalItems > 5 ? 'warning' : 'good',
            ];
        })->sortByDesc('sales')->values();
    }

    protected function getCriticalStockListCompany(array $totalsByItem, array $minRequiredByItem)
    {
        $items = Item::where('company_id', $this->companyId)
            ->with(['satuan:id,code'])
            ->get()
            ->keyBy('id');

        $rows = collect();

        foreach ($minRequiredByItem as $itemId => $requiredMin) {

            if (! isset($items[$itemId])) {
                continue;
            }

            $totalQty = (float) ($totalsByItem[$itemId] ?? 0);

            if ($totalQty > ((float) $requiredMin * 1.5)) {
                continue;
            }

            $item = $items[$itemId];

            $rows->push([
                'name' => $item->name,
                'total_qty' => round($totalQty, 2),
                'required_min' => round((float) $requiredMin, 2),
                'unit' => $item->satuan->code ?? '',
                'status' => $totalQty <= (float) $requiredMin ? 'critical' : 'low',
            ]);
        }

        return $rows
            ->sortBy('total_qty')
            ->take(10)
            ->values();
    }

    protected function getTransferHeatmap()
    {
        $branches = CabangResto::where('company_id', $this->companyId)->get();
        $heatmap = [];

        foreach ($branches as $from) {
            foreach ($branches as $to) {
                if ($from->id === $to->id) {
                    continue;
                }

                $count = InventoryTrans::where('cabang_id_from', $from->id)
                    ->where('cabang_id_to', $to->id)
                    ->whereMonth('trans_date', now()->month)
                    ->whereYear('trans_date', now()->year)
                    ->where('status', '!=', 'DRAFT')
                    ->count();

                $heatmap[] = [
                    'from' => $from->name,
                    'to' => $to->name,
                    'count' => $count,
                ];
            }
        }

        return collect($heatmap)->sortByDesc('count')->take(10)->values();
    }

    protected function getRecentActivities()
    {
        $activities = collect();

        $transfers = InventoryTrans::whereIn('cabang_id_to', $this->branchIds)
            ->with(['cabangFrom:id,name', 'cabangTo:id,name'])
            ->orderByDesc('created_by')
            ->limit(10)
            ->get()
            ->map(function ($transfer) {
                return [
                    'type' => 'transfer',
                    'from' => $transfer->cabangFrom->name ?? 'N/A',
                    'to' => $transfer->cabangTo->name ?? 'N/A',
                    'reference' => $transfer->trans_code,
                    'status' => $transfer->status,
                    'note' => $transfer->note ?? 'Transfer Antar Cabang',
                    'time' => Carbon::parse($transfer->created_at)->diffForHumans(),
                    'timestamp' => $transfer->created_at,
                ];
            });

        $pos = PurchaseOrder::whereIn('cabang_resto_id', $this->branchIds)
            ->with(['cabangResto:id,name', 'supplier:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($po) {
                return [
                    'type' => 'purchase_order',
                    'branch' => $po->cabangResto->name ?? 'N/A',
                    'supplier' => $po->supplier->name ?? 'N/A',
                    'reference' => $po->po_number,
                    'status' => $po->status,
                    'note' => 'Purchase Order',
                    'time' => Carbon::parse($po->created_at)->diffForHumans(),
                    'timestamp' => $po->created_at,
                ];
            });

        return $activities
            ->merge($transfers)
            ->merge($pos)
            ->sortByDesc('timestamp')
            ->take(15)
            ->values();
    }
}
