<x-app-layout :branchCode="$branchCode">

<div class="min-h-screen bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

{{-- ================= HEADER ================= --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">
                Dashboard Cabang {{ $branchName }}
            </h1>
            <p class="text-sm text-gray-600">
                Ringkasan stok dan aktivitas operasional cabang
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ now()->format('d M Y H:i') }}
        </div>
    </div>
</div>



{{-- ================= ALERTS ================= --}}
@if($dashboard['criticalItems'] > 0 || $dashboard['expiringSoonItems'] > 0)
<div class="space-y-3">
    @if($dashboard['criticalItems'] > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-red-700 mb-1">Peringatan Stok Kritis</div>
                <p class="text-sm text-red-600">
                    Terdapat <strong>{{ $dashboard['criticalItems'] }}</strong> item di bawah batas minimum. 
                    Segera lakukan pengadaan atau request dari cabang lain.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($dashboard['expiringSoonItems'] > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-amber-700 mb-1">Peringatan Kadaluarsa</div>
                <p class="text-sm text-amber-600">
                    Terdapat <strong>{{ $dashboard['expiringSoonItems'] }}</strong> item yang akan kadaluarsa dalam 7 hari. 
                    Pertimbangkan untuk promosi atau penggunaan prioritas.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

{{-- ================= TRANSACTION KPI ================= --}}
<div>
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Transaksi & Aktivitas</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <x-kpi-card
            title="Request Masuk"
            value="{{ $dashboard['incomingRequests'] }}"
            subtitle="Bulan Ini"
            iconColor="text-blue-600"
            iconBg="bg-blue-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>'
        />

        <x-kpi-card
            title="Request Keluar"
            value="{{ $dashboard['outgoingRequests'] }}"
            subtitle="Bulan Ini"
            iconColor="text-indigo-600"
            iconBg="bg-indigo-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>'
        />

        <x-kpi-card
            title="Purchase Order"
            value="{{ $dashboard['purchaseOrders'] }}"
            subtitle="Bulan Ini"
            iconColor="text-emerald-600"
            iconBg="bg-emerald-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
        />

        <x-kpi-card
            title="Barang Diterima"
            value="{{ $dashboard['receivedGoods'] }}"
            subtitle="Bulan Ini"
            iconColor="text-cyan-600"
            iconBg="bg-cyan-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'
        />

    </div>
</div>

{{-- ================= SALES KPI ================= --}}
<div>
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Penjualan (POS)</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <x-kpi-card
            title="Penjualan Hari Ini"
            value="Rp {{ number_format($dashboard['todaySales'], 0, ',', '.') }}"
            subtitle="{{ $dashboard['todayOrders'] }} transaksi"
            iconColor="text-green-600"
            iconBg="bg-green-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />

        <x-kpi-card
            title="Order Hari Ini"
            value="{{ $dashboard['todayOrders'] }}"
            subtitle="Transaksi"
            iconColor="text-purple-600"
            iconBg="bg-purple-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'
        />

        <x-kpi-card
            title="Penjualan Bulan Ini"
            value="Rp {{ number_format($dashboard['monthSales'], 0, ',', '.') }}"
            subtitle="{{ $dashboard['monthOrders'] }} transaksi"
            iconColor="text-teal-600"
            iconBg="bg-teal-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
        />

        <x-kpi-card
            title="Order Bulan Ini"
            value="{{ $dashboard['monthOrders'] }}"
            subtitle="Transaksi"
            iconColor="text-pink-600"
            iconBg="bg-pink-100"
            svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
        />

    </div>
</div>

{{-- ================= CHARTS ================= --}}
<div>
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Analitik</h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- STOCK DISTRIBUTION --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Distribusi Status Stok</h3>
                    <p class="text-xs text-gray-500 mt-1">Kondisi item di cabang</p>
                </div>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    </svg>
                </div>
            </div>
            <canvas id="stockChart" height="220"></canvas>
        </div>

        

        {{-- EXPIRY --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Status Kadaluarsa</h3>
                    <p class="text-xs text-gray-500 mt-1">Monitoring expired date</p>
                </div>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <canvas id="expiryChart" height="220"></canvas>
        </div>

        {{-- SALES TREND --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Trend Penjualan</h3>
                    <p class="text-xs text-gray-500 mt-1">7 hari terakhir</p>
                </div>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <canvas id="salesChart" height="220"></canvas>
        </div>
        
    </div>
</div>
<div>
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Aktivitas Terbaru</h2>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="space-y-3">
            @forelse ($dashboard['recentActivities'] as $activity)
                <div class="flex gap-4 py-3 border-b border-gray-100 last:border-0">
                    @php
                        $iconConfig = match($activity['type']) {
                            'stock_in' => ['bg' => 'bg-blue-100', 'icon' => 'text-blue-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>'],
                            'stock_out' => ['bg' => 'bg-orange-100', 'icon' => 'text-orange-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>'],
                            'transfer_in' => ['bg' => 'bg-green-100', 'icon' => 'text-green-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>'],
                            'transfer_out' => ['bg' => 'bg-purple-100', 'icon' => 'text-purple-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>'],
                            default => ['bg' => 'bg-gray-100', 'icon' => 'text-gray-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>']
                        };
                    @endphp
                    
                    <div class="w-10 h-10 {{ $iconConfig['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 {{ $iconConfig['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $iconConfig['svg'] !!}
                        </svg>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 mb-1">
                            {{ $activity['note'] }}
                        </div>
                        
                        @if(isset($activity['item']))
                            <div class="text-xs text-gray-600 mb-1">
                                {{ $activity['item'] }} • {{ $activity['warehouse'] }} • {{ $activity['qty'] }} unit
                            </div>
                        @endif
                        
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            @if(isset($activity['from']) && isset($activity['to']))
                                <span>{{ $activity['from'] }}</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <span>{{ $activity['to'] }}</span>
                                <span class="mx-1">•</span>
                            @endif
                            
                            @if(isset($activity['reference']))
                                <span class="font-mono">{{ $activity['reference'] }}</span>
                                <span class="mx-1">•</span>
                            @endif
                            
                            <span>{{ $activity['time'] }}</span>
                            
                            @if(isset($activity['status']))
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium {{ 
                                    $activity['status'] === 'REQUESTED' ? 'bg-yellow-100 text-yellow-700' :
                                    ($activity['status'] === 'IN_TRANSIT' ? 'bg-blue-100 text-blue-700' :
                                    ($activity['status'] === 'COMPLETED' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'))
                                }}">
                                    {{ $activity['status'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mb-1">Belum ada aktivitas</p>
                    <p class="text-xs text-gray-500">Aktivitas transaksi akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>



</div>
</div>

{{-- ================= CHART JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const stockData = @json($dashboard['chartStock']);
const expiryData = @json($dashboard['chartExpiry']);
const salesData = @json($dashboard['chartSales']);

// Stock Distribution Chart
new Chart(document.getElementById('stockChart'), {
    type: 'doughnut',
    data: {
        labels: stockData.labels,
        datasets: [{
            data: stockData.data,
            backgroundColor: stockData.colors,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 }
            }
        }
    }
});

// Expiry Chart
new Chart(document.getElementById('expiryChart'), {
    type: 'pie',
    data: {
        labels: expiryData.labels,
        datasets: [{
            data: expiryData.data,
            backgroundColor: expiryData.colors,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 11, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});

// Sales Trend Chart
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesData.labels,
        datasets: [{
            label: 'Penjualan',
            data: salesData.data,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#10B981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
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
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                ticks: { 
                    font: { size: 11 }, 
                    color: '#6B7280',
                    callback: function(value) {
                        return 'Rp ' + (value/1000) + 'k';
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11, weight: '500' }, color: '#374151' }
            }
        }
    }
});
</script>
</x-app-layout>