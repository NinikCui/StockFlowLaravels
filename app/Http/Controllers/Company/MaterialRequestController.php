<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\InventoryTrans;
use App\Models\InvenTransDetail;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    /**
     * INDEX — Owner melihat semua request antar cabang
     */
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        // Ambil semua gudang dari seluruh cabang perusahaan
        $warehouseIds = Warehouse::whereHas('cabangResto', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->pluck('id');

        $requests = InventoryTrans::with([
            'warehouseFrom.cabangResto',
            'warehouseTo.cabangResto',
            'details.stock.item',
        ])
            ->whereIn('warehouse_id_to', $warehouseIds)
            ->orderByDesc('id')
            ->get();

        return view('company.request-cabang.index', compact('requests', 'companyCode'));
    }

    /**
     * CREATE — Owner memilih cabang asal, cabang tujuan, dan item
     */
    public function create($companyCode)
    {
        $companyId = session('role.company.id');

        $branches = CabangResto::where('company_id', $companyId)
            ->with('warehouses')
            ->orderBy('name')
            ->get();

        $items = Item::with('satuan')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('company.request-cabang.create', [
            'branches' => $branches,
            'items' => $items,
            'companyCode' => $companyCode,
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
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        $warehouseFrom = Warehouse::where('cabang_resto_id', $validated['cabang_from_id'])->firstOrFail();
        $warehouseTo = Warehouse::where('cabang_resto_id', $validated['cabang_to_id'])->firstOrFail();

        $trans = InventoryTrans::create([
            'warehouse_id_from' => $warehouseFrom->id,
            'warehouse_id_to' => $warehouseTo->id,
            'trans_number' => 'MR-'.now()->format('YmdHis'),
            'trans_date' => today(),
            'status' => 'REQUESTED',
            'note' => $request->note,
            'reason' => 'MATERIAL_REQUEST',
            'created_by' => session('user.id'),
        ]);

        foreach ($validated['items'] as $row) {

            // Stock gudang asal
            $stock = Stock::where('warehouse_id', $warehouseFrom->id)
                ->where('item_id', $row['item_id'])
                ->first();

            if (! $stock) {
                continue; // Owner bebas, skip item yg stoknya tidak ditemukan
            }

            InvenTransDetail::create([
                'inven_trans_id' => $trans->id,
                'stocks_id' => $stock->id,
                'qty' => $row['qty'],
            ]);
        }

        return redirect()->route('request.index', $companyCode)
            ->with('success', 'Request berhasil dibuat.');
    }

    /**
     * SHOW
     */
    public function show($companyCode, $id)
    {
        $trans = InventoryTrans::with([
            'warehouseFrom.cabangResto',
            'warehouseTo.cabangResto',
            'details.stock.item.satuan',
        ])->findOrFail($id);

        return view('company.request-cabang.show', compact('trans', 'companyCode'));
    }

    /**
     * APPROVE — Owner approve request
     */
    public function approve($companyCode, $id)
    {
        $trans = InventoryTrans::findOrFail($id);

        if ($trans->status !== 'REQUESTED') {
            return back()->with('error', 'Request hanya bisa di-approve dari REQUESTED.');
        }

        $trans->update([
            'status' => 'APPROVED',
            'posted_at' => now(),
        ]);

        return back()->with('success', 'Request disetujui.');
    }

    /**
     * SEND — Owner melakukan pengiriman barang
     */
    public function send($companyCode, $id)
    {
        $trans = InventoryTrans::with('details.stock.item')->findOrFail($id);

        if ($trans->status !== 'APPROVED') {
            return back()->with('error', 'Hanya bisa kirim dari status APPROVED.');
        }

        foreach ($trans->details as $d) {

            if ($d->stock->qty < $d->qty) {
                return back()->with('error', "Stok tidak cukup untuk item {$d->stock->item->name}");
            }

            StockMovement::create([
                'company_id' => $trans->company_id,
                'warehouse_id' => $trans->warehouse_id_from,
                'stock_id' => $d->stocks_id,
                'item_id' => $d->stock->item_id,
                'type' => 'TRANSFER_OUT',
                'qty' => -$d->qty,
                'reference' => $trans->trans_number,
                'created_by' => session('user.id'),
            ]);

            $d->stock->decrement('qty', $d->qty);
        }

        $trans->update(['status' => 'IN_TRANSIT']);

        return back()->with('success', 'Barang sedang dikirim.');
    }

    /**
     * RECEIVE — Owner melakukan penerimaan di cabang tujuan
     */
    public function receive($companyCode, $id)
    {
        $trans = InventoryTrans::with('details.stock.item')->findOrFail($id);

        if ($trans->status !== 'IN_TRANSIT') {
            return back()->with('error', 'Hanya bisa terima dari status IN_TRANSIT.');
        }

        foreach ($trans->details as $d) {

            $targetStock = Stock::updateOrCreate(
                [
                    'warehouse_id' => $trans->warehouse_id_to,
                    'item_id' => $d->stock->item_id,
                ],
                [
                    'qty' => DB::raw("qty + {$d->qty}"),
                ]
            );

            StockMovement::create([
                'company_id' => $trans->company_id,
                'warehouse_id' => $trans->warehouse_id_to,
                'stock_id' => $targetStock->id,
                'item_id' => $targetStock->item_id,
                'type' => 'TRANSFER_IN',
                'qty' => $d->qty,
                'reference' => $trans->trans_number,
                'created_by' => session('user.id'),
            ]);
        }

        $trans->update(['status' => 'RECEIVED']);

        return back()->with('success', 'Barang berhasil diterima.');
    }
}
