<div class="bg-white border rounded-2xl p-6 shadow-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Stok di Gudang</h2>

        <x-add-button 
            href="/company/{{ $companyCode }}/gudang/{{ $warehouse->id }}/stock/create"
            text="+ Tambah Stok Masuk"
            variant="primary"
        />
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden border border-gray-200 rounded-xl">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-700 border-b">
                    <th class="p-3 font-semibold text-left">Kode Stok</th>
                    <th class="p-3 font-semibold text-left">Item</th>
                    <th class="p-3 font-semibold text-left">Kategori</th>
                    <th class="p-3 font-semibold text-left">Qty</th>
                    <th class="p-3 font-semibold text-left">Kadaluarsa</th>
                    <th class="p-3 font-semibold text-center w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($stocks as $s)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- KODE STOK --}}
                        <td class="p-3 border-b text-gray-800 font-mono">
                            {{ $s->code }}
                        </td>

                        {{-- ITEM --}}
                        <td class="p-3 border-b text-gray-900 font-medium">
                            {{ $s->item->name }}
                        </td>

                        {{-- KATEGORI --}}
                        <td class="p-3 border-b">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium
                                bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                                {{ $s->item->kategori->name ?? '-' }}
                            </span>
                        </td>

                        {{-- QTY --}}
                        <td class="p-3 border-b text-gray-800 font-semibold">
                            <button 
                                class="text-emerald-700 font-semibold hover:underline cursor-pointer"
                                onclick="openAdjustModal(
                                    {{ $s->id }},
                                    '{{ $s->item->name }}',
                                    {{ $s->qty }},
                                    '{{ $s->item->satuan->name }}'
                                )">
                                {{ number_format($s->qty, 2) }}
                                <span class="text-gray-500 text-xs ml-1">
                                    {{ $s->item->satuan->name }}
                                </span>
                            </button>
                        </td>
                        <td class="p-3 border-b text-gray-800 font-mono">
                            {{ $s->expired_at ? \Carbon\Carbon::parse($s->expired_at)->translatedFormat('d F Y') : '-' }}
                        </td>
                        <td class="p-3 border-b">
    <div class="flex justify-center gap-2">

        {{-- BUTTON HISTORY --}}
        <a href="{{ route('stock.item.history', [$companyCode, $warehouse->id, $s->id]) }}"
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

        {{-- BUTTON DELETE (HANYA KALAU QTY=0) --}}
        @if ($s->qty == 0)
            <form method="POST" 
                  action="{{ route('stock.delete', [$companyCode, $warehouse->id, $s->id]) }}"
                  onsubmit="return confirm('Yakin ingin menghapus stok ini?');">
                @csrf
                @method('DELETE')

                <button
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg
                           bg-red-50 text-red-700 hover:bg-red-100 border border-red-200
                           transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hapus
                </button>
            </form>
        @endif

    </div>
</td>


                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500 text-sm">
                            Belum ada stok item di gudang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



    <div id="adjustModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">

            <h2 class="text-xl font-bold mb-3 text-gray-900">Sesuaikan Stok</h2>

            <p class="text-gray-700 mb-4">
                <span id="adj_item_name" class="font-semibold"></span>
            </p>

            <form id="adjustForm" method="POST"
                action="{{ route('stock.adjust.store', [$companyCode, $warehouse->id]) }}">
                @csrf

                <input type="hidden" name="stock_id" id="adj_stock_id">
                <input type="hidden" name="prev_qty" id="adj_prev_qty">

                {{-- ERROR HANDLING --}}
                @if ($errors->has('after_qty'))
                    <div class="mb-3 text-red-600 text-sm">
                        {{ $errors->first('after_qty') }}
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            document.getElementById("adjustModal").classList.remove("hidden");
                        });
                    </script>
                @endif

                {{-- AFTER QTY --}}
                <div class="mb-4">
                    <label class="font-semibold text-gray-700 mb-1">Setelah Penyesuaian</label>
                    <input type="number" min="0" step="0.1" name="after_qty" id="adj_after_qty"
                        class="w-full border-gray-300 rounded-lg px-4 py-2
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                </div>

                {{-- CATEGORY ISSUE --}}
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

</div>

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
