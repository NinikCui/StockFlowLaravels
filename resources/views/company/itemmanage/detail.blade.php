<x-app-layout :companyCode="$companyCode">
    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <div class="bg-white border rounded-xl p-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
            <p class="text-sm text-gray-600">
                Forecast Prediksi Berdasarkan Histori {{ $monthsBack }} bulan terakhir
            </p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total Stok (All Cabang)</p>
                    <p class="text-2xl font-bold">{{ $overall['stock_qty'] }}
</p>
                </div>
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Forecast Pemakaian (All Cabang)</p>
                    <p class="text-2xl font-bold">
                        {{ $overall['recommended_restock'] }}
                    </p>
                </div>
            </div>
        </div>

<div class="bg-white border rounded-xl overflow-hidden">
    <div class="p-4 border-b">
        <h2 class="font-semibold text-gray-900">Per Cabang</h2>
        <p class="text-xs text-gray-500 mt-1">
            Menampilkan stok, forecast, serta batas min/max stok per cabang.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">Cabang</th>
                    <th class="text-right px-4 py-3">Total Stok</th>
                    <th class="text-right px-4 py-3">Min Stock</th>
                    <th class="text-right px-4 py-3">Max Stock</th>
                    <th class="text-right px-4 py-3">Forecast Pemakaian (Next)</th>
                    <th class="text-left px-4 py-3">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($rows as $r)
                    @php
                        $qty = (float) ($r['total_qty'] ?? 0);

                        // default kalau belum ada record min/max (harusnya sudah ada karena auto-create)
                        $min = isset($r['min_stock']) ? (float) $r['min_stock'] : 0;
                        $max = isset($r['max_stock']) ? (float) $r['max_stock'] : 0;

                        $isLow  = $min > 0 && $qty < $min;
                        $isOver = $max > 0 && $qty > $max;

                        $statusText = $isLow ? 'LOW' : ($isOver ? 'OVER' : 'OK');
                        $badgeClass = $isLow
                            ? 'bg-red-100 text-red-700 border-red-200'
                            : ($isOver
                                ? 'bg-amber-100 text-amber-700 border-amber-200'
                                : 'bg-emerald-100 text-emerald-700 border-emerald-200');
                    @endphp

                    <tr>
                        <td class="px-4 py-3">
                            {{ $r['branch']->nama_cabang ?? $r['branch']->name }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ $r['total_qty'] }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $min }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $max }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $r['recommended_restock'] === null ? '-' : $r['recommended_restock'] }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $badgeClass }}">
                                {{ $statusText }}
                            </span>

                            @if ($isLow)
                                <span class="ml-2 text-xs text-gray-500">
                                    (Kurang {{ max(0, $min - $qty) }})
                                </span>
                            @elseif ($isOver)
                                <span class="ml-2 text-xs text-gray-500">
                                    (Lebih {{ max(0, $qty - $max) }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    </div>
</x-app-layout>
