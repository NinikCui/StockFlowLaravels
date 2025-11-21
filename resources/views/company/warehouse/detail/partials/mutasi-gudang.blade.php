<div class="bg-white border rounded-2xl shadow-sm p-6">

    {{-- HEADER --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Mutasi Stok Gudang</h2>
        <p class="text-sm text-gray-500 mt-1">
            Aktivitas stok pada gudang <strong class="text-gray-700">{{ $warehouse->name }}</strong>.
        </p>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        {{-- FROM --}}
        <div>
            <label class="text-sm font-medium">Dari Tanggal</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="w-full px-3 py-2 border rounded-lg">
        </div>

        {{-- TO --}}
        <div>
            <label class="text-sm font-medium">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="w-full px-3 py-2 border rounded-lg">
        </div>

        {{-- ISSUE --}}
        <div>
            <label class="text-sm font-medium">Kategori Mutasi</label>
            <select name="issue" class="w-full px-3 py-2 border rounded-lg">
                <option value="">Semua</option>

                <optgroup label="Movement">
                    <option value="Stok Masuk" @selected(request('issue') == 'Stok Masuk')>Stok Masuk</option>
                    <option value="Stok Keluar" @selected(request('issue') == 'Stok Keluar')>Stok Keluar</option>
                    <option value="Transfer Masuk" @selected(request('issue') == 'Transfer Masuk')>Transfer Masuk</option>
                    <option value="Transfer Keluar" @selected(request('issue') == 'Transfer Keluar')>Transfer Keluar</option>
                </optgroup>

                <optgroup label="Penyesuaian">
                    @foreach ($categoriesIssues as $ci)
                        <option value="{{ $ci->name }}" @selected(request('issue') == $ci->name)>
                            {{ $ci->name }}
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        {{-- BUTTONS --}}
        <div class="flex items-end gap-3">
            <button class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                Terapkan
            </button>
            <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}?tab=mutasi"
               class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Reset</a>
        </div>

    </form>

    {{-- TABEL MUTASI --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wide">
                <tr>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Kode Stok</th>
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

                @forelse ($warehouseMutations as $m)
                    @php
                        $colors = [
                            'Stok Masuk'      => 'emerald',
                            'Stok Keluar'     => 'red',
                            'Transfer Masuk'  => 'sky',
                            'Transfer Keluar' => 'orange',
                        ];

                        $badge = $colors[$m->issue_name] ?? 'purple';

                        $diffColor =
                            $m->diff > 0 ? 'text-emerald-700' :
                            ($m->diff < 0 ? 'text-red-600' : 'text-gray-500');
                    @endphp

                    <tr class="hover:bg-gray-50">

                        {{-- DATE --}}
                        <td class="p-3">
                            <div class="font-medium">{{ Carbon\Carbon::parse($m->date)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ Carbon\Carbon::parse($m->date)->format('H:i') }}</div>
                        </td>

                        {{-- STOCK CODE --}}
                        <td class="p-3 font-mono text-gray-800">{{ $m->stock_code ?? '-' }}</td>

                        {{-- ITEM --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $m->item_name }}
                        </td>

                        {{-- ISSUE --}}
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-md
                                bg-{{ $badge }}-50 text-{{ $badge }}-700 border border-{{ $badge }}-200">
                                {{ $m->issue_name }}
                            </span>
                        </td>

                        {{-- PREV QTY --}}
                        <td class="p-3 text-center">
                            {{ $m->prev_qty !== null ? number_format($m->prev_qty,2) : '-' }}
                        </td>

                        {{-- AFTER QTY --}}
                        <td class="p-3 text-center">
                            {{ $m->after_qty !== null ? number_format($m->after_qty,2) : '-' }}
                        </td>

                        {{-- DIFF --}}
                        <td class="p-3 text-center font-bold {{ $diffColor }}">
                            {{ number_format($m->diff,2) }}
                        </td>

                        {{-- NOTES --}}
                        <td class="p-3 text-gray-700 max-w-[180px]">
                            <div class="line-clamp-2 break-words overflow-hidden text-ellipsis">
                                {{ $m->note ?: '-' }}
                            </div>
                        </td>

                        {{-- USER --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $m->created_by_name ?? 'System' }}
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-gray-500">
                            Tidak ada mutasi stok.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>

</div>
