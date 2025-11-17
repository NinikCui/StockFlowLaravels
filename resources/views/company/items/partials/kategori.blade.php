<div class="max-w-5xl mx-auto mt-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Kategori</h2>

        <x-add-button 
            href="/items/category/create"
            text="+ Tambah Kategori"
            variant="primary"
        />
    </div>

    {{-- CARD WRAPPER --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="p-3 font-semibold text-left w-40">Kode</th>
                        <th class="p-3 font-semibold text-left">Nama</th>
                        <th class="p-3 font-semibold text-center w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kategori as $kat)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- KODE --}}
                            <td class="p-3 border-t">
                                <span class="px-3 py-1 text-xs rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $kat->code }}
                                </span>
                            </td>

                            {{-- NAMA --}}
                            <td class="p-3 border-t text-gray-900 font-medium">
                                {{ $kat->name }}
                            </td>

                            {{-- ACTION --}}
                            <td class="p-3 border-t">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('items.category.edit', [$companyCode, $kat->code]) }}"
                                        class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition">
                                        Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form method="POST"
                                        action="{{ route('items.category.delete', [$companyCode, $kat->code]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            onclick="return confirm('Yakin ingin menghapus kategori {{ $kat->name }}?');"
                                            class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-gray-500">
                                Belum ada kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
