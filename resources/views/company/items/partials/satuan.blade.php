<div class="bg-white border rounded-xl p-6 shadow-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-bold text-gray-800">Daftar Satuan</h2>

                    <x-add-button 
                        href="/items/satuan/create"
                        text="+ Tambah Satuan"
                        variant="primary"
                    />
    </div>

    {{-- TABLE WRAPPER --}}
    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-3 border-b font-semibold">Kode</th>
                    <th class="p-3 border-b font-semibold">Nama</th>
                    <th class="p-3 border-b text-center font-semibold w-32">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($satuan as $sat)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- KODE --}}
                        <td class="p-3 border-b">
                            <span class="px-2 py-1 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                {{ $sat->code }}
                            </span>
                        </td>

                        {{-- NAMA --}}
                        <td class="p-3 border-b font-medium text-gray-800">
                            {{ $sat->name }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="p-3 border-b">
                            <div class="flex justify-center items-center gap-2">

                                {{-- EDIT --}}
                                <a href="{{ route('items.satuan.edit', [$companyCode, $sat->code]) }}"
                                   class="px-3 py-1 text-xs rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 transition">
                                    Edit
                                </a>

                                {{-- DELETE --}}
                                <form method="POST"
                                      action="{{ route('items.satuan.delete', [$companyCode, $sat->code]) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        onclick="return confirm('Yakin ingin menghapus satuan {{ $sat->name }}?');"
                                        class="px-3 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="3" class="p-3 text-center text-gray-500 py-8">
                            Belum ada satuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
