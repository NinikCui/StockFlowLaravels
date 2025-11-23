<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Top Bar --}}
            <div class="flex items-center justify-between mb-6">

                <a href="{{ route('po.index', $companyCode) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition">
                    ← Kembali
                </a>

                <div class="flex items-center gap-3">

                   
                    @if ($po->status == 'DRAFT')

                        <a href="{{ route('po.edit', [$companyCode, $po->id]) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Edit PO
                        </a>

                        <form action="{{ route('po.destroy', [$companyCode, $po->id]) }}" method="POST"
                            onsubmit="return confirm('Hapus PO ini?')">
                            @csrf @method('DELETE')
                            <button class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm font-medium">
                                Hapus
                            </button>
                        </form>

                        <form action="{{ route('po.updateStatus', [$companyCode, $po->id]) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="APPROVED">
                            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                Accept PO
                            </button>
                        </form>

                    @endif


                    @if ($po->status == 'APPROVED')

                        <form action="{{ route('po.updateStatus', [$companyCode, $po->id]) }}" method="POST"
                            onsubmit="return confirm('Batalkan PO ini?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="CANCELLED">
                            <button class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm font-medium">
                                Cancel PO
                            </button>
                        </form>

                        <a href="{{ route('po.receive.show', [$companyCode, $po->id]) }}"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                            Receive Items
                        </a>

                    @endif


                    @if ($po->status == 'PARTIAL')

                        <a href="{{ route('po.receive.show', [$companyCode, $po->id]) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Continue Receive
                        </a>

                    @endif

                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ $po->po_number }}</h1>
                        <p class="text-slate-600 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $po->po_date }}
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        @php
                            $status = strtoupper($po->status);
                            $statusConfig = [
                                'DRAFT' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                'APPROVED' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-300', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'PARTIAL' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-300', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'RECEIVED' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-300', 'icon' => 'M5 13l4 4L19 7'],
                                'CANCELLED' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'border' => 'border-rose-300', 'icon' => 'M6 18L18 6M6 6l12 12'],
                            ];
                            $config = $statusConfig[$status] ?? $statusConfig['DRAFT'];
                        @endphp
                        
                        <span class="inline-flex items-center gap-2 px-4 py-2 {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }} rounded-full font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                            </svg>
                            {{ $status }}
                        </span>
                        
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Informasi PO --}}
                <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Informasi PO</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Nomor PO</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->po_number }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tanggal PO</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->po_date }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Dibuat Oleh</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->createdByUser->username ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Cabang Tujuan --}}
                <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Cabang Tujuan</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Cabang</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->cabangResto->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Kode Cabang</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->cabangResto->code ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Alamat</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->cabangResto->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Supplier --}}
                <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Supplier</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Supplier</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->supplier->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Kontak</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->supplier->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Alamat</label>
                            <p class="text-slate-900 font-medium mt-1">{{ $po->supplier->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan (if exists) --}}
            @if ($po->note)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <div>
                        <h3 class="font-bold text-amber-900 mb-2">Catatan</h3>
                        <p class="text-amber-800">{{ $po->note }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Item List --}}
            <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 mb-1">Rincian Item Pembelian</h2>
                            <p class="text-slate-600">{{ count($po->details) }} item dalam pesanan</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">QTY</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($po->details as $d)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-900">{{ $d->item->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-slate-900">{{ number_format($d->qty_ordered, 2) }} {{ $d->item->satuan->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-slate-900">Rp {{ number_format($d->unit_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold text-slate-900">Rp {{ number_format($d->qty_ordered * $d->unit_price, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-slate-900">Total Pembelian</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($po->details->sum(fn($d) => $d->qty_ordered * $d->unit_price), 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    


    

</div>

</x-app-layout>
