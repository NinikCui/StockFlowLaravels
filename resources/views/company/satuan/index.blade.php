<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Satuan</h1>

        <x-add-button 
            href="/{{ $companyCode }}/product/satuan/create"
            text="+ Tambah Satuan"
            variant="primary"
        />
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">

        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-700 text-sm">
                <tr class="border-b">
                    <th class="py-3 px-4 font-semibold">Nama Satuan</th>
                    <th class="px-4 font-semibold">Kode</th>
                    <th class="px-4 font-semibold">Status</th>
                    <th class="px-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">
                @forelse ($satuan as $row)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-3 px-4 font-medium">
                            {{ $row->name }}
                        </td>

                        <td class="px-4 text-gray-600">
                            {{ $row->code }}
                        </td>

                        <td class="px-4">
                            @if ($row->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded-lg text-xs font-semibold">
                                    Aktif
                                </span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded-lg text-xs font-semibold">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right flex gap-2 justify-end">

                            <a href="{{ route('satuan.edit', [$companyCode, $row->code]) }}"
                               class="text-blue-600 text-sm hover:underline hover:text-blue-700">
                                ✎ Edit
                            </a>

                            <form action="{{ route('satuan.destroy', [$companyCode, $row->code]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus satuan ini?')">
                                @csrf
                                @method('DELETE')

                                <button class="text-red-600 text-sm hover:underline hover:text-red-700">
                                    🗑 Hapus
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">
                            Belum ada satuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</main>
</x-app-layout>
