<x-app-layout :branchCode="$branchCode">

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- =============================== --}}
        {{-- HEADER SECTION --}}
        {{-- =============================== --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 rounded-2xl shadow-2xl">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 h-48 w-48 rounded-full bg-white opacity-10"></div>
            <div class="absolute top-1/2 right-1/4 h-32 w-32 rounded-full bg-white opacity-5"></div>
            
            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    
                    {{-- Left Side --}}
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-xl border border-white/30">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">
                                Dashboard Cabang {{ $branchName }}
                            </h1>
                            <p class="text-emerald-100 text-base">
                                Monitoring stok, aktivitas, dan performa cabang secara real-time
                            </p>
                        </div>
                    </div>

                    {{-- Right Side --}}
                    <div class="flex flex-col items-start lg:items-end gap-3">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-xl border border-white/30 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            {{ $branchCode }}
                        </span>
                        <div class="flex items-center gap-2 text-emerald-100 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Update: {{ now()->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================== --}}
        {{-- KPI CARDS ROW 1 --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <x-kpi-card 
                title="Total Item Aktif" 
                value="{{ $totalItemsInBranch }}"
                iconBg="bg-gradient-to-br from-emerald-100 to-green-100"
                iconColor="text-emerald-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>' 
            />

            <x-kpi-card 
                title="Request Masuk (bulan ini)" 
                value="{{ $incomingRequests }}"
                iconBg="bg-gradient-to-br from-blue-100 to-indigo-100"
                iconColor="text-blue-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16l-4-4m0 0l4-4m-4 4h18"/>'
            />

            <x-kpi-card 
                title="Request Keluar (bulan ini)" 
                value="{{ $outgoingRequests }}"
                iconBg="bg-gradient-to-br from-purple-100 to-pink-100"
                iconColor="text-purple-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8l4 4m0 0l-4 4m4-4H3"/>'
            />

        </div>

        {{-- =============================== --}}
        {{-- KPI CARDS ROW 2 --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <x-kpi-card 
                title="Purchase Order (bulan ini)" 
                value="{{ $purchaseOrders }}"
                iconBg="bg-gradient-to-br from-green-100 to-emerald-100"
                iconColor="text-green-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' 
            />

            <x-kpi-card 
                title="Penerimaan Barang (bulan ini)" 
                value="{{ $receivedGoods }}"
                iconBg="bg-gradient-to-br from-cyan-100 to-sky-100"
                iconColor="text-cyan-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5v0m0 0h2m2 0h2m-6 9l2 2 4-4"/>' 
            />

            <x-kpi-card 
                title="Item Low Stock" 
                value="{{ $lowStockItems }}"
                iconBg="bg-gradient-to-br from-amber-100 to-orange-100"
                iconColor="text-amber-600"
                svg='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M5.062 21h13.876c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 18c-.77 1.333.192 3 1.732 3z"/>' 
            />

        </div>


        {{-- =============================== --}}
        {{-- TABLE ROW --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ================= TOP 10 ITEMS ================= --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Top 10 Item Terlaris</h2>
                        <p class="text-xs text-gray-500">Berdasarkan volume transaksi</p>
                    </div>
                </div>

                @if(count($topItems) > 0)
                    <div class="space-y-3">
                        @foreach($topItems as $index => $item)
                            <div class="group relative bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">
                                                {{ $item['name'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-emerald-600 group-hover:scale-110 transition-transform">
                                            {{ number_format($item['qty'], 0) }}
                                        </p>
                                        <p class="text-xs text-gray-500">unit</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada data transaksi</p>
                    </div>
                @endif
            </div>

            {{-- ================= RECENT ACTIVITY ================= --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Aktivitas Terkini</h2>
                        <p class="text-xs text-gray-500">Transaksi stok terbaru</p>
                    </div>
                </div>

                @if(count($recentActivities) > 0)
                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($recentActivities as $activity)
                            <div class="group bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start gap-3">
                                    {{-- ICON --}}
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $activity['type'] === 'in' ? 'bg-gradient-to-br from-emerald-100 to-green-100' : 'bg-gradient-to-br from-blue-100 to-indigo-100' }} flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                        @if($activity['type'] === 'in')
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- TEXT --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 font-semibold group-hover:text-blue-600 transition-colors">
                                            {{ $activity['note'] }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="text-xs text-gray-600 font-medium">{{ $activity['from'] ?? '-' }}</span>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                            </svg>
                                            <span class="text-xs text-gray-600 font-medium">{{ $activity['to'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-2">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs text-gray-400">{{ $activity['time'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada aktivitas stok</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>


<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

</x-app-layout>