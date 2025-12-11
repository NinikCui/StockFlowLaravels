<x-app-layout :companyCode="$companyCode">

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- =============================== --}}
        {{-- HEADER SECTION --}}
        {{-- =============================== --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-green-600 to-lime-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-40 w-40 rounded-full bg-white opacity-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold">Dashboard Perusahaan</h1>
                <p class="text-blue-100 mt-2 text-lg">Ringkasan aktivitas operasional seluruh cabang</p>
            </div>
        </div>

        {{-- =============================== --}}
        {{-- KPI TOP CARDS --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Total Cabang --}}
            <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 rounded-xl group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 group-hover:text-white/80 transition-colors duration-300 font-medium">Total Cabang</p>
                    <h2 class="text-4xl font-bold mt-2 text-gray-900 group-hover:text-white transition-colors duration-300">{{ $totalBranches }}</h2>
                </div>
            </div>

            {{-- Supplier --}}
            <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 rounded-xl group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-emerald-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 group-hover:text-white/80 transition-colors duration-300 font-medium">Supplier Terdaftar</p>
                    <h2 class="text-4xl font-bold mt-2 text-gray-900 group-hover:text-white transition-colors duration-300">{{ $totalSuppliers }}</h2>
                </div>
            </div>

            {{-- Items --}}
            <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 rounded-xl group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 group-hover:text-white/80 transition-colors duration-300 font-medium">Item</p>
                    <h2 class="text-4xl font-bold mt-2 text-gray-900 group-hover:text-white transition-colors duration-300">{{ $totalItems }}</h2>
                </div>
            </div>

            {{-- Karyawan --}}
            <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-orange-100 rounded-xl group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-orange-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 group-hover:text-white/80 transition-colors duration-300 font-medium">Karyawan</p>
                    <h2 class="text-4xl font-bold mt-2 text-gray-900 group-hover:text-white transition-colors duration-300">{{ $totalEmployees }}</h2>
                </div>
            </div>

        </div>

        {{-- =============================== --}}
        {{-- MONTHLY ACTIVITIES --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="relative bg-white rounded-2xl shadow-lg p-6 overflow-hidden border-l-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-blue-50"></div>
                <div class="relative">
                    <p class="text-gray-600 text-sm font-medium mb-2">Request Bulan Ini</p>
                    <h2 class="text-5xl font-bold text-blue-600">{{ $requestMonth }}</h2>
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Periode: Bulan Ini</span>
                    </div>
                </div>
            </div>

            <div class="relative bg-white rounded-2xl shadow-lg p-6 overflow-hidden border-l-4 border-emerald-500 hover:shadow-xl transition-shadow duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-emerald-50"></div>
                <div class="relative">
                    <p class="text-gray-600 text-sm font-medium mb-2">Purchase Order Bulan Ini</p>
                    <h2 class="text-5xl font-bold text-emerald-600">{{ $poMonth }}</h2>
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Periode: Bulan Ini</span>
                    </div>
                </div>
            </div>

            <div class="relative bg-white rounded-2xl shadow-lg p-6 overflow-hidden border-l-4 border-indigo-500 hover:shadow-xl transition-shadow duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-indigo-50"></div>
                <div class="relative">
                    <p class="text-gray-600 text-sm font-medium mb-2">Barang Diterima Bulan Ini</p>
                    <h2 class="text-5xl font-bold text-indigo-600">{{ $receivedMonth }}</h2>
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Periode: Bulan Ini</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- =============================== --}}
        {{-- TREND CHART --}}
        {{-- =============================== --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Trend Request dan PO</h2>
                    <p class="text-gray-500 text-sm mt-1">Data 12 bulan terakhir</p>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        <span class="text-gray-600">Request</span>
                    </div>
                    <div class="flex items-center ml-4">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></span>
                        <span class="text-gray-600">PO</span>
                    </div>
                </div>
            </div>
            <div class="relative">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>

        {{-- =============================== --}}
        {{-- HEATMAP TRANSFER ANTAR CABANG --}}
        {{-- =============================== --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Heatmap Transfer Antar Cabang</h2>
                <p class="text-gray-500 text-sm mt-1">Visualisasi perpindahan barang antar cabang</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 sticky left-0 bg-gray-100">From \ To</th>
                            @foreach($heatmap as $from => $row)
                                <th class="px-4 py-3 text-center font-semibold text-gray-700">{{ $from }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($heatmap as $from => $row)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 font-semibold text-gray-700 sticky left-0 bg-white border-r border-gray-200">{{ $from }}</td>

                                @foreach($row as $to => $value)
                                    <td class="px-4 py-3 text-center font-medium transition-all duration-200
                                        {{ $value > 10 ? 'bg-red-100 text-red-700 hover:bg-red-200' : 
                                           ($value > 3 ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 
                                           'bg-green-50 text-green-700 hover:bg-green-100') }}">
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-end space-x-4 text-xs">
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-green-50 border border-green-200 rounded mr-2"></span>
                    <span class="text-gray-600">Low (≤3)</span>
                </div>
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-yellow-100 border border-yellow-200 rounded mr-2"></span>
                    <span class="text-gray-600">Medium (4-10)</span>
                </div>
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-red-100 border border-red-200 rounded mr-2"></span>
                    <span class="text-gray-600">High (>10)</span>
                </div>
            </div>
        </div>

        {{-- =============================== --}}
        {{-- FAST MOVING ITEMS --}}
        {{-- =============================== --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Fast Moving Items</h2>
                <p class="text-gray-500 text-sm mt-1">10 barang paling aktif</p>
            </div>

            <div class="space-y-3">
                @foreach ($fastItems as $index => $fi)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-blue-50 hover:to-white transition-all duration-200 border border-gray-100 hover:border-blue-200 hover:shadow-md group">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-200">
                                {{ $index + 1 }}
                            </div>
                            <span class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-200">{{ $fi->item->name }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">{{ number_format($fi->total, 2) }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =============================== --}}
        {{-- LATEST REQUEST / PO / RECEIVE --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- REQUEST --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Latest Request</h2>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach ($latestRequest as $req)
                        <div class="p-4 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200 border border-gray-100 hover:border-blue-200">
                            <div class="flex items-center text-sm mb-2">
                                <span class="font-semibold text-gray-900">{{ $req->cabangFrom->name }}</span>
                                <svg class="w-4 h-4 mx-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                <span class="font-semibold text-gray-900">{{ $req->cabangTo->name }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="font-mono bg-white px-2 py-1 rounded">#{{ $req->id }}</span>
                                <span>{{ $req->trans_date }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PO --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Latest PO</h2>
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach ($latestPO as $po)
                        <div class="p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 transition-colors duration-200 border border-gray-100 hover:border-emerald-200">
                            <p class="font-semibold text-gray-900 mb-2">{{ $po->cabangResto->name }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <span class="font-mono bg-white px-2 py-1 rounded">PO #{{ $po->po_number }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RECEIVE --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Latest Receive</h2>
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach ($latestReceive as $rc)
                        <div class="p-4 rounded-xl bg-gray-50 hover:bg-indigo-50 transition-colors duration-200 border border-gray-100 hover:border-indigo-200">
                            <p class="font-semibold text-gray-900 mb-2">{{ $rc->purchaseOrder->cabangResto->name }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <span class="font-mono bg-white px-2 py-1 rounded">Receive #{{ $rc->id }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>

{{-- =============================== --}}
{{-- CHART.JS --}}
{{-- =============================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('trendChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($requestTrend->keys()) !!},
            datasets: [
                {
                    label: 'Request',
                    data: {!! json_encode($requestTrend->values()) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Purchase Order',
                    data: {!! json_encode($poTrend->values()) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>

</x-app-layout>