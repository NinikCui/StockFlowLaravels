<div class="bg-white p-6 rounded-2xl border shadow-sm">

    {{-- TITLE --}}
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Warehouse Types</h2>

    {{-- FORM INPUT ADD --}}
    <form action="{{ route('warehouse.types.store', $companyCode) }}"
          method="POST"
          class="flex gap-3 mb-6">
        @csrf

        <input type="text"
               name="name"
               placeholder="Contoh: FREEZER, DRY STORAGE, CHILLER"
               class="border rounded-lg px-4 py-2 text-sm w-full"
               required>

        <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
            + Tambah
        </button>
    </form>

    {{-- LIST WAREHOUSE TYPES --}}
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-gray-600 border-b">
                <th class="px-4 py-2 text-left">Nama Tipe</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($types as $t)
                <tr class="border-b">
                    <td class="px-4 py-2 text-gray-900">
                        {{ $t->name }}
                    </td>
                    <td class="px-4 py-2 text-right">

                        <form action="{{ route('warehouse.types.destroy', [$companyCode, $t->id]) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus tipe gudang ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline text-sm">
                                Hapus
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-6 text-center text-gray-500">
                        Belum ada tipe warehouse
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
