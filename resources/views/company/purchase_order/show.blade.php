<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $po->po_number }}</h1>

                    {{-- STATUS BADGE --}}
                    @php
                        $status = strtoupper($po->status);  

                        $statusColor = [
                            'DRAFT'     => 'bg-gray-100 text-gray-700 border-gray-300',
                            'APPROVED'  => 'bg-emerald-100 text-emerald-700 border-emerald-300',
                            'PARTIAL'   => 'bg-amber-100 text-amber-700 border-amber-300',
                            'RECEIVED'  => 'bg-blue-100 text-blue-700 border-blue-300',
                            'CANCELLED' => 'bg-rose-100 text-rose-700 border-rose-300',
                        ][$status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                    @endphp

                    <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusColor }}">
                        {{ $status }}
                    </span>
                </div>

                <p class="text-sm text-gray-500 mt-1">Detail Purchase Order • {{ $po->po_date }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('po.index', $companyCode) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>

                @if ($po->status == 'DRAFT')
                    {{-- TOMBOL EDIT --}}
                    <a href="{{ route('po.edit', [$companyCode, $po->id]) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit PO
                    </a>

                    {{-- TOMBOL HAPUS --}}
                    <form action="{{ route('po.destroy', [$companyCode, $po->id]) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus Purchase Order ini?')">
                        @csrf @method('DELETE')
                        <button class="inline-flex items-center px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- CARD INFO PO --}}
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Informasi PO</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Nomor PO</p>
                        <p class="text-sm font-medium text-gray-900">{{ $po->po_number }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal PO</p>
                        <p class="text-sm font-medium text-gray-900">{{ $po->po_date }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Dibuat Oleh</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $po->createdByUser->username ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- CARD CABANG TUJUAN --}}
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Cabang Tujuan</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Nama Cabang</p>
                        <p class="text-sm font-medium text-gray-900">{{ $po->cabangResto->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Kode Cabang</p>
                        <p class="text-sm font-medium text-gray-900">{{ $po->cabangResto->code ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Alamat</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $po->cabangResto->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- CARD SUPPLIER --}}
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Supplier</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Nama Supplier</p>
                        <p class="text-sm font-medium text-gray-900">{{ $po->supplier->name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Kontak</p>
                        <p class="text-sm text-gray-700">{{ $po->supplier->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Alamat</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $po->supplier->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6">
            {{-- CARD CATATAN --}}
            @if ($po->note)
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>

                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">Catatan</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $po->note }}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- CARD ITEM LIST --}}
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Rincian Item Pembelian</h2>
                    <p class="text-xs text-gray-500 mt-1">{{ count($po->details) }} item dalam pesanan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">QTY</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                        @foreach ($po->details as $d)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $d->item->name }}</div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="font-medium text-gray-900">{{ number_format($d->qty_ordered, 2) }}</span>
                                    <span class="text-gray-500 text-xs ml-1">{{ $d->item->satuan->name }}</span>
                                </td>

                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    Rp {{ number_format($d->unit_price, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Rp {{ number_format($d->qty_ordered * $d->unit_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Pembelian</span>
                        <span class="text-xl font-bold text-emerald-600">
                            Rp {{ number_format($po->details->sum(fn($d) => $d->qty_ordered * $d->unit_price), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            

        </div>

    </div>

</div>

</x-app-layout>
