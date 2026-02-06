<x-app-layout :branchCode="$branchCode">
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- ================= HEADER ================= --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-lime-600 p-8 text-white shadow-xl">
            <div class="absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-white/80 text-sm mb-2">
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-semibold">
                            Branch
                        </span>
                        <span class="opacity-90">Dashboard</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">
                        Cabang {{ $branchName }}
                    </h1>
                    <p class="text-white/80 mt-2">
                        Ringkasan stok dan aktivitas operasional cabang
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-4 py-2 text-sm font-semibold text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ now()->format('d M Y H:i') }}
                    </div>

                    <div class="flex items-center gap-2">
                        
                        <button onclick="location.reload()"
                            class="inline-flex items-center gap-2 rounded-2xl bg-black/20 px-4 py-2 text-sm font-semibold text-white hover:bg-black/30 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v6h6M20 20v-6h-6M20 10a8 8 0 00-14.9-3M4 14a8 8 0 0014.9 3"/>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= ALERTS ================= --}}
        @if(($dashboard['criticalItems'] ?? 0) > 0 || ($dashboard['expiringSoonItems'] ?? 0) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                @if(($dashboard['criticalItems'] ?? 0) > 0)
                    <a href="{{ route('branch.item.index', [$branchCode, 'stock' => 'low']) }}"
                    class="block rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm
                            hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
                        <div class="flex items-start gap-4">
                            <div class="h-11 w-11 rounded-2xl bg-red-500 flex items-center justify-center shadow">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-extrabold text-red-800">Stok Kritis</p>
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                        {{ $dashboard['criticalItems'] }} item
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-red-700">
                                    Terdapat item di bawah batas minimum. Segera lakukan pengadaan atau request dari cabang lain.
                                </p>

                                <div class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-red-700">
                                    Lihat daftar item
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                @if(($dashboard['expiringSoonItems'] ?? 0) > 0)
                    <a href="{{ route('branch.item.index', [$branchCode, 'expire' => 'soon']) }}"
                    class="block rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm
                            hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
                        <div class="flex items-start gap-4">
                            <div class="h-11 w-11 rounded-2xl bg-amber-500 flex items-center justify-center shadow">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-extrabold text-amber-800">Akan Kadaluarsa</p>
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                                        {{ $dashboard['expiringSoonItems'] }} item
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-amber-700">
                                    Item akan kadaluarsa dalam 7 hari. Pertimbangkan promosi atau prioritas penggunaan.
                                </p>

                                <div class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-amber-800">
                                    Lihat daftar item
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif


            </div>
        @endif

        {{-- ================= KPI: TRANSAKSI ================= --}}
        <div>
            <div class="flex items-end justify-between mb-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-700 uppercase tracking-wide">Transaksi & Aktivitas</h2>
                    <p class="text-xs text-slate-500 mt-1">Ringkasan aktivitas operasional bulan ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-kpi-card
                    title="Request Masuk"
                    value="{{ $dashboard['incomingRequests'] }}"
                    subtitle="Bulan Ini"
                    iconColor="text-blue-700"
                    iconBg="bg-blue-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>'
                />

                <x-kpi-card
                    title="Request Keluar"
                    value="{{ $dashboard['outgoingRequests'] }}"
                    subtitle="Bulan Ini"
                    iconColor="text-indigo-700"
                    iconBg="bg-indigo-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>'
                />

                <x-kpi-card
                    title="Purchase Order"
                    value="{{ $dashboard['purchaseOrders'] }}"
                    subtitle="Bulan Ini"
                    iconColor="text-emerald-700"
                    iconBg="bg-emerald-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                />

                <x-kpi-card
                    title="Barang Diterima"
                    value="{{ $dashboard['receivedGoods'] }}"
                    subtitle="Bulan Ini"
                    iconColor="text-cyan-700"
                    iconBg="bg-cyan-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'
                />
            </div>
        </div>

        {{-- ================= KPI: SALES ================= --}}
        <div>
            <div class="flex items-end justify-between mb-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-700 uppercase tracking-wide">Penjualan (POS)</h2>
                    <p class="text-xs text-slate-500 mt-1">Ringkasan transaksi dari POS</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-kpi-card
                    title="Penjualan Hari Ini"
                    value="Rp {{ number_format($dashboard['todaySales'], 0, ',', '.') }}"
                    subtitle="{{ $dashboard['todayOrders'] }} transaksi"
                    iconColor="text-emerald-700"
                    iconBg="bg-emerald-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                />

                <x-kpi-card
                    title="Order Hari Ini"
                    value="{{ $dashboard['todayOrders'] }}"
                    subtitle="Transaksi"
                    iconColor="text-purple-700"
                    iconBg="bg-purple-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'
                />

                <x-kpi-card
                    title="Penjualan Bulan Ini"
                    value="Rp {{ number_format($dashboard['monthSales'], 0, ',', '.') }}"
                    subtitle="{{ $dashboard['monthOrders'] }} transaksi"
                    iconColor="text-teal-700"
                    iconBg="bg-teal-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
                />

                <x-kpi-card
                    title="Order Bulan Ini"
                    value="{{ $dashboard['monthOrders'] }}"
                    subtitle="Transaksi"
                    iconColor="text-pink-700"
                    iconBg="bg-pink-100"
                    svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
                />
            </div>
        </div>

        {{-- ================= CHARTS ================= --}}
        <div>
            <div class="flex items-end justify-between mb-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-700 uppercase tracking-wide">Analitik</h2>
                    <p class="text-xs text-slate-500 mt-1">Visualisasi kondisi stok, expiry, dan penjualan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Distribusi Status Stok</h3>
                            <p class="text-xs text-slate-500 mt-1">Kondisi item di cabang</p>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Status Kadaluarsa</h3>
                            <p class="text-xs text-slate-500 mt-1">Monitoring expired date</p>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="expiryChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Trend Penjualan</h3>
                            <p class="text-xs text-slate-500 mt-1">7 hari terakhir</p>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= RECENT ACTIVITIES ================= --}}
        <div>
            <div class="flex items-end justify-between mb-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-700 uppercase tracking-wide">Aktivitas Terbaru</h2>
                    <p class="text-xs text-slate-500 mt-1">Log transaksi & perpindahan barang</p>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="space-y-4">
                    @forelse ($dashboard['recentActivities'] as $activity)
                        @php
                            $iconConfig = match($activity['type']) {
                                'stock_in' => ['bg' => 'bg-blue-100', 'icon' => 'text-blue-700', 'ring' => 'ring-blue-200', 'label' => 'Stock In'],
                                'stock_out' => ['bg' => 'bg-orange-100', 'icon' => 'text-orange-700', 'ring' => 'ring-orange-200', 'label' => 'Stock Out'],
                                'transfer_in' => ['bg' => 'bg-emerald-100', 'icon' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'label' => 'Transfer In'],
                                'transfer_out' => ['bg' => 'bg-purple-100', 'icon' => 'text-purple-700', 'ring' => 'ring-purple-200', 'label' => 'Transfer Out'],
                                default => ['bg' => 'bg-slate-100', 'icon' => 'text-slate-700', 'ring' => 'ring-slate-200', 'label' => 'Activity'],
                            };

                            $statusClass = 'bg-slate-100 text-slate-700';
                            if (($activity['status'] ?? null) === 'REQUESTED') $statusClass = 'bg-amber-100 text-amber-800';
                            if (($activity['status'] ?? null) === 'IN_TRANSIT') $statusClass = 'bg-blue-100 text-blue-800';
                            if (($activity['status'] ?? null) === 'COMPLETED') $statusClass = 'bg-emerald-100 text-emerald-800';
                        @endphp

                        <div class="relative flex gap-4 rounded-2xl bg-slate-50 p-4 hover:bg-slate-100 transition">
                            <div class="flex-shrink-0">
                                <div class="h-11 w-11 rounded-2xl {{ $iconConfig['bg'] }} {{ $iconConfig['ring'] }} ring-1 flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $iconConfig['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @switch($activity['type'])
                                            @case('stock_in')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                                @break
                                            @case('stock_out')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                @break
                                            @case('transfer_in')
                                            @case('transfer_out')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @endswitch
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-extrabold text-slate-900 truncate">
                                                {{ $activity['note'] }}
                                            </p>
                                            <span class="hidden sm:inline-flex items-center rounded-full bg-white px-2 py-0.5 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">
                                                {{ $iconConfig['label'] }}
                                            </span>
                                        </div>

                                        @if(isset($activity['item']))
                                            <p class="text-xs text-slate-600 mt-1">
                                                {{ $activity['item'] }}
                                                <span class="mx-1 text-slate-300">•</span>
                                                {{ $activity['warehouse'] }}
                                                <span class="mx-1 text-slate-300">•</span>
                                                <span class="font-semibold">{{ $activity['qty'] }}</span> unit
                                            </p>
                                        @endif

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                            @if(isset($activity['from']) && isset($activity['to']))
                                                <span class="inline-flex items-center rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">
                                                    {{ $activity['from'] }}
                                                    <svg class="w-3 h-3 mx-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                    {{ $activity['to'] }}
                                                </span>
                                            @endif

                                            @if(isset($activity['reference']))
                                                <span class="font-mono rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">
                                                    {{ $activity['reference'] }}
                                                </span>
                                            @endif

                                            <span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">
                                                {{ $activity['time'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if(isset($activity['status']))
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold {{ $statusClass }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 mb-1">Belum ada aktivitas</p>
                            <p class="text-xs text-slate-500">Aktivitas transaksi akan muncul di sini</p>
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
const stockData  = @json($dashboard['chartStock']);
const expiryData = @json($dashboard['chartExpiry']);
const salesData  = @json($dashboard['chartSales']);

const baseTooltip = {
    backgroundColor: 'rgba(15, 23, 42, 0.92)',
    padding: 12,
    cornerRadius: 10,
    titleFont: { size: 13, weight: '700' },
    bodyFont: { size: 12 }
};

// Stock Distribution
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
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 14, usePointStyle: true, pointStyle: 'circle' }
            },
            tooltip: baseTooltip
        }
    }
});

// Expiry
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
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 14, usePointStyle: true, pointStyle: 'circle' }
            },
            tooltip: baseTooltip
        }
    }
});

// Sales Trend
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesData.labels,
        datasets: [{
            label: 'Penjualan',
            data: salesData.data,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16,185,129,0.14)',
            borderWidth: 2,
            fill: true,
            tension: 0.38,
            pointRadius: 3.5,
            pointHoverRadius: 6,
            pointBackgroundColor: '#10B981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                ...baseTooltip,
                callbacks: {
                    label: function(ctx) {
                        return 'Rp ' + (ctx.parsed.y ?? 0).toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(2,6,23,0.06)', drawBorder: false },
                ticks: {
                    color: '#64748B',
                    callback: function(value) {
                        return 'Rp ' + (value/1000) + 'k';
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#334155', font: { weight: '600' } }
            }
        }
    }
});
</script>

<style>
@media print {
    button { display: none !important; }
}
</style>
</x-app-layout>
