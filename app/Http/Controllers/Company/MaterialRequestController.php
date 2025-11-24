<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InventoryTrans;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        $cabangId = session('role.branch.id');
        $gudangCabang = Warehouse::where('cabang_resto_id', $cabangId)->first();

        $trans = InventoryTrans::create([
            'warehouse_id_from' => $gudangCabang->id,      // atau gudang pusat
            'warehouse_id_to' => $gudangCabang->id,      // cabang itself
            'trans_number' => 'MR-'.now()->format('YmdHis'),
            'trans_date' => today(),
            'status' => 'REQUESTED',
            'created_by' => session('user.id'),
            'note' => $request->note,
        ]);

        foreach ($request->items as $row) {
            $stock = Stock::where('warehouse_id', $gudangCabang->id)
                ->where('item_id', $row['item_id'])
                ->first();

            InvenTransDetail::create([
                'inven_trans_id' => $trans->id,
                'stocks_id' => $stock->id,
                'qty' => $row['qty'],
            ]);
        }

        return redirect()->route('request.index', $companyCode)
            ->with('success', 'Request cabang berhasil diajukan.');
    }
}
