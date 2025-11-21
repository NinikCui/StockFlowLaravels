<x-app-layout>
<main class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Mutasi Item</h1>
        <p class="text-gray-600 text-sm mt-1">
            Gudang: <strong>{{ $warehouse->name }}</strong> <br>
            Kode Stok: <strong>{{ $stock->code }}</strong> <br>
            Item: <strong>{{ $item->name }}</strong>
        </p>
    </div>

    <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}"
       class="inline-flex items-center px-4 py-2 mb-6 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">
        ← Kembali
    </a>

    {{-- FILTERS --}}
    <div class="bg-white border rounded-xl shadow-sm p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- DARI --}}
            <div>
                <label class="text-sm text-gray-700 font-medium">Dari Tanggal</label>
                <input type="date" name="from" id="date_from"
                       value="{{ request('from', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>

            {{-- SAMPAI --}}
            <div>
                <label class="text-sm text-gray-700 font-medium">Sampai Tanggal</label>
                <input type="date" name="to" id="date_to"
                       value="{{ request('to', now()->format('Y-m-d')) }}"
                       min="{{ request('from', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="text-sm text-gray-700 font-medium">Kategori</label>
                <select name="issue" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua</option>

                    <optgroup label="Penyesuaian">
                        @foreach($categoriesIssues as $ci)
                            <option value="{{ $ci->name }}" @selected(request('issue') == $ci->name)>
                                {{ $ci->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>

            </div>

            {{-- USER --}}
            <div>
                <label class="text-sm text-gray-700 font-medium">User</label>
                <select name="user" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user') == $u->id)>
                            {{ $u->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTON --}}
            <div class="col-span-1 md:col-span-4 flex gap-3 pt-3">
                <button class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Terapkan Filter
                </button>

                <a href="{{ route('stock.item.history', [$companyCode, $warehouse->id, $stock->id]) }}"
                   class="px-5 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white border rounded-xl shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Kategori</th>
                        <th class="p-3 text-center">Qty Lama</th>
                        <th class="p-3 text-center">Qty Baru</th>
                        <th class="p-3 text-center">Selisih</th>
                        <th class="p-3">Catatan</th>
                        <th class="p-3">User</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $h)
                        @php
                            $badgeColor = [
                                'Stok Masuk' => 'emerald',
                                'Stok Keluar' => 'red',
                            ][$h->issue_name] ?? 'blue';
                        @endphp

                        <tr class="hover:bg-gray-50">

                            {{-- Tanggal --}}
                            <td class="p-3">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($h->date)->format('H:i') }}</div>
                            </td>

                            {{-- Kategori --}}
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-md bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border border-{{ $badgeColor }}-200">
                                    {{ $h->issue_name }}
                                </span>
                            </td>

                            {{-- Qty Lama --}}
                            <td class="p-3 text-center">
                                {{ $h->prev_qty !== null ? number_format($h->prev_qty,2) : '-' }}
                            </td>

                            {{-- Qty Baru --}}
                            <td class="p-3 text-center">
                                {{ $h->after_qty !== null ? number_format($h->after_qty,2) : '-' }}
                            </td>

                            {{-- Selisih --}}
                            <td class="p-3 text-center font-semibold
                                @if($h->diff > 0) text-emerald-700
                                @elseif($h->diff < 0) text-red-600
                                @else text-gray-500 @endif">
                                {{ number_format($h->diff,2) }}
                            </td>

                            {{-- Catatan --}}
                            <td class="p-3 text-gray-700 max-w-xs line-clamp-2">
                                {{ $h->note ?: '-' }}
                            </td>

                            {{-- User --}}
                            <td class="p-3 font-medium text-gray-900">
                                {{ $h->created_by_name ?? 'System' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-gray-500">
                                Tidak ada mutasi untuk item ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</main>

<script>
    const fromInput = document.getElementById("date_from");
    const toInput = document.getElementById("date_to");
    const today = new Date().toISOString().split("T")[0];

    toInput.max = today;
    fromInput.max = today;

    fromInput.addEventListener("change", () => {
        if (fromInput.value > today) fromInput.value = today;
        toInput.min = fromInput.value;
        if (toInput.value < fromInput.value) toInput.value = fromInput.value;
    });

    toInput.addEventListener("change", () => {
        if (toInput.value > today) toInput.value = today;
        if (toInput.value < fromInput.value) toInput.value = fromInput.value;
    });
</script>

</x-app-layout>
