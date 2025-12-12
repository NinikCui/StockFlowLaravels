<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- =============================== --}}
    {{-- HEADER SECTION --}}
    {{-- =============================== --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Item</h1>
                <p class="mt-2 text-sm text-gray-600">Kelola dan pantau stok item di cabang Anda</p>
            </div>

            {{-- Tombol Tambah Item --}}
            <x-crud-add 
                resource="branch.item"
                :companyCode="$companyCode"
                permissionPrefix="item"
            />
        </div>
    </div>

    {{-- Hitung berapa item hampir expired dan stok rendah --}}
    @php
        $expiredCount = $items->filter(fn($i) => !is_null($i->days_to_expire) && $i->days_to_expire <= 7)->count();
        $lowStockCount = $items->filter(fn($i) => ($i->total_qty ?? 0) < $i->min_stock)->count();
    @endphp

    {{-- =============================== --}}
    {{-- STATS CARDS --}}
    {{-- =============================== --}}
    @if ($expiredCount > 0 || $lowStockCount > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Alert Hampir Expired --}}
        @if ($expiredCount > 0)
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-red-800">Item Hampir Kadaluarsa</h3>
                    <p class="mt-1 text-sm text-red-700">
                        <span class="font-bold text-lg">{{ $expiredCount }}</span> item akan segera kadaluarsa dalam 7 hari ke depan
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Alert Stok Rendah --}}
        @if ($lowStockCount > 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-amber-800">Stok Rendah</h3>
                    <p class="mt-1 text-sm text-amber-700">
                        <span class="font-bold text-lg">{{ $lowStockCount }}</span> item memiliki stok di bawah minimum
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- =============================== --}}
    {{-- TABLE CARD --}}
    {{-- =============================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Semua Item</h2>

                <div class="flex items-center gap-3">
                    {{-- TOTAL ITEM --}}
                    <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-lg shadow-sm">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        {{ $items->count() }} Item
                    </span>
                </div>
            </div>
        </div>

        {{-- =============================== --}}
        {{-- TABLE --}}
        {{-- =============================== --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Informasi Item
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Status Stok
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition-all duration-200">

                        {{-- =================================== --}}
                        {{-- NAMA ITEM + WARNING EXPIRED --}}
                        {{-- =================================== --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-base">
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    </span>
                                </div>

                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $item->name }}
                                    </div>

                                    {{-- ⭐ WARNING KADALUARSA --}}
                                    @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                        @php
                                            if ($item->days_to_expire <= 2) {
                                                $bgColor = 'bg-red-100';
                                                $borderColor = 'border-red-300';
                                                $textColor = 'text-red-800';
                                                $iconColor = 'text-red-600';
                                            } else {
                                                $bgColor = 'bg-yellow-100';
                                                $borderColor = 'border-yellow-300';
                                                $textColor = 'text-yellow-800';
                                                $iconColor = 'text-yellow-600';
                                            }
                                        @endphp

                                        <div class="mt-2 flex items-center text-xs {{ $textColor }} {{ $bgColor }} 
                                                    border {{ $borderColor }} px-2.5 py-1.5 rounded-lg shadow-sm w-fit">

                                            <svg class="h-4 w-4 mr-1.5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>

                                            <span class="font-medium">
                                                Kadaluarsa dalam <strong>{{ ceil($item->days_to_expire) }}</strong> hari
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- =================================== --}}
                        {{-- STOK + FORECAST --}}
                        {{-- =================================== --}}
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                {{-- BADGE STOK --}}
                                <div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold shadow-sm
                                        {{ ($item->total_qty ?? 0) >= $item->min_stock 
                                            ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' 
                                            : 'bg-red-100 text-red-800 border border-red-200' }}">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Stok: {{ $item->total_qty ?? 0 }} unit
                                    </span>
                                </div>

                                {{-- WARNING FORECAST AKTIF --}}
                                @if ($item->forecast_enabled == 1 && ($item->total_qty ?? 0) < $item->min_stock)
                                    <div class="bg-gradient-to-br from-red-50 to-orange-50 border-l-4 border-red-500 rounded-lg p-3 text-xs space-y-2 shadow-sm">
                                        <div class="flex items-center text-red-800 font-semibold">
                                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Stok di Bawah Minimum
                                        </div>

                                        <div class="grid grid-cols-1 gap-1.5 pl-5">
                                            <div class="text-gray-700">
                                                <span class="text-gray-600">Prediksi pemakaian:</span> 
                                                <span class="font-bold text-gray-900">{{ $item->predicted_usage }} unit</span>
                                            </div>

                                            <div class="text-blue-700">
                                                <span class="text-blue-600">Rekomendasi restock:</span> 
                                                <span class="font-bold text-blue-900">{{ $item->recommended_restock }} unit</span>
                                            </div>
                                        </div>
                                    </div>

                                {{-- FORECAST NON-AKTIF --}}
                                @elseif ($item->forecast_enabled == 0 && ($item->total_qty ?? 0) < $item->min_stock)
                                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 text-xs shadow-sm">
                                        <div class="flex items-center text-red-800 font-semibold">
                                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Stok di Bawah Minimum
                                        </div>

                                        <div class="text-gray-600 italic mt-1.5 pl-5">
                                            Forecast tidak diaktifkan
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- =================================== --}}
                        {{-- ACTION BUTTONS --}}
                        {{-- =================================== --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">

                                {{-- DETAIL --}}
                                <a href="{{ route('branch.item.show', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-xs font-semibold rounded-lg 
                                          text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 
                                          transition-all duration-200 shadow-sm hover:shadow">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>

                                {{-- HISTORY --}}
                                <a href="{{ route('branch.item.history', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center px-3 py-2 border border-blue-300 text-xs font-semibold rounded-lg
                                          text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-400
                                          transition-all duration-200 shadow-sm hover:shadow">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Riwayat
                                </a>

                                {{-- CRUD BUTTONS --}}
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
                        <td colspan="3" class="px-6 py-16">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-900">Belum Ada Item</p>
                                <p class="mt-2 text-sm text-gray-500">Mulai dengan menambahkan item pertama Anda</p>
                                <div class="mt-6">
                                    <x-crud-add 
                                        resource="branch.item"
                                        :companyCode="$companyCode"
                                        permissionPrefix="item"
                                    />
                                </div>
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