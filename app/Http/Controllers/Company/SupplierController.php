<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // LIST
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $suppliers = Supplier::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.supplier.index', compact('suppliers', 'companyCode'));
    }

    // FORM TAMBAH
    public function create($companyCode)
    {
        return view('company.supplier.create', compact('companyCode'));
    }

    // SIMPAN
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name'   => 'required|max:100',
            'email'  => 'nullable|email|max:100',
            'phone'  => 'nullable|max:50',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Supplier::create([
            'company_id'   => $company->id,
            'name'         => $request->name,
            'contact_name' => $request->contact_name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'city'         => $request->city,
            'notes'        => $request->notes,
            'is_active'    => true,
        ]);

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('company.supplier.edit', compact('supplier', 'companyCode'));
    }

    // UPDATE
    public function update(Request $request, $companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name'   => 'required|max:100',
            'email'  => 'nullable|email|max:100',
            'phone'  => 'nullable|max:50',
        ]);

        $supplier->update([
            'name'         => $request->name,
            'contact_name' => $request->contact_name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'city'         => $request->city,
            'notes'        => $request->notes,
            'is_active'    => $request->is_active ?? false,
        ]);
        return redirect()->route('supplier.show',[ $companyCode,$supplier->id])
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    // DELETE
    public function destroy($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil dihapus.');
    }

    //DETAIL
    public function show($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $supplier = Supplier::where('company_id', $company->id)->findOrFail($id);

        // 🔵 ITEM SUPPLIER
        $items = $supplier->suppliedItems()->with('kategori','satuan')->get();
        $allItems = Item::orderBy('name')->with('kategori','satuan')->get();

        // 🔵 PERFORMANCE
        $purchaseOrders = PurchaseOrder::where('suppliers_id', $supplier->id)->get();
        $totalOrders = $purchaseOrders->count();
        $onTime = $purchaseOrders->where('delivered_date','<=','expected_date')->count();
        $onTimeRate = $totalOrders > 0 ? round(($onTime / $totalOrders) * 100, 2) : 0;
        $late = $totalOrders - $onTime;

        $avgLead = $purchaseOrders
            ->whereNotNull('delivered_date')
            ->avg(fn($po) => Carbon::parse($po->delivered_date)->diffInDays($po->order_date));

        return view('company.supplier.detail', compact(
            'supplier',
            'companyCode',
            'items',
            'allItems',
            'totalOrders',
            'onTimeRate',
            'late',
            'avgLead'
        ));
    }


    // CREATE ITEM SUPPLIER
    public function itemStore(Request $request, $companyCode, Supplier $supplier)
    {
        $validator = \Validator::make($request->all(), [
            'items_id'      => 'required|exists:items,id',
            'price'         => 'required|numeric|min:0',
            'min_order_qty' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('modal', 'add')
                ->withErrors($validator)
                ->withInput();
        }

        // 🔥 CEK DUPLIKAT
        if ($supplier->suppliedItems()->where('items_id', $request->items_id)->exists()) {

            // Tambahkan error manual
            return back()
                ->with('modal', 'add')
                ->withErrors(['items_id' => 'Item ini sudah pernah ditambahkan untuk supplier ini.'])
                ->withInput();
        }

        // SIMPAN
        $supplier->suppliedItems()->attach($request->items_id, [
            'price'             => $request->price,
            'min_order_qty'     => $request->min_order_qty,
            'last_price_update' => now(),
        ]);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }
    // UPDATE ITEM SUPPLIER
    public function itemUpdate(Request $request, $companyCode, Supplier $supplier, $itemId)
    {
        $request->validate([
            'price'         => 'required|numeric',
            'min_order_qty' => 'nullable|numeric',
        ]);

        $supplier->suppliedItems()->updateExistingPivot($itemId, [
            'price'             => $request->price,
            'min_order_qty'     => $request->min_order_qty,
            'last_price_update' => Carbon::now(),
        ]);

        return back()->with('success','Item supplier berhasil diperbarui.');
    }

    // DELETE ITEM SUPPLIER
    public function itemDestroy($companyCode, Supplier $supplier, $itemId)
    {
        $supplier->suppliedItems()->detach($itemId);

        return back()->with('success','Item supplier berhasil dihapus.');
    }

}