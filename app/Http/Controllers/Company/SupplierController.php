<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Item;
use App\Models\PoDetail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierScore;
use App\Models\SuppliersItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    // LIST
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

    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $itemFilter = request('item_id');
        $performance = request('performance');
        $sort = request('sort');

        $suppliers = Supplier::where('company_id', $company->id)
            ->when($itemFilter, function ($q) use ($itemFilter) {
                $q->whereHas('suppliedItems', function ($i) use ($itemFilter) {
                    $i->where('items.id', $itemFilter);
                });
            })
            ->get();

        // 🔥 HITUNG KPI LIVE UNTUK SETIAP SUPPLIER
        foreach ($suppliers as $s) {

            // Ambil PO
            $poQuery = PurchaseOrder::where('suppliers_id', $s->id);
            $totalOrders = $poQuery->count();

            // ON TIME
            $onTime = (clone $poQuery)
                ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
                ->count();

            $s->kpi_on_time = $totalOrders > 0
                ? round(($onTime / $totalOrders) * 100, 2)
                : 0;

            // REJECT RATE
            $returns = DB::table('po_receive_detail as rd')
                ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
                ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
                ->where('po.suppliers_id', $s->id)
                ->selectRaw('SUM(rd.qty_received) total_received, SUM(rd.qty_returned) total_returned')
                ->first();

            $received = $returns->total_received ?? 0;
            $returned = $returns->total_returned ?? 0;

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

        // 🔥 FILTER PERFORMA LIVE
        if ($performance) {
            $suppliers = $suppliers->filter(function ($s) use ($performance) {

                if ($performance === 'good') {
                    return $s->kpi_on_time >= 90
                        && $s->kpi_reject <= 10
                        && $s->kpi_var <= 5;
                }

                if ($performance === 'average') {
                    return $s->kpi_on_time >= 70
                        && $s->kpi_reject <= 20;
                }

                if ($performance === 'poor') {
                    return $s->kpi_on_time < 70
                        || $s->kpi_reject > 20;
                }

                return true;
            });
        }

        // 🔥 SORTING KPI LIVE
        if ($sort) {
            $suppliers = $suppliers->sortBy(function ($s) use ($sort) {

                return match ($sort) {
                    'on_time' => -$s->kpi_on_time,
                    'reject_low' => $s->kpi_reject,
                    'variance_low' => $s->kpi_var,
                    'name_asc' => $s->name,
                    default => $s->id,
                };
            });

            if ($sort === 'name_desc') {
                $suppliers = $suppliers->sortByDesc('name');
            }
        }

        // Semua item
        $allItems = Item::orderBy('name')->get();

        return view('company.supplier.index', compact(
            'suppliers',
            'allItems',
            'companyCode'
        ));
    }

    // FORM TAMBAH
    public function create($companyCode)
    {
        return view('company.supplier.create', compact('companyCode'));
    }

    // SIMPAN
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:50',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Supplier::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('company.supplier.edit', compact('supplier', 'companyCode'));
    }

    // UPDATE
    public function update(Request $request, $companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:50',
        ]);

        $supplier->update([
            'name' => $request->name,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'notes' => $request->notes,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('supplier.show', [$companyCode, $supplier->id])
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    // DELETE
    public function destroy($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil dihapus.');
    }

    // DETAIL
    public function show($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $supplier = Supplier::where('company_id', $company->id)
            ->with(['scores' => function ($q) {
                $q->orderBy('period_year', 'desc')->orderBy('period_month', 'desc');
            }])
            ->findOrFail($id);

        // ITEMS
        $items = $supplier->suppliedItems()
            ->with(['kategori', 'satuan'])
            ->get();

        $allItems = Item::with(['kategori', 'satuan'])
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $mode = request('mode', 'all');
        $month = request('period_month');
        $year = request('period_year');

        if ($mode === 'period' && $month && $year) {
            // Periode
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $poQuery = PurchaseOrder::where('suppliers_id', $supplier->id)
                ->whereBetween('po_date', [$startDate, $endDate]);

            $receiveQuery = DB::table('po_receive_detail as rd')
                ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
                ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
                ->where('po.suppliers_id', $supplier->id)
                ->whereBetween('r.received_at', [$startDate, $endDate]);

        } else {
            // ALL TIME
            $poQuery = PurchaseOrder::where('suppliers_id', $supplier->id);

            $receiveQuery = DB::table('po_receive_detail as rd')
                ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
                ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
                ->where('po.suppliers_id', $supplier->id);
        }

        $totalOrders = $poQuery->count();

        // ON TIME
        $onTime = (clone $poQuery)
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalOrders > 0
            ? round(($onTime / $totalOrders) * 100, 2)
            : 0;

        // LATE
        $late = $totalOrders - $onTime;

        // LEAD TIME
        $avgLead = (clone $poQuery)
            ->whereNotNull('delivered_date')
            ->avg(DB::raw('DATEDIFF(delivered_date, po_date)'));
        $avgLead = $avgLead ? round($avgLead, 2) : 0;

        // RECEIVE DETAIL
        $returns = $receiveQuery
            ->selectRaw('SUM(rd.qty_received) as total_received, SUM(rd.qty_returned) as total_returned')
            ->first();

        $totalReceived = $returns->total_received ?? 0;
        $totalReturned = $returns->total_returned ?? 0;

        // REJECT RATE
        $rejectRate = $totalReceived > 0
            ? round(($totalReturned / $totalReceived) * 100, 2)
            : 0;

        // QUANTITY ACCURACY
        $qtyAccuracy = ($totalReceived + $totalReturned) > 0
            ? round(($totalReceived / ($totalReceived + $totalReturned)) * 100, 2)
            : 0;

        // PRICE VARIANCE
        $poIds = $poQuery->pluck('id');

        $prices = PoDetail::whereIn('purchase_order_id', $poIds)
            ->pluck('unit_price')
            ->toArray();

        $priceVariance = 0;

        if (count($prices) > 1) {
            $avg = array_sum($prices) / count($prices);

            $variance = 0;
            foreach ($prices as $p) {
                $variance += pow($p - $avg, 2);
            }

            $std = sqrt($variance / (count($prices) - 1));

            $priceVariance = round(($std / $avg) * 100, 2);
        }
        $availableYears = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->selectRaw('YEAR(po_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('company.supplier.detail', compact(
            'companyCode',
            'supplier',
            'items',
            'allItems',

            // KPI
            'mode',
            'month',
            'year',
            'totalOrders',
            'onTimeRate',
            'late',
            'avgLead',
            'rejectRate',
            'qtyAccuracy',
            'priceVariance',

            'availableYears'
        ));
    }

    // CREATE ITEM SUPPLIER
    public function itemStore(Request $request, $companyCode, Supplier $supplier)
    {
        $validator = \Validator::make($request->all(), [
            'items_id' => 'required|exists:items,id',
            'price' => 'required|numeric|min:0',
            'min_order_qty' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('modal', 'add')
                ->withErrors($validator)
                ->withInput();
        }

        // 🔥 CEK DUPLIKAT
        if ($supplier->suppliedItems()->where('items_id', $request->items_id)->exists()) {

            // Tambahkan error manual
            return back()
                ->with('modal', 'add')
                ->withErrors(['items_id' => 'Item ini sudah pernah ditambahkan untuk supplier ini.'])
                ->withInput();
        }

        // SIMPAN
        $supplier->suppliedItems()->attach($request->items_id, [
            'price' => $request->price,
            'min_order_qty' => $request->min_order_qty,
            'last_price_update' => now(),
        ]);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    // UPDATE ITEM SUPPLIER
    public function itemUpdate(Request $request, $companyCode, Supplier $supplier, $itemId)
    {
        $request->validate([
            'price' => 'required|numeric',
            'min_order_qty' => 'nullable|numeric',
        ]);

        $supplier->suppliedItems()->updateExistingPivot($itemId, [
            'price' => $request->price,
            'min_order_qty' => $request->min_order_qty,
            'last_price_update' => Carbon::now(),
        ]);

        return back()->with('success', 'Item supplier berhasil diperbarui.');
    }

    // DELETE ITEM SUPPLIER
    public function itemDestroy($companyCode, Supplier $supplier, $itemId)
    {
        $supplier->suppliedItems()->detach($itemId);

        return back()->with('success', 'Item supplier berhasil dihapus.');
    }

    private function stdDeviation($array)
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

        return sqrt($variance / ($count - 1));
    }

    public function generateScore(Request $request, $companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $supplier = Supplier::where('company_id', $company->id)->findOrFail($id);

        $purchaseOrders = PurchaseOrder::where('suppliers_id', $supplier->id)->get();
        $totalOrders = $purchaseOrders->count();

        $onTime = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalOrders > 0
            ? round(($onTime / $totalOrders) * 100, 2)
            : 0;

        $avgLead = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->whereNotNull('delivered_date')
            ->avg(DB::raw('DATEDIFF(delivered_date, po_date)'));

        $avgLead = $avgLead ? round($avgLead, 2) : 0;

        $returns = DB::table('po_receive_detail as rd')
            ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
            ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
            ->where('po.suppliers_id', $supplier->id)
            ->selectRaw('SUM(rd.qty_received) as total_received, SUM(rd.qty_returned) as total_returned')
            ->first();

        $totalReceived = $returns->total_received ?? 0;
        $totalReturned = $returns->total_returned ?? 0;

        $rejectRate = $totalReceived > 0
            ? round(($totalReturned / $totalReceived) * 100, 2)
            : 0;

        $qtyAccuracy = ($totalReceived + $totalReturned) > 0
            ? round(($totalReceived / ($totalReceived + $totalReturned)) * 100, 2)
            : 0;

        $prices = SuppliersItem::where('suppliers_id', $supplier->id)
            ->pluck('price')
            ->toArray();

        $priceVariance = 0;

        if (count($prices) > 1) {
            $avg = array_sum($prices) / count($prices);

            $variance = 0;
            foreach ($prices as $p) {
                $variance += pow($p - $avg, 2);
            }

            $std = sqrt($variance / (count($prices) - 1));
            $priceVariance = $avg > 0
                ? round(($std / $avg) * 100, 2)
                : 0;
        }

        SupplierScore::create([
            'suppliers_id' => $supplier->id,
            'on_time_rate' => $onTimeRate,
            'reject_rate' => $rejectRate,
            'avg_quality' => (100 - $rejectRate),
            'price_variance' => $priceVariance,
            'notes' => 'Generated Automatically',
            'calculated_at' => now(),
        ]);

        return back()->with('success', 'Performance supplier berhasil dihitung & disimpan!');
    }

    public function generateScoreWithPeriod(Request $request, $companyCode, $id)
    {
        $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        $month = $request->period_month;
        $year = $request->period_year;

        $company = Company::where('code', $companyCode)->firstOrFail();
        $supplier = Supplier::where('company_id', $company->id)->findOrFail($id);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $poQuery = PurchaseOrder::where('suppliers_id', $supplier->id)
            ->whereBetween('po_date', [$startDate, $endDate]);

        $receiveQuery = DB::table('po_receive_detail as rd')
            ->join('po_receive as r', 'r.id', '=', 'rd.po_receive_id')
            ->join('purchase_order as po', 'po.id', '=', 'r.purchase_order_id')
            ->where('po.suppliers_id', $supplier->id)
            ->whereBetween('r.received_at', [$startDate, $endDate]);

        $totalOrders = $poQuery->count();

        // ON TIME
        $onTime = (clone $poQuery)
            ->whereColumn('delivered_date', '<=', 'expected_delivery_date')
            ->count();

        $onTimeRate = $totalOrders > 0
            ? round(($onTime / $totalOrders) * 100, 2)
            : 0;

        // LEAD TIME
        $avgLead = (clone $poQuery)
            ->whereNotNull('delivered_date')
            ->avg(DB::raw('DATEDIFF(delivered_date, po_date)'));
        $avgLead = $avgLead ? round($avgLead, 2) : 0;

        // RECEIVE DATA
        $returns = $receiveQuery
            ->selectRaw('SUM(rd.qty_received) as total_received, SUM(rd.qty_returned) as total_returned')
            ->first();

        $totalReceived = $returns->total_received ?? 0;
        $totalReturned = $returns->total_returned ?? 0;

        $rejectRate = $totalReceived > 0
            ? round(($totalReturned / $totalReceived) * 100, 2)
            : 0;

        $qtyAccuracy = ($totalReceived + $totalReturned) > 0
            ? round(($totalReceived / ($totalReceived + $totalReturned)) * 100, 2)
            : 0;

        // PRICE VARIANCE
        $poIds = $poQuery->pluck('id');

        $prices = PoDetail::whereIn('purchase_order_id', $poIds)
            ->pluck('unit_price')
            ->toArray();
        $priceVariance = 0;

        if (count($prices) > 1) {
            $avg = array_sum($prices) / count($prices);

            $variance = 0;
            foreach ($prices as $p) {
                $variance += pow($p - $avg, 2);
            }

            $std = sqrt($variance / (count($prices) - 1));
            $priceVariance = round(($std / $avg) * 100, 2);
        }

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
}
