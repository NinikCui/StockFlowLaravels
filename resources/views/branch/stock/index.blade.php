<x-app-layout :branchCode="$branchCode">

    <div class="mx-auto px-6 py-10 space-y-8">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stok Barang</h1>
                <p class="text-gray-600 text-sm mt-1">
                    Pantau persediaan bahan pada setiap gudang cabang.
                </p>
            </div>

            <x-crud-add 
                resource="branch.stock"
                :companyCode="$companyCode"
                permissionPrefix="warehouse"
            />
        </div>

        {{-- FILTERS --}}
        <form method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-xl shadow-sm border">

            <div>
                <input type="text" name="q" value="{{ $search }}"
                    placeholder="Cari nama / kode item..."
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-300">
            </div>

            <div>
                <select name="category"
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-emerald-300">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="warehouse"
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-emerald-300">
                    <option value="">Semua Gudang</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" @selected($selectedWarehouse == $wh->id)>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg shadow hover:bg-emerald-700">
                    Terapkan Filter
                </button>
            </div>

        </form>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Kode Stock</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Item</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Gudang</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700 text-sm">Stok</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 text-sm">Kadaluarsa</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 text-sm w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- CODE --}}
                            <td class="px-4 py-4 font-mono text-sm">{{ $stock->code }}</td>

                            {{-- ITEM --}}
                            <td class="px-4 py-4">
                                <div class="font-semibold">{{ $stock->item->name }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->item->code }}</div>
                            </td>

                            {{-- CATEGORY --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $stock->item->kategori->name ?? '-' }}
                            </td>

                            {{-- WAREHOUSE --}}
                            <td class="px-4 py-4 text-gray-700">{{ $stock->warehouse->name }}</td>

                            {{-- STOCK QTY --}}
                            <td class="px-4 py-4 text-right font-semibold">
                                <button onclick="openAdjustModal(
                                    {{ $stock->id }},
                                    '{{ $stock->item->name }}',
                                    {{ $stock->qty }},
                                    '{{ $stock->item->satuan->name }}'
                                )" class="text-emerald-700 hover:underline">
                                    {{ $stock->qty }}
                                    <span class="text-gray-400">{{ $stock->item->satuan->name }}</span>
                                </button>
                            </td>

                            {{-- EXPIRED --}}
                            <td class="px-4 py-4 text-center">

                                @if ($stock->expired_at)

                                    @php
                                        $days = $stock->days_to_expire;
                                        $date = \Carbon\Carbon::parse($stock->expired_at)->format('d M Y');

                                        if ($days < 0) {
                                            $color = 'bg-red-200 text-red-800';
                                            $label = 'Kadaluarsa';
                                        } elseif ($days <= 2) {
                                            $color = 'bg-red-100 text-red-700';
                                            $label = "Habis $days hari";
                                        } elseif ($days <= 7) {
                                            $color = 'bg-yellow-100 text-yellow-700';
                                            $label = "Habis $days hari";
                                        } else {
                                            $color = 'bg-emerald-100 text-emerald-800';
                                            $label = $date;
                                        }
                                    @endphp

                                    <span class="px-2 py-1 rounded-md text-xs font-medium {{ $color }}">
                                        {{ $label }}
                                    </span>

                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif

                            </td>

                            {{-- ACTION BUTTONS --}}
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">

                                    {{-- HISTORY --}}
                                    <a href="{{ route('branch.stock.history', [$branchCode, $stock->id]) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg
                                        bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100">
                                        History
                                    </a>

                                    {{-- DELETE IF QTY ZERO --}}
                                    @if ($stock->qty == 0)
                                        <form method="POST"
                                            action="{{ route('branch.stock.delete', [$branchCode, $stock->id]) }}"
                                            onsubmit="return confirm('Yakin hapus stok ini?');">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg
                                                bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-500">
                                Tidak ada data stok ditemukan.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $stocks->appends(request()->query())->links() }}
        </div>

    </div>


    {{-- ======================= --}}
    {{-- MODAL PENYESUAIAN STOK --}}
    {{-- ======================= --}}
    <div id="adjustModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">

            <h2 class="text-xl font-bold mb-3 text-gray-900">Sesuaikan Stok</h2>

            <p class="text-gray-700 mb-4">
                <span id="adj_item_name" class="font-semibold"></span>
            </p>

            <form id="adjustForm" method="POST"
                action="{{ route('branch.stock.adjust.store', [$branchCode]) }}">
                @csrf

                <input type="hidden" name="stock_id" id="adj_stock_id">
                <input type="hidden" name="prev_qty" id="adj_prev_qty">

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Setelah Penyesuaian</label>
                    <input type="number" min="0" step="0.1" name="after_qty" id="adj_after_qty"
                        class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500"
                        required>
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Kategori Penyesuaian</label>
                    <select name="categories_issues_id"
                        class="w-full border-gray-300 rounded-lg px-4 py-2">
                        @foreach ($categoriesIssues as $ci)
                            <option value="{{ $ci->id }}">{{ $ci->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea name="note" rows="3"
                        class="w-full border-gray-300 rounded-lg px-4 py-2"
                        placeholder="Contoh: koreksi stok, hasil opname..."></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAdjustModal()"
                        class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>

                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- SCRIPT --}}

    <script>
        window.openAdjustModal = function (id, name, qty, satuan) {
            document.getElementById('adj_stock_id').value = id;
            document.getElementById('adj_prev_qty').value = qty;
            document.getElementById('adj_item_name').innerText = `${name} (${qty} ${satuan})`;
            document.getElementById('adj_after_qty').value = qty;
            document.getElementById('adjustModal').classList.remove('hidden');
        }

        window.closeAdjustModal = function () {
            document.getElementById('adjustModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
