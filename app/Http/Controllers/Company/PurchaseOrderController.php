<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\PoDetail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
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
        // Ambil supplier + items lewat tabel pivot
        $suppliers = Supplier::with(['supplierItems.item'])->get();
        $cabangs = CabangResto::all();

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
            'supplierItems' => $supplierItems,
        ]);
    }

    public function store(Request $request, $companyCode)
    {
        $data = $request->validate([
            'cabang_resto_id' => 'required|exists:cabang_resto,id',
            'suppliers_id' => 'required|exists:suppliers,id',

            // ⬇ PERBAIKAN PENTING
            'po_date' => 'required|date|before_or_equal:today',
            'expected_delivery_date' => 'nullable|date|after:today',

            'note' => 'nullable|string|max:200',

            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0',
            'items.*.quality' => 'nullable|numeric|min:0|max:5',
        ]);

        // Generate PO number (simple)
        $poNumber = 'PO-'.strtoupper($companyCode).'-'.now()->format('YmdHis');

        // Create PO
        $po = PurchaseOrder::create([
            'cabang_resto_id' => $data['cabang_resto_id'],
            'suppliers_id' => $data['suppliers_id'],
            'po_date' => $data['po_date'],
            'status' => 'DRAFT',
            'note' => $data['note'],
            'expected_delivery_date' => $data['expected_delivery_date'],
            'po_number' => $poNumber,
            'created_by' => auth()->id(),
            'ontime' => false,
        ]);

        // Insert items
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
     * Receive (barang diterima)
     * Nanti integrasi stok masuk
     */
    public function receive(Request $request, $companyCode, $id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        if (! in_array($po->status, ['APPROVED', 'PARTIAL'])) {
            return back()->with('error', 'PO belum di-approve.');
        }

        $data = $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'required|exists:po_detail,id',
            'details.*.qty_received' => 'required|numeric|min:0',
        ]);

        // Loop all items
        foreach ($data['details'] as $row) {
            $detail = PoDetail::find($row['id']);
            $received = $row['qty_received'];

            // Update quality if needed
            $detail->update([
                'quality' => $detail->quality,
            ]);
        }

        $po->update([
            'status' => 'RECEIVED',
            'ontime' => now()->toDateString() <= $po->expected_delivery_date,
        ]);

        return back()->with('success', 'Barang berhasil diterima.');
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
}
