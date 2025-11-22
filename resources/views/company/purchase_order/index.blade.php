<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- ======================= --}}
    {{-- HEADER --}}
    {{-- ======================= --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Order</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua transaksi pembelian.</p>
        </div>

        <a href="{{ route('po.create', $companyCode) }}"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            + Buat PO
        </a>
    </div>


    {{-- ======================= --}}
    {{-- 🔍 FILTER SECTION --}}
    {{-- ======================= --}}
    <form method="GET" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-8">

        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Filter Pencarian</h2>
            <a href="{{ route('po.index', $companyCode) }}"
               class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                Reset Filter
            </a>
        </div>

        <div class="grid grid-cols-4 gap-5">

            {{-- PO NUMBER --}}
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 mb-1">PO Number</label>
                <input type="text" name="po_number" value="{{ request('po_number') }}"
                    placeholder="Cari berdasarkan PO..."
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- SUPPLIER --}}
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 mb-1">Supplier</label>
                <select name="supplier_id"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}"
                            {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS --}}
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    @foreach(['DRAFT','APPROVED','PARTIAL','RECEIVED','CANCELLED'] as $st)
                        <option value="{{ $st }}"
                            {{ request('status') == $st ? 'selected' : '' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TANGGAL DARI --}}
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 mb-1">Tanggal Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- TANGGAL SAMPAI --}}
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 mb-1">Tanggal Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

        </div>

        {{-- APPLY BUTTON --}}
        <div class="mt-6">
            <button type="submit"
                class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>


    {{-- ======================= --}}
    {{-- 📋 TABEL PO --}}
    {{-- ======================= --}}
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left font-medium">PO Number</th>
                    <th class="p-3 text-left font-medium">Supplier</th>
                    <th class="p-3 text-left font-medium">Tanggal</th>
                    <th class="p-3 text-left font-medium">Status</th>
                    <th class="p-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($po as $row)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 font-medium text-gray-900">
                        {{ $row->po_number }}
                    </td>

                    <td class="p-3 text-gray-700">
                        {{ $row->supplier->name }}
                    </td>

                    <td class="p-3 text-gray-700">
                        {{ $row->po_date }}
                    </td>

                    <td class="p-3">
                        @php
                            $colors = [
                                'DRAFT'     => 'bg-gray-100 text-gray-700',
                                'APPROVED'  => 'bg-blue-100 text-blue-700',
                                'PARTIAL'   => 'bg-yellow-100 text-yellow-700',
                                'RECEIVED'  => 'bg-emerald-100 text-emerald-700',
                                'CANCELLED' => 'bg-red-100 text-red-700',
                            ];
                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$row->status] }}">
                            {{ $row->status }}
                        </span>
                    </td>

                    <td class="p-3 text-right">
                        <a href="{{ route('po.show', [$companyCode, $row->id]) }}"
                           class="text-emerald-600 hover:underline font-medium">
                            Detail
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">Belum ada PO.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $po->links() }}
    </div>

</div>


{{-- ======================= --}}
{{-- 🛡️ SECURITY + DATE VALIDATION SCRIPT --}}
{{-- ======================= --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const today = new Date().toISOString().split("T")[0];

    const dateFrom  = document.querySelector("input[name='date_from']");
    const dateTo    = document.querySelector("input[name='date_to']");
    const submitBtn = document.querySelector("form[method='GET'] button[type='submit']");

    // Set max tanggal hari ini
    if (dateFrom) dateFrom.max = today;
    if (dateTo)   dateTo.max   = today;

    // Error message
    const errorMsg = document.createElement("p");
    errorMsg.className = "text-xs text-red-600 mt-1 hidden";
    errorMsg.innerText = "Tanggal tidak boleh melebihi hari ini dan harus dalam rentang yang valid.";
    dateTo.parentNode.appendChild(errorMsg);

    function validateDates() {
        let invalid = false;

        // Cegah tanggal melebihi hari ini
        if (dateFrom.value > today) {
            dateFrom.value = today;
            invalid = true;
        }
        if (dateTo.value > today) {
            dateTo.value = today;
            invalid = true;
        }

        // Cegah range terbalik
        if (dateFrom.value && dateTo.value && dateFrom.value > dateTo.value) {
            invalid = true;
        }

        // Render status UI
        if (invalid) {
            errorMsg.classList.remove("hidden");
            submitBtn.disabled = true;
            submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        } else {
            errorMsg.classList.add("hidden");
            submitBtn.disabled = false;
            submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        }
    }

    dateFrom?.addEventListener("change", validateDates);
    dateTo?.addEventListener("change", validateDates);

    validateDates();
});
</script>

</x-app-layout>
