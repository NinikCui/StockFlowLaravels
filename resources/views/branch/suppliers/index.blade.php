<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daftar Supplier</h1>
            <p class="text-gray-500 mt-1">Supplier perusahaan & cabang ini.</p>
        </div>

        <div>
            <x-crud-add 
                resource="branch.supplier"
                :companyCode="$companyCode"
                permissionPrefix="supplier"
                :routeParams="[$branchCode]" 
            />
        </div>
    </div>


    {{-- ========================= --}}
    {{-- SEGMENTED FILTER --}}
    {{-- ========================= --}}
    <div class="mb-6">
        <div class="inline-flex items-center bg-white border rounded-xl overflow-hidden shadow">

            <a href="{{ route('branch.supplier.index', [$branchCode, 'filter' => 'all']) }}"
               class="px-5 py-2.5 text-sm font-medium transition
                   {{ ($filter ?? 'all') === 'all'
                       ? 'bg-emerald-600 text-white'
                       : 'text-gray-600 hover:bg-gray-50' }}">
                Semua
            </a>

            <a href="{{ route('branch.supplier.index', [$branchCode, 'filter' => 'company']) }}"
               class="px-5 py-2.5 text-sm font-medium border-l transition
                   {{ ($filter ?? '') === 'company'
                       ? 'bg-emerald-600 text-white'
                       : 'text-gray-600 hover:bg-gray-50' }}">
                Supplier Perusahaan
            </a>

            <a href="{{ route('branch.supplier.index', [$branchCode, 'filter' => 'branch']) }}"
               class="px-5 py-2.5 text-sm font-medium border-l transition
                   {{ ($filter ?? '') === 'branch'
                       ? 'bg-emerald-600 text-white'
                       : 'text-gray-600 hover:bg-gray-50' }}">
                Supplier Cabang Ini
            </a>

        </div>
    </div>


    {{-- ========================= --}}
    {{-- ADVANCED FILTERS --}}
    {{-- ========================= --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        {{-- SEARCH --}}
        <div class="col-span-1 md:col-span-2 relative">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Cari supplier..."
                class="w-full pl-12 pr-4 py-3 bg-white border rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500">
            <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </div>

        {{-- ITEM FILTER --}}
        <div>
            <select name="item_id"
                    class="w-full px-4 py-3 border rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-emerald-500">
                <option value="">Filter Item</option>
                @foreach ($allItems as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- PERFORMANCE FILTER --}}
        <div>
            <select name="performance"
                class="w-full px-4 py-3 border rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-emerald-500">
                <option value="">Performa</option>
                <option value="good" {{ request('performance') === 'good' ? 'selected' : '' }}>Good</option>
                <option value="average" {{ request('performance') === 'average' ? 'selected' : '' }}>Average</option>
                <option value="poor" {{ request('performance') === 'poor' ? 'selected' : '' }}>Poor</option>
            </select>
        </div>

        {{-- SORTING --}}
        <div>
            <select name="sort"
                class="w-full px-4 py-3 border rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-emerald-500">
                <option value="">Urutkan</option>
                <option value="on_time" {{ request('sort') === 'on_time' ? 'selected' : '' }}>On-time Tinggi</option>
                <option value="reject_low" {{ request('sort') === 'reject_low' ? 'selected' : '' }}>Reject Terendah</option>
                <option value="variance_low" {{ request('sort') === 'variance_low' ? 'selected' : '' }}>Variance Terendah</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>A-Z</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Z-A</option>
            </select>
        </div>

        {{-- SUBMIT --}}
        <div class="md:col-span-4 flex justify-end">
            <button class="px-5 py-3 bg-emerald-600 text-white rounded-xl shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>


    {{-- ========================= --}}
    {{-- GRID LIST --}}
    {{-- ========================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($suppliers as $s)
            <div class="bg-white p-6 rounded-xl border shadow-sm hover:shadow-md transition">

                {{-- NAME + BADGE --}}
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $s->name }}
                    </h3>

                    {{-- COMPANY / BRANCH --}}
                    @if ($s->cabang_resto_id)
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            Cabang
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">
                            Company
                        </span>
                    @endif
                </div>


                {{-- KPI BADGES --}}
                <div class="flex flex-wrap gap-2 text-xs mb-4">

                    {{-- ON-TIME --}}
                    <span class="px-2 py-1 rounded 
                        {{ $s->kpi_on_time >= 90 ? 'bg-green-100 text-green-700' 
                        : ($s->kpi_on_time >= 70 ? 'bg-yellow-100 text-yellow-700' 
                        : 'bg-red-100 text-red-700') }}">
                        On-time: {{ $s->kpi_on_time }}%
                    </span>

                    {{-- REJECT --}}
                    <span class="px-2 py-1 rounded
                        {{ $s->kpi_reject <= 10 ? 'bg-green-100 text-green-700'
                        : ($s->kpi_reject <= 20 ? 'bg-yellow-100 text-yellow-700'
                        : 'bg-red-100 text-red-700') }}">
                        Reject: {{ $s->kpi_reject }}%
                    </span>

                    {{-- VARIANCE --}}
                    <span class="px-2 py-1 rounded
                        {{ $s->kpi_var <= 5 ? 'bg-green-100 text-green-700'
                        : ($s->kpi_var <= 15 ? 'bg-yellow-100 text-yellow-700'
                        : 'bg-red-100 text-red-700') }}">
                        Var: {{ $s->kpi_var }}%
                    </span>

                </div>


                {{-- CONTACT --}}
                @if ($s->phone)
                    <p class="text-sm text-gray-600 mb-1">📞 {{ $s->phone }}</p>
                @endif

                @if ($s->email)
                    <p class="text-sm text-gray-600 mb-1">✉️ {{ $s->email }}</p>
                @endif

                {{-- ADDRESS --}}
                <p class="text-sm text-gray-500 line-clamp-2 mt-2">
                    {{ $s->address ?? 'Alamat tidak tersedia' }}
                </p>

                {{-- DETAIL BUTTON --}}
                <div class="mt-6">
                    <a href="{{ route('branch.supplier.show', [$branchCode, $s->id]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 w-full text-sm font-medium
                               rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                        Detail
                    </a>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 col-span-full py-12">
                Tidak ada supplier ditemukan.
            </div>
        @endforelse

    </div>


</div>

</x-app-layout>
