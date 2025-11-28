<div class="bg-white p-6 rounded-2xl border shadow-sm">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Warehouse</h2>


        <x-crud-add 
            resource="warehouse"
            :companyCode="$companyCode"
            permissionPrefix="warehouse"
        />
    </div>

    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                    <th class="px-4 py-3 text-right font-semibold w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($warehouses as $w)
                    <tr class="hover:bg-gray-50 transition border-b">

                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $w->name }}
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            <span class="px-2 py-1 rounded-md bg-gray-100 border text-gray-800 text-xs">
                                {{ $w->code }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $w->type->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">

                                {{-- Detail --}}
                                <a href="{{ route('warehouse.show', [$companyCode, $w->id]) }}"
                                   class="text-blue-600 hover:underline text-sm font-medium">
                                    Detail
                                </a>

                                
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                            Belum ada warehouse.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
