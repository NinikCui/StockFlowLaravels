<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- BREADCRUMB --}}
        <div class="mb-8">
            <a href="{{ route('request.index', $companyCode) }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke daftar request
            </a>
        </div>

        {{-- HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">
                            {{ $req->trans_number }}
                        </h1>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Diajukan pada {{ $req->trans_date->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $statusConfig = match($req->status) {
                            'REQUESTED' => [
                                'bg' => 'bg-yellow-100',
                                'text' => 'text-yellow-700',
                                'border' => 'border-yellow-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label' => 'REQUESTED'
                            ],
                            'APPROVED' => [
                                'bg' => 'bg-blue-100',
                                'text' => 'text-blue-700',
                                'border' => 'border-blue-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label' => 'APPROVED'
                            ],
                            'IN_TRANSIT' => [
                                'bg' => 'bg-purple-100',
                                'text' => 'text-purple-700',
                                'border' => 'border-purple-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                'label' => 'IN TRANSIT'
                            ],
                            'RECEIVED' => [
                                'bg' => 'bg-emerald-100',
                                'text' => 'text-emerald-700',
                                'border' => 'border-emerald-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                                'label' => 'RECEIVED'
                            ],
                            'REJECTED' => [
                                'bg' => 'bg-red-100',
                                'text' => 'text-red-700',
                                'border' => 'border-red-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
                                'label' => 'REJECTED'
                            ],
                            'CANCELLED' => [
                                'bg' => 'bg-red-100',
                                'text' => 'text-red-700',
                                'border' => 'border-red-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
                                'label' => 'CANCELLED'
                            ],
                            default => [
                                'bg' => 'bg-gray-100',
                                'text' => 'text-gray-700',
                                'border' => 'border-gray-200',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label' => $req->status
                            ],
                        };
                    @endphp

                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} text-sm font-semibold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $statusConfig['icon'] !!}
                        </svg>
                        {{ $statusConfig['label'] }}
                    </span>

                    @if($req->status === 'REQUESTED')
                        <x-crud 
            resource="request"
            :model="$req"
            :companyCode="$companyCode"
            permissionPrefix="inventory"
            keyField="id"
        />
                    @endif
                </div>
            </div>
        </div>

        {{-- INFORMASI CABANG --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Transfer</h2>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Cabang Asal --}}
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cabang Asal</p>
                                <p class="font-semibold text-gray-900 text-lg">
                                    {{ $req->warehouseFrom->cabangResto->name }}
                                </p>
                                <p class="text-sm text-gray-600 mt-0.5">
                                    Kode: <span class="font-medium">{{ $req->warehouseFrom->cabangResto->code }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="pl-13">
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs font-medium text-blue-700 mb-1">Gudang Pengirim</p>
                                <p class="text-sm font-semibold text-blue-900">{{ $req->warehouseFrom->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Cabang Tujuan --}}
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mt-0.5">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cabang Tujuan</p>
                                <p class="font-semibold text-gray-900 text-lg">
                                    {{ $req->warehouseTo->cabangResto->name }}
                                </p>
                                <p class="text-sm text-gray-600 mt-0.5">
                                    Kode: <span class="font-medium">{{ $req->warehouseTo->cabangResto->code }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="pl-13">
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                                <p class="text-xs font-medium text-emerald-700 mb-1">Gudang Penerima</p>
                                <p class="text-sm font-semibold text-emerald-900">{{ $req->warehouseTo->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL ITEM --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Detail Item</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Total: {{ $req->details->count() }} item</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 border-b-2 border-gray-200">
                        <tr class="text-left text-gray-700">
                            <th class="px-6 py-4 font-semibold">No</th>
                            <th class="px-6 py-4 font-semibold">Item</th>
                            <th class="px-6 py-4 text-center font-semibold w-32">Quantity</th>
                            <th class="px-6 py-4 font-semibold w-40">Satuan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                    @foreach ($req->details as $d)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $d->item->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">SKU: {{ $d->item->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-900 rounded-lg font-semibold">
                                        {{ number_format($d->qty, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded text-xs font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $d->item->satuan->name }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 font-medium">Total Item</span>
                    <span class="text-gray-900 font-bold text-lg">{{ $req->details->count() }} item</span>
                </div>
            </div>
        </div>

        {{-- CATATAN --}}
        @if ($req->note)
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Catatan</h2>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">{{ $req->note }}</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>