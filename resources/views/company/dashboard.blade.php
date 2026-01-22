<x-app-layout :companyCode="$companyCode">
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- ================= HEADER ================= --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-lime-600 p-8 text-white shadow-xl">
            <div class="absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-white/80 text-sm mb-1">
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-semibold">
                            Company
                        </span>
                        <span class="opacity-90">Dashboard</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Dashboard Perusahaan</h1>
                    <p class="text-white/80 mt-2">Ringkasan aktivitas operasional seluruh cabang</p>
                </div>

                <div class="flex items-center gap-2">
                    

                    <button onclick="location.reload()"
                        class="inline-flex items-center gap-2 rounded-xl bg-black/20 px-4 py-2 text-sm font-semibold text-white hover:bg-black/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v6h6M20 20v-6h-6M20 10a8 8 0 00-14.9-3M4 14a8 8 0 0014.9 3"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= KPI CARDS ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $cards = [
                    [
                        'label' => 'Critical Items',
                        'value' => $dashboard['criticalItemsCompany'] ?? 0,
                        'desc'  => 'Item di bawah minimum',
                        'ring'  => 'ring-red-200',
                        'bg'    => 'bg-red-50',
                        'text'  => 'text-red-600',
                        'icon'  => 'M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z',
                    ],
                    [
                        'label' => 'Expiring Soon',
                        'value' => $dashboard['expiringItemsCompany'] ?? 0,
                        'desc'  => 'Kadaluarsa ≤ 7 hari',
                        'ring'  => 'ring-amber-200',
                        'bg'    => 'bg-amber-50',
                        'text'  => 'text-amber-600',
                        'icon'  => 'M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z',
                    ],
                    [
                        'label' => 'Low Stock Branches',
                        'value' => $dashboard['lowStockBranches'] ?? 0,
                        'desc'  => 'Cabang stok rendah',
                        'ring'  => 'ring-sky-200',
                        'bg'    => 'bg-sky-50',
                        'text'  => 'text-sky-600',
                        'icon'  => 'M3 10h18M5 6h14M7 14h10M9 18h6',
                    ],
                    [
                        'label' => 'Pending Transfers',
                        'value' => $dashboard['pendingTransfers'] ?? 0,
                        'desc'  => 'Transfer dalam proses',
                        'ring'  => 'ring-indigo-200',
                        'bg'    => 'bg-indigo-50',
                        'text'  => 'text-indigo-600',
                        'icon'  => 'M7 7h10M7 17h10M10 7l-3 3m0 0l3 3M14 17l3-3m0 0l-3-3',
                    ],
                ];
            @endphp

            @foreach($cards as $c)
                <div class="group rounded-2xl bg-white shadow-sm ring-1 {{ $c['ring'] }} hover:shadow-xl transition overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">{{ $c['label'] }}</p>
                                <div class="mt-2 text-4xl font-extrabold {{ $c['text'] }}">{{ $c['value'] }}</div>
                                <p class="mt-1 text-xs text-slate-500">{{ $c['desc'] }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-2xl {{ $c['bg'] }} flex items-center justify-center ring-1 {{ $c['ring'] }} group-hover:scale-110 transition">
                                <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
                                </svg>
                            </div>
                        </div>

                        {{-- tiny progress aesthetic --}}
                        <div class="mt-4 h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                            @php $w = min(100, ($c['value'] ?? 0) * 12); @endphp
                            <div class="h-full {{ $c['text'] }} bg-current/90" style="width: {{ $w }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= SALES CARDS ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-emerald-200 hover:shadow-xl transition p-6">
                <p class="text-sm font-semibold text-slate-600">Sales Hari Ini</p>
                <div class="mt-2 text-3xl font-extrabold text-emerald-600">
                    Rp {{ number_format($dashboard['totalSalesToday'] ?? 0, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Total penjualan hari ini</p>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-emerald-200 hover:shadow-xl transition p-6">
                <p class="text-sm font-semibold text-slate-600">Sales Bulan Ini</p>
                <div class="mt-2 text-3xl font-extrabold text-emerald-600">
                    Rp {{ number_format($dashboard['totalSalesMonth'] ?? 0, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Total penjualan bulan ini</p>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition p-6">
                <p class="text-sm font-semibold text-slate-600">Top Performing Branch</p>
                <div class="mt-2 text-xl font-extrabold text-slate-900">
                    {{ $dashboard['topPerformingBranch']['name'] ?? 'N/A' }}
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <p class="text-sm font-semibold text-emerald-600">
                        Rp {{ number_format($dashboard['topPerformingBranch']['sales'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6l2 4 4 .5-3 3 .8 4.5L12 16l-3.8 2 .8-4.5-3-3 4-.5 2-4z"/>
                        </svg>
                        TOP
                    </span>
                </div>
            </div>
        </div>

        {{-- ================= CHARTS ROW 1 ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Sales Trend (7 Hari)</h3>
                    <span class="text-xs text-slate-500">Realtime</span>
                </div>
                <div class="p-6">
                    <canvas id="salesTrendChart" height="90"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Branch Sales Comparison</h3>
                </div>
                <div class="p-6">
                    <canvas id="branchSalesChart" height="90"></canvas>
                </div>
            </div>
        </div>

        {{-- ================= CHARTS ROW 2 ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Branch Stock Items</h3>
                </div>
                <div class="p-6">
                    <canvas id="branchStockChart" height="140"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Purchase Trend (6 Bulan)</h3>
                </div>
                <div class="p-6">
                    <canvas id="purchaseTrendChart" height="140"></canvas>
                </div>
            </div>

            
        </div>

        {{-- ================= TRANSFER + HEATMAP ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            

            <div class="lg:col-span-2 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">Transfer Heatmap (Top 10)</h3>
                        <p class="text-xs text-slate-500 mt-1">Periode: bulan ini</p>
                    </div>
                </div>

                @php
                    $heat = $dashboard['transferHeatmap'] ?? collect();
                    if (is_array($heat)) $heat = collect($heat);
                    $maxHeat = max(1, (int)($heat->max('count') ?? 1));
                @endphp

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="py-2">From</th>
                                <th class="py-2">To</th>
                                <th class="py-2 text-right">Transfers</th>
                                <th class="py-2 w-56">Activity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($heat as $t)
                                @php
                                    $count = (int)($t['count'] ?? 0);
                                    $pct = min(100, ($count / $maxHeat) * 100);
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 font-semibold text-slate-900">{{ $t['from'] ?? '-' }}</td>
                                    <td class="py-3 text-slate-700">{{ $t['to'] ?? '-' }}</td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                            {{ $count }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-500">Tidak ada data transfer</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Fast Moving Items</h3>
                    <p class="text-xs text-slate-500 mt-1">Item paling aktif bulan ini</p>
                </div>
                <div class="p-6 space-y-3">
                    @forelse(($dashboard['fastMovingItems'] ?? []) as $item)
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 hover:bg-blue-50 transition">
                            <div class="font-semibold text-slate-900 truncate pr-3">
                                {{ $item['name'] ?? '-' }}
                            </div>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                                {{ $item['qty'] ?? 0 }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 text-sm py-6">Tidak ada data</div>
                    @endforelse
                </div>
            </div>
        </div>



        {{-- ================= BRANCH PERFORMANCE ================= --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Branch Performance</h3>
                    <p class="text-xs text-slate-500 mt-1">Periode: bulan ini</p>
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="py-2">Branch</th>
                            <th class="py-2 text-right">Sales</th>
                            <th class="py-2 text-center">Orders</th>
                            <th class="py-2 text-center">Critical Items</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($dashboard['branchPerformance'] ?? []) as $b)
                            @php $warn = ($b['status'] ?? '') !== 'good'; @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-semibold text-slate-900">{{ $b['name'] ?? '-' }}</td>
                                <td class="py-3 text-right font-semibold text-slate-900">
                                    Rp {{ number_format($b['sales'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center">{{ $b['orders'] ?? 0 }}</td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold
                                        {{ ($b['critical_items'] ?? 0) > 5 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $b['critical_items'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold
                                        {{ $warn ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        <span class="h-2 w-2 rounded-full {{ $warn ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                                        {{ $warn ? 'Warning' : 'Good' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">Tidak ada data cabang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= RECENT ACTIVITIES ================= --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-xl transition">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Recent Activities</h3>
                <p class="text-xs text-slate-500 mt-1">Aktivitas terbaru (transfer & PO)</p>
            </div>

            <div class="p-6 space-y-3">
                @forelse(($dashboard['recentActivities'] ?? []) as $a)
                    @php
                        $isTransfer = ($a['type'] ?? '') === 'transfer';
                        $st = $a['status'] ?? '';
                        $badge = 'bg-slate-100 text-slate-700';
                        if (in_array($st, ['COMPLETED','DONE','PAID','RECEIVED'])) $badge='bg-emerald-100 text-emerald-800';
                        elseif (in_array($st, ['PENDING','REQUESTED','IN_TRANSIT'])) $badge='bg-amber-100 text-amber-800';
                    @endphp

                    <div class="flex items-start justify-between gap-4 rounded-2xl bg-slate-50 p-4 hover:bg-slate-100 transition">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-2xl flex items-center justify-center
                                {{ $isTransfer ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($isTransfer)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 17h10M10 7l-3 3m0 0l3 3M14 17l3-3m0 0l-3-3"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v4H3V3zm0 6h18v12H3V9zm4 3h10"/>
                                    @endif
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">{{ $a['note'] ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    @if($isTransfer)
                                        {{ $a['from'] ?? '-' }} → {{ $a['to'] ?? '-' }}
                                    @else
                                        {{ $a['branch'] ?? '-' }} - {{ $a['supplier'] ?? '-' }}
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-1 font-mono">
                                    {{ $a['reference'] ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">
                                {{ $st ?: '-' }}
                            </span>
                            <div class="text-xs text-slate-500 mt-2">{{ $a['time'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 text-sm py-6">Tidak ada aktivitas terbaru</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Print: hide actions --}}
<style>
@media print {
    button { display: none !important; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dashboard['chartSalesTrend']['labels'] ?? []) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($dashboard['chartSalesTrend']['data'] ?? []) !!},
                tension: 0.4,
                fill: true,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.12)',
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('branchSalesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($dashboard['branchSalesComparison'] ?? [])->pluck('branch')->toArray()) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode(collect($dashboard['branchSalesComparison'] ?? [])->pluck('sales')->toArray()) !!},
                backgroundColor: '#3B82F6'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('branchStockChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($dashboard['chartBranchStock']['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($dashboard['chartBranchStock']['data'] ?? []) !!},
                backgroundColor: {!! json_encode($dashboard['chartBranchStock']['colors'] ?? []) !!}
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
    });

    new Chart(document.getElementById('purchaseTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dashboard['chartPurchaseTrend']['labels'] ?? []) !!},
            datasets: [{
                label: 'PO',
                data: {!! json_encode($dashboard['chartPurchaseTrend']['data'] ?? []) !!},
                tension: 0.4,
                fill: true,
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('transferChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($dashboard['chartInterBranchTransfer']['labels'] ?? []) !!},
            datasets: [{
                label: 'Transfers',
                data: {!! json_encode($dashboard['chartInterBranchTransfer']['data'] ?? []) !!},
                backgroundColor: {!! json_encode($dashboard['chartInterBranchTransfer']['colors'] ?? []) !!}
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

});
</script>
</x-app-layout>
