<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
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

    {{-- TABLE --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
                <tr class="border-b">
                    <th class="py-3 px-4">Nama</th>
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
                            @if ($s->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                            <a href="{{ route('supplier.edit', [$companyCode, $s->id]) }}"
                               class="text-blue-600 hover:text-blue-700 text-xs font-medium">
                                ✎ Edit
                            </a>

                            <form action="{{ route('supplier.destroy', [$companyCode, $s->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-700 text-xs font-medium">
                                    🗑 Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500 text-sm">
                            Belum ada supplier.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</main>
</x-app-layout>
