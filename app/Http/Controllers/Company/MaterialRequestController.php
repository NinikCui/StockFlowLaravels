<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    /**
     * INDEX — Owner melihat semua request antar cabang
     */
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        // Ambil semua cabang perusahaan
        $branches = CabangResto::where('company_id', $companyId)->get();

        // Query dasar (langsung filter berdasarkan cabang tujuan)
        $query = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item',
        ])
            ->whereIn('cabang_id_to', $branches->pluck('id'));

        // Cabang Asal
        if ($from = request('from')) {
            $query->where('cabang_id_from', $from);
        }

        // Cabang Tujuan
        if ($to = request('to')) {
            $query->where('cabang_id_to', $to);
        }

        // Status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Tanggal
        if ($date = request('date')) {
            $query->whereDate('trans_date', $date);
        }

        $requests = $query->orderByDesc('id')->get();

        return view('company.request-cabang.index', compact(
            'requests',
            'companyCode',
            'branches'
        ));
    }

    /**
     * CREATE — Owner memilih cabang asal, cabang tujuan, dan item
     */
    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $branches = CabangResto::where('company_id', $company->id)->get();

        $stocks = Stock::with(['item.satuan'])->get();

        $warehouses = Warehouse::all();

        $itemsPerBranch = [];

        foreach ($warehouses as $w) {

            if (! isset($itemsPerBranch[$w->cabang_resto_id])) {
                $itemsPerBranch[$w->cabang_resto_id] = [];
            }

            $alreadyAdded = [];

            foreach ($stocks->where('warehouse_id', $w->id) as $s) {

                if (in_array($s->item->id, $alreadyAdded)) {
                    continue;
                }

                $alreadyAdded[] = $s->item->id;

                $itemsPerBranch[$w->cabang_resto_id][] = [
                    'id' => $s->item->id,
                    'name' => $s->item->name,
                    'satuan' => $s->item->satuan->code,
                ];
            }
        }

        return view('company.request-cabang.create', [
            'companyCode' => $companyCode,
            'branches' => $branches,
            'itemsPerBranch' => $itemsPerBranch,
        ]);
    }

    /**
     * STORE — Owner membuat request antar cabang
     */
    public function store(Request $request, $companyCode)
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
            ->route('request.index', $companyCode)
            ->with('success', 'Request berhasil dibuat.');
    }

    /**
     * SHOW
     */
    public function show($companyCode, $id)
    {
        $requestTrans = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item.satuan',
        ])
            ->where('id', $id)
            ->firstOrFail();

        return view('company.request-cabang.show', [
            'companyCode' => $companyCode,
            'req' => $requestTrans,
        ]);
    }

    public function edit($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        // Load header + relasi
        $req = InventoryTrans::with([
            'cabangFrom',
            'cabangTo',
            'details.item.satuan',
        ])->findOrFail($id);

        // Tidak boleh edit jika bukan REQUESTED
        if ($req->status !== 'REQUESTED') {
            return redirect()
                ->route('request.show', [$companyCode, $id])
                ->with('error', 'Request ini sudah tidak dapat diedit.');
        }

        // Ambil semua cabang milik perusahaan
        $branches = CabangResto::where('company_id', $company->id)->get();

        // Ambil semua warehouse milik cabang tsb
        $warehouses = Warehouse::whereIn('cabang_resto_id', $branches->pluck('id'))->get();

        // Ambil semua stok dari warehouse tersebut (sesuai DB)
        $stocks = Stock::with(['item.satuan'])
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->get();

        // Grup item per cabang
        $itemsPerBranch = [];

        foreach ($warehouses as $w) {

            if (! isset($itemsPerBranch[$w->cabang_resto_id])) {
                $itemsPerBranch[$w->cabang_resto_id] = [];
            }

            $added = []; // mencegah duplikasi item

            foreach ($stocks->where('warehouse_id', $w->id) as $s) {

                if (in_array($s->item_id, $added)) {
                    continue;
                }

                $added[] = $s->item_id;

                $itemsPerBranch[$w->cabang_resto_id][] = [
                    'id' => $s->item->id,
                    'name' => $s->item->name,
                    'satuan' => $s->item->satuan->code,
                ];
            }
        }

        return view('company.request-cabang.edit', [
            'companyCode' => $companyCode,
            'req' => $req,
            'branches' => $branches,
            'itemsPerBranch' => $itemsPerBranch,
        ]);
    }

    public function update(Request $request, $companyCode, $id)
    {
        $validated = $request->validate([
            'cabang_id_from' => 'required|exists:cabang_resto,id',
            'cabang_id_to' => 'required|exists:cabang_resto,id|different:cabang_id_from',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $req = InventoryTrans::findOrFail($id);

        // Cuma REQUESTED yang bisa di-update
        if ($req->status !== 'REQUESTED') {
            return redirect()
                ->route('request.show', [$companyCode, $id])
                ->with('error', 'Tidak dapat mengedit request ini.');
        }

        // ======================
        //   UPDATE HEADER
        // ======================
        $req->update([
            'cabang_id_from' => $validated['cabang_id_from'],
            'cabang_id_to' => $validated['cabang_id_to'],
            'note' => $validated['note'],
        ]);

        // ======================
        //   UPDATE DETAILS
        // ======================
        $req->details()->delete();

        foreach ($validated['items'] as $row) {
            InvenTransDetail::create([
                'inven_trans_id' => $req->id,
                'items_id' => $row['item_id'],
                'qty' => $row['qty'],
            ]);
        }

        return redirect()
            ->route('request.show', [$companyCode, $req->id])
            ->with('success', 'Request berhasil diperbarui.');
    }

    public function cabangAnalytics($companyCode)
    {
        $companyId = session('role.company.id');

        $branches = CabangResto::where('company_id', $companyId)->get();

        $branchNames = $branches->pluck('name', 'id');

        $warehouses = Warehouse::whereIn('cabang_resto_id', $branches->pluck('id'))->get();

        $warehouseToBranch = $warehouses->pluck('cabang_resto_id', 'id');

        $trans = InventoryTrans::whereIn('warehouse_id_from', $warehouses->pluck('id'))
            ->whereIn('warehouse_id_to', $warehouses->pluck('id'))
            ->get([
                'warehouse_id_from',
                'warehouse_id_to',
            ]);

        $heatmap = [];

        foreach ($branchNames as $fromBranchId => $fromBranchName) {
            foreach ($branchNames as $toBranchId => $toBranchName) {
                $heatmap[$fromBranchName][$toBranchName] = 0;
            }
        }

        foreach ($trans as $t) {
            $fromBranch = $warehouseToBranch[$t->warehouse_id_from] ?? null;
            $toBranch = $warehouseToBranch[$t->warehouse_id_to] ?? null;

            if (! $fromBranch || ! $toBranch) {
                continue;
            }

            $fromName = $branchNames[$fromBranch];
            $toName = $branchNames[$toBranch];

            $heatmap[$fromName][$toName]++;
        }

        $outbound = [];
        $inbound = [];

        foreach ($heatmap as $from => $cols) {
            $outbound[$from] = array_sum($cols);
        }

        foreach ($branchNames as $bname) {
            $inbound[$bname] = 0;
        }
        foreach ($heatmap as $from => $cols) {
            foreach ($cols as $to => $qty) {
                $inbound[$to] += $qty;
            }
        }

        $itemRanking = InvenTransDetail::selectRaw('items_id, SUM(qty) as total_qty')
            ->whereIn('inven_trans_id', $trans->pluck('id'))
            ->groupBy('items_id')
            ->pluck('total_qty', 'items_id');

        $totalRequest = $trans->count();

        $avgPerMonth = InventoryTrans::whereIn('warehouse_id_from', $warehouses->pluck('id'))
            ->whereIn('warehouse_id_to', $warehouses->pluck('id'))
            ->selectRaw("DATE_FORMAT(trans_date, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total')
            ->avg() ?? 0;

        return view('company.request-cabang.analytics', compact(
            'companyCode',
            'branches',
            'heatmap',
            'outbound',
            'inbound',
            'itemRanking',
            'totalRequest',
            'avgPerMonth'
        ));
    }

    public function destroy($companyCode, $id)
    {
        $req = InventoryTrans::findOrFail($id);

        if ($req->status !== 'REQUESTED') {
            return redirect()
                ->route('request.show', [$companyCode, $id])
                ->with('error', 'Tidak dapat menghapus request ini.');
        }

        $req->details()->delete();
        $req->delete();

        return redirect()
            ->route('request.index', $companyCode)
            ->with('success', 'Request berhasil dihapus.');
    }
}
