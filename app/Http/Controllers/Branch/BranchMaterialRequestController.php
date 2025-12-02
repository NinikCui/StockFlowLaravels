<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

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

        if (request('from')) {
            $asReceiver->where('cabang_id_from', request('from'));
            $asSender->where('cabang_id_from', request('from'));
        }

        if (request('to')) {
            $asReceiver->where('cabang_id_to', request('to'));
            $asSender->where('cabang_id_to', request('to'));
        }

        if (request('status')) {
            $asReceiver->where('status', request('status'));
            $asSender->where('status', request('status'));
        }

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

        // Cabang aktif (CABANG TUJUAN)
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Cabang asal (CABANG LAIN)
        $branchesFrom = CabangResto::where('company_id', $companyId)
            ->where('id', '!=', $branch->id)
            ->get();

        // Load stock item untuk cabang ini
        $warehouses = Warehouse::where('cabang_resto_id', $branch->id)->get();
        $stocks = Stock::with(['item.satuan'])
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->get();

        $items = [];
        $added = [];

        foreach ($stocks as $s) {
            if (! in_array($s->item->id, $added)) {
                $added[] = $s->item->id;
                $items[] = [
                    'id' => $s->item->id,
                    'name' => $s->item->name,
                    'satuan' => $s->item->satuan->code,
                ];
            }
        }

        return view('branch.request-cabang.create', [
            'branchCode' => $branchCode,
            'branch' => $branch,
            'branchesFrom' => $branchesFrom,
            'items' => $items,
        ]);
    }

    public function store(Request $request, $branchCode)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $validated = $request->validate([
            'cabang_from_id' => 'required|exists:cabang_resto,id|not_in:'.$branch->id,
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        // FROM = cabang lain
        $warehouseFrom = Warehouse::where('cabang_resto_id', $validated['cabang_from_id'])->firstOrFail();

        // TO = cabang ini
        $warehouseTo = Warehouse::where('cabang_resto_id', $branch->id)->firstOrFail();

        $trans = InventoryTrans::create([
            'warehouse_id_from' => $warehouseFrom->id,
            'warehouse_id_to' => $warehouseTo->id,
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
            ]);
        }

        return redirect()
            ->route('branch.request.index', $branchCode)
            ->with('success', 'Request berhasil dibuat.');
    }
}
