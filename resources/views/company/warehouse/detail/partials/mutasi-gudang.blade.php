{{-- =============================== --}}
{{-- MUTASI GUDANG — FINAL CLEAN UI --}}
{{-- =============================== --}}

<div class="bg-white border rounded-2xl shadow-sm p-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Mutasi Stok Gudang</h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar seluruh aktivitas pergerakan dan penyesuaian stok di 
                <strong class="text-gray-700">{{ $warehouse->name }}</strong>.
            </p>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- 🔵 FILTERS --}}
    {{-- ========================= --}}
    <form method="GET" 
        x-data="mutasiFilter()" 
        class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">

        {{-- FROM --}}
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input type="date" name="from"
            id="date_from"
            value="{{ request('from', now()->format('Y-m-d')) }}"
            max="{{ now()->format('Y-m-d') }}"
            class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-400">
        </div>

        {{-- TO --}}
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input type="date" name="to"
            id="date_to"
            min="{{ request('from', now()->format('Y-m-d')) }}"
            max="{{ now()->format('Y-m-d') }}"
            value="{{ request('to', now()->format('Y-m-d')) }}"
            class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-400">

        </div>

        {{-- ISSUE --}}
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700">Kategori</label>
            <select name="issue"
                    class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-400">
                <option value="">Semua</option>

                <optgroup label="Movement">
                    <option value="Stok Masuk" @selected(request('issue')=='Stok Masuk')>Stok Masuk</option>
                    <option value="Stok Keluar" @selected(request('issue')=='Stok Keluar')>Stok Keluar</option>
                    <option value="Transfer Masuk" @selected(request('issue')=='Transfer Masuk')>Transfer Masuk</option>
                    <option value="Transfer Keluar" @selected(request('issue')=='Transfer Keluar')>Transfer Keluar</option>
                </optgroup>

                <optgroup label="Penyesuaian">
                    @foreach ($categoriesIssues as $ci)
                        <option value="{{ $ci->name }}"
                            @selected(request('issue')==$ci->name)>
                            {{ $ci->name }}
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div></div>

        {{-- BUTTONS --}}
        <div class="col-span-1 md:col-span-5 flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                Terapkan Filter
            </button>

            <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}?tab=mutasi"
            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>

    {{-- ========================= --}}
    {{-- 📘 TABLE --}}
    {{-- ========================= --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                <tr>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Item</th>
                    <th class="p-3 text-left">Kategori</th>
                    <th class="p-3 text-center">Qty Lama</th>
                    <th class="p-3 text-center">Qty Baru</th>
                    <th class="p-3 text-center">Selisih</th>
                    <th class="p-3 text-left">Catatan</th>
                    <th class="p-3 text-left">User</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($warehouseMutations as $h)

                    {{-- Dynamic badge color --}}
                    @php
                        $badgeColor = [
                            'Stok Masuk'      => 'emerald',
                            'Stok Keluar'     => 'red',
                            'Transfer Masuk'  => 'blue',
                            'Transfer Keluar' => 'yellow',
                        ][$h->issue_name] ?? 'blue';
                    @endphp

                    <tr class="hover:bg-gray-50 transition">

                        {{-- DATE --}}
                        <td class="p-3 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($h->date)->format('H:i') }}
                            </div>
                        </td>

                        {{-- ITEM --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $h->item_name }}
                        </td>

                        {{-- KATEGORI --}}
                        <td class="p-3">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold border 
                                bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border-{{ $badgeColor }}-200">
                                {{ $h->issue_name }}
                            </span>
                        </td>

                        {{-- QTY LAMA --}}
                        <td class="p-3 text-center text-gray-800">
                            {{ $h->prev_qty !== null ? number_format($h->prev_qty, 2) : '-' }}
                        </td>

                        {{-- QTY BARU --}}
                        <td class="p-3 text-center text-gray-800">
                            {{ $h->after_qty !== null ? number_format($h->after_qty, 2) : '-' }}
                        </td>

                        {{-- SELISIH --}}
                        <td class="p-3 text-center font-bold
                            @if($h->diff > 0) text-emerald-700
                            @elseif($h->diff < 0) text-red-600
                            @else text-gray-500 @endif">
                            {{ number_format($h->diff, 2) }}
                        </td>

                        {{-- CATATAN --}}
                        <td class="p-3 text-gray-700 max-w-xs line-clamp-2">
                            {{ $h->note ?: '-' }}
                        </td>

                        {{-- USER --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $h->created_by_name ?? 'System' }}
                        </td>

                    </tr>

                @empty

                    {{-- EMPTY STATE --}}
                    <tr>
                        <td colspan="8" class="px-8 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                     class="h-12 w-12 text-gray-300" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 17v-6h6v6m-7 4h8a2 2 0 002-2v-6a2 2 0 00-2-2H8a2 2 0 00-2 2v6a2 2 0 002 2zm3-20h2a2 2 0 012 2v2H10V3a2 2 0 012-2z" />
                                </svg>
                                <p class="text-gray-600">Tidak ada mutasi untuk gudang ini.</p>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>
<script>
    const fromInput = document.getElementById('date_from');
    const toInput = document.getElementById('date_to');
    const today = new Date().toISOString().split("T")[0];

    toInput.max = today;

    fromInput.addEventListener("change", () => {
        const from = fromInput.value;
        toInput.min = from;

        if (toInput.value < from) {
            toInput.value = from;
        }

        if (from > today) {
            fromInput.value = today;
        }
    });

    toInput.addEventListener("change", () => {
        const from = fromInput.value;
        const to = toInput.value;

        if (to < from) {
            toInput.value = from;
        }

        if (to > today) {
            toInput.value = today;
        }
    });
</script>
</div>
