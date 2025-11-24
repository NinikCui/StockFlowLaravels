<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Material Request Antar Cabang</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola permintaan transfer bahan antar cabang.</p>
        </div>

        <a href="{{ route('request.create', $companyCode) }}"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            + Buat Request
        </a>
    </div>

    {{-- ERROR --}}
    @isset($error)
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            {{ $error }}
        </div>
    @endisset

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-left text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Transaksi</th>
                    <th class="px-4 py-3">Cabang Asal</th>
                    <th class="px-4 py-3">Cabang Tujuan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($requests as $req)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $req->trans_number }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $req->warehouseFrom?->cabangResto?->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $req->warehouseTo?->cabangResto?->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ date('d M Y', strtotime($req->trans_date)) }}
                        </td>

                        <td class="px-4 py-3">
                            @php
                                $color = match($req->status) {
                                    'REQUESTED' => 'bg-yellow-100 text-yellow-700',
                                    'APPROVED' => 'bg-blue-100 text-blue-700',
                                    'IN_TRANSIT' => 'bg-purple-100 text-purple-700',
                                    'RECEIVED' => 'bg-emerald-100 text-emerald-700',
                                    'CANCELLED' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                                {{ $req->status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">

                            <a href="{{ route('request.show', [$companyCode, $req->id]) }}"
                                class="px-3 py-1.5 bg-gray-800 text-white rounded-lg text-xs hover:bg-black">
                                Detail
                            </a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-gray-500">
                            Belum ada request.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</x-app-layout>
