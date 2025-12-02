<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function approve($branchCode, $id)
    {
        $requestTrans = InventoryTrans::findOrFail($id);

        // pastikan user login adalah cabang pengirim
        if ($requestTrans->cabang_id_from != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $requestTrans->update([
            'status' => 'APPROVED',
        ]);

        return back()->with('success', 'Request berhasil disetujui.');
    }

    public function reject($branchCode, $id)
    {
        $requestTrans = InventoryTrans::findOrFail($id);

        if ($requestTrans->cabang_id_from != session('role.branch.id')) {
            abort(403, 'Tidak punya akses.');
        }

        $requestTrans->update([
            'status' => 'REJECTED',
        ]);

        return back()->with('success', 'Request berhasil ditolak.');
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
}
