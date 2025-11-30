<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Gudang Cabang</h2>
            <p class="text-gray-600 text-sm mt-1">Kelola dan pantau gudang dalam cabang ini.</p>
        </div>
    </div>
            <x-crud-add 
                resource="branch.warehouse"
                :companyCode="$companyCode"
                permissionPrefix="warehouse"
            />
    {{-- FILTERS --}}
    <form method="GET"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-xl shadow-sm border mb-6">

        {{-- SEARCH --}}
        <div class="col-span-1">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama / kode gudang..."
                class="w-full px-4 py-2.5 border rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-300"
            >
        </div>

        {{-- FILTER TYPE --}}
        <div class="col-span-1">
            <select name="type"
                    class="w-full px-4 py-2.5 border rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-emerald-300">
                <option value="">Semua Tipe</option>
                @foreach ($types as $t)
                    <option value="{{ $t->id }}" @selected(request('type') == $t->id)>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SORT --}}
        <div class="col-span-1">
            <select name="sort"
                    class="w-full px-4 py-2.5 border rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-emerald-300">
                <option value="name_asc"  @selected(request('sort') == 'name_asc')>Nama A–Z</option>
                <option value="name_desc" @selected(request('sort') == 'name_desc')>Nama Z–A</option>
                <option value="latest"    @selected(request('sort') == 'latest')>Terbaru</option>
            </select>
        </div>

        {{-- BUTTON --}}
        <div class="col-span-1">
            <button class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-2xl border shadow-sm">

        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold">Item</th>
                        <th class="px-4 py-3 text-center font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-right font-semibold w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($warehouses as $w)
                        <tr class="hover:bg-gray-50 transition border-b">

                            {{-- NAMA --}}
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $w->name }}
                            </td>

                            {{-- KODE --}}
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-md bg-gray-100 border text-gray-800 text-xs">
                                    {{ $w->code }}
                                </span>
                            </td>

                            {{-- TYPE --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $w->type->name ?? '-' }}
                            </td>

                            {{-- JUMLAH ITEM --}}
                            <td class="px-4 py-3 text-center">
                                <span class="text-gray-800 font-semibold">
                                    {{ $w->stocks_count }}
                                </span>
                            </td>

                            {{-- TOTAL QTY --}}
                            <td class="px-4 py-3 text-center">
                                <span class="text-emerald-700 font-semibold">
                                    {{ number_format($w->stocks_sum_qty, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <x-crud
    resource="branch.warehouse"
    :model="$w"
    :companyCode="$companyCode"   
    permissionPrefix="warehouse"
    :routeParams="[$branchCode, $w->id]"
/>
                                    <a href="{{ route('branch.stock.index', $branchCode) }}?warehouse={{ $w->id }}"
                                       class="text-blue-600 hover:underline text-sm font-medium">
                                        Lihat Stok
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 text-sm">
                                Tidak ada gudang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

</x-app-layout>
