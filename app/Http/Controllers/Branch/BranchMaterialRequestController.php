<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchMaterialRequestController extends Controller
{
    public function index($branchCode)
    {
        $companyId = session('role.company.id');

        // Cabang aktif
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Semua cabang milik perusahaan (digunakan untuk filter)
        $branches = CabangResto::where('company_id', $companyId)->get();

        $asReceiver = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item',
        ])->where('cabang_id_to', $branch->id);

        $asSender = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item',
        ])->where('cabang_id_from', $branch->id);

        // Filter untuk tab RECEIVER - hanya filter cabang asal
        if (request('tab') === 'receiver' && request('from')) {
            $asReceiver->where('cabang_id_from', request('from'));
        }

        // Filter untuk tab SENDER - hanya filter cabang tujuan
        if (request('tab') === 'sender' && request('to')) {
            $asSender->where('cabang_id_to', request('to'));
        }

        // Filter status untuk kedua tab
        if (request('status')) {
            $asReceiver->where('status', request('status'));
            $asSender->where('status', request('status'));
        }

        // Filter tanggal untuk kedua tab
        if (request('date')) {
            $asReceiver->whereDate('trans_date', request('date'));
            $asSender->whereDate('trans_date', request('date'));
        }

        return view('branch.request-cabang.index', [
            'branchCode' => $branchCode,
            'branch' => $branch,
            'branches' => $branches,
            'asSender' => $asSender->orderByDesc('id')->get(),
            'asReceiver' => $asReceiver->orderByDesc('id')->get(),
        ]);
    }

    public function create($branchCode)
    {
        $companyId = session('role.company.id');

        // Cabang aktif sebagai penerima
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Semua cabang lain sebagai cabang asal
        $branchesFrom = CabangResto::where('company_id', $companyId)
            ->where('id', '!=', $branch->id)
            ->get();

        // Ambil item per cabang asal
        $itemsPerBranch = [];

        foreach ($branchesFrom as $b) {

            $warehouses = Warehouse::where('cabang_resto_id', $b->id)->get();

            $stocks = Stock::with(['item.satuan'])
                ->whereIn('warehouse_id', $warehouses->pluck('id'))
                ->get();

            $uniqueItems = [];
            $added = [];

            foreach ($stocks as $s) {
                if (! in_array($s->item->id, $added)) {
                    $added[] = $s->item->id;

                    $uniqueItems[] = [
                        'id' => $s->item->id,
                        'name' => $s->item->name,
                        'satuan' => $s->item->satuan->code,
                    ];
                }
            }

            $itemsPerBranch[$b->id] = $uniqueItems;
        }

        return view('branch.request-cabang.create', [
            'branchCode' => $branchCode,
            'branch' => $branch,
            'branchesFrom' => $branchesFrom,
            'itemsPerBranch' => $itemsPerBranch, // ★ dikirim ke blade
        ]);
    }

    public function store(Request $request, $branchCode)
    {
        $validated = $request->validate([
            'cabang_from_id' => 'required|exists:cabang_resto,id',
            'cabang_to_id' => 'required|exists:cabang_resto,id|different:cabang_from_id',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',

            'note' => 'nullable|string',
        ]);

        $trans = InventoryTrans::create([
            'cabang_id_from' => $validated['cabang_from_id'],
            'cabang_id_to' => $validated['cabang_to_id'],

            'trans_number' => 'MR-'.now()->format('YmdHis'),
            'trans_date' => today(),
            'status' => 'REQUESTED',
            'reason' => 'MATERIAL_REQUEST',
            'note' => $validated['note'] ?? null,

            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $row) {
            InvenTransDetail::create([
                'inven_trans_id' => $trans->id,
                'items_id' => $row['item_id'],
                'qty' => $row['qty'],
                'note' => null,
            ]);
        }

        return redirect()
            ->route('branch.request.index', $branchCode)
            ->with('success', 'Request berhasil dibuat.');
    }

    public function show($branchCode, $id)
    {
        $companyId = session('role.company.id');

        // Pastikan request berasal dari perusahaan yang sama
        $requestTrans = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item.satuan',
        ])
            ->where('id', $id)
            ->whereHas('cabangTo', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->firstOrFail();

        return view('branch.request-cabang.show', [
            'branchCode' => $branchCode,
            'req' => $requestTrans,
        ]);
    }

    public function approve(Request $request, $branchCode, $id)
    {
        $req = InventoryTrans::with(['details.item'])->findOrFail($id);

        // hanya cabang pengirim yang boleh approve
        if ($req->cabang_id_from != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $request->validate([
            'alloc' => 'required|array',
            'alloc.*' => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            Log::info("=== APPROVE REQUEST {$req->id} DIMULAI ===");

            foreach ($req->details as $detail) {

                // FIX PALING PENTING:
                $itemId = $detail->item->id;   // gunakan relasi, bukan item_id

                $needed = $detail->qty;

                Log::info('ITEM PROCESS START', [
                    'item_id' => $itemId,
                    'item_name' => $detail->item->name,
                    'needed' => $needed,
                ]);

                // ambil alokasi untuk item ini
                $alloc = $request->alloc[$itemId] ?? null;

                Log::info('ALLOC RECEIVED', [
                    'item_id' => $itemId,
                    'alloc' => $alloc,
                ]);

                if (! $alloc) {
                    DB::rollBack();
                    Log::error('ALLOC TIDAK DITEMUKAN', ['item_id' => $itemId]);

                    return back()->withErrors("Alokasi stok untuk item {$detail->item->name} tidak ditemukan.");
                }

                $totalAlloc = array_sum($alloc);

                if ($totalAlloc != $needed) {
                    DB::rollBack();

                    Log::error('ALLOC != NEEDED', [
                        'item_id' => $itemId,
                        'needed' => $needed,
                        'total_alloc' => $totalAlloc,
                    ]);

                    return back()->withErrors("Total alokasi item {$detail->item->name} harus tepat {$needed}.");
                }

                // kurangi stok per warehouse
                foreach ($alloc as $stockId => $qty) {

                    Log::info('CEK STOCK RECORD', [
                        'stock_id' => $stockId,
                        'alloc_qty' => $qty,
                    ]);

                    $stock = Stock::where('id', $stockId)
                        ->where('item_id', $itemId)
                        ->lockForUpdate()
                        ->first();

                    if (! $stock) {
                        DB::rollBack();

                        return back()->withErrors("Stock ID {$stockId} tidak ditemukan untuk item {$detail->item->name}.");
                    }

                    if ($stock->qty < $qty) {
                        DB::rollBack();

                        return back()->withErrors("Stok (code: {$stock->code}) tidak cukup. Punya {$stock->qty}, butuh {$qty}.");
                    }

                    $stock->qty -= $qty;
                    $stock->save();

                    Log::info('STOK UPDATE', [
                        'stock_id' => $stockId,
                        'before' => $stock->qty + $qty,
                        'after' => $stock->qty,
                    ]);
                }

            }

            // update status
            $req->update(['status' => 'APPROVED']);

            Log::info('STATUS UPDATED', ['request_id' => $req->id]);

            DB::commit();

            Log::info('=== APPROVE FINISH ===');

            return redirect()
                ->route('branch.request.show', [$branchCode, $req->id])
                ->with('success', 'Request berhasil di-approve.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('EXCEPTION', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }

    public function reject(Request $request, $branchCode, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        $req = InventoryTrans::findOrFail($id);

        // hanya cabang pengirim boleh reject
        if ($req->cabang_id_from != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $req->update([
            'status' => 'REJECTED',
            'reason' => $request->reason,
        ]);

        return redirect()
            ->route('branch.request.show', [$branchCode, $id])
            ->with('success', 'Request berhasil ditolak.');
    }

    public function destroy($branchCode, $id)
    {
        $req = InventoryTrans::findOrFail($id);

        if ($req->cabang_id_to != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $req->details()->delete();
        $req->delete();

        return redirect()->route('branch.request.index', $branchCode)
            ->with('success', 'Request berhasil dihapus.');
    }

    public function edit($branchCode, $id)
    {
        $companyId = session('role.company.id');

        $req = InventoryTrans::with([
            'details.item.satuan',
            'cabangFrom',
            'cabangTo',
        ])->findOrFail($id);

        // hanya cabang penerima boleh edit
        if ($req->cabang_id_to != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        // Semua cabang pengirim yang valid (selain cabang penerima)
        $branchesFrom = CabangResto::where('company_id', $companyId)
            ->where('id', '!=', $req->cabang_id_to)
            ->get();

        // ITEM PER CABANG MELALUI STOCK (PAKAI WAREHOUSE)
        $itemsPerBranch = [];

        foreach ($branchesFrom as $b) {

            // Ambil daftar warehouse milik cabang ini
            $warehouseIds = Warehouse::where('cabang_resto_id', $b->id)
                ->pluck('id');

            // Ambil stok item berdasarkan warehouse cabang
            $stocks = Stock::with(['item.satuan'])
                ->whereIn('warehouse_id', $warehouseIds)
                ->get();

            $uniqueItems = [];
            $seen = [];

            foreach ($stocks as $s) {
                if (! in_array($s->item->id, $seen)) {
                    $seen[] = $s->item->id;
                    $uniqueItems[] = [
                        'id' => $s->item->id,
                        'name' => $s->item->name,
                        'satuan' => $s->item->satuan->code,
                        'qty' => $s->qty,
                    ];
                }
            }

            $itemsPerBranch[$b->id] = $uniqueItems;
        }

        return view('branch.request-cabang.edit', [
            'branchCode' => $branchCode,
            'req' => $req,
            'branchesFrom' => $branchesFrom,
            'itemsPerBranch' => $itemsPerBranch,
        ]);
    }

    public function send($branchCode, $id)
    {
        $req = InventoryTrans::findOrFail($id);

        // hanya cabang pengirim
        if ($req->cabang_id_from != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        // hanya boleh kirim jika APPROVED
        if ($req->status !== 'APPROVED') {
            return back()->withErrors('Request belum di-approve.');
        }

        $req->update([
            'status' => 'IN_TRANSIT',
        ]);

        return back()->with('success', 'Barang berhasil dikirim (IN TRANSIT).');
    }

    public function update(Request $request, $branchCode, $id)
    {
        $req = InventoryTrans::findOrFail($id);

        if ($req->cabang_id_to != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $validated = $request->validate([
            'cabang_from_id' => 'required|exists:cabang_resto,id|different:'.$req->cabang_id_to,
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        // Update header
        $req->update([
            'cabang_id_from' => $validated['cabang_from_id'],
            'note' => $validated['note'] ?? null,
            'status' => 'REQUESTED', // reset status jika di-edit
        ]);

        // Hapus detail lama
        $req->details()->delete();

        // Tambahkan detail baru
        foreach ($validated['items'] as $row) {
            InvenTransDetail::create([
                'inven_trans_id' => $req->id,
                'items_id' => $row['item_id'],
                'qty' => $row['qty'],
            ]);
        }

        DB::commit();

        return redirect()
            ->route('branch.request.show', [$branchCode, $id])
            ->with('success', 'Request berhasil diperbarui.');
    }

    public function receive(Request $request, $branchCode, $id)
    {
        $companyId = session('role.company.id');

        $req = InventoryTrans::with('details.item')->findOrFail($id);

        // hanya cabang tujuan yang boleh menerima barang
        if ($req->cabang_id_to != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        // status harus IN_TRANSIT
        if ($req->status !== 'IN_TRANSIT') {
            return back()->withErrors('Barang belum dikirim, tidak dapat diterima.');
        }

        $data = $request->receive;

        DB::beginTransaction();

        try {

            foreach ($req->details as $detail) {

                $itemId = $detail->item->id;

                // DATA DARI FORM
                $warehouseId = $data[$itemId]['warehouse_id'] ?? null;
                $qtyReceived = $data[$itemId]['qty'] ?? 0;
                $expiredAt = $data[$itemId]['expired_at'] ?? null;

                // LOGGING
                Log::info('RECEIVE ITEM', [
                    'item_id' => $itemId,
                    'warehouse_id' => $warehouseId,
                    'qty_received' => $qtyReceived,
                    'expired_at' => $expiredAt,
                ]);

                // =============================
                // VALIDASI DATA PER ITEM
                // =============================
                if (! $warehouseId) {
                    return back()->withErrors("Gudang wajib dipilih untuk item {$detail->item->name}");
                }

                if ($qtyReceived < 0) {
                    return back()->withErrors("Qty tidak boleh minus untuk item {$detail->item->name}");
                }

                // Validasi Expired Date
                if (! $expiredAt) {
                    return back()->withErrors("Expired date wajib diisi untuk item {$detail->item->name}");
                }

                if (! strtotime($expiredAt)) {
                    return back()->withErrors("Format expired date tidak valid untuk item {$detail->item->name}");
                }

                if ($expiredAt < date('Y-m-d')) {
                    return back()->withErrors("Expired date tidak boleh tanggal lewat untuk item {$detail->item->name}");
                }

                // =============================
                // BUAT STOK BARU
                // =============================
                $newCode = Stock::generateCode($warehouseId);

                Log::info('CREATE NEW STOCK', [
                    'warehouse_id' => $warehouseId,
                    'item_id' => $itemId,
                    'qty' => $qtyReceived,
                    'code' => $newCode,
                    'expired_at' => $expiredAt,
                ]);

                $stock = Stock::create([
                    'company_id' => $companyId,
                    'warehouse_id' => $warehouseId,
                    'item_id' => $itemId,
                    'qty' => $qtyReceived,
                    'code' => $newCode,
                    'expired_at' => $expiredAt,
                ]);

                // =============================
                // STOCK MOVEMENT
                // =============================
                StockMovement::create([
                    'company_id' => $companyId,
                    'warehouse_id' => $warehouseId,
                    'stock_id' => $stock->id,
                    'item_id' => $itemId,
                    'created_by' => auth()->id(),
                    'type' => 'IN',
                    'qty' => $qtyReceived,
                    'expired_at' => $expiredAt,
                    'reference' => "Transfer Receive #{$req->trans_number}",
                    'notes' => 'Receive from inter-branch transfer',
                ]);
            }

            // =============================
            // UPDATE STATUS REQUEST
            // =============================
            $req->update([
                'status' => 'RECEIVED',
                'received_at' => now(),
            ]);

            Log::info('REQUEST RECEIVED', [
                'id' => $req->id,
                'status' => 'RECEIVED',
            ]);

            DB::commit();

            return back()->with('success', 'Barang berhasil diterima dan stok telah diperbarui.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('RECEIVE ERROR', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }
}
