<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 via-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-green-500/40">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">
                    Dashboard Cabang {{ $branchName ?? 'Nama Cabang' }}
                </h1>
                <p class="text-sm text-gray-600">
                    Monitoring stok, aktivitas, dan performa cabang secara real-time.
                </p>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <span class="inline-flex px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-200">
                Branch Level · {{ $branchCode ?? 'BR-001' }}
            </span>
            <span class="text-xs text-gray-500">
                Update: {{ now()->format('d M Y, H:i') }}
            </span>
        </div>
    </div>

    {{-- KPI CARDS - ROW 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        {{-- Total Item di Cabang --}}
        <x-kpi-card 
            title="Total Item Aktif" 
            value="{{ $totalItemsInBranch ?? 0 }}" 
            iconBg="bg-gradient-to-br from-green-100 to-emerald-100" 
            iconColor="text-green-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'
        />

        {{-- Total Stok Value --}}
        <x-kpi-card 
            title="Nilai Total Stok" 
            value="Rp {{ number_format($totalStockValue ?? 0, 0, ',', '.') }}" 
            iconBg="bg-gradient-to-br from-emerald-100 to-teal-100" 
            iconColor="text-emerald-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4"/>'
        />

        {{-- Request Masuk Bulan Ini --}}
        <x-kpi-card 
            title="Request Masuk (bulan ini)" 
            value="{{ $incomingRequests ?? 0 }}" 
            iconBg="bg-gradient-to-br from-teal-100 to-cyan-100" 
            iconColor="text-teal-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16l-4-4m0 0l4-4m-4 4h18"/>'
        />

        {{-- Request Keluar Bulan Ini --}}
        <x-kpi-card 
            title="Request Keluar (bulan ini)" 
            value="{{ $outgoingRequests ?? 0 }}" 
            iconBg="bg-gradient-to-br from-lime-100 to-green-100" 
            iconColor="text-lime-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8l4 4m0 0l-4 4m4-4H3"/>'
        />
    </div>

    {{-- KPI CARDS - ROW 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

        {{-- PO Bulan Ini --}}
        <x-kpi-card 
            title="Purchase Order (bulan ini)" 
            value="{{ $purchaseOrders ?? 0 }}" 
            iconBg="bg-gradient-to-br from-green-50 to-emerald-50" 
            iconColor="text-green-700"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
        />

        {{-- Penerimaan Bulan Ini --}}
        <x-kpi-card 
            title="Penerimaan Barang (bulan ini)" 
            value="{{ $receivedGoods ?? 0 }}" 
            iconBg="bg-gradient-to-br from-emerald-50 to-teal-50" 
            iconColor="text-emerald-700"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'
        />

        {{-- Item Low Stock --}}
        <x-kpi-card 
            title="Item Low Stock" 
            value="{{ $lowStockItems ?? 0 }}" 
            iconBg="bg-gradient-to-br from-amber-100 to-orange-100" 
            iconColor="text-amber-600"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
        />
    </div>

    {{-- CHARTS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

        {{-- Trend Stok 6 Bulan --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Trend Stok (6 bulan terakhir)</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Tracking
                </span>
            </div>
            <canvas id="stockTrendChart" height="150"></canvas>
        </div>

        {{-- Aktivitas Request --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Aktivitas Request (12 bulan)</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    Analysis
                </span>
            </div>
            <canvas id="requestActivityChart" height="150"></canvas>
        </div>
    </div>

    {{-- DATA TABLES ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-20">

        {{-- Top 10 Item Terlaris --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-900">Top 10 Item Terlaris</h2>
            </div>

            @if(isset($topItems) && count($topItems))
                <ul class="space-y-3">
                    @foreach($topItems as $index => $item)
                        <li class="bg-white rounded-xl p-4 flex items-center justify-between border border-green-100 hover:border-green-300 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-white text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item['name'] ?? 'Item Name' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['code'] ?? 'ITM-000' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">{{ number_format($item['qty'] ?? 0) }}</p>
                                <p class="text-xs text-gray-500">unit</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="bg-white rounded-xl p-8 text-center border border-green-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-xs text-gray-500">Belum ada data item terlaris</p>
                </div>
            @endif
        </div>

        {{-- Recent Activities --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-900">Aktivitas Terkini</h2>
            </div>

            @if(isset($recentActivities) && count($recentActivities))
                <ul class="space-y-3">
                    @foreach($recentActivities as $activity)
                        <li class="bg-white rounded-xl p-4 border border-emerald-100 hover:border-emerald-300 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center flex-shrink-0">
                                    @if($activity['type'] === 'in')
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                        </svg>
                                    @elseif($activity['type'] === 'out')
                                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 font-medium">{{ $activity['description'] ?? '' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] ?? '' }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="bg-white rounded-xl p-8 text-center border border-emerald-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-xs text-gray-500">Belum ada aktivitas terkini</p>
                </div>
            @endif
        </div>

    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Stock Trend Chart
const stockTrendLabels = {!! json_encode($stockTrendLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!};
const stockTrendData   = {!! json_encode($stockTrendData ?? [120, 145, 135, 160, 155, 180]) !!};

new Chart(document.getElementById('stockTrendChart'), {
    type: 'line',
    data: {
        labels: stockTrendLabels,
        datasets: [{
            label: 'Total Stok',
            data: stockTrendData,
            borderWidth: 3,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Request Activity Chart
const requestLabels = {!! json_encode($requestLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
const requestInData = {!! json_encode($requestInData ?? [12, 19, 15, 25, 22, 30, 28, 32, 29, 35, 33, 40]) !!};
const requestOutData = {!! json_encode($requestOutData ?? [8, 14, 11, 18, 16, 22, 20, 24, 21, 26, 24, 30]) !!};

new Chart(document.getElementById('requestActivityChart'), {
    type: 'bar',
    data: {
        labels: requestLabels,
        datasets: [
            {
                label: 'Request Masuk',
                data: requestInData,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 6
            },
            {
                label: 'Request Keluar',
                data: requestOutData,
                backgroundColor: 'rgba(20, 184, 166, 0.8)',
                borderColor: '#14b8a6',
                borderWidth: 2,
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 11
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

</x-app-layout>