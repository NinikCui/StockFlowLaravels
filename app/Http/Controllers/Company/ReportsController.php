<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    private function validateCompany($companyCode)
    {
        return Company::where('code', $companyCode)->firstOrFail();
    }

    // =========================================================
    // 1. LAPORAN STOK GLOBAL
    // =========================================================
    public function stockGlobal($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        $stocks = Stock::with([
            'item.kategori',
            'warehouse.cabangResto',
        ])
            ->where('company_id', $company->id)
            ->get()
            ->groupBy('item_id');

        return view('company.reports.stock-global', compact('companyCode', 'company', 'stocks'));
    }

    // =========================================================
    // 2. LAPORAN MUTASI STOK
    // =========================================================
    public function mutasiStok($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        $mutasi = StockMovement::with([
            'item',
            'warehouse.cabangResto',
        ])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->get();

        return view('company.reports.mutasi-stok', compact('companyCode', 'company', 'mutasi'));
    }

    // =========================================================
    // 3. LAPORAN PENGGUNAAN BAHAN (BOM)
    // =========================================================
    public function bom($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        // USER MENGGUNAKAN order_detail (BUKAN pos_order_items)
        // Field produk = products_id
        $bom = DB::table('order_detail')
            ->join('boms', 'order_detail.products_id', '=', 'boms.product_id')
            ->join('items', 'boms.item_id', '=', 'items.id')
            ->selectRaw('
                items.name AS bahan,
                SUM(boms.qty_per_unit * order_detail.qty) AS total_penggunaan
            ')
            ->where('boms.company_id', $company->id)
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_penggunaan')
            ->get();

        return view('company.reports.bom', compact('companyCode', 'company', 'bom'));
    }

    public function purchaseOrder($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        $pos = PurchaseOrder::with([
            'supplier',
            'cabangResto',
            'warehouse',
            'details',
        ])
            ->whereHas('cabangResto', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($po) {
                // Hitung total PO
                $po->total_amount = $po->details->sum(function ($d) {
                    $subtotal = $d->qty_ordered * $d->unit_price;
                    $discount = $subtotal * ($d->discount_pct / 100);

                    return $subtotal - $discount;
                });

                return $po;
            });

        return view('company.reports.purchase-order', compact('companyCode', 'company', 'pos'));
    }

    // =========================================================
    // 5. LAPORAN EXPIRED
    // =========================================================
    public function expired($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        $expiredSoon = Stock::with(['item', 'warehouse.cabangResto'])
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', Carbon::now()->addDays(14))
            ->where('company_id', $company->id)
            ->orderBy('expired_at')
            ->get();

        return view('company.reports.expired', compact('companyCode', 'company', 'expiredSoon'));
    }

    public function performance($companyCode)
    {
        $company = $this->validateCompany($companyCode);

        // Ambil semua cabang milik perusahaan
        $cabang = CabangResto::where('company_id', $company->id)->get();

        // Hitung total request stok KELUAR (cabang pengirim)
        $requestKeluar = DB::table('inven_trans')
            ->selectRaw('cabang_id_from, COUNT(*) AS total_keluar')
            ->where('cabang_id_from', '!=', null)
            ->groupBy('cabang_id_from')
            ->get()
            ->keyBy('cabang_id_from');

        // Hitung total request stok MASUK (cabang penerima)
        $requestMasuk = DB::table('inven_trans')
            ->selectRaw('cabang_id_to, COUNT(*) AS total_masuk')
            ->where('cabang_id_to', '!=', null)
            ->groupBy('cabang_id_to')
            ->get()
            ->keyBy('cabang_id_to');

        return view('company.reports.performance', compact(
            'companyCode',
            'company',
            'cabang',
            'requestKeluar',
            'requestMasuk'
        ));
    }
}
