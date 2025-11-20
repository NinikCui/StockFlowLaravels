<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\PoDetail;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    /**
     * List PO
     */
    public function index($companyCode)
    {
        $po = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('company.purchase_order.index', compact('po', 'companyCode'));
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
            $supplierItems[$s->id] = $s->supplierItems->map(function($si) {
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
        $poNumber = "PO-" . strtoupper($companyCode) . "-" . now()->format('YmdHis');

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
        $po = PurchaseOrder::with(['details.item', 'supplier'])->findOrFail($id);

        return view('company.purchase_order.show', compact('po', 'companyCode'));
    }

    /**
     * Edit PO (only draft)
     */
    public function edit($companyCode, $id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diedit setelah approve.');
        }

        $suppliers = Supplier::all();
        $cabangs = CabangResto::all();
        $items = Item::all();

        return view('company.purchase_order.edit', compact('po', 'cabangs', 'suppliers', 'items', 'companyCode'));
    }

    /**
     * Update PO draft
     */
    public function update(Request $request, $companyCode, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diupdate setelah approve.');
        }

        $data = $request->validate([
            'note' => 'nullable|string|max:200',
        ]);

        $po->update([
            'note' => $data['note'],
        ]);

        return back()->with('success', 'PO berhasil diperbarui.');
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

        if (!in_array($po->status, ['APPROVED', 'PARTIAL'])) {
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
