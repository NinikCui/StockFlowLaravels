<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Item;
use App\Models\PoDetail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierScore;
use App\Models\SuppliersItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchSupplierController extends Controller
{
    // ======================================================
    // UTIL: Variance Calculator
    // ======================================================
    private function varianceFromArray($array)
    {
        $count = count($array);
        if ($count < 2) {
            return 0;
        }

        $mean = array_sum($array) / $count;
        $variance = 0;

        foreach ($array as $value) {
            $variance += pow(($value - $mean), 2);
        }

        $std = sqrt($variance / ($count - 1));

        return $mean > 0 ? round(($std / $mean) * 100, 2) : 0;
    }

    // ======================================================
    // LIST SUPPLIER (FILTER + KPI + SORT)
    // ======================================================
    public function index(Request $request, $branchCode)
    {
        $companyId = session('role.company.id');
        $companyCode = session('role.company.code');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $filter = $request->filter ?? 'all';
        $itemFilter = request('item_id');
        $performance = request('performance');
        $sort = request('sort');

        // Ambil supplier milik perusahaan + milik cabang
        $suppliers = Supplier::where('company_id', $companyId)
            ->when($filter === 'branch', fn ($q) => $q->where('cabang_resto_id', $branch->id))
            ->when($filter === 'company', fn ($q) => $q->whereNull('cabang_resto_id'))
            ->when($itemFilter, function ($q) use ($itemFilter) {
                $q->whereHas('suppliedItems', fn ($i) => $i->where('items.id', $itemFilter));
            })
            ->get();

        // ======================================================
        // HITUNG KPI LIVE
        // ======================================================
        foreach ($suppliers as $s) {

            $poQuery = PurchaseOrder::where('suppliers_id', $s->id);

            // ✅ ON TIME: HANYA RECEIVED
            $receivedQuery = (clone $poQuery)->where('status', 'RECEIVED');
            $totalReceivedOrders = (clone $receivedQuery)->count();

            $onTime = (clone $receivedQuery)
                ->whereNotNull('delivered_date')
                ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
                ->count();

            $s->kpi_on_time = $totalReceivedOrders > 0
                ? round(($onTime / $totalReceivedOrders) * 100, 2)
                : 0;

            // REJECT RATE (hanya dari data receive)
            $ret = DB::table('po_receive_detail as rd')
                ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
                ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
                ->where('po.suppliers_id', $s->id)
                ->selectRaw('SUM(rd.qty_received) total_received, SUM(rd.qty_returned) total_returned')
                ->first();

            $received = $ret->total_received ?? 0;
            $returned = $ret->total_returned ?? 0;

            $s->kpi_reject = $received > 0
                ? round(($returned / $received) * 100, 2)
                : 0;

            // PRICE VARIANCE (ambil harga dari PO Detail)
            $poIds = $poQuery->pluck('id');
            $prices = PoDetail::whereIn('purchase_order_id', $poIds)
                ->pluck('unit_price')
                ->toArray();

            $s->kpi_var = $this->varianceFromArray($prices);
        }

        // ======================================================
        // FILTER PERFORMA
        // ======================================================
        if ($performance) {
            $suppliers = $suppliers->filter(function ($s) use ($performance) {
                return match ($performance) {
                    'good' => $s->kpi_on_time >= 90 && $s->kpi_reject <= 10 && $s->kpi_var <= 5,
                    'average' => $s->kpi_on_time >= 70 && $s->kpi_reject <= 20,
                    'poor' => $s->kpi_on_time < 70 || $s->kpi_reject > 20,
                    default => true,
                };
            });
        }

        // ======================================================
        // SORTING
        // ======================================================
        if ($sort) {
            $suppliers = $suppliers->sortBy(fn ($s) => match ($sort) {
                'on_time' => -$s->kpi_on_time,
                'reject_low' => $s->kpi_reject,
                'variance_low' => $s->kpi_var,
                'name_asc' => $s->name,
                default => $s->id,
            });

            if ($sort === 'name_desc') {
                $suppliers = $suppliers->sortByDesc('name');
            }
        }

        // Ambil semua item
        $allItems = Item::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.suppliers.index', compact(
            'suppliers',
            'allItems',
            'companyCode',
            'branchCode',
            'filter'
        ));
    }

    // ======================================================
    // DETAIL
    // ======================================================
    public function show($branchCode, $id)
    {
        $companyId = session('role.company.id');
        $companyCode = session('role.company.code');

        $supplier = Supplier::where('company_id', $companyId)
            ->with(['scores' => fn ($q) => $q->orderBy('period_year', 'desc')->orderBy('period_month', 'desc')])
            ->findOrFail($id);

        $mode = request('mode', 'all');
        $month = request('period_month');
        $year = request('period_year');

        // Filter periode
        if ($mode === 'period' && $month && $year) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();

            $poQuery = PurchaseOrder::where('suppliers_id', $supplier->id)
                ->whereBetween('po_date', [$start, $end]);
        } else {
            $start = null;
            $end = null;
            $poQuery = PurchaseOrder::where('suppliers_id', $supplier->id);
        }

        // total semua PO (kalau mau tampil)
        $totalOrders = (clone $poQuery)->count();

        // ✅ ON TIME: HANYA RECEIVED
        $receivedPoQuery = (clone $poQuery)->where('status', 'RECEIVED');
        $totalReceivedOrders = (clone $receivedPoQuery)->count();

        $onTime = (clone $receivedPoQuery)
            ->whereNotNull('delivered_date')
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalReceivedOrders > 0
            ? round(($onTime / $totalReceivedOrders) * 100, 2)
            : 0;

        // ✅ late hanya dari RECEIVED
        $late = $totalReceivedOrders - $onTime;

        // ✅ lead time lebih fair dari RECEIVED
        $avgLead = (clone $receivedPoQuery)
            ->whereNotNull('delivered_date')
            ->avg(DB::raw('DATEDIFF(delivered_date, po_date)'));
        $avgLead = $avgLead ? round($avgLead, 2) : 0;

        // RECEIVE
        $ret = DB::table('po_receive_detail as rd')
            ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
            ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
            ->where('po.suppliers_id', $supplier->id);

        if ($mode === 'period' && $start && $end) {
            $ret->whereBetween('r.received_at', [$start, $end]);
        }

        $ret = $ret->selectRaw('SUM(rd.qty_received) total_received, SUM(rd.qty_returned) total_returned')
            ->first();

        $received = $ret->total_received ?? 0;
        $returned = $ret->total_returned ?? 0;

        $rejectRate = $received > 0 ? round(($returned / $received) * 100, 2) : 0;

        $qtyAccuracy = ($received + $returned) > 0
            ? round(($received / ($received + $returned)) * 100, 2)
            : 0;

        // Price variance (pakai semua PO sesuai filter period/all)
        $poIds = $poQuery->pluck('id');
        $prices = PoDetail::whereIn('purchase_order_id', $poIds)
            ->pluck('unit_price')
            ->toArray();

        $priceVariance = $this->varianceFromArray($prices);

        // Items
        $items = $supplier->suppliedItems()->with(['kategori', 'satuan'])->get();

        $allItems = Item::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        // Tahun tersedia
        $availableYears = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->selectRaw('YEAR(po_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('branch.suppliers.detail', compact(
            'supplier',
            'branchCode',
            'companyCode',
            'items',
            'allItems',

            // KPI
            'mode',
            'month',
            'year',
            'totalOrders',           // semua status
            'totalReceivedOrders',
            'onTimeRate',
            'late',
            'avgLead',
            'rejectRate',
            'qtyAccuracy',
            'priceVariance',
            'availableYears'
        ));
    }

    // ======================================================
    // ITEM CRUD
    // ======================================================
    public function itemStore(Request $request, $branchCode, Supplier $supplier)
    {
        $request->validate([
            'items_id' => 'required|exists:items,id',
            'price' => 'required|numeric|min:0',
            'min_order_qty' => 'required|numeric|min:0',
        ]);

        if ($supplier->suppliedItems()->where('items_id', $request->items_id)->exists()) {
            return back()->withErrors(['items_id' => 'Item sudah ada.'])->withInput();
        }

        $supplier->suppliedItems()->attach($request->items_id, [
            'price' => $request->price,
            'min_order_qty' => $request->min_order_qty,
            'last_price_update' => now(),
        ]);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function itemUpdate(Request $request, $branchCode, Supplier $supplier, $itemId)
    {
        $request->validate([
            'price' => 'required|numeric',
            'min_order_qty' => 'nullable|numeric',
        ]);

        $supplier->suppliedItems()->updateExistingPivot($itemId, [
            'price' => $request->price,
            'min_order_qty' => $request->min_order_qty,
            'last_price_update' => now(),
        ]);

        return back()->with('success', 'Item berhasil diupdate.');
    }

    public function itemDestroy($branchCode, Supplier $supplier, $itemId)
    {
        $supplier->suppliedItems()->detach($itemId);

        return back()->with('success', 'Item berhasil dihapus.');
    }

    // ======================================================
    // GENERATE SCORE (ALL TIME)
    // ======================================================
    public function generateScore(Request $request, $branchCode, $id)
    {
        $companyId = session('role.company.id');

        $supplier = Supplier::where('company_id', $companyId)->findOrFail($id);

        // ✅ ON TIME: RECEIVED ONLY
        $receivedQuery = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->where('status', 'RECEIVED');

        $totalReceivedOrders = (clone $receivedQuery)->count();

        $onTime = (clone $receivedQuery)
            ->whereNotNull('delivered_date')
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalReceivedOrders > 0
            ? round(($onTime / $totalReceivedOrders) * 100, 2)
            : 0;

        // REJECT RATE
        $ret = DB::table('po_receive_detail as rd')
            ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
            ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
            ->where('po.suppliers_id', $supplier->id)
            ->selectRaw('SUM(rd.qty_received) total_received, SUM(rd.qty_returned) total_returned')
            ->first();

        $received = $ret->total_received ?? 0;
        $returned = $ret->total_returned ?? 0;

        $rejectRate = $received > 0 ? round(($returned / $received) * 100, 2) : 0;

        $prices = SuppliersItem::where('suppliers_id', $supplier->id)->pluck('price')->toArray();
        $priceVariance = $this->varianceFromArray($prices);

        SupplierScore::create([
            'suppliers_id' => $supplier->id,
            'on_time_rate' => $onTimeRate,
            'reject_rate' => $rejectRate,
            'avg_quality' => 100 - $rejectRate,
            'price_variance' => $priceVariance,
            'notes' => 'Generated Automatically',
            'calculated_at' => now(),
        ]);

        return back()->with('success', 'Performance berhasil dihitung dan disimpan.');
    }

    // ======================================================
    // GENERATE SCORE (PERIOD)
    // ======================================================
    public function generateScoreWithPeriod(Request $request, $branchCode, $id)
    {
        $request->validate([
            'period_month' => 'required',
            'period_year' => 'required',
        ]);

        $companyId = session('role.company.id');
        $supplier = Supplier::where('company_id', $companyId)->findOrFail($id);

        $month = $request->period_month;
        $year = $request->period_year;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        // ✅ RECEIVED ONLY periode ini
        $receivedQuery = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->where('status', 'RECEIVED')
            ->whereBetween('po_date', [$start, $end]);

        $totalReceivedOrders = (clone $receivedQuery)->count();

        $onTime = (clone $receivedQuery)
            ->whereNotNull('delivered_date')
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalReceivedOrders > 0
            ? round(($onTime / $totalReceivedOrders) * 100, 2)
            : 0;

        // REJECT RATE periode ini
        $ret = DB::table('po_receive_detail as rd')
            ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
            ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
            ->where('po.suppliers_id', $supplier->id)
            ->whereBetween('r.received_at', [$start, $end])
            ->selectRaw('SUM(rd.qty_received) total_received, SUM(rd.qty_returned) total_returned')
            ->first();

        $received = $ret->total_received ?? 0;
        $returned = $ret->total_returned ?? 0;

        $rejectRate = $received > 0 ? round(($returned / $received) * 100, 2) : 0;

        // PRICE VARIANCE periode ini (dari PO Detail RECEIVED periode ini)
        $poIds = $receivedQuery->pluck('id');
        $prices = PoDetail::whereIn('purchase_order_id', $poIds)->pluck('unit_price')->toArray();
        $priceVariance = $this->varianceFromArray($prices);

        SupplierScore::create([
            'suppliers_id' => $supplier->id,
            'period_month' => $month,
            'period_year' => $year,
            'on_time_rate' => $onTimeRate,
            'reject_rate' => $rejectRate,
            'avg_quality' => 100 - $rejectRate,
            'price_variance' => $priceVariance,
            'notes' => "Generated for {$month}/{$year}",
            'calculated_at' => now(),
        ]);

        return back()->with('success', "Performance periode {$month}/{$year} berhasil dihitung & disimpan!");
    }

    // ======================================================
    // EDIT / UPDATE / DELETE / CREATE / STORE
    // ======================================================
    public function edit($branchCode, $id)
    {
        $companyId = session('role.company.id');

        $supplier = Supplier::where('company_id', $companyId)
            ->where('cabang_resto_id', $this->getBranchId($branchCode))
            ->findOrFail($id);

        return view('branch.suppliers.edit', [
            'supplier' => $supplier,
            'branchCode' => $branchCode,
            'companyCode' => session('role.company.code'),
        ]);
    }

    public function update(Request $request, $branchCode, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|max:50',
        ]);

        $companyId = session('role.company.id');

        $supplier = Supplier::where('company_id', $companyId)
            ->where('cabang_resto_id', $this->getBranchId($branchCode))
            ->findOrFail($id);

        $supplier->update([
            'name' => $request->name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'address' => $request->address,
            'notes' => $request->notes,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('branch.supplier.show', [$branchCode, $supplier->id])
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    private function getBranchId($branchCode)
    {
        return CabangResto::where('company_id', session('role.company.id'))
            ->where('code', $branchCode)
            ->firstOrFail()
            ->id;
    }

    public function destroy($branchCode, $id)
    {
        $companyId = session('role.company.id');

        $supplier = Supplier::where('company_id', $companyId)
            ->where('cabang_resto_id', $this->getBranchId($branchCode))
            ->findOrFail($id);

        $supplier->delete();

        return redirect()
            ->route('branch.supplier.index', [$branchCode])
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function create($branchCode)
    {
        $branch = CabangResto::where('code', $branchCode)
            ->where('company_id', session('role.company.id'))
            ->firstOrFail();

        return view('branch.suppliers.create', [
            'branch' => $branch,
            'branchCode' => $branchCode,
        ]);
    }

    public function store(Request $request, $branchCode)
    {
        $request->validate([
            'name' => 'required|max:100',
            'contact_name' => 'nullable|max:100',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'notes' => 'nullable|max:255',
        ]);

        $branch = CabangResto::where('code', $branchCode)
            ->where('company_id', session('role.company.id'))
            ->firstOrFail();

        Supplier::create([
            'company_id' => $branch->company_id,
            'cabang_resto_id' => $branch->id,
            'name' => $request->name,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        return redirect()
            ->route('branch.supplier.index', $branchCode)
            ->with('success', 'Supplier berhasil ditambahkan.');
    }
}
