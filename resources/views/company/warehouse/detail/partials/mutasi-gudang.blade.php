<div class="max-w-7xl mx-auto px-6 py-10 space-y-8">



    {{-- FILTERS --}}
    <form method="GET"
          class="bg-white p-4 rounded-xl shadow-sm border grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- DATE FROM --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Dari Tanggal</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
        </div>

        {{-- DATE TO --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
        </div>



        {{-- ISSUE CATEGORY --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Kategori Issue</label>
            <select name="issue"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach ($categoriesIssues as $ci)
                    <option value="{{ $ci->name }}" @selected(request('issue') == $ci->name)>
                        {{ $ci->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-full flex justify-end pt-2">
            <button class="bg-emerald-600 text-white px-6 py-2 rounded-lg shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode Stok</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Selisih</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Catatan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pengguna</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($warehouseMutations as $row)

                    @php
                        $diff = $row->diff ?? 0;
                        $user = $row->user ?? $row->created_by_name ?? '-';

                        // WARNA BADGE KATEGORI
                        $badgeColor = 'blue';
                        if ($diff > 0) $badgeColor = 'emerald';
                        if ($diff < 0) $badgeColor = 'red';
                    @endphp

                    <tr class="hover:bg-gray-50 transition">

                        {{-- DATE --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($row->date)->format('H:i') }}
                            </div>
                        </td>

                        {{-- STOCK CODE --}}
                        <td class="px-4 py-3 font-mono text-gray-900">
                            {{ $row->stock_code ?? '-' }}
                        </td>



                        {{-- DIFF --}}
                        <td class="px-4 py-3 text-right font-bold
                            @if($diff > 0) text-emerald-600
                            @elseif($diff < 0) text-red-600
                            @else text-gray-600 @endif">
                            {{ number_format($diff, 2) }}
                        </td>

                        {{-- ISSUE --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border border-{{ $badgeColor }}-200">
                                {{ $row->issue_name ?? $row->source ?? '-' }}
                            </span>
                        </td>

                        {{-- NOTE --}}
                        <td class="px-4 py-3 text-gray-700 max-w-xs line-clamp-2">
                            {{ $row->note ?: '-' }}
                        </td>

                        {{-- USER --}}
                        <td class="px-4 py-3 text-gray-900 font-medium">
                            {{ $user }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="text-center py-10 text-gray-500">
                            Tidak ada riwayat ditemukan.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</div>