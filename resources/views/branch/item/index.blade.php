<x-app-layout :branchCode="$branchCode">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- ===============================
            HEADER
        =============================== --}}
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        Daftar Item
                    </h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </span>
                        <span class="font-medium">Kelola dan pantau stok item di cabang</span>
                    </p>
                </div>
                
                <x-crud-add 
                    resource="branch.item"
                    :companyCode="$companyCode"
                    permissionPrefix="item"
                />
            </div>
        </div>

        @php
            $expiredCount = $items->filter(fn($i) => !is_null($i->days_to_expire) && $i->days_to_expire <= 7)->count();
            $lowStockCount = $items->filter(fn($i) => $i->is_low_stock)->count();
            $nearStockCount = $items->filter(fn($i) => $i->is_near_low_stock)->count();
            $totalItems = $items->count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            
            {{-- Total Items --}}
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl opacity-20 group-hover:opacity-30 blur transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-blue-50 via-blue-50 to-blue-100 rounded-3xl p-6 border border-blue-200/50 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="px-3 py-1.5 bg-blue-200/60 rounded-full">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Total Item</p>
                    <p class="text-4xl font-black text-blue-900 mb-1">{{ $totalItems }}</p>
                    <div class="h-1 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                </div>
            </div>

            {{-- Hampir Expired --}}
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-red-500 to-red-600 rounded-3xl opacity-20 group-hover:opacity-30 blur transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-red-50 via-red-50 to-red-100 rounded-3xl p-6 border border-red-200/50 shadow-lg hover:shadow-xl transition-all duration-300 {{ $expiredCount ? '' : 'opacity-60' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        @if($expiredCount)
                            <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-black rounded-full animate-pulse shadow-md">!</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-red-700 uppercase tracking-wider mb-2">Hampir Kadaluarsa</p>
                    <p class="text-4xl font-black text-red-900 mb-1">{{ $expiredCount }}</p>
                    <div class="flex items-center gap-2">
                        <div class="h-1 w-16 bg-gradient-to-r from-red-500 to-red-600 rounded-full"></div>
                        <span class="text-xs font-bold text-red-700">≤ 7 hari</span>
                    </div>
                </div>
            </div>

            {{-- Mendekati Minimum --}}
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-3xl opacity-20 group-hover:opacity-30 blur transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-yellow-50 via-yellow-50 to-yellow-100 rounded-3xl p-6 border border-yellow-200/50 shadow-lg hover:shadow-xl transition-all duration-300 {{ $nearStockCount ? '' : 'opacity-60' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        @if($nearStockCount)
                            <span class="px-3 py-1.5 bg-yellow-600 text-white text-xs font-black rounded-full shadow-md">⚠</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-yellow-700 uppercase tracking-wider mb-2">Mendekati Minimum</p>
                    <p class="text-4xl font-black text-yellow-900 mb-1">{{ $nearStockCount }}</p>
                    <div class="flex items-center gap-2">
                        <div class="h-1 w-16 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full"></div>
                        <span class="text-xs font-bold text-yellow-700">Perlu pantau</span>
                    </div>
                </div>
            </div>

            {{-- Stok Kritis --}}
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-rose-500 to-rose-600 rounded-3xl opacity-20 group-hover:opacity-30 blur transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-rose-50 via-rose-50 to-rose-100 rounded-3xl p-6 border border-rose-200/50 shadow-lg hover:shadow-xl transition-all duration-300 {{ $lowStockCount ? '' : 'opacity-60' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        @if($lowStockCount)
                            <span class="px-3 py-1.5 bg-rose-600 text-white text-xs font-black rounded-full animate-pulse shadow-md">!!</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-2">Stok Kritis</p>
                    <p class="text-4xl font-black text-rose-900 mb-1">{{ $lowStockCount }}</p>
                    <div class="flex items-center gap-2">
                        <div class="h-1 w-16 bg-gradient-to-r from-rose-500 to-rose-600 rounded-full"></div>
                        <span class="text-xs font-bold text-rose-700">Segera isi</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===============================
            TABLE
        =============================== --}}
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900">
                            <th class="px-6 py-5 text-left">
                                <div class="flex items-center gap-2.5 text-xs font-black text-white uppercase tracking-wider">
                                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    Item
                                </div>
                            </th>
                            <th class="px-6 py-5 text-left">
                                <div class="flex items-center gap-2.5 text-xs font-black text-white uppercase tracking-wider">
                                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    Status Stok
                                </div>
                            </th>
                            <th class="px-6 py-5 text-center">
                                <div class="flex items-center justify-center gap-2.5 text-xs font-black text-white uppercase tracking-wider">
                                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                    </div>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-100">
                    
                    @forelse ($items as $item)
                    <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent transition-all duration-200 group">
                        
                        {{-- ITEM INFO --}}
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="h-16 w-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-xl group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    </div>
                                    @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                        <span class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white text-xs font-black animate-pulse shadow-lg border-2 border-white">!</span>
                                    @endif
                                </div>
                                
                                <div>
                                    <div class="font-bold text-gray-900 text-lg mb-1">{{ $item->name }}</div>
                                    
                                    @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl text-xs font-bold shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kadaluarsa {{ ceil($item->days_to_expire) }} hari lagi
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        {{-- STOK STATUS --}}
                        <td class="px-6 py-6">
                            <div class="space-y-3">
                                
                                {{-- BADGE STOK --}}
                                <div>
                                    <span class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-2xl text-sm font-black shadow-lg
                                        {{ $item->is_low_stock 
                                            ? 'bg-gradient-to-r from-red-500 via-red-600 to-red-700 text-white' 
                                            : ($item->is_near_low_stock 
                                                ? 'bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 text-white' 
                                                : 'bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 text-white') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Stok: {{ $item->total_qty ?? 0 }}
                                    </span>
                                </div>
                                
                                {{-- WARNING + FORECAST --}}
                                @if ($item->forecast_enabled && ($item->is_low_stock || $item->is_near_low_stock))
                                <div class="rounded-2xl p-4 border-l-4 shadow-lg
                                    {{ $item->is_low_stock 
                                        ? 'bg-gradient-to-r from-red-50 via-red-50 to-red-100 border-red-500' 
                                        : 'bg-gradient-to-r from-yellow-50 via-yellow-50 to-yellow-100 border-yellow-500' }}">
                                    
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 {{ $item->is_low_stock ? 'bg-red-500' : 'bg-yellow-500' }} rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="font-black text-sm {{ $item->is_low_stock ? 'text-red-900' : 'text-yellow-900' }}">
                                            {{ $item->is_low_stock ? 'Stok di Bawah Minimum' : 'Stok Mendekati Minimum' }}
                                        </div>
                                    </div>
                                    
                                    <div class="ml-13 space-y-2 text-xs">
                                        <div class="flex items-center gap-2 text-gray-800">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            Prediksi pemakaian: <span class="font-black text-gray-900">{{ $item->predicted_usage }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-blue-800">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            Rekomendasi restock: <span class="font-black text-blue-900">{{ $item->recommended_restock }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @elseif (!$item->forecast_enabled && ($item->is_low_stock || $item->is_near_low_stock))
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-xl text-xs text-gray-600 font-medium border border-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Forecast tidak diaktifkan
                                </div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- AKSI --}}
                        <td class="px-6 py-6">
                            <div class="flex justify-center gap-2 flex-wrap">
                                
                                {{-- DETAIL --}}
                                <a href="{{ route('branch.item.show', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:scale-105 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                
                                {{-- RIWAYAT --}}
                                <a href="{{ route('branch.item.history', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-blue-700 bg-blue-50 border-2 border-blue-300 rounded-xl hover:bg-blue-100 hover:border-blue-400 hover:scale-105 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Riwayat
                                </a>
                                
                                {{-- REQUEST CABANG --}}
                                @if ($item->is_low_stock || $item->is_near_low_stock)
                                    <a href="{{ route('branch.request.create', [
                                            $branchCode,
                                            'item_id' => $item->id
                                        ]) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black rounded-xl shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200
                                        {{ $item->is_low_stock 
                                            ? 'bg-gradient-to-r from-red-600 via-red-700 to-red-800 text-white hover:from-red-700 hover:via-red-800 hover:to-red-900' 
                                            : 'bg-gradient-to-r from-yellow-500 via-yellow-600 to-yellow-700 text-white hover:from-yellow-600 hover:via-yellow-700 hover:to-yellow-800' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Request Cabang
                                    </a>
                                @endif
                                
                                {{-- CRUD --}}
                                <x-crud 
                                    resource="branch.item"
                                    keyField="id"
                                    :companyCode="$branchCode"
                                    :model="$item"
                                    permissionPrefix="item"
                                />
                            </div>
                        </td>
                    
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl mb-6 shadow-lg">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-bold text-lg mb-2">Belum ada item</p>
                                <p class="text-sm text-gray-500">Mulai tambahkan item untuk mengelola stok</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>