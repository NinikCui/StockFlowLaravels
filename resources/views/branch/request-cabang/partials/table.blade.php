<div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
            <tr>
                <th class="px-6 py-3 text-left">No. Transaksi</th>
                <th class="px-6 py-3 text-left">Cabang Asal</th>
                <th class="px-6 py-3 text-left">Cabang Tujuan</th>
                <th class="px-6 py-3 text-left">Tanggal</th>
                <th class="px-6 py-3 text-center">Jumlah Item</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse ($requests as $req)
                <tr class="hover:bg-gray-50 transition">

                    {{-- TRANS NUMBER --}}
                    <td class="px-6 py-4 font-semibold text-gray-900">
                        {{ $req->trans_number ?? 'TRF-' . $req->id }}
                    </td>

                    {{-- CABANG ASAL --}}
                    <td class="px-6 py-4 text-gray-800">
                        {{ $req->cabangFrom->name ?? '-' }}
                    </td>

                    {{-- CABANG TUJUAN --}}
                    <td class="px-6 py-4 text-gray-800">
                        {{ $req->cabangTo->name ?? '-' }}
                    </td>

                    {{-- DATE --}}
                    <td class="px-6 py-4 text-gray-700">
                        {{ \Carbon\Carbon::parse($req->trans_date)->format('d M Y') }}
                    </td>

                    {{-- ITEM COUNT --}}
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">
                            {{ $req->details->count() }} item
                        </span>
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4 text-center">
                        @php
                            $colors = [
                                'REQUESTED' => 'bg-yellow-100 text-yellow-700',
                                'APPROVED'  => 'bg-blue-100 text-blue-700',
                                'IN_TRANSIT'=> 'bg-indigo-100 text-indigo-700',
                                'RECEIVED'  => 'bg-emerald-100 text-emerald-700',
                                'CANCELLED' => 'bg-red-100 text-red-700',
                            ];
                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>

                    {{-- ACTION --}}
                    <td class="px-6 py-4 text-right flex justify-end items-center gap-2">

                        {{-- DETAIL --}}
                        <a href="{{ route('branch.request.show', [$branchCode, $req->id]) }}"
                           class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-xs font-medium">
                            Detail
                        </a>


                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        Belum ada data transfer.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
