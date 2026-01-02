<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

    {{-- ===============================
        HEADER
    =============================== --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Daftar Stok (Company)
        </h1>
        <p class="text-gray-600 mt-1 text-sm">
            Monitoring stok seluruh cabang dan gudang.
        </p>
    </div>

    {{-- ===============================
        FILTERS
    =============================== --}}
    <form method="GET"
          class="bg-white p-4 rounded-xl shadow-sm border
                 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">

        {{-- CABANG --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Cabang</label>
            <select name="branch"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Cabang</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" @selected($selectedBranch == $b->id)>
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ITEM --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Item</label>
            <select name="item"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Item</option>
                @foreach ($items as $i)
                    <option value="{{ $i->id }}" @selected($selectedItem == $i->id)>
                        {{ $i->name }}
                    </option>
                @endforeach
            </select>
        </div>



        {{-- KATEGORI --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Kategori</label>
            <select name="category"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected($selectedCategory == $c->id)>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SEARCH --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1 block">Cari Item</label>
            <input type="text" name="q" value="{{ $search }}"
                placeholder="Nama / kode item"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
        </div>

        {{-- ACTION --}}
        <div class="flex items-end gap-2">
            <button
                class="bg-emerald-600 text-white px-5 py-2 rounded-lg shadow hover:bg-emerald-700">
                Terapkan
            </button>

            <a href="{{ route('stockmanage.index', [
                'companyCode' => request()->route('companyCode')
            ]) }}"
            class="bg-gray-100 px-5 py-2 rounded-lg shadow text-gray-700 hover:bg-gray-200">
                Reset
            </a>
        </div>

    </form>

    {{-- ===============================
        TABLE
    =============================== --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Item</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Cabang</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Gudang</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode Stok</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Kadaluarsa</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($stocks as $stock)

                    <tr class="hover:bg-gray-50 transition">

                        {{-- ITEM --}}
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">
                                {{ $stock->item->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $stock->item->satuan->name ?? '-' }}
                            </div>
                        </td>

                        {{-- CABANG --}}
                        <td class="px-4 py-3">
                            {{ $stock->warehouse->cabangResto->name ?? '-' }}
                        </td>

                        {{-- GUDANG --}}
                        <td class="px-4 py-3">
                            {{ $stock->warehouse->name }}
                        </td>

                        {{-- STOCK CODE --}}
                        <td class="px-4 py-3 font-mono text-gray-900">
                            {{ $stock->code }}
                        </td>

                        {{-- QTY --}}
                        <td class="px-4 py-3 text-right font-bold">
                            {{ number_format($stock->qty, 2) }}
                        </td>

                        {{-- EXPIRED --}}
                        <td class="px-4 py-3 text-center">
                            @if ($stock->days_to_expire !== null)
                                <span class="px-2 py-1 rounded-lg text-xs font-bold
                                    {{ $stock->days_to_expire <= 7
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-gray-100 text-gray-700' }}">
                                    {{ $stock->days_to_expire }} hari
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('stockmanage.history', [
                                'companyCode' => request()->route('companyCode'),
                                'stock' => $stock->id
                            ]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2
                                   text-xs font-bold text-blue-700
                                   bg-blue-50 border border-blue-200
                                   rounded-lg hover:bg-blue-100">
                                Riwayat
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-500">
                            Tidak ada stok ditemukan.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ===============================
        PAGINATION
    =============================== --}}
    <div>
        {{ $stocks->links() }}
    </div>

</div>

</x-app-layout>
