<x-app-layout :branchCode="$branchCode">

<div class="max-w-6xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">
        Daftar Stok: {{ $item->name }}
    </h1>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

        <table class="min-w-full text-sm divide-y divide-gray-200">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Stock Code</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Gudang</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Riwayat Stock</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Kadaluarsa</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @foreach ($stocks as $stock)
                    <tr class="hover:bg-gray-50">

                        {{-- STOCK CODE --}}
                        <td class="px-4 py-3 font-mono">{{ $stock->code }}</td>

                        {{-- WAREHOUSE --}}
                        <td class="px-4 py-3">
                            {{ $stock->warehouse->name }}
                        </td>

                        {{-- QTY CLICK TO OPEN MODAL --}}
                        <td class="px-4 py-3 text-right">
                            <button 
                                class="text-emerald-700 font-semibold hover:underline"
                                onclick="openAdjustModal(
                                    {{ $stock->id }},
                                    '{{ $item->name }}',
                                    {{ $stock->qty }},
                                    '{{ $item->satuan->name ?? '' }}'
                                )">
                                {{ $stock->qty }}
                                <span class="text-gray-400">{{ $item->satuan->name ?? '' }}</span>
                            </button>
                        </td>

                        <td class="px-4 py-3 text-center">
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
                        </td>
                        <td class="px-4 py-3 text-center">

                            @if ($stock->expired_at)

                                @php
                                    $days = ceil($stock->days_to_expire);
                                    $isCritical = $days <= 2;
                                    $isWarning  = $days <= 7 && $days > 2;
                                @endphp

                                {{-- 🔴 MERAH jika ≤ 2 hari --}}
                                @if ($isCritical)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-red-100 text-red-700 border border-red-300">
                                        Kadaluarsa dalam {{ $days }} hari
                                    </span>

                                {{-- 🟡 KUNING jika ≤ 7 hari --}}
                                @elseif ($isWarning)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-yellow-100 text-yellow-700 border border-yellow-300">
                                        Kadaluarsa dalam {{ $days }} hari
                                    </span>

                                {{-- 🟢 NORMAL --}}
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-green-100 text-green-700 border border-green-300">
                                        Exp: {{ \Carbon\Carbon::parse($stock->expired_at)->format('d M Y') }}
                                    </span>
                                @endif

                            @else
                                <span class="text-gray-400 text-xs italic">—</span>
                            @endif

                        </td>


                    </tr>
                @endforeach

            </tbody>

        </table>
    </div>

</div>


{{-- ADJUST MODAL COPY SAME AS MAIN STOCK PAGE --}}
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
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- JS --}}
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
