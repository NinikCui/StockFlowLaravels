<div class="bg-white border rounded-xl p-6 shadow-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-bold text-gray-800">Daftar Item</h2>

        <x-add-button 
                        href="/items/create"
                        text="+ Tambah Item"
                        variant="primary"
                    />
                    
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-3 border-b text-left font-semibold">Nama Item</th>
                    <th class="p-3 border-b text-left font-semibold">Kategori</th>
                    <th class="p-3 border-b text-left font-semibold">Satuan</th>
                    <th class="p-3 border-b text-center font-semibold w-32">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- ITEM NAME --}}
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

                        {{-- ACTIONS --}}
                        <td class="p-3 border-b">
                            <div class="flex items-center justify-center gap-2">

                                {{-- EDIT --}}
                                <a href="{{ route('items.item.edit', [$companyCode, $item->id]) }}"
                                   class="px-3 py-1 text-xs rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 transition">
                                    Edit
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('items.item.delete', [$companyCode, $item->id]) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-3 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-gray-500 py-8">
                            Belum ada item.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>
