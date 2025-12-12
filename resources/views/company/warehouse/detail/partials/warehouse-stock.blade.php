<div class="bg-white border rounded-2xl shadow-md overflow-hidden">

    {{-- HEADER --}}
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Stok di Gudang</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola dan pantau stok item gudang</p>
            </div>

            <x-add-button 
                href="/company/{{ $companyCode }}/gudang/{{ $warehouse->id }}/stock/create"
                text="+ Tambah Stok Masuk"
                variant="primary"
            />
        </div>
    </div>

    {{-- STATS SUMMARY (Optional) --}}
    @if($stocks->count() > 0)
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-medium">Total Stok</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stocks->count() }}</p>
                </div>
            </div>

            @php
                $expiringSoon = $stocks->filter(function($s) {
                    if (!$s->expired_at) return false;
                    $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($s->expired_at), false);
                    return $daysLeft >= 0 && $daysLeft <= 7;
                })->count();
                
                $expired = $stocks->filter(function($s) {
                    if (!$s->expired_at) return false;
                    return now()->diffInDays(\Carbon\Carbon::parse($s->expired_at), false) < 0;
                })->count();
            @endphp

            @if($expiringSoon > 0)
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-medium">Hampir Expired</p>
                    <p class="text-xl font-bold text-yellow-700">{{ $expiringSoon }}</p>
                </div>
            </div>
            @endif

            @if($expired > 0)
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-medium">Sudah Expired</p>
                    <p class="text-xl font-bold text-red-700">{{ $expired }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-700 border-b-2 border-gray-200">
                    <th class="px-6 py-4 font-bold text-left text-xs uppercase tracking-wider">Kode Stok</th>
                    <th class="px-6 py-4 font-bold text-left text-xs uppercase tracking-wider">Item</th>
                    <th class="px-6 py-4 font-bold text-left text-xs uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 font-bold text-left text-xs uppercase tracking-wider">Kuantitas</th>
                    <th class="px-6 py-4 font-bold text-left text-xs uppercase tracking-wider">Status Expired</th>
                    <th class="px-6 py-4 font-bold text-center text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($stocks as $s)
                    <tr class="hover:bg-gray-50 transition-all duration-200">

                        {{-- KODE STOK --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                </div>
                                <span class="font-mono text-gray-800 font-semibold text-sm">{{ $s->code }}</span>
                            </div>
                        </td>

                        {{-- ITEM --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                                    <span class="text-white font-bold text-sm">
                                        {{ strtoupper(substr($s->item->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $s->item->name }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold
                                bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg shadow-sm">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ $s->item->kategori->name ?? '-' }}
                            </span>
                        </td>

                        {{-- QTY --}}
                        <td class="px-6 py-4">
                            <button 
                                class="group inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 
                                       border border-emerald-200 rounded-lg transition-all duration-200 shadow-sm hover:shadow"
                                onclick="openAdjustModal(
                                    {{ $s->id }},
                                    '{{ $s->item->name }}',
                                    {{ $s->qty }},
                                    '{{ $s->item->satuan->name }}'
                                )">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <div class="text-left">
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($s->qty, 2) }}</div>
                                    <div class="text-xs text-gray-600">{{ $s->item->satuan->name }}</div>
                                </div>
                            </button>
                        </td>

                        {{-- EXPIRED STATUS --}}
                        <td class="px-6 py-4">
                            @php
                                $exp = $s->expired_at ? \Carbon\Carbon::parse($s->expired_at) : null;
                                $daysLeft = $exp ? now()->diffInDays($exp, false) : null;
                            @endphp

                            @if (!$exp)
                                {{-- TIDAK ADA EXPIRED --}}
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                    </svg>
                                    Tidak Ada
                                </span>

                            @else
                                <div class="space-y-1">

                                    {{-- SUDAH EXPIRED --}}
                                    @if ($daysLeft < 0)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold 
                                                    bg-red-100 text-red-800 border border-red-300 shadow-sm">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Sudah Expired
                                        </span>

                                        {{-- TAMPILKAN TANGGAL --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-medium">{{ $exp->translatedFormat('d F Y') }}</span>
                                        </div>


                                    {{-- <= 2 HARI --}}
                                    @elseif ($daysLeft <= 2)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold 
                                                    bg-orange-100 text-orange-800 border border-orange-300 shadow-sm">
                                            {{ $daysLeft }} Hari Lagi
                                        </span>

                                        {{-- TAMPILKAN TANGGAL --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-medium">{{ $exp->translatedFormat('d F Y') }}</span>
                                        </div>


                                    {{-- 3–7 HARI --}}
                                    @elseif ($daysLeft <= 7)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold 
                                                    bg-yellow-100 text-yellow-800 border border-yellow-300 shadow-sm">
                                            {{ $daysLeft }} Hari Lagi
                                        </span>

                                        {{-- TAMPILKAN TANGGAL --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M8 7V3m8 4V3m-9 8d10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-medium">{{ $exp->translatedFormat('d F Y') }}</span>
                                        </div>


                                    {{-- >7 HARI — HANYA TAMPILKAN TANGGAL SEKALI --}}
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold 
                                                    bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $exp->translatedFormat('d F Y') }}
                                        </span>

                                        {{-- ❌ TIDAK MENAMPILKAN TANGGAL LAGI --}}
                                    @endif
                                </div>
                            @endif
                        </td>



                        {{-- ACTION BUTTONS --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">

                                {{-- BUTTON HISTORY --}}
                                <a href="{{ route('stock.item.history', [$companyCode, $warehouse->id, $s->id]) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg
                                           bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200
                                           transition-all duration-200 shadow-sm hover:shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg
                                                   bg-red-50 text-red-700 hover:bg-red-100 border border-red-200
                                                   transition-all duration-200 shadow-sm hover:shadow">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                        <td colspan="6" class="px-6 py-16">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-900">Belum Ada Stok</p>
                                <p class="mt-2 text-sm text-gray-500">Mulai tambahkan stok item ke gudang ini</p>
                                <div class="mt-6">
                                    <x-add-button 
                                        href="/company/{{ $companyCode }}/gudang/{{ $warehouse->id }}/stock/create"
                                        text="+ Tambah Stok Masuk"
                                        variant="primary"
                                    />
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ADJUST MODAL --}}
<div id="adjustModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 rounded-t-2xl">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Sesuaikan Stok
            </h2>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    <span class="font-semibold text-gray-900">Item:</span>
                    <span id="adj_item_name" class="font-bold text-emerald-700"></span>
                </p>
            </div>

            <form id="adjustForm" method="POST"
                action="{{ route('stock.adjust.store', [$companyCode, $warehouse->id]) }}">
                @csrf

                <input type="hidden" name="stock_id" id="adj_stock_id">
                <input type="hidden" name="prev_qty" id="adj_prev_qty">

                {{-- ERROR HANDLING --}}
                @if ($errors->has('after_qty'))
                    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-red-800 font-medium">{{ $errors->first('after_qty') }}</p>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            document.getElementById("adjustModal").classList.remove("hidden");
                        });
                    </script>
                @endif

                {{-- AFTER QTY --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Jumlah Setelah Penyesuaian
                    </label>
                    <div class="relative">
                        <input type="number" min="0" step="0.1" name="after_qty" id="adj_after_qty"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-lg font-semibold
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="0.00"
                            required>
                    </div>
                </div>

                {{-- CATEGORY ISSUE --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Kategori Penyesuaian
                    </label>
                    <select name="categories_issues_id"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        @foreach ($categoriesIssues as $ci)
                            <option value="{{ $ci->id }}">{{ $ci->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- NOTE --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Catatan
                    </label>
                    <textarea name="note" rows="3"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        placeholder="Contoh: koreksi stok, hasil opname, kerusakan barang..."></textarea>
                </div>

                {{-- BUTTONS --}}
                <div class="flex gap-3">
                    <button type="button"
                        onclick="closeAdjustModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold 
                               rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                        Batal
                    </button>

                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 
                               text-white font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
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
            alert("Qty penyesuaian tidak berubah dari nilai sebelumnya.");
        }
    });
</script>