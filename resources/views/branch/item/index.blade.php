<x-app-layout :branchCode="$branchCode">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- ===============================
            HEADER
        =============================== --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Daftar Item
                    </h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <span class="w-7 h-7 bg-emerald-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </span>
                        Kelola dan pantau stok item di cabang
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

        {{-- ===============================
            STATISTICS CARDS
        =============================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            {{-- Total Items --}}
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Total Item</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalItems }}</p>
            </div>

            {{-- Hampir Expired --}}
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow {{ $expiredCount ? 'ring-2 ring-red-100' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    @if($expiredCount)
                        <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">!</span>
                    @endif
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Hampir Kadaluarsa</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $expiredCount }}</p>
                    <span class="text-xs font-medium text-red-600">≤ 7 hari</span>
                </div>
            </div>

            {{-- Mendekati Minimum --}}
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow {{ $nearStockCount ? 'ring-2 ring-yellow-100' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    @if($nearStockCount)
                        <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">⚠</span>
                    @endif
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Mendekati Minimum</p>
                <p class="text-3xl font-bold text-gray-900">{{ $nearStockCount }}</p>
            </div>

            {{-- Stok Kritis --}}
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow {{ $lowStockCount ? 'ring-2 ring-rose-100' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    @if($lowStockCount)
                        <span class="px-2 py-1 bg-rose-500 text-white text-xs font-bold rounded-full animate-pulse">!!</span>
                    @endif
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Stok Kritis</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $lowStockCount }}</p>
                    <span class="text-xs font-medium text-rose-600">Segera isi</span>
                </div>
            </div>

        </div>

        {{-- ===============================
            TABLE
        =============================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left">
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Item
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Status Stok
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-100">
                    
                    @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        {{-- ITEM INFO --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <div class="h-14 w-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    </div>
                                    @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white">!</span>
                                    @endif
                                </div>
                                
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-gray-900 text-base mb-1 truncate">{{ $item->name }}</div>
                                    
                                    @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kadaluarsa {{ ceil($item->days_to_expire) }} hari lagi
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        {{-- STOK STATUS --}}
                        <td class="px-6 py-4">
                            <div class="space-y-2.5">
                                
                                {{-- BADGE STOK --}}
                                <div class="inline-flex items-center gap-2 relative group">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold shadow-sm
                                        {{ $item->is_low_stock 
                                            ? 'bg-red-100 text-red-700 ring-1 ring-red-200' 
                                            : ($item->is_near_low_stock 
                                                ? 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200' 
                                                : 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Stok: 
                                        {{
                                            ($item->total_qty ?? 0) == floor($item->total_qty ?? 0)
                                                ? number_format($item->total_qty ?? 0, 0, ',', '.')
                                                : number_format($item->total_qty ?? 0, 2, ',', '.')
                                        }}
                                        {{ $item->satuan->code ?? '' }}
                                    </span>

                                    {{-- TOOLTIP KONVERSI --}}
                                    @if (
                                        isset($unitConversions[$item->satuan_id]) &&
                                        $unitConversions[$item->satuan_id]->count()
                                    )
                                        <span class="w-4 h-4 text-xs font-semibold flex items-center justify-center rounded-full bg-gray-200 text-gray-600 cursor-help hover:bg-gray-300 transition-colors">
                                            ?
                                        </span>

                                        <div class="absolute left-0 top-full mt-2 w-56 hidden group-hover:block bg-white border border-gray-200 shadow-lg rounded-lg p-3 text-xs text-gray-700 z-50">
                                            <div class="font-semibold text-gray-900 mb-2">
                                                Konversi Satuan
                                            </div>
                                            <ul class="space-y-1.5">
                                                @foreach ($unitConversions[$item->satuan_id] as $conv)
                                                    <li class="flex items-center gap-1.5">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                        </svg>
                                                        <span class="font-medium">
                                                            {{
                                                                (($item->total_qty ?? 0) * $conv->factor)
                                                                    == floor(($item->total_qty ?? 0) * $conv->factor)
                                                                        ? number_format(($item->total_qty ?? 0) * $conv->factor, 0, ',', '.')
                                                                        : number_format(($item->total_qty ?? 0) * $conv->factor, 3, ',', '.')
                                                            }}
                                                            {{ $conv->toSatuan->code }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- WARNING + FORECAST --}}
                                @if ($item->forecast_enabled && ($item->is_low_stock || $item->is_near_low_stock))
                                <div class="rounded-lg p-3 border-l-3
                                    {{ $item->is_low_stock 
                                        ? 'bg-red-50 border-red-400' 
                                        : 'bg-yellow-50 border-yellow-400' }}">
                                    
                                    <div class="flex items-start gap-2.5 mb-2">
                                        <div class="w-8 h-8 {{ $item->is_low_stock ? 'bg-red-500' : 'bg-yellow-500' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="font-semibold text-sm {{ $item->is_low_stock ? 'text-red-800' : 'text-yellow-800' }}">
                                            {{ $item->is_low_stock ? 'Stok di Bawah Minimum' : 'Stok Mendekati Minimum' }}
                                        </div>
                                    </div>
                                    
                                    <div class="ml-10 space-y-1.5 text-xs">
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <circle cx="10" cy="10" r="3"/>
                                            </svg>
                                            Prediksi pemakaian: <span class="font-semibold text-gray-900">{{ $item->predicted_usage }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-blue-700">
                                            <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <circle cx="10" cy="10" r="3"/>
                                            </svg>
                                            Rekomendasi restock: <span class="font-semibold text-blue-900">{{ $item->recommended_restock }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @elseif (!$item->forecast_enabled && ($item->is_low_stock || $item->is_near_low_stock))
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg text-xs text-gray-600 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Forecast tidak diaktifkan
                                </div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- AKSI --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2 flex-wrap">
                                
                                {{-- DETAIL --}}
                                <a href="{{ route('branch.item.show', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                
                                {{-- RIWAYAT --}}
                                <a href="{{ route('branch.item.history', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors">
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
                                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg transition-colors
                                        {{ $item->is_low_stock 
                                            ? 'bg-red-600 text-white hover:bg-red-700' 
                                            : 'bg-yellow-500 text-white hover:bg-yellow-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Request
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
                        <td colspan="3" class="py-16">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="text-gray-900 font-semibold text-base mb-1">Belum ada item</p>
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