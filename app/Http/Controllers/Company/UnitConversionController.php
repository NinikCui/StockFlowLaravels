<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use App\Models\UnitConversion;
use Illuminate\Http\Request;

class UnitConversionController extends Controller
{
    /**
     * Form tambah konversi satuan
     */
    public function create(string $companyCode)
    {
        return view('company.items.unit-conversion.create', [
            'companyCode' => $companyCode,
            'satuan' => Satuan::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan konversi baru
     */
    public function store(Request $request, string $companyCode)
    {
        $request->validate([
            'from_satuan_id' => [
                'required',
                'exists:satuan,id',
                'different:to_satuan_id',
            ],
            'to_satuan_id' => [
                'required',
                'exists:satuan,id',
            ],
            'factor' => [
                'required',
                'numeric',
                'min:0.000001',
            ],
        ]);

        $existing = UnitConversion::where('from_satuan_id', $request->from_satuan_id)
            ->where('to_satuan_id', $request->to_satuan_id)
            ->first();

        // 1️⃣ Sudah ada & masih aktif → TOLAK
        if ($existing && $existing->is_active) {
            return back()
                ->withErrors([
                    'from_satuan_id' => 'Konversi satuan ini sudah tersedia',
                ])
                ->withInput();
        }

        // 2️⃣ Sudah ada tapi nonaktif → AKTIFKAN ULANG
        if ($existing && ! $existing->is_active) {
            $existing->update([
                'factor' => $request->factor,
                'is_active' => true,
            ]);

            return redirect()
                ->route('items.index', $companyCode)
                ->with('activeTab', 'satuan')
                ->with('success', 'Konversi satuan berhasil diaktifkan kembali');
        }

        // 3️⃣ Belum ada → CREATE BARU
        UnitConversion::create([
            'from_satuan_id' => $request->from_satuan_id,
            'to_satuan_id' => $request->to_satuan_id,
            'factor' => $request->factor,
            'is_active' => true,
        ]);

        return redirect()
            ->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Konversi satuan berhasil ditambahkan');
    }

    /**
     * Form edit konversi
     */
    public function edit(string $companyCode, int $id)
    {
        $conversion = UnitConversion::with(['fromSatuan', 'toSatuan'])
            ->findOrFail($id);

        return view('company.items.unit-conversion.edit', [
            'companyCode' => $companyCode,
            'conversion' => $conversion,
        ]);
    }

    /**
     * Update nilai konversi
     */
    public function update(Request $request, string $companyCode, int $id)
    {
        $conversion = UnitConversion::findOrFail($id);

        $request->validate([
            'factor' => ['required', 'numeric', 'min:0.000001'],
        ]);

        $conversion->update([
            'factor' => $request->factor,
        ]);

        return redirect()
            ->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Konversi satuan berhasil diperbarui');
    }

    /**
     * Nonaktifkan konversi
     * (lebih aman daripada delete fisik)
     */
    public function destroy(string $companyCode, int $id)
    {
        $conversion = UnitConversion::findOrFail($id);

        $conversion->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Konversi satuan berhasil dinonaktifkan');
    }
}
