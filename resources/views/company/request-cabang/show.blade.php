<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- BREADCRUMB --}}
    <a href="{{ route('request.index', $companyCode) }}"
       class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar request
    </a>

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $trans->trans_number }}</h1>
            
            <div class="mt-2 text-sm text-gray-600 space-y-1">
                <div>
                    <span class="font-medium text-gray-800">Tanggal:</span>
                    {{ date('d M Y', strtotime($trans->trans_date)) }}
                </div>

                <div>
                    <span class="font-medium text-gray-800">Cabang Asal:</span>
                    {{ $trans->warehouseFrom?->cabangResto?->name ?? '-' }}
                </div>

                <div>
                    <span class="font-medium text-gray-800">Cabang Tujuan:</span>
                    {{ $trans->warehouseTo?->cabangResto?->name ?? '-' }}
                </div>
            </div>
        </div>

        {{-- STATUS BADGE --}}
        @php
            $color = match($trans->status) {
                'REQUESTED' => 'bg-yellow-100 text-yellow-700',
                'APPROVED' => 'bg-blue-100 text-blue-700',
                'IN_TRANSIT' => 'bg-purple-100 text-purple-700',
                'RECEIVED' => 'bg-emerald-100 text-emerald-700',
                'CANCELLED' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <span class="px-3 py-1 rounded-lg text-sm font-semibold {{ $color }}">
            {{ $trans->status }}
        </span>
    </div>

    {{-- CATATAN --}}
    @if ($trans->note)
    <div class="mb-8 bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <div class="text-sm text-gray-700">
            <span class="font-medium">Catatan:</span> {{ $trans->note }}
        </div>
    </div>
    @endif

    {{-- ITEMS TABLE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-left text-gray-600">
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Satuan</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($trans->details as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $row->stock->item->name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ number_format($row->qty, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $row->stock->item->satuan->name ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex gap-3">

        {{-- OWNER / ADMIN: Bisa approve request jika status REQUESTED --}}
        @if ($trans->status === 'REQUESTED')
            <form action="{{ route('request.approve', [$companyCode, $trans->id]) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                    Approve Request
                </button>
            </form>
        @endif

        {{-- OWNER: Kirim barang setelah approve --}}
        @if ($trans->status === 'APPROVED')
            <form action="{{ route('request.send', [$companyCode, $trans->id]) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow">
                    Kirim Barang
                </button>
            </form>
        @endif

        {{-- MANAGER CABANG TUJUAN: Terima barang --}}
        @if ($trans->status === 'IN_TRANSIT')
            <form action="{{ route('request.receive', [$companyCode, $trans->id]) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow">
                    Terima Barang
                </button>
            </form>
        @endif

    </div>

</div>
</x-app-layout>
