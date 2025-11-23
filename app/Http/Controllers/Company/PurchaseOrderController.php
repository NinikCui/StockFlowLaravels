<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\PoDetail;
use App\Models\PoReceive;
use App\Models\PoReceiveDetail;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * List PO
     */
    public function index(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $suppliers = Supplier::where('company_id', $company->id)->get();

        $query = PurchaseOrder::with('supplier', 'cabangResto')
            ->whereHas('cabangResto', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });

        if ($request->filled('po_number')) {
            $query->where('po_number', 'LIKE', '%'.$request->po_number.'%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('suppliers_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        $po = $query->orderBy('po_date', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('company.purchase_order.index', compact(
            'po',
            'suppliers',
            'companyCode'
        ));
    }

    /**
     * Create PO Form
     */
    public function create($companyCode)
    {
        $suppliers = Supplier::with(['supplierItems.item'])->get();
        $cabangs = CabangResto::all();
        $warehouses = Warehouse::with('cabangResto')->get();

        $supplierItems = [];

        foreach ($suppliers as $s) {
            $supplierItems[$s->id] = $s->supplierItems->map(function ($si) {
                return [
                    'id' => $si->items_id,
                    'name' => $si->item->name,
                ];
            });
        }

        return view('company.purchase_order.create', [
            'suppliers' => $suppliers,
            'cabangs' => $cabangs,
            'companyCode' => $companyCode,
            'warehouses' => $warehouses,
            'supplierItems' => $supplierItems,
        ]);
    }

    public function store(Request $request, $companyCode)
    {
        $data = $request->validate([
            'cabang_resto_id' => 'required|exists:cabang_resto,id',
            'warehouse_id' => 'required|exists:warehouse,id',
            'suppliers_id' => 'required|exists:suppliers,id',

            'po_date' => 'required|date|before_or_equal:today',
            'expected_delivery_date' => 'nullable|date|after:today',

            'note' => 'nullable|string|max:200',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0|max:100',
            'items.*.quality' => 'nullable|numeric|min:0|max:5',
        ]);
        $warehouse = Warehouse::findOrFail($data['warehouse_id']);

        if ($warehouse->cabang_resto_id != $data['cabang_resto_id']) {
            return back()->withErrors([
                'warehouse_id' => 'Gudang tidak sesuai dengan cabang yang dipilih.',
            ]);
        }

        $poNumber = 'PO-'.strtoupper($companyCode).'-'.now()->format('YmdHis');

        $po = PurchaseOrder::create([
            'cabang_resto_id' => $data['cabang_resto_id'],
            'warehouse_id' => $data['warehouse_id'],
            'suppliers_id' => $data['suppliers_id'],

            'po_date' => $data['po_date'],
            'expected_delivery_date' => $data['expected_delivery_date'],
            'note' => $data['note'],

            'po_number' => $poNumber,
            'status' => 'DRAFT',
            'created_by' => auth()->id(),
            'ontime' => false,
        ]);

        foreach ($data['items'] as $i) {
            PoDetail::create([
                'purchase_order_id' => $po->id,
                'items_id' => $i['item_id'],
                'qty_ordered' => $i['qty_ordered'],
                'unit_price' => $i['unit_price'],
                'discount_pct' => $i['discount_pct'] ?? 0,
                'quality' => $i['quality'] ?? 0,
            ]);
        }

        return redirect()->route('po.show', [$companyCode, $po->id])
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    /**
     * Show PO Detail
     */
    public function show($companyCode, $id)
    {
        $po = PurchaseOrder::with([
            'details.item',
            'supplier',
            'cabangResto',
        ])->findOrFail($id);

        return view('company.purchase_order.show', compact('po', 'companyCode'));
    }

    public function edit($companyCode, $id)
    {
        $po = PurchaseOrder::with([
            'details.item',
            'supplier.supplierItems.item',
            'cabangResto',
        ])->findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diedit karena status bukan DRAFT.');
        }

        // SUPPLIER LIST
        $suppliers = Supplier::with(['supplierItems.item'])
            ->orderBy('name')
            ->get();

        // CABANG LIST
        $cabangs = CabangResto::orderBy('name')->get();

        // BUILD supplierItems EXACTLY seperti halaman CREATE
        $supplierItems = [];

        foreach ($suppliers as $s) {
            $supplierItems[$s->id] = $s->supplierItems->map(function ($si) {
                return [
                    'id' => $si->items_id,
                    'name' => $si->item->name,
                ];
            })->toArray();
        }

        // Tambahkan LEGACY ITEM agar tetap muncul
        $supplierId = $po->suppliers_id;

        foreach ($po->details as $d) {
            $exists = collect($supplierItems[$supplierId])
                ->contains(fn ($i) => $i['id'] == $d->item_id);

        }

        return view('company.purchase_order.edit', compact(
            'po',
            'cabangs',
            'suppliers',
            'supplierItems',
            'companyCode'
        ));
    }

    /**
     * Update PO draft
     */
    public function update(Request $request, $companyCode, $id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diupdate karena status bukan DRAFT.');
        }

        // VALIDATE
        $data = $request->validate([
            'note' => 'nullable|string|max:200',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_ordered' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        // UPDATE NOTE
        $po->update([
            'note' => $data['note'],
        ]);

        // HAPUS DETAIL LAMA
        $po->details()->delete();

        // INSERT ULANG DETAIL
        foreach ($data['items'] as $item) {
            $po->details()->create([
                'items_id' => $item['item_id'],       // ← FIX WAJIB
                'qty_ordered' => $item['qty_ordered'],
                'unit_price' => $item['unit_price'],
                'discount_pct' => $item['discount_pct'] ?? 0,
                'quality' => $i['quality'] ?? 0,
            ]);
        }

        return redirect()->route('po.show', [$companyCode, $po->id])
            ->with('success', 'PO berhasil diperbarui.');
    }

    /**
     * Approve PO
     */
    public function approve($companyCode, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO hanya bisa diapprove dari status DRAFT.');
        }

        $po->update(['status' => 'APPROVED']);

        return back()->with('success', 'PO berhasil diapprove.');
    }

    /**
     * Cancel PO
     */
    public function cancel($companyCode, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status === 'RECEIVED') {
            return back()->with('error', 'PO yang sudah diterima tidak dapat dibatalkan.');
        }

        $po->update(['status' => 'CANCELLED']);

        return back()->with('success', 'PO berhasil dibatalkan.');
    }

    /**
     * Delete draft PO
     */
    public function destroy($companyCode, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'Hanya PO draft yang bisa dihapus.');
        }

        $po->delete();

        return redirect()->route('po.index', $companyCode)
            ->with('success', 'PO berhasil dihapus.');
    }

    public function updateStatus(Request $request, $companyCode, PurchaseOrder $id)
    {
        $request->validate([
            'status' => 'required|in:DRAFT,APPROVED,PARTIAL,RECEIVED,CANCELLED',
        ]);
        $status = $request->status;
        $allowed = ['DRAFT', 'APPROVED', 'RECEIVED', 'CANCELLED', 'PARTIAL'];

        if (! in_array($status, $allowed)) {
            return back()->with('error', 'Status tidak valid.');
        }

        // Jangan ubah received atau cancelled
        if (in_array($id->status, ['RECEIVED', 'CANCELLED'])) {
            return back()->with('error', 'Status tidak bisa diubah.');
        }

        if ($request->status == 'RECEIVED') {
            return redirect()->route('po.receive.show', [$companyCode, $id->id]);
        }

        $id->update(['status' => $request->status]);

        return back()->with('success', 'Status PO diperbarui.');
    }

    public function showReceiveForm($companyCode, PurchaseOrder $po)
    {
        // Load item + satuan + receive detail sebelumnya
        $po->load(['details.item.satuan', 'details.receives']);

        // Hitung remaining
        foreach ($po->details as $detail) {
            $detail->total_received_before = $detail->receives->sum('qty_received');
            $detail->remaining = $detail->qty_ordered - $detail->total_received_before;
        }

        return view('company.purchase_order.receive', [
            'po' => $po,
            'companyCode' => $companyCode,
        ]);
    }

    public function processReceive(Request $request, $companyCode, PurchaseOrder $po)
    {
        $po->load(['details.item', 'details.receives']);
        $receiveQty = $request->input('receive_qty', []);
        $returnQty = $request->input('return_qty', []);

        $warehouse = Warehouse::find($po->warehouse_id);

        foreach ($po->details as $detail) {

            $alreadyReceived = $detail->receives->sum('qty_received');
            $remaining = $detail->qty_ordered - $alreadyReceived;

            $recv = floatval($receiveQty[$detail->id] ?? 0);
            $ret = floatval($returnQty[$detail->id] ?? 0);

            if ($recv < 0 || $ret < 0) {
                return back()->withErrors(["{$detail->item->name}: nilai tidak boleh negatif"]);
            }

            if ($recv + $ret != $remaining) {
                return back()->withErrors([
                    "{$detail->item->name}: total (received + return) harus = {$remaining}",
                ]);
            }
        }

        $receiveHeader = PoReceive::create([
            'purchase_order_id' => $po->id,
            'warehouse_id' => $warehouse->id,
            'received_by' => auth()->id(),
            'received_at' => now(),
        ]);

        foreach ($po->details as $detail) {

            $alreadyReceived = $detail->receives->sum('qty_received');
            $remaining = $detail->qty_ordered - $alreadyReceived;

            $recv = floatval($receiveQty[$detail->id] ?? 0);
            $ret = floatval($returnQty[$detail->id] ?? 0);

            // save receive detail
            PoReceiveDetail::create([
                'po_receive_id' => $receiveHeader->id,
                'po_detail_id' => $detail->id,
                'item_id' => $detail->items_id,
                'qty_received' => $recv,
                'qty_returned' => $ret,
            ]);

            if ($recv > 0) {

                // generate code
                $prefix = 'STK-'.strtoupper($warehouse->code).'-';
                $last = Stock::where('warehouse_id', $warehouse->id)->orderBy('id', 'DESC')->first();
                $next = $last ? intval(substr($last->code, -4)) + 1 : 1;
                $code = $prefix.str_pad($next, 4, '0', STR_PAD_LEFT);

                $stock = Stock::create([
                    'company_id' => $po->cabangResto->company_id,
                    'warehouse_id' => $warehouse->id,
                    'item_id' => $detail->items_id,
                    'qty' => 0,
                    'code' => $code,
                ]);
            }

            $stock->increment('qty', $recv);

            StockMovement::create([
                'company_id' => $po->cabangResto->company_id,
                'warehouse_id' => $warehouse->id,
                'stock_id' => $stock->id,
                'item_id' => $detail->items_id,
                'created_by' => auth()->id(),
                'type' => 'IN',
                'qty' => $recv,
                'reference' => "PO#{$po->po_number}",
                'notes' => 'Receive PO',
            ]);
        }

        /* =====================================================
         | UPDATE STATUS
         ===================================================== */
        $allRemaining = true;

        foreach ($po->details as $detail) {
            $receivedTotal = $detail->receives->sum('qty_received') + ($receiveQty[$detail->id] ?? 0);

            if ($receivedTotal < $detail->qty_ordered) {
                $allRemaining = false;
            }
        }

        $po->status = 'RECEIVED';
        $po->delivered_date = now();
        $po->save();

        return redirect()
            ->route('po.show', [$companyCode, $po->id])
            ->with('success', 'Penerimaan PO berhasil disimpan.');
    }
}
