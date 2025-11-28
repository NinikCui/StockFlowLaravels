<x-app-layout>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- PAGE HEADER --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Supplier</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola pemasok dan pantau performanya secara real-time</p>
            </div>

            <x-crud-add 
                        resource="supplier"
                        :companyCode="$companyCode"
                        permissionPrefix="supplier"
                    />
        </div>
    </div>

    {{-- FILTERS SECTION --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Filter & Sorting</h3>
        </div>
        
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                
                {{-- FILTER ITEM --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                    <select name="item_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="">Semua Item</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FILTER PERFORMANCE --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Performa</label>
                    <select name="performance" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="">Semua Performa</option>
                        <option value="good" {{ request('performance') == 'good' ? 'selected' : '' }}>⭐ Good (≥90%)</option>
                        <option value="average" {{ request('performance') == 'average' ? 'selected' : '' }}>⚡ Average (70-89%)</option>
                        <option value="poor" {{ request('performance') == 'poor' ? 'selected' : '' }}>❗ Poor (<70%)</option>
                    </select>
                </div>

                {{-- SORTING --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select name="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="">Default</option>
                        <option value="name_asc" {{ request('sort')=='name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        <option value="on_time" {{ request('sort')=='on_time' ? 'selected' : '' }}>On-Time Terbaik</option>
                        <option value="reject_low" {{ request('sort')=='reject_low' ? 'selected' : '' }}>Reject Terendah</option>
                        <option value="variance_low" {{ request('sort')=='variance_low' ? 'selected' : '' }}>Variance Terendah</option>
                    </select>
                </div>

                {{-- BUTTONS --}}
                <div class="flex gap-2 items-end">

                    {{-- APPLY FILTER --}}
                    <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700
                            text-white font-medium rounded-lg text-sm shadow-sm 
                            transition duration-200 ease-in-out transform hover:scale-[1.02]">
                        Terapkan Filter
                    </button>

                    {{-- RESET FILTER --}}
                    <a href="{{ route('supplier.index', $companyCode) }}" 
                        class="px-6 py-2.5 bg-gray-100 border border-gray-300 text-gray-600 
                            hover:bg-gray-200 font-medium rounded-lg text-sm shadow-sm
                            transition duration-200 ease-in-out">
                        Reset
                    </a>

                </div>

            </div>
        </form>
    </div>


    {{-- TABLE SECTION --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- TABLE RESPONSIVE WRAPPER --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                
                {{-- TABLE HEADER --}}
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr class="border-b border-gray-200">
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Supplier
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Telepon
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Lokasi
                        </th>
                        <th class="py-4 px-6 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            KPI Performance
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="py-4 px-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- TABLE BODY --}}
                <tbody class="divide-y divide-gray-200">
                    @forelse ($suppliers as $s)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">

                            {{-- SUPPLIER NAME --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($s->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $s->name }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- CONTACT --}}
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">{{ $s->contact_name ?: '-' }}</div>
                            </td>

                            {{-- PHONE --}}
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">{{ $s->phone ?: '-' }}</div>
                            </td>

                            {{-- CITY --}}
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">{{ $s->city ?: '-' }}</div>
                            </td>

                            {{-- KPI METRICS --}}
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-2">
                                    
                                    {{-- ON-TIME --}}
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-xs text-gray-600 font-medium min-w-[60px]">On-Time</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full {{ $s->kpi_on_time >= 90 ? 'bg-emerald-500' : ($s->kpi_on_time >= 70 ? 'bg-orange-500' : 'bg-red-500') }}"
                                                 style="width: {{ min($s->kpi_on_time, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold min-w-[45px] text-right {{ $s->kpi_on_time >= 90 ? 'text-emerald-600' : ($s->kpi_on_time >= 70 ? 'text-orange-600' : 'text-red-600') }}">
                                            {{ number_format($s->kpi_on_time, 1) }}%
                                        </span>
                                    </div>

                                    {{-- REJECT --}}
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-xs text-gray-600 font-medium min-w-[60px]">Reject</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full {{ $s->kpi_reject <= 5 ? 'bg-emerald-500' : ($s->kpi_reject <= 15 ? 'bg-orange-500' : 'bg-red-500') }}"
                                                 style="width: {{ min($s->kpi_reject, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold min-w-[45px] text-right {{ $s->kpi_reject <= 5 ? 'text-emerald-600' : ($s->kpi_reject <= 15 ? 'text-orange-600' : 'text-red-600') }}">
                                            {{ number_format($s->kpi_reject, 1) }}%
                                        </span>
                                    </div>

                                    {{-- VARIANCE --}}
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-xs text-gray-600 font-medium min-w-[60px]">Variance</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full {{ $s->kpi_var <= 5 ? 'bg-emerald-500' : ($s->kpi_var <= 15 ? 'bg-orange-500' : 'bg-red-500') }}"
                                                 style="width: {{ min($s->kpi_var, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold min-w-[45px] text-right {{ $s->kpi_var <= 5 ? 'text-emerald-600' : ($s->kpi_var <= 15 ? 'text-orange-600' : 'text-red-600') }}">
                                            {{ number_format($s->kpi_var, 1) }}%
                                        </span>
                                    </div>

                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-4 px-6">
                                @if($s->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('supplier.show', [$companyCode, $s->id]) }}"
                                   class="inline-flex items-center px-4 py-2 border border-blue-200 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Belum ada supplier terdaftar</p>
                                    <p class="text-gray-400 text-sm mt-1">Mulai tambahkan supplier baru untuk melihat data</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION (if needed) --}}
        @if(method_exists($suppliers, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $suppliers->links() }}
            </div>
        @endif

    </div>

</main>
</x-app-layout>