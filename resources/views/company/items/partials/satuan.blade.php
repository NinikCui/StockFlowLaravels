<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6 px-2">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Satuan</h2>

        <x-add-button 
            href="/items/satuan/create"
            text="+ Tambah Satuan"
            variant="primary"
        />
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <div class="overflow-hidden border border-gray-200 rounded-xl">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="p-3 border-b text-center font-semibold w-40">Kode</th>
                        <th class="p-3 border-b text-center font-semibold">Nama</th>
                        <th class="p-3 border-b text-center font-semibold w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($satuan as $sat)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- KODE --}}
                            <td class="p-3 border-b text-center">
                                <span class="px-3 py-1 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                    {{ $sat->code }}
                                </span>
                            </td>

                            {{-- NAMA --}}
                            <td class="p-3 border-b text-center font-medium text-gray-900">
                                {{ $sat->name }}
                            </td>

                            {{-- ACTIONS --}}
                            <td class="p-3 border-b">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('items.satuan.edit', [$companyCode, $sat->code]) }}"
                                        class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition">
                                        Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form method="POST"
                                          action="{{ route('items.satuan.delete', [$companyCode, $sat->code]) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Yakin ingin menghapus satuan {{ $sat->name }}?');"
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
                                Belum ada satuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
