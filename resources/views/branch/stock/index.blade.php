<x-app-layout :branchCode="$branchCode">

    <div class="mx-auto px-6 py-10 space-y-8">

        {{-- HEADER + ACTION --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stok Barang</h1>
                <p class="text-gray-600 text-sm mt-1">
                    Pantau persediaan bahan pada setiap gudang cabang.
                </p>
            </div>

            {{-- BUTTON TAMBAH --}}
            <x-crud-add 
                resource="branch.stock"
                :companyCode="$companyCode"
                permissionPrefix="warehouse"
            />
        </div>

        {{-- FILTERS --}}
        <form method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-xl shadow-sm border">

            {{-- SEARCH --}}
            <div class="col-span-1">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama / kode item..."
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
                >
            </div>

            {{-- CATEGORY --}}
            <div class="col-span-1">
                <select 
                    name="category"
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-emerald-300"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" 
                            @selected($selectedCategory == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- WAREHOUSE --}}
            <div class="col-span-1">
                <select 
                    name="warehouse"
                    class="w-full px-4 py-2.5 border rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-emerald-300"
                >
                    <option value="">Semua Gudang</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" 
                            @selected($selectedWarehouse == $wh->id)>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SUBMIT --}}
            <div class="col-span-1">
                <button 
                    class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg shadow hover:bg-emerald-700 transition">
                    Terapkan Filter
                </button>
            </div>

        </form>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">
                
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kode Stock</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Item</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kategori</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gudang</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Stok</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- CODE --}}
                            <td class="px-4 py-4">
                                <div class="font-mono text-sm text-gray-900">
                                    {{ $stock->code }}
                                </div>
                            </td>

                            {{-- ITEM --}}
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $stock->item->name }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->item->code }}</div>
                            </td>

                            {{-- CATEGORY --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $stock->item->category->name ?? '-' }}
                            </td>

                            {{-- WAREHOUSE --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $stock->warehouse->name }}
                            </td>

                            {{-- QTY (CLICK TO OPEN MODAL) --}}
                            <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                <button 
                                    class="text-emerald-700 font-semibold hover:underline cursor-pointer"
                                    onclick="openAdjustModal(
                                        {{ $stock->id }},
                                        '{{ $stock->item->name }}',
                                        {{ $stock->qty }},
                                        '{{ $stock->item->satuan->name }}'
                                    )">
                                    {{ number_format($stock->qty, 0, ',', '.') }}


                                    <span class="text-gray-400">{{ $stock->item->satuan->name }}</span>
                                </button>
                                
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">

                                    

                                    {{-- HISTORY BUTTON --}}
                                    <a href="{{ route('branch.stock.history', [$branchCode, $stock->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg
                                               bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200
                                               transition font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        History
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                Tidak ada data stok yang ditemukan.
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


    {{-- ADJUST MODAL --}}
    <div id="adjustModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">

            <h2 class="text-xl font-bold mb-3 text-gray-900">Sesuaikan Stok</h2>

            <p class="text-gray-700 mb-4">
                <span id="adj_item_name" class="font-semibold"></span>
            </p>

            <form id="adjustForm" method="POST"
                action="{{ route('branch.stock.adjust.store', [ $branchCode]) }}">
                @csrf

                <input type="hidden" name="stock_id" id="adj_stock_id">
                <input type="hidden" name="prev_qty" id="adj_prev_qty">

                {{-- AFTER QTY --}}
                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Setelah Penyesuaian</label>
                    <input type="number" min="0" step="0.1" name="after_qty" id="adj_after_qty"
                        class="w-full border-gray-300 rounded-lg px-4 py-2
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                </div>

                {{-- ISSUE CATEGORY --}}
                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Kategori Penyesuaian</label>
                    <select name="categories_issues_id"
                        class="w-full border-gray-300 rounded-lg px-4 py-2">
                        @foreach ($categoriesIssues as $ci)
                            <option value="{{ $ci->id }}">{{ $ci->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- NOTE --}}
                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea name="note" rows="3"
                        class="w-full border-gray-300 rounded-lg px-4 py-2"
                        placeholder="Contoh: koreksi stok, hasil opname..."></textarea>
                </div>

                {{-- BUTTONS --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                        onclick="closeAdjustModal()"
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


    {{-- JS for modal --}}
    <script>
        window.openAdjustModal = function (stockId, itemName, prevQty, satuan) {
            document.getElementById("adj_stock_id").value = stockId;
            document.getElementById("adj_prev_qty").value = prevQty;

            document.getElementById("adj_item_name").innerText =
                `${itemName} (${prevQty} ${satuan})`;

            document.getElementById("adj_after_qty").value = prevQty;

            document.getElementById("adjustModal").classList.remove("hidden");
        };

        window.closeAdjustModal = function () {
            document.getElementById("adjustModal").classList.add("hidden");
        };

        document.getElementById("adjustForm").addEventListener("submit", function (e) {
            const prev = parseFloat(document.getElementById("adj_prev_qty").value);
            const after = parseFloat(document.getElementById("adj_after_qty").value);

            if (prev === after) {
                e.preventDefault();
                alert("Qty penyesuaian tidak berubah.");
            }
        });
    </script>

</x-app-layout>
