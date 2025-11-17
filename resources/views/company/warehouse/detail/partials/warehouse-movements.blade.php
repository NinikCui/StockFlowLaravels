<div class="bg-white border rounded-xl p-6 shadow-sm">

    <h2 class="text-xl font-bold text-gray-800 mb-5">Mutasi Stok</h2>

    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-3 border-b font-semibold">Tanggal</th>
                    <th class="p-3 border-b font-semibold">Item</th>
                    <th class="p-3 border-b font-semibold">Jenis</th>
                    <th class="p-3 border-b font-semibold">Qty</th>
                    <th class="p-3 border-b font-semibold">Catatan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($movements as $mv)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="p-3 border-b text-gray-700">
                            {{ $mv->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="p-3 border-b font-medium text-gray-900">
                            {{ $mv->item->name }}
                        </td>

                        <td class="p-3 border-b">
                            @if ($mv->type === 'IN')
                                <span class="text-emerald-600 font-semibold">Stok Masuk</span>
                            @else
                                <span class="text-red-600 font-semibold">Stok Keluar</span>
                            @endif
                        </td>

                        <td class="p-3 border-b font-semibold text-gray-800">
                            {{ $mv->qty }}
                        </td>

                        <td class="p-3 border-b text-gray-600">
                            {{ $mv->notes ?? '-' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            Belum ada mutasi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
