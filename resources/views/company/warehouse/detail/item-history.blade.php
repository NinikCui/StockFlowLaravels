<x-app-layout>
<main class="max-w-4xl mx-auto px-6 py-10">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Mutasi Item</h1>
        <p class="text-gray-500 text-sm">
            Gudang: <strong>{{ $warehouse->name }}</strong><br>
            Item: <strong>{{ $item->name }}</strong>
        </p>
    </div>

    <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}"
       class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 mb-6 inline-block">
        ← Kembali
    </a>
    <div class="bg-white border rounded-xl shadow-sm p-6 mb-6">

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Dari Tanggal --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="from"
                    value="{{ request('from') }}"
                    class="px-3 py-2 w-full border rounded-lg focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
            </div>

            {{-- Sampai Tanggal --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="to"
                    value="{{ request('to') }}"
                    class="px-3 py-2 w-full border rounded-lg focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
            </div>

            {{-- Kategori --}}
           <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">Kategori</label>

                <select name="issue"
                        class="px-3 py-2 w-full border rounded-lg focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                    <option value="">Semua</option>
                    <option value="Stok Masuk"   @selected(request('issue') === 'Stok Masuk')>Stok Masuk</option>
                    <option value="Stok Keluar"  @selected(request('issue') === 'Stok Keluar')>Stok Keluar</option>
                    <option value="Penyesuaian"  @selected(request('issue') === 'Penyesuaian')>Penyesuaian Stok</option>
                </select>
            </div>

            {{-- User --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">User</label>
                <select name="user"
                        class="px-3 py-2 w-full border rounded-lg focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                    <option value="">Semua</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user') == $u->id)>
                            {{ $u->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol --}}
            <div class="col-span-1 md:col-span-4 flex flex-wrap gap-3 pt-2">
                <button
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-sm transition">
                    Terapkan Filter
                </button>

                <a href="{{ route('stock.item.history', [$companyCode, $warehouse->id, $item->id]) }}"
                class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 shadow-sm transition">
                    Reset
                </a>
            </div>

        </form>

    </div>


    {{-- RESULT TABLE --}}
<div class="bg-white border rounded-2xl shadow-sm p-6">

    {{-- TITLE --}}
    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        Riwayat Mutasi Item
    </h2>

    {{-- TABLE WRAPPER --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                <tr>
                    <th class="p-3 text-left font-semibold">Tanggal</th>
                    <th class="p-3 text-left font-semibold">Kategori</th>
                    <th class="p-3 text-center font-semibold">Qty Lama</th>
                    <th class="p-3 text-center font-semibold">Qty Baru</th>
                    <th class="p-3 text-center font-semibold">Selisih</th>
                    <th class="p-3 text-left font-semibold">Catatan</th>
                    <th class="p-3 text-left font-semibold">Oleh</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($history as $h)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- TANGGAL --}}
                        <td class="p-3 text-gray-800 whitespace-nowrap">
                            <div class="font-medium">
                                {{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($h->date)->format('H:i') }}
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td class="p-3">
                            @php
                                $color = match($h->issue_name) {
                                    'Stok Masuk' => 'emerald',
                                    'Stok Keluar' => 'red',
                                    default => 'blue'
                                };
                            @endphp

                            <span class="inline-flex items-center px-2 py-1 rounded-md border text-xs font-medium
                                bg-{{ $color }}-50 text-{{ $color }}-700 border-{{ $color }}-200">
                                {{ $h->issue_name }}
                            </span>
                        </td>

                        {{-- QTY LAMA --}}
                        <td class="p-3 text-center">
                            {{ $h->prev_qty !== null ? number_format($h->prev_qty, 2) : '-' }}
                        </td>

                        {{-- QTY BARU --}}
                        <td class="p-3 text-center">
                            {{ $h->after_qty !== null ? number_format($h->after_qty, 2) : '-' }}
                        </td>

                        {{-- SELISIH --}}
                        <td class="p-3 text-center font-semibold
                            @if($h->diff > 0) text-emerald-700
                            @elseif($h->diff < 0) text-red-700
                            @else text-gray-600 @endif">
                            {{ number_format($h->diff, 2) }}
                        </td>

                        {{-- CATATAN --}}
                        <td class="p-3 text-gray-700 max-w-xs">
                            <div class="line-clamp-2">
                                {{ $h->note ?: '-' }}
                            </div>
                        </td>

                        {{-- USER --}}
                        <td class="p-3 text-gray-900">
                            {{ $h->created_by_name ?? 'System' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-gray-500 text-sm">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 17v-6h6v6m-7 4h8a2 2 0 002-2v-6a2 2 0 00-2-2H8a2 2 0 00-2 2v6a2 2 0 002 2zm3-20h2a2 2 0 012 2v2H10V3a2 2 0 012-2z" />
                                </svg>
                                <p>Tidak ada history mutasi untuk item ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


</main>
</x-app-layout>
