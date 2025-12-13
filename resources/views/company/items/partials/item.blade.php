<div class="bg-white border rounded-xl p-6 shadow-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-bold text-gray-800">Daftar Item</h2>

        <x-crud-add 
            resource="item"
            :companyCode="$companyCode"
            permissionPrefix="item"
        />
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-sm whitespace-nowrap">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-3 border-b text-left font-semibold">Nama Item</th>
                    <th class="p-3 border-b text-left font-semibold">Kategori</th>
                    <th class="p-3 border-b text-left font-semibold">Satuan</th>
                    <th class="p-3 border-b text-center font-semibold">Bahan Baku Utama</th>
                    <th class="p-3 border-b text-center font-semibold">Min Stok</th>
                    <th class="p-3 border-b text-center font-semibold">Max Stok</th>
                    <th class="p-3 border-b text-center font-semibold">Forecast</th>
                    <th class="p-3 border-b text-center font-semibold w-32">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- NAMA ITEM --}}
                        <td class="p-3 border-b">
                            <span class="font-medium text-gray-800">{{ $item->name }}</span>
                        </td>

                        {{-- KATEGORI --}}
                        <td class="p-3 border-b">
                            @if ($item->kategori)
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs rounded-md border border-emerald-200">
                                    {{ $item->kategori->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- SATUAN --}}
                        <td class="p-3 border-b">
                            @if ($item->satuan)
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-md border border-blue-200">
                                    {{ $item->satuan->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- MUDAH RUSAK --}}
                        <td class="p-3 border-b text-center">
                            @if ($item->is_main_ingredient)
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-xs rounded-md border border-red-200">
                                    Ya
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-50 text-gray-600 text-xs rounded-md border border-gray-200">
                                    Tidak
                                </span>
                            @endif
                        </td>

                        {{-- MIN STOCK --}}
                        <td class="p-3 border-b text-center">
                            <span class="font-medium">{{ $item->min_stock }}</span>
                        </td>

                        {{-- MAX STOCK --}}
                        <td class="p-3 border-b text-center">
                            <span class="font-medium">{{ $item->max_stock }}</span>
                        </td>

                        {{-- FORECAST --}}
                        <td class="p-3 border-b text-center">
                            @if ($item->forecast_enabled)
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-md border border-indigo-200">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-50 text-gray-600 text-xs rounded-md border border-gray-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="p-3 border-b">
                            <div class="flex items-center justify-center gap-2">
                                <x-crud 
                                    resource="item"
                                    :model="$item"
                                    :companyCode="$companyCode"
                                    permissionPrefix="item"
                                    keyField="id"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-3 text-center text-gray-500 py-8">
                            Belum ada item.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
