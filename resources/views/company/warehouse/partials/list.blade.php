<div class="bg-white p-6 rounded-2xl border shadow-sm">

    <div class="flex justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Daftar Warehouse</h2>

        <a href="{{ route('warehouse.create', $companyCode) }}"
            class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
            + Tambah Warehouse
        </a>
    </div>

    <table class="w-full text-sm border-b">
        <thead>
            <tr class="bg-gray-50 text-gray-500">
                <th class="px-4 py-2 text-left">Nama</th>
                <th class="px-4 py-2 text-left">Kode</th>
                <th class="px-4 py-2 text-left">Tipe</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouses as $w)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $w->name }}</td>
                <td class="px-4 py-2">{{ $w->code }}</td>
                <td class="px-4 py-2">{{ $w->type->name ?? '-' }}</td>
                <td class="px-4 py-2 text-right">
                    <a href="{{ route('warehouse.edit', [$companyCode, $w->id]) }}"
                        class="text-blue-600 hover:underline">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                    Belum ada warehouse
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
