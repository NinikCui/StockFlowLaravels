<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\PosShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosShiftController extends Controller
{
    protected function loadBranch($branchCode)
    {
        $companyCode = session('role.company.code');
        $company = Company::where('code', $companyCode)->firstOrFail();

        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();
        $activeShift = PosShift::where('cabang_resto_id', $branch->id)
            ->where('status', 'OPEN')
            ->first();

        return [$company, $branch, $activeShift];
    }

    public function index($branchCode)
    {
        $companyCode = session('role.company.code');

        [$company, $branch] = $this->loadBranch($branchCode);

        $activeShift = PosShift::where('cabang_resto_id', $branch->id)
            ->where('status', 'OPEN')
            ->first();

        $shifts = PosShift::where('cabang_resto_id', $branch->id)
            ->orderBy('opened_at', 'desc')
            ->get();

        return view('branch.pos.shift.index', compact(
            'companyCode', 'branchCode', 'branch', 'activeShift', 'shifts'
        ));
    }

    // ============================
    // OPEN SHIFT — FORM
    // ============================
    public function openForm($branchCode)
    {
        $companyCode = session('role.company.code');

        [$company, $branch] = $this->loadBranch($branchCode);

        // Cek apakah kasir masih punya shift aktif
        if (PosShift::where('cabang_resto_id', $branch->id)
            ->where('opened_by', Auth::id())
            ->where('status', 'OPEN')
            ->exists()) {
            return redirect()->route('branch.pos.shift.index', [$companyCode, $branchCode])
                ->with('error', 'Masih ada shift yang belum ditutup.');
        }

        return view('branch.pos.shift.open', compact(
            'companyCode', 'branchCode', 'branch'
        ));
    }

    // ============================
    // OPEN SHIFT — PROCESS
    // ============================
    public function open(Request $request, $branchCode)
    {
        $companyCode = session('role.company.code');

        [$company, $branch] = $this->loadBranch($branchCode);

        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($branch, $validated) {
            PosShift::create([
                'cabang_resto_id' => $branch->id,
                'opened_by' => Auth::id(),
                'opened_at' => now(),
                'opening_cash' => $validated['opening_cash'],
                'status' => 'OPEN',
            ]);
        });

        return redirect()->route('branch.pos.shift.index', [$branchCode])
            ->with('success', 'Shift berhasil dibuka.');
    }

    // ============================
    // CLOSE SHIFT — FORM
    // ============================
    public function closeForm($branchCode, PosShift $shift)
    {
        $companyCode = session('role.company.code');
        [$company, $branch] = $this->loadBranch($branchCode);

        if ($shift->cabang_resto_id !== $branch->id) {
            abort(403, 'Shift bukan milik cabang ini.');
        }

        if ($shift->status !== 'OPEN') {
            return back()->with('error', 'Shift sudah ditutup.');
        }

        // Summary omset POS — nanti diganti function kamu
        $totalSales = 0; // placeholder
        $expectedCash = $shift->opening_cash + $totalSales;

        return view('branch.pos.shift.close', compact(
            'companyCode', 'branchCode', 'branch', 'shift', 'expectedCash'
        ));
    }

    // ============================
    // CLOSE SHIFT — PROCESS
    // ============================
    public function close(Request $request, $branchCode, PosShift $shift)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        if ($shift->cabang_resto_id !== $branch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:200',
        ]);

        DB::transaction(function () use ($shift, $validated) {
            $shift->update([
                'closed_by' => Auth::id(),
                'closed_at' => now(),
                'closing_cash' => $validated['closing_cash'],
                'status' => 'CLOSED',
                'note' => $validated['note'] ?? null,
            ]);
        });

        return redirect()->route('branch.pos.shift.index', [$branchCode])
            ->with('success', 'Shift berhasil ditutup.');
    }

    public function history($branchCode, $shiftId)
    {
        $companyCode = session('role.company.code');

        [$company, $branch] = $this->loadBranch($branchCode);

        $shift = PosShift::with([
            'orders.details.product',
            'orders.payments',
        ])
            ->where('id', $shiftId)
            ->where('cabang_resto_id', $branch->id)
            ->firstOrFail();

        return view('branch.pos.shift.history', compact(
            'companyCode',
            'branchCode',
            'branch',
            'shift'
        ));
    }
}
