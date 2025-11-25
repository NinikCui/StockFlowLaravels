<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h4l3 10 4-18 3 8h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">
                    Analitik Perpindahan Stok Antar Cabang
                </h1>
                <p class="text-sm text-gray-600">
                    Ringkasan aktivitas transfer bahan antar cabang: cabang tersibuk, barang paling sering dipindah, dan pola aliran antar cabang.
                </p>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
                Owner View · Monitoring Only
            </span>
            <a href="{{ route('request.index', $companyCode) }}"
               class="inline-flex items-center gap-2 text-xs text-gray-500 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke daftar request antar cabang
            </a>
        </div>
    </div>

    @php
        $outboundCollection = collect($outbound ?? []);
        $inboundCollection  = collect($inbound ?? []);

        $topOutboundName  = $outboundCollection->sortDesc()->keys()->first();
        $topOutboundValue = $outboundCollection->sortDesc()->first();

        $topInboundName   = $inboundCollection->sortDesc()->keys()->first();
        $topInboundValue  = $inboundCollection->sortDesc()->first();

        $maxOutbound = max($outboundCollection->max() ?? 1, 1);
        $maxInbound  = max($inboundCollection->max() ?? 1, 1);

        $itemRankingCollection = collect($itemRanking ?? []);
        $maxItemQty = max($itemRankingCollection->max() ?? 1, 1);
    @endphp

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Total Request --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Request</p>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h10M4 18h6"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">
                {{ number_format($totalRequest ?? 0) }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                Semua request transfer antar cabang yang tercatat
            </p>
        </div>

        {{-- Rata-rata per Bulan --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rata-rata/bulan</p>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 19h16M4 15h10M4 11h6M4 7h2"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">
                {{ number_format($avgPerMonth ?? 0, 1) }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                Rata-rata request antar cabang per bulan
            </p>
        </div>

        {{-- Cabang Terbanyak Mengirim --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cabang Terbanyak Mengirim</p>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-50 text-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h4l3 10 4-18 3 8h4"/>
                    </svg>
                </span>
            </div>
            <p class="text-sm font-semibold text-gray-900">
                {{ $topOutboundName ?? '–' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $topOutboundValue ? number_format($topOutboundValue) . ' request keluar' : 'Belum ada data' }}
            </p>
        </div>

        {{-- Cabang Terbanyak Menerima --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cabang Terbanyak Menerima</p>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-50 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
            </div>
            <p class="text-sm font-semibold text-gray-900">
                {{ $topInboundName ?? '–' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $topInboundValue ? number_format($topInboundValue) . ' request masuk' : 'Belum ada data' }}
            </p>
        </div>
    </div>

    {{-- MAIN GRID: Outbound/Inbound Ranking + Top Items --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

        {{-- Outbound Ranking --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Cabang Tersibuk (Mengirim)</h2>
                <span class="text-xs text-gray-400">Outbound</span>
            </div>

            @if($outboundCollection->count())
                <ul class="space-y-3">
                    @foreach($outboundCollection->sortDesc() as $branchName => $count)
                        @php
                            $percent = $maxOutbound > 0 ? ($count / $maxOutbound) * 100 : 0;
                        @endphp
                        <li>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700">{{ $branchName }}</span>
                                <span class="text-xs text-gray-500">{{ number_format($count) }} req</span>
                            </div>
                            <div class="h-2 rounded-full bg-emerald-50 overflow-hidden">
                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-500">Belum ada data outbound.</p>
            @endif
        </div>

        {{-- Inbound Ranking --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Cabang Tersibuk (Menerima)</h2>
                <span class="text-xs text-gray-400">Inbound</span>
            </div>

            @if($inboundCollection->count())
                <ul class="space-y-3">
                    @foreach($inboundCollection->sortDesc() as $branchName => $count)
                        @php
                            $percent = $maxInbound > 0 ? ($count / $maxInbound) * 100 : 0;
                        @endphp
                        <li>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700">{{ $branchName }}</span>
                                <span class="text-xs text-gray-500">{{ number_format($count) }} req</span>
                            </div>
                            <div class="h-2 rounded-full bg-blue-50 overflow-hidden">
                                <div class="h-2 rounded-full bg-blue-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-500">Belum ada data inbound.</p>
            @endif
        </div>

        {{-- Top Items --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-gray-900">Top 10 Barang Paling Sering Dipindah</h2>
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Berdasarkan total kuantitas yang berpindah antar cabang.
            </p>

            @if($itemRankingCollection->count())
                <ul class="space-y-3 max-h-64 overflow-auto pr-1">
                    @foreach($itemRankingCollection->sortDesc()->take(10) as $itemNameOrId => $qty)
                        @php
                            $percent = $maxItemQty > 0 ? ($qty / $maxItemQty) * 100 : 0;
                        @endphp
                        <li>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700">
                                    {{ $itemNameOrId }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ number_format($qty) }}
                                </span>
                            </div>
                            <div class="h-2 rounded-full bg-purple-50 overflow-hidden">
                                <div class="h-2 rounded-full bg-purple-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-500">Belum ada data per item.</p>
            @endif
        </div>
    </div>

    {{-- HEATMAP CABANG -> CABANG --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-10">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Peta Aliran Antar Cabang</h2>
                <p class="text-xs text-gray-500">
                    Matriks jumlah request dari cabang asal (baris) ke cabang tujuan (kolom).
                </p>
            </div>
        </div>

        @if(!empty($heatmap))
            @php
                $rows = $heatmap;
                $colNames = [];
                foreach ($rows as $fromName => $cols) {
                    foreach ($cols as $toName => $val) {
                        $colNames[$toName] = true;
                    }
                }
                $colNames = array_keys($colNames);
            @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold text-gray-700 border-b border-r border-gray-200">
                                Dari \ Ke
                            </th>
                            @foreach($colNames as $toName)
                                <th class="px-3 py-3 text-center font-semibold text-gray-700 border-b border-gray-200">
                                    {{ $toName }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $fromName => $cols)
                            <tr class="hover:bg-gray-50/60">
                                {{-- Row header --}}
                                <th class="px-3 py-2 text-left font-medium text-gray-700 bg-gray-50 border-r border-gray-200 whitespace-nowrap">
                                    {{ $fromName }}
                                </th>
                                @foreach($colNames as $toName)
                                    @php
                                        $val = $cols[$toName] ?? 0;
                                        $intVal = (int) $val;
                                        $bgClass = $intVal == 0 ? 'bg-white' :
                                                   ($intVal < 5 ? 'bg-emerald-50' :
                                                   ($intVal < 15 ? 'bg-emerald-100' :
                                                   ($intVal < 30 ? 'bg-emerald-200' : 'bg-emerald-300')));
                                        $textClass = $intVal == 0 ? 'text-gray-400' : 'text-gray-800';
                                    @endphp
                                    <td class="px-3 py-2 text-center border-t border-gray-100 {{ $bgClass }} {{ $textClass }}">
                                        {{ $intVal ?: '–' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-gray-500">Belum ada data heatmap perpindahan antar cabang.</p>
        @endif
    </div>

</div>
</x-app-layout>
