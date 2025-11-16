<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Supplier</h1>
            <p class="text-sm text-gray-500">Kelola pemasok bahan dan produk untuk restoran.</p>
        </div>

        <x-add-button 
            href="/{{ $companyCode }}/supplier/create"
            text="+ Tambah Supplier"
            variant="primary"
        />
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-600 font-semibold">
                <tr class="border-b">
                    <th class="py-3 px-4">Supplier</th>
                    <th class="px-4">Kontak</th>
                    <th class="px-4">Telepon</th>
                    <th class="px-4">Kota</th>
                    <th class="px-4">Status</th>
                    <th class="px-4 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-800">
                @forelse ($suppliers as $s)
                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="py-3 px-4 font-medium">{{ $s->name }}</td>
                        <td class="px-4">{{ $s->contact_name ?: '-' }}</td>
                        <td class="px-4">{{ $s->phone ?: '-' }}</td>
                        <td class="px-4">{{ $s->city ?: '-' }}</td>

                        <td class="px-4">
                            @if($s->is_active)
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('supplier.show', [$companyCode, $s->id]) }}"
                               class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                🔍 Detail
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Belum ada supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</main>
</x-app-layout>
