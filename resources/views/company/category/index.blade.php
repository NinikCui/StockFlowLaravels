<x-app-layout>

<main class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Kategori Produk</h1>

        <x-add-button 
            href="/{{ $companyCode }}/product/categories/create"
            text="+ Tambah Kategori"
            variant="primary"
        />
    </div>


    {{-- CARD WRAPPER --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">

        {{-- TABLE --}}
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-700 text-sm">
                <tr class="border-b">
                    <th class="py-3 px-4 font-semibold">Nama Kategori</th>
                    <th class="px-4 font-semibold">Kode</th>
                    <th class="px-4 font-semibold">Status</th>
                    <th class="px-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">
                @forelse ($categories as $cat)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-3 px-4">
                            <span class="font-medium">{{ $cat->name }}</span>
                        </td>

                        <td class="px-4">
                            <span class="text-gray-600">{{ $cat->code }}</span>
                        </td>

                        <td class="px-4">
                            @if ($cat->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded-lg text-xs font-semibold">
                                    Active
                                </span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded-lg text-xs font-semibold">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right flex items-center justify-end gap-2">

                            {{-- EDIT BUTTON --}}
                            <a href="{{ route('category.edit', [$companyCode, $cat->code]) }}"
                               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 hover:underline text-sm">
                                ✎ <span>Edit</span>
                            </a>

                            {{-- DELETE BUTTON --}}
                            <form action="{{ route('category.destroy', [$companyCode, $cat->code]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')

                                <button class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 hover:underline text-sm">
                                    🗑 <span>Delete</span>
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">
                            Belum ada kategori.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</main>

</x-app-layout>
