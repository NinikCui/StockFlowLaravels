<x-app-layout :companyCode="$companyCode">
    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        {{-- =======================
            HEADER + KPI
        ======================== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 sm:p-7 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-white/15 ring-1 ring-white/20 flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V7a2 2 0 00-2-2h-5m0 0V3m0 2h-2m2 0h2M4 17V7a2 2 0 012-2h5" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold leading-tight">{{ $item->name }}</h1>
                                <p class="text-sm text-emerald-100 mt-1">
                                    Forecast prediksi berdasarkan histori {{ $monthsBack }} bulan terakhir
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Status overall --}}
                    @php
                        $overallStatusText = $overall['is_low_stock'] ? 'LOW' : ($overall['is_near_low_stock'] ? 'NEAR' : 'OK');
                        $overallBadgeClass = $overall['is_low_stock']
                            ? 'bg-red-500/15 text-red-50 ring-1 ring-red-200/30'
                            : ($overall['is_near_low_stock']
                                ? 'bg-amber-500/15 text-amber-50 ring-1 ring-amber-200/30'
                                : 'bg-emerald-500/15 text-emerald-50 ring-1 ring-emerald-200/30');
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $overallBadgeClass }}">
                            <span class="h-2 w-2 rounded-full bg-white/70"></span>
                            Status Overall: {{ $overallStatusText }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    {{-- Total Stok --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Stok (All Cabang)</p>
                                <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">
                                    {{ $overall['stock_qty'] }}
                                </p>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7l9-4 9 4-9 4-9-4z M3 7v10l9 4 9-4V7" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Min: <span class="font-medium text-gray-700">{{ $overall['min_stock'] }}</span> ·
                            Max: <span class="font-medium text-gray-700">{{ $overall['max_stock'] }}</span>
                        </div>
                    </div>

                    {{-- Forecast Next --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Forecast Pemakaian (Next)</p>
                                <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">
                                    {{ $overall['forecast_next'] }}
                                </p>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 3v18m0 0l-4-4m4 4l4-4M4 7h16" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Alpha: <span class="font-medium text-gray-700">{{ $alpha }}</span>
                        </div>
                    </div>

                    

                    {{-- Ringkas status --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-gray-500">Catatan</p>
                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                            <div class="flex items-center justify-between">
                                <span>LOW jika qty &lt; min</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">LOW</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>OVER jika qty &gt; max</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">OVER</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Selain itu OK</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">OK</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- =======================
            TABLE PER CABANG
        ======================== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Per Cabang</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Menampilkan stok, forecast, serta batas min/max stok per cabang.
                        </p>
                    </div>

                    {{-- mini legend --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full border bg-white text-gray-600">
                            <span class="h-2 w-2 rounded-full bg-red-400"></span> LOW
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full border bg-white text-gray-600">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span> OVER
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full border bg-white text-gray-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span> OK
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-white/95 backdrop-blur border-b">
                        <tr class="text-gray-600">
                            <th class="text-left px-4 py-3 font-semibold">Cabang</th>
                            <th class="text-right px-4 py-3 font-semibold">Total Stok</th>
                            <th class="text-right px-4 py-3 font-semibold">Min</th>
                            <th class="text-right px-4 py-3 font-semibold">Max</th>
                            <th class="text-right px-4 py-3 font-semibold">Forecast (Next)</th>
                            <th class="text-right px-4 py-3 font-semibold">Rekom. Restock</th>
                            <th class="text-left px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rows as $r)
                            @php
                                $qty = (float) ($r['total_qty'] ?? 0);
                                $min = isset($r['min_stock']) ? (float) $r['min_stock'] : 0;
                                $max = isset($r['max_stock']) ? (float) $r['max_stock'] : 0;

                                $isLow  = $min > 0 && $qty < $min;
                                $isOver = $max > 0 && $qty > $max;

                                $statusText = $isLow ? 'LOW' : ($isOver ? 'OVER' : 'OK');
                                $badgeClass = $isLow
                                    ? 'bg-red-50 text-red-700 border-red-200'
                                    : ($isOver
                                        ? 'bg-amber-50 text-amber-700 border-amber-200'
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-200');

                                // Progress terhadap min/max (optional tampilan bar)
                                $progress = 0;
                                if ($max > 0) {
                                    $progress = min(100, max(0, ($qty / $max) * 100));
                                } elseif ($min > 0) {
                                    $progress = min(100, max(0, ($qty / $min) * 100));
                                }
                            @endphp

                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">
                                        {{ $r['branch']->nama_cabang ?? $r['branch']->name }}
                                    </div>
                                    <div class="mt-1 h-1.5 w-full max-w-[220px] rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                        <div class="h-full rounded-full bg-gray-400" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500">
                                        {{ number_format($progress, 0) }}% dari batas (indikatif)
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                    {{ $r['total_qty'] }}
                                </td>

                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ $min }}
                                </td>

                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ $max }}
                                </td>

                                <td class="px-4 py-3 text-right text-gray-900">
                                    {{ $r['forecast_next'] ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    {{ $r['recommended_restock'] === null ? '-' : $r['recommended_restock'] }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $badgeClass }}">
                                            {{ $statusText }}
                                        </span>

                                        @if ($isLow)
                                            <span class="text-xs text-gray-500">
                                                (Kurang {{ max(0, $min - $qty) }})
                                            </span>
                                        @elseif ($isOver)
                                            <span class="text-xs text-gray-500">
                                                (Lebih {{ max(0, $qty - $max) }})
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if (count($rows) === 0)
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada data cabang untuk item ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t bg-gray-50 text-xs text-gray-500">
                Tips: Sticky header aktif saat scroll. Bar “indikatif” hanya gambaran relatif terhadap batas min/max.
            </div>
        </div>

    </div>
</x-app-layout>
