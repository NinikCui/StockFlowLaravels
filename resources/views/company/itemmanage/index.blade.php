<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ===============================
            HEADER
        =============================== --}}
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        Daftar Item (Company)
                    </h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </span>
                        <span class="font-medium">
                            Pantau stok item lintas seluruh cabang
                        </span>
                    </p>
                </div>

                {{-- ADD ITEM --}}
                <x-crud-add
                    resource="item"
                                            :companyCode="$companyCode"

                    permissionPrefix="item"
                />
            </div>
        </div>

        {{-- ===============================
            FILTER CABANG (KHUSUS COMPANY)
        =============================== --}}
        <form method="GET" class="mb-8">
            <div class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-2xl shadow border">
                <div class="text-sm font-bold text-gray-700">Filter Cabang</div>

                <select name="branch_id"
                    class="rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}"
                            @selected($selectedBranch == $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>

                <button
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                    Terapkan
                </button>
            </div>
        </form>

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
                            <th class="px-6 py-5 text-left text-xs font-black text-white uppercase">Item</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-white uppercase">Status Stok</th>
                            <th class="px-6 py-5 text-center text-xs font-black text-white uppercase">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $item)
                            <tr class="hover:bg-gray-50 transition group">
                                {{-- ITEM --}}
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-16 w-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center text-white font-black text-xl">
                                            {{ strtoupper(substr($item->name, 0, 2)) }}
                                        </div>

                                        <div>
                                            <div class="font-bold text-gray-900 text-lg">
                                                {{ $item->name }}
                                            </div>

                                            @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                                <div
                                                    class="inline-flex items-center gap-2 px-3 py-1 mt-1 bg-red-600 text-white rounded-xl text-xs font-bold">
                                                    Kadaluarsa {{ ceil($item->days_to_expire) }} hari
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-6">
                                    <span
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl text-sm font-black
                                        {{ $item->is_low_stock
                                            ? 'bg-red-600 text-white'
                                            : ($item->is_near_low_stock
                                                ? 'bg-yellow-500 text-white'
                                                : 'bg-emerald-600 text-white') }}">
                                        Stok: {{ $item->total_qty ?? 0 }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-6">
                                    <div class="flex justify-center gap-2">
                                        {{-- DETAIL 
                                        <a href="{{ route('itemmanage.show', $item->id) }}"
                                            class="px-4 py-2 text-xs font-bold bg-gray-100 rounded-xl hover:bg-gray-200">
                                            Detail
                                        </a>--}}

                                        {{-- RIWAYAT --}}
                                        <a href="{{ route('itemmanage.history', [$companyCode, $item->id]) }}"
                                            class="px-4 py-2 text-xs font-bold bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200">
                                            Riwayat
                                        </a>


                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-20 text-center text-gray-500 font-bold">
                                    Belum ada item
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
