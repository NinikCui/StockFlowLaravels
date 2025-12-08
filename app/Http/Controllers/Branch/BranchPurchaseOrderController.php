<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\PoDetail;
use App\Models\PoReceive;
use App\Models\PoReceiveDetail;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class BranchPurchaseOrderController extends Controller
{
    public function index(Request $request, $branchCode)
    {
        $companyCode = session('company_code');
        $branch = CabangResto::where('code', $branchCode)->firstOrFail();
        $branchId = $branch->id;

        $query = PurchaseOrder::with(['supplier'])
            ->where('cabang_resto_id', $branchId);

        // =======================
        // FILTER: Search
        // =======================
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                    ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%$search%")
                    );
            });
        }

        // FILTER: Status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $pos = $query->orderBy('created_at', 'DESC')->paginate(12);

        return view('branch.purchase-order.index', [
            'companyCode' => $companyCode,
            'branchCode' => $branchCode,
            'branch' => $branch,
            'pos' => $pos,
        ]);
    }

    public function create($branchCode)
    {
        $companyId = session('role.company.id');

        // cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // warehouse hanya milik cabang ini
        $warehouses = Warehouse::where('cabang_resto_id', $branch->id)->get();

        $suppliers = Supplier::where('company_id', $companyId)
            ->where(function ($q) use ($branch) {
                $q->whereNull('cabang_resto_id')
                    ->orWhere('cabang_resto_id', $branch->id);
            })
            ->get();

        return view('branch.purchase-order.create', [
            'branch' => $branch,
            'warehouses' => $warehouses,
            'suppliers' => $suppliers,
            'branchCode' => $branch->code,
        ]);
    }

    public function store(Request $request, $branchCode)
    {
        // AMBIL COMPANY ID DARI SESSION
        $companyId = session('role.company.id');

        // VALIDASI CABANG
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // VALIDASI REQUEST
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'suppliers_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after_or_equal:po_date',
            'note' => 'required|string',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_ordered' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        // GENERATE PO NUMBER
        $poNumber = 'PO-'.strtoupper($branchCode).'-'.now()->format('YmdHis');

        // SIMPAN PURCHASE ORDER
        $po = PurchaseOrder::create([
            'company_id' => $companyId,
            'cabang_resto_id' => $branch->id,
            'warehouse_id' => $validated['warehouse_id'],
            'suppliers_id' => $validated['suppliers_id'],
            'po_date' => $validated['po_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'note' => $validated['note'],
            'status' => 'DRAFT',
            'ontime' => 0,
            'po_number' => $poNumber,
        ]);

        // SIMPAN ITEM DETAIL
        foreach ($validated['items'] as $item) {
            PoDetail::create([
                'purchase_order_id' => $po->id,
                'items_id' => $item['item_id'],
                'qty_ordered' => $item['qty_ordered'],
                'unit_price' => $item['unit_price'],
                'discount_pct' => $item['discount_pct'] ?? 0,
                'quality' => $item['quality'] ?? 0,

            ]);
        }

        return redirect()
            ->route('branch.po.show', [$branch->code, $po->id])
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    public function ajaxSupplierItems($branchCode, $supplierId)
    {

        $branch = CabangResto::where('code', $branchCode)->firstOrFail();
        $branchId = $branch->id;

        $supplier = Supplier::with(['supplierItems.item'])->findOrFail($supplierId);

        $items = $supplier->supplierItems->map(function ($si) {
            return [
                'id' => $si->items_id,
                'name' => $si->item->name,
                'price' => $si->price,
                'min_order_qty' => $si->min_order_qty,
            ];
        });

        return response()->json($items);
    }

    public function show($branchCode, $id)
    {
        $companyId = session('role.company.id');

        // Validasi cabang berdasarkan company + branchCode
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil PO yang memang milik cabang & company ini
        $po = PurchaseOrder::with([
            'details.item.satuan',
            'supplier',
            'cabangResto',
            'createdByUser',
        ])
            ->where('cabang_resto_id', $branch->id)
            ->findOrFail($id);

        return view('branch.purchase-order.show', [
            'po' => $po,
            'branch' => $branch,
            'branchCode' => $branchCode,
        ]);
    }

    public function edit($branchCode, $id)
    {
        $companyId = session('role.company.id');

        // validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // PO milik cabang ini
        $po = PurchaseOrder::with([
            'details.item',
            'supplier.supplierItems.item',
            'cabangResto',
            'warehouse',
        ])
            ->where('cabang_resto_id', $branch->id)
            ->findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diedit karena status bukan DRAFT.');
        }

        // Supplier items mapping
        $suppliers = Supplier::with(['supplierItems.item'])->get();

        $supplierItems = [];

        foreach ($suppliers as $s) {
            $supplierItems[$s->id] = $s->supplierItems->map(function ($si) {
                return [
                    'id' => $si->items_id,
                    'name' => $si->item->name,
                    'price' => $si->price,
                    'min_order_qty' => $si->min_order_qty,
                ];
            })->toArray();
        }

        // Supplier default dari PO
        $supplierId = $po->suppliers_id;

        // tambahkan item lama jika sudah tidak dijual
        foreach ($po->details as $d) {
            $exists = collect($supplierItems[$supplierId])
                ->contains(fn ($i) => $i['id'] == $d->item_id);

            if (! $exists) {
                $supplierItems[$supplierId][] = [
                    'id' => $d->item_id,
                    'name' => $d->item->name.' (Tidak lagi dijual)',
                    'price' => $d->unit_price,
                    'min_order_qty' => 1,
                ];
            }
        }

        return view('branch.purchase-order.edit', [
            'branch' => $branch,
            'branchCode' => $branchCode,
            'po' => $po,
            'supplierItems' => $supplierItems,
        ]);
    }

    public function update(Request $request, $branchCode, $id)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $po = PurchaseOrder::with('details')
            ->where('cabang_resto_id', $branch->id)
            ->findOrFail($id);

        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO tidak bisa diupdate karena status bukan DRAFT.');
        }

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

        // DELETE OLD DETAILS
        $po->details()->delete();

        // INSERT NEW
        foreach ($data['items'] as $i) {
            $po->details()->create([
                'items_id' => $i['item_id'],
                'qty_ordered' => $i['qty_ordered'],
                'unit_price' => $i['unit_price'],
                'discount_pct' => $i['discount_pct'] ?? 0,
                'quality' => $item['quality'] ?? 0,
            ]);
        }

        return redirect()
            ->route('branch.po.show', [$branchCode, $po->id])
            ->with('success', 'PO berhasil diperbarui.');
    }

    public function destroy($branchCode, $id)
    {
        $companyId = session('role.company.id');

        // Validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil PO
        $po = PurchaseOrder::where('cabang_resto_id', $branch->id)
            ->where('id', $id)
            ->firstOrFail();

        // Hanya DRAFT yang boleh dihapus
        if ($po->status !== 'DRAFT') {
            return back()->with('error', 'PO hanya bisa dihapus jika status masih DRAFT.');
        }

        // Hapus detail dulu
        $po->details()->delete();

        // Hapus PO utama
        $po->delete();

        return redirect()
            ->route('branch.po.index', $branch->code)
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

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

    public function updateStatus(Request $request, $branchCode, $poId)
    {
        $request->validate([
            'status' => 'required|in:DRAFT,APPROVED,PARTIAL,RECEIVED,CANCELLED',
        ]);

        $companyId = session('role.company.id');

        // Validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil PO
        $po = PurchaseOrder::where('cabang_resto_id', $branch->id)
            ->where('id', $poId)
            ->firstOrFail();

        $newStatus = $request->status;
        $allowed = ['DRAFT', 'APPROVED', 'PARTIAL', 'RECEIVED', 'CANCELLED'];

        // Status tidak valid
        if (! in_array($newStatus, $allowed)) {
            return back()->with('error', 'Status tidak valid.');
        }

        // Received & Cancelled tidak boleh diubah lagi
        if (in_array($po->status, ['RECEIVED', 'CANCELLED'])) {
            return back()->with('error', 'Status tidak bisa diubah.');
        }

        // Jika pindah ke RECEIVED → gunakan halaman receive
        if ($newStatus === 'RECEIVED') {
            return redirect()
                ->route('branch.po.receive.show', [$branchCode, $po->id]);
        }

        // Update status
        $po->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', 'Status PO berhasil diperbarui.');
    }

    public function showReceiveForm($branchCode, $poId)
    {
        $companyId = session('role.company.id');

        // Validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil PO milik cabang
        $po = PurchaseOrder::where('cabang_resto_id', $branch->id)
            ->with(['details.item.satuan', 'details.receives'])
            ->findOrFail($poId);

        // Hitung sisa receive
        foreach ($po->details as $detail) {
            $detail->total_received_before = $detail->receives->sum('qty_received');
            $detail->remaining = $detail->qty_ordered - $detail->total_received_before;
        }

        return view('branch.purchase-order.receive', [
            'po' => $po,
            'branchCode' => $branchCode,
        ]);
    }

    public function processReceive(Request $request, $branchCode, $poId)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $po = PurchaseOrder::where('cabang_resto_id', $branch->id)
            ->with(['details.item', 'details.receives'])
            ->findOrFail($poId);

        $receiveQty = $request->input('receive_qty', []);
        $returnQty = $request->input('return_qty', []);
        $expiredDates = $request->input('expired_at', []);

        $warehouse = Warehouse::findOrFail($po->warehouse_id);

        /* =============================
            VALIDASI QTY + EXPIRED DATE
        ============================= */
        foreach ($po->details as $detail) {

            $alreadyReceived = $detail->receives->sum('qty_received');
            $remaining = $detail->qty_ordered - $alreadyReceived;

            $recv = floatval($receiveQty[$detail->id] ?? 0);
            $ret = floatval($returnQty[$detail->id] ?? 0);
            $exp = $expiredDates[$detail->id] ?? null;

            if ($recv + $ret != $remaining) {
                return back()->withErrors([
                    "{$detail->item->name}: total received + return harus = {$remaining}",
                ]);
            }

            if (! $exp) {
                return back()->withErrors([
                    "{$detail->item->name}: Expired date wajib diisi",
                ]);
            }

            if (! strtotime($exp)) {
                return back()->withErrors([
                    "{$detail->item->name}: Format expired date tidak valid",
                ]);
            }

            if ($exp < date('Y-m-d')) {
                return back()->withErrors([
                    "{$detail->item->name}: Expired date tidak boleh tanggal lewat",
                ]);
            }
        }

        /* =============================
            SIMPAN HEADER
        ============================= */
        $receiveHeader = PoReceive::create([
            'purchase_order_id' => $po->id,
            'warehouse_id' => $warehouse->id,
            'received_by' => auth()->id(),
            'received_at' => now(),
        ]);

        /* =============================
            SIMPAN STOCK PER ITEM
        ============================= */
        foreach ($po->details as $detail) {

            $recv = floatval($receiveQty[$detail->id] ?? 0);
            $ret = floatval($returnQty[$detail->id] ?? 0);
            $exp = $expiredDates[$detail->id];

            PoReceiveDetail::create([
                'po_receive_id' => $receiveHeader->id,
                'po_detail_id' => $detail->id,
                'item_id' => $detail->items_id,
                'qty_received' => $recv,
                'qty_returned' => $ret,
            ]);

            if ($recv > 0) {

                // Generate stock code
                $prefix = 'STK-'.strtoupper($warehouse->code).'-';
                $last = Stock::where('warehouse_id', $warehouse->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                $next = $last ? intval(substr($last->code, -4)) + 1 : 1;
                $code = $prefix.str_pad($next, 4, '0', STR_PAD_LEFT);

                // CREATE STOCK WITH EXPIRED DATE
                $stock = Stock::create([
                    'company_id' => $branch->company_id,
                    'warehouse_id' => $warehouse->id,
                    'item_id' => $detail->items_id,
                    'qty' => 0,
                    'code' => $code,
                    'expired_at' => $exp,
                ]);

                $stock->increment('qty', $recv);

                StockMovement::create([
                    'company_id' => $branch->company_id,
                    'warehouse_id' => $warehouse->id,
                    'stock_id' => $stock->id,
                    'item_id' => $detail->items_id,
                    'created_by' => auth()->id(),
                    'type' => 'IN',
                    'qty' => $recv,
                    'expired_at' => $exp,
                    'reference' => "PO#{$po->po_number}",
                    'notes' => 'Receive PO',
                ]);
            }
        }

        $po->status = 'RECEIVED';
        $po->delivered_date = now();
        $po->save();

        return redirect()
            ->route('branch.po.show', [$branchCode, $po->id])
            ->with('success', 'Penerimaan PO berhasil disimpan.');
    }
}
