<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Item</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan pantau stok item di cabang Anda</p>
            </div>

            <x-crud-add 
                resource="branch.item"
                :companyCode="$companyCode"
                permissionPrefix="item"
            />
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Item</h2>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-full">
                    {{ $items->count() }} item
                </span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nama Item
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Stok & Forecast
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">

                        <!-- NAMA ITEM -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- STOK + FORECAST -->
                        <td class="px-6 py-4">
                            {{-- BADGE STOK --}}
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium
                                    {{ ($item->total_qty ?? 0) >= $item->min_stock 
                                        ? 'bg-emerald-100 text-emerald-800' 
                                        : 'bg-red-100 text-red-800' }}">
                                    Stok: {{ $item->total_qty ?? 0 }} unit
                                </span>
                            </div>

                            {{-- WARNING FORECAST AKTIF --}}
                            @if ($item->forecast_enabled == 1 && ($item->total_qty ?? 0) < $item->min_stock)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-2.5 text-xs space-y-1">
                                    <div class="flex items-center text-red-700 font-medium">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01"/>
                                        </svg>
                                        Stok di bawah minimal
                                    </div>
                                    <div class="text-gray-700">
                                        Prediksi pemakaian (ES): <span class="font-semibold">{{ $item->predicted_usage }}</span> unit
                                    </div>
                                    <div class="text-blue-700 font-medium">
                                        Rekomendasi restock: <span class="font-semibold">{{ $item->recommended_restock }}</span> unit
                                    </div>
                                </div>

                            {{-- WARNING FORECAST NONAKTIF --}}
                            @elseif ($item->forecast_enabled == 0 && ($item->total_qty ?? 0) < $item->min_stock)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-2.5 text-xs">
                                    <div class="flex items-center text-red-700 font-medium">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01"/>
                                        </svg>
                                        Stok di bawah minimal
                                    </div>
                                    <div class="text-gray-600 italic mt-1">
                                        Forecast tidak diaktifkan untuk item ini
                                    </div>
                                </div>
                            @endif
                        </td>

                        <!-- ACTION BUTTONS -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('branch.item.show', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md 
                                          text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>

                                <a href="{{ route('branch.item.history', [$branchCode, $item->id]) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-xs font-medium rounded-md
                                          text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Riwayat
                                </a>

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
                        <td colspan="3" class="px-6 py-12">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-900">Belum ada item yang terdaftar</p>
                                <p class="mt-1 text-xs text-gray-500">Klik tombol "Tambah Item" untuk menambahkan item baru</p>
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