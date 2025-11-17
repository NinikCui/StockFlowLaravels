<div class="bg-white border rounded-xl p-6 shadow-sm">

    <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-bold text-gray-800">Stok di Gudang</h2>


         <x-add-button 
    href="/{{ $companyCode }}/gudang/{{ $warehouse->id }}/stock/create"
    text="+ Tambah Stok Masuk"
    variant="primary"
/>
   
       
    </div>

    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-3 border-b font-semibold">Item</th>
                    <th class="p-3 border-b font-semibold">Kategori</th>
                    <th class="p-3 border-b font-semibold">Qty</th>
                    <th class="p-3 border-b text-center font-semibold w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($stocks as $s)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="p-3 border-b font-medium text-gray-900">
                            {{ $s->item->name }}
                        </td>

                        <td class="p-3 border-b">
                            <span class="px-2 py-1 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                {{ $s->item->kategori->name ?? '-' }}
                            </span>
                        </td>

                        <td class="p-3 border-b font-semibold text-gray-800">
                            {{ $s->qty }} {{ $s->item->satuan->name }}
                        </td>

                        <td class="p-3 border-b">
                            <div class="flex justify-center gap-2">

                                {{-- STOCK OUT --}}
                                <a href="{{ route('stock.out.create', [$companyCode, $warehouse->code, $s->item->id]) }}"
                                   class="px-3 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                    Stok Keluar
                                </a>

                                {{-- HISTORY --}}
                                <a href="#"
                                   class="px-3 py-1 text-xs rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                    History
                                </a>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            Belum ada stok item di gudang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
