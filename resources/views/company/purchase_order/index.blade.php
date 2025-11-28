<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- ======================= --}}
    {{-- HEADER --}}
    {{-- ======================= --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Order</h1>
            <p class="text-gray-600 mt-2">Kelola semua transaksi pembelian Anda</p>
        </div>

        <x-crud-add 
                        resource="po"
                        :companyCode="$companyCode"
                        permissionPrefix="purchase"
                    />
    </div>


    {{-- ======================= --}}
    {{-- 🔍 FILTER SECTION --}}
    {{-- ======================= --}}
    <form method="GET" class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 mb-8">

        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">Filter Pencarian</h2>
            </div>
            <a href="{{ route('po.index', $companyCode) }}"
               class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset Filter
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- PO NUMBER --}}
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    PO Number
                </label>
                <input type="text" name="po_number" value="{{ request('po_number') }}"
                    placeholder="Cari berdasarkan nomor PO..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>

            {{-- SUPPLIER --}}
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Supplier
                </label>
                <select name="supplier_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
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
                <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Status
                </label>
                <select name="status"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
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
                <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Tanggal Dari
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>

            {{-- TANGGAL SAMPAI --}}
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Tanggal Sampai
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>

        </div>

        {{-- APPLY BUTTON --}}
        <div class="mt-8 flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:bg-emerald-700 hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Terapkan Filter
            </button>
        </div>

    </form>


    {{-- ======================= --}}
    {{-- 📋 TABEL PO --}}
    {{-- ======================= --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b-2 border-emerald-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            PO Number
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Supplier
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($po as $row)
                    <tr class="hover:bg-emerald-50 transition-colors duration-150">

                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900 text-base">{{ $row->po_number }}</span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-gray-700 font-medium">{{ $row->supplier->name }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $row->po_date }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $statusConfig = [
                                    'DRAFT'     => ['bg' => 'bg-gray-100',     'text' => 'text-gray-700',     'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                    'APPROVED'  => ['bg' => 'bg-blue-100',     'text' => 'text-blue-700',     'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'PARTIAL'   => ['bg' => 'bg-yellow-100',   'text' => 'text-yellow-700',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'RECEIVED'  => ['bg' => 'bg-emerald-100',  'text' => 'text-emerald-700',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'CANCELLED' => ['bg' => 'bg-red-100',      'text' => 'text-red-700',      'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ];
                                $config = $statusConfig[$row->status];
                            @endphp

                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                </svg>
                                {{ $row->status }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('po.show', [$companyCode, $row->id]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Belum ada Purchase Order</p>
                                <p class="text-gray-400 text-sm">Mulai dengan membuat PO baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
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

    // Error message dengan styling yang lebih baik
    const errorMsg = document.createElement("div");
    errorMsg.className = "mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 hidden flex items-center gap-2";
    errorMsg.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>Tanggal tidak boleh melebihi hari ini dan harus dalam rentang yang valid.</span>
    `;
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
            errorMsg.classList.add("flex");
            submitBtn.disabled = true;
            submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        } else {
            errorMsg.classList.add("hidden");
            errorMsg.classList.remove("flex");
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