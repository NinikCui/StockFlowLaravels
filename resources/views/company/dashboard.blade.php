<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-emerald-50/50 via-white to-teal-50/30">
<div class="max-w-7xl mx-auto px-6 py-12">

    {{-- HEADER MINIMAL --}}
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-xs font-medium text-emerald-600 tracking-wide uppercase">Company Overview</span>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            Dashboard Perusahaan
        </h1>
        <p class="text-gray-500">
            Real-time monitoring untuk seluruh cabang & operasional
        </p>
    </div>

    {{-- MAIN STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <div class="group bg-white rounded-3xl p-6 hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 border border-gray-100/50">
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalBranches }}</p>
            <p class="text-sm text-gray-500">Total Cabang</p>
        </div>

        <div class="group bg-white rounded-3xl p-6 hover:shadow-xl hover:shadow-blue-100/50 transition-all duration-300 border border-gray-100/50">
            <div class="w-10 h-10 rounded-2xl bg-blue-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalItems }}</p>
            <p class="text-sm text-gray-500">Total Item</p>
        </div>

        <div class="group bg-white rounded-3xl p-6 hover:shadow-xl hover:shadow-orange-100/50 transition-all duration-300 border border-gray-100/50">
            <div class="w-10 h-10 rounded-2xl bg-orange-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalSuppliers }}</p>
            <p class="text-sm text-gray-500">Total Supplier</p>
        </div>

        <div class="group bg-white rounded-3xl p-6 hover:shadow-xl hover:shadow-purple-100/50 transition-all duration-300 border border-gray-100/50">
            <div class="w-10 h-10 rounded-2xl bg-purple-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $requestMonth }}</p>
            <p class="text-sm text-gray-500">Request (bulan ini)</p>
        </div>

    </div>

    {{-- SECONDARY STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-16">
        
        <div class="bg-white rounded-3xl p-6 border border-gray-100/50">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500">Purchase Order</p>
                <div class="w-2 h-2 rounded-full bg-sky-500"></div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $poMonth }}</p>
            <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100/50">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500">Penerimaan Barang</p>
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $receivedMonth }}</p>
            <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white">
            <p class="text-sm opacity-90 mb-3">Rata-rata Request</p>
            <p class="text-3xl font-bold">{{ number_format($avgPerMonth ?? 0, 1) }}</p>
            <p class="text-xs opacity-75 mt-1">Per bulan</p>
        </div>

    </div>

    {{-- CHARTS --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-16">
        
        <div class="bg-white rounded-3xl p-8 border border-gray-100/50">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Request Trend</h3>
                    <p class="text-sm text-gray-400 mt-1">Antar cabang, 12 bulan</p>
                </div>
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            </div>
            <canvas id="reqChart" height="200"></canvas>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-gray-100/50">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Order</h3>
                    <p class="text-sm text-gray-400 mt-1">Volume PO, 12 bulan</p>
                </div>
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
            </div>
            <canvas id="poChart" height="200"></canvas>
        </div>

    </div>

    {{-- BOTTOM SECTION --}}
    <div class="grid lg:grid-cols-2 gap-6">
        
        {{-- HEATMAP --}}
        <div class="bg-white rounded-3xl p-8 border border-gray-100/50">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-gray-900">Peta Aliran Cabang</h3>
                <span class="text-xs text-gray-400">Flow matrix</span>
            </div>

            @if(!empty($heatmap))
                <div class="overflow-x-auto -mx-2">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-3 py-3 text-left text-gray-500 font-medium">Dari \ Ke</th>
                                @foreach(array_keys($heatmap) as $toName)
                                    <th class="px-3 py-3 text-center text-gray-500 font-medium">{{ $toName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($heatmap as $from => $cols)
                                <tr class="border-b border-gray-50">
                                    <th class="px-3 py-3 text-left text-gray-700 font-medium">{{ $from }}</th>
                                    @foreach($cols as $to => $val)
                                        @php
                                            $level = $val == 0 ? 'bg-white' :
                                                     ($val < 5 ? 'bg-emerald-50' :
                                                     ($val < 15 ? 'bg-emerald-100' :
                                                     ($val < 30 ? 'bg-emerald-200' : 'bg-emerald-300')));
                                        @endphp
                                        <td class="px-3 py-3 text-center rounded-lg {{ $level }}">
                                            <span class="font-medium text-gray-700">{{ $val ?: '–' }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada data aliran</p>
                </div>
            @endif
        </div>

        {{-- FAST MOVING --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 border border-emerald-100/50">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-gray-900">Fast Moving Items</h3>
                <span class="text-xs text-emerald-600 font-medium">Top movers</span>
            </div>

            @if(count($fastItems))
                <div class="space-y-3">
                    @foreach($fastItems as $index => $item)
                        @if($index < 8)
                            <div class="bg-white rounded-2xl p-4 flex items-center justify-between border border-emerald-100/50 hover:border-emerald-200 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ optional(\App\Models\Item::find($item['id'] ?? 0))->name ?? 'Item #'.($item['id'] ?? 0) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">{{ number_format($item['qty'] ?? 0) }}</p>
                                    <p class="text-xs text-gray-400">units</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center mx-auto mb-4 border border-emerald-100/50">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada data</p>
                </div>
            @endif
        </div>

    </div>

</div>
</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
Chart.defaults.animation.duration = 1000;
Chart.defaults.animation.easing = 'easeInOutQuart';

const requestTrendLabels = {!! json_encode(array_keys($requestTrend->toArray())) !!};
const requestTrendData   = {!! json_encode(array_values($requestTrend->toArray())) !!};

const poTrendLabels = {!! json_encode(array_keys($poTrend->toArray())) !!};
const poTrendData   = {!! json_encode(array_values($poTrend->toArray())) !!};

// Request Chart
new Chart(document.getElementById('reqChart'), {
    type: 'line',
    data: {
        labels: requestTrendLabels,
        datasets: [{
            data: requestTrendData,
            borderColor: '#10b981',
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                return gradient;
            },
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#10b981',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                displayColors: false,
                callbacks: {
                    title: () => '',
                    label: (context) => `${context.parsed.y} requests`
                }
            }
        },
        scales: {
            y: {
                border: { display: false },
                grid: {
                    color: 'rgba(0, 0, 0, 0.03)',
                    drawTicks: false
                },
                ticks: {
                    padding: 10,
                    color: '#9ca3af',
                    font: { size: 11 }
                }
            },
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: {
                    padding: 10,
                    color: '#9ca3af',
                    font: { size: 11 }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// PO Chart
new Chart(document.getElementById('poChart'), {
    type: 'bar',
    data: {
        labels: poTrendLabels,
        datasets: [{
            data: poTrendData,
            backgroundColor: '#3b82f6',
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                displayColors: false,
                callbacks: {
                    title: () => '',
                    label: (context) => `${context.parsed.y} PO`
                }
            }
        },
        scales: {
            y: {
                border: { display: false },
                grid: {
                    color: 'rgba(0, 0, 0, 0.03)',
                    drawTicks: false
                },
                ticks: {
                    padding: 10,
                    color: '#9ca3af',
                    font: { size: 11 }
                }
            },
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: {
                    padding: 10,
                    color: '#9ca3af',
                    font: { size: 11 }
                }
            }
        }
    }
});
</script>

</x-app-layout>