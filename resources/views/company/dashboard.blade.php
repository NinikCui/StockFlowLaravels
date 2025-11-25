<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h4l3 10 4-18 3 8h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">
                    Dashboard Perusahaan
                </h1>
                <p class="text-sm text-gray-600">
                    Ringkasan performa seluruh cabang, stok, dan aktivitas logistik perusahaan.
                </p>
            </div>
        </div>

        <div class="flex flex-col items-end">
            <span class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full border border-emerald-100">
                Company Level · Monitoring
            </span>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        {{-- Total Cabang --}}
        <x-kpi-card 
            title="Total Cabang" 
            value="{{ $totalBranches }}" 
            iconBg="bg-emerald-100" 
            iconColor="text-emerald-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 10h16M4 14h10M4 18h6"/>'
        />

        {{-- Total Item --}}
        <x-kpi-card 
            title="Total Item" 
            value="{{ $totalItems }}" 
            iconBg="bg-blue-100" 
            iconColor="text-blue-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2h-3M8 4H5a2 2 0 00-2 2v7m0 0v5a2 2 0 002 2h3m11-7v5a2 2 0 01-2 2h-3m0-9V4m0 0H8"/>'
        />

        {{-- Total Supplier --}}
        <x-kpi-card 
            title="Total Supplier" 
            value="{{ $totalSuppliers }}" 
            iconBg="bg-orange-100" 
            iconColor="text-orange-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 10-8 0m8 0a4 4 0 01-8 0m8 0a4 4 0 100 8m-8 0a4 4 0 118 0"/>'
        />

        {{-- Request Antar Cabang Bulan Ini --}}
        <x-kpi-card 
            title="Request Antar Cabang (bulan ini)" 
            value="{{ $requestMonth }}" 
            iconBg="bg-purple-100" 
            iconColor="text-purple-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4"/>'
        />
    </div>

    {{-- SECOND ROW KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

        {{-- PO bulan ini --}}
        <x-kpi-card 
            title="Purchase Order (bulan ini)" 
            value="{{ $poMonth }}" 
            iconBg="bg-sky-100" 
            iconColor="text-sky-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4"/>'
        />

        {{-- Receive bulan ini --}}
        <x-kpi-card 
            title="Penerimaan Barang (bulan ini)" 
            value="{{ $receivedMonth }}" 
            iconBg="bg-emerald-100" 
            iconColor="text-emerald-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7"/>'
        />

        {{-- Rata-rata request/bulan --}}
        <x-kpi-card 
            title="Rata-rata Request / Bulan" 
            value="{{ number_format($avgPerMonth ?? 0, 1) }}" 
            iconBg="bg-pink-100" 
            iconColor="text-pink-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h4l3 10 4-18 3 8h4"/>'
        />
    </div>

    {{-- TRENDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

        {{-- Trend Request --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Trend Request Antar Cabang (12 bulan)</h2>
            <canvas id="reqChart" height="150"></canvas>
        </div>

        {{-- Trend PO --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Trend Purchase Order (12 bulan)</h2>
            <canvas id="poChart" height="150"></canvas>
        </div>
    </div>

    {{-- HEATMAP + FAST ITEMS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-20">

        {{-- HEATMAP --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Peta Aliran Antar Cabang</h2>

            @if(!empty($heatmap))
                <div class="overflow-x-auto">
                <table class="min-w-full text-xs border border-gray-200 rounded">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 border-r border-gray-200 text-left">Dari \ Ke</th>
                            @foreach(array_keys($heatmap) as $toName)
                                <th class="px-3 py-2 text-center border-gray-200">{{ $toName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatmap as $from => $cols)
                            <tr>
                                <th class="px-3 py-2 border-r bg-gray-50 text-gray-700 text-left">{{ $from }}</th>
                                @foreach($cols as $to => $val)
                                    @php
                                        $level = $val == 0 ? 'bg-white' :
                                                 ($val < 5 ? 'bg-emerald-50' :
                                                 ($val < 15 ? 'bg-emerald-100' :
                                                 ($val < 30 ? 'bg-emerald-200' : 'bg-emerald-300')));
                                    @endphp
                                    <td class="px-3 py-2 text-center border-gray-100 {{ $level }}">
                                        {{ $val ?: '–' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <p class="text-xs text-gray-500">Belum ada data aliran antar cabang.</p>
            @endif
        </div>

        {{-- FAST MOVING ITEMS --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Fast Moving Items</h2>

            @if(count($fastItems))
                <ul class="divide-y divide-gray-100">
                    @foreach($fastItems as $itemId => $qty)
                        <li class="py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-700">
                                {{ optional(\App\Models\Item::find($itemId))->name ?? 'Item #'.$itemId }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">{{ number_format($qty) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-500">Belum ada item yang sering dipindah.</p>
            @endif
        </div>

    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const requestTrendLabels = {!! json_encode(array_keys($requestTrend->toArray())) !!};
const requestTrendData   = {!! json_encode(array_values($requestTrend->toArray())) !!};

const poTrendLabels = {!! json_encode(array_keys($poTrend->toArray())) !!};
const poTrendData   = {!! json_encode(array_values($poTrend->toArray())) !!};

new Chart(document.getElementById('reqChart'), {
    type: 'line',
    data: {
        labels: requestTrendLabels,
        datasets: [{
            label: 'Request',
            data: requestTrendData,
            borderWidth: 2,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            tension: 0.3
        }]
    }
});

new Chart(document.getElementById('poChart'), {
    type: 'bar',
    data: {
        labels: poTrendLabels,
        datasets: [{
            label: 'PO',
            data: poTrendData,
            backgroundColor: '#3b82f6',
            borderWidth: 2
        }]
    }
});
</script>

</x-app-layout>
