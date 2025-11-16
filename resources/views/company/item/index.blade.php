<x-app-layout>

<main class="max-w-7xl mx-auto px-6 py-10">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Item</h1>

        <x-add-button 
            href="/{{ $companyCode }}/product/items/create"
            text="+ Tambah Item"
            variant="primary"
        />
    </div>

    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">

        <table class="w-full text-left">
            <thead class="bg-gray-50 text-sm text-gray-700">
                <tr class="border-b">
                    <th class="py-3 px-4">Nama</th>
                    <th class="px-4">Kategori</th>
                    <th class="px-4">Satuan</th>
                    <th class="px-4">Min-Max</th>
                    <th class="px-4">Mudah Rusak</th>
                    <th class="px-4 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-3 px-4 font-medium">{{ $item->name }}</td>
                    <td class="px-4">{{ $item->kategori->name }}</td>
                    <td class="px-4">{{ $item->satuan->name }}</td>
                    <td class="px-4">{{ $item->min_stock }} - {{ $item->max_stock }}</td>
                    <td class="px-4">
                        @if($item->mudah_rusak)
                            <span class="text-red-600 text-sm">Ya</span>
                        @else
                            <span class="text-gray-500 text-sm">Tidak</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                        <a href="{{ route('item.edit', [$companyCode, $item->id]) }}"
                           class="text-blue-600 hover:underline">✎ Edit</a>

                        <form action="{{ route('item.destroy', [$companyCode, $item->id]) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus item ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">🗑 Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</main>

</x-app-layout>
