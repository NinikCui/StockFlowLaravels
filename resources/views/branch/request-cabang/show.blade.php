<x-app-layout :branchCode="$branchCode">
    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- BREADCRUMB --}}
        <div class="mb-8">
            <a href="{{ route('branch.request.index', $branchCode) }}"
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

                {{-- LEFT --}}
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600
                                flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">
                            {{ $req->trans_number }}
                        </h1>

                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Diajukan pada {{ $req->trans_date->format('d M Y') }}
                        </div>
                    </div>
                </div>

                {{-- STATUS BADGE --}}
                <div class="flex items-center gap-3">
                    @php
                        $statusConfig = match($req->status) {
                            'REQUESTED' => ['bg'=>'bg-yellow-100','text'=>'text-yellow-700','border'=>'border-yellow-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label'=>'REQUESTED'],
                            'APPROVED' => ['bg'=>'bg-blue-100','text'=>'text-blue-700','border'=>'border-blue-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label'=>'APPROVED'],
                            'IN_TRANSIT' => ['bg'=>'bg-purple-100','text'=>'text-purple-700','border'=>'border-purple-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                'label'=>'IN TRANSIT'],
                            'RECEIVED' => ['bg'=>'bg-emerald-100','text'=>'text-emerald-700','border'=>'border-emerald-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"/>',
                                'label'=>'RECEIVED'],
                            'CANCELLED','REJECTED' => ['bg'=>'bg-red-100','text'=>'text-red-700','border'=>'border-red-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>',
                                'label'=>$req->status],
                            default => ['bg'=>'bg-gray-100','text'=>'text-gray-700','border'=>'border-gray-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'label'=>$req->status],
                        };
                    @endphp

                    <div class="flex items-center gap-3">

                        {{-- BADGE STATUS --}}
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border 
                            {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}
                            text-sm font-semibold shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $statusConfig['icon'] !!}
                            </svg>
                            {{ $statusConfig['label'] }}
                        </span>

                        {{-- TOMBOL UNTUK PENGIRIM --}}
                        @if(session('role.branch.id') == $req->cabang_id_from)

                            @if($req->status === 'REQUESTED')
                                <form action="{{ route('branch.request.approve', [$branchCode, $req->id]) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm">Accept</button>
                                </form>

                                <form action="{{ route('branch.request.reject', [$branchCode, $req->id]) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm">Tolak</button>
                                </form>
                            @endif
                        
                        {{-- TOMBOL UNTUK PENERIMA --}}
                        @elseif(session('role.branch.id') == $req->cabang_id_to)

                            <x-crud 
                                resource="branch.request"
                                keyField="id"
                                :companyCode="$branchCode"
                                :model="$req"
                                permissionPrefix="inventory"
                            />

                        @endif

                    </div>

                </div>
            </div>
        </div>

        {{-- INFORMASI CABANG --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Informasi Transfer
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- FROM --}}
                <div class="space-y-2">
                    <p class="text-xs text-gray-500 uppercase">Cabang Asal</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $req->cabangFrom->name }}</p>
                    <p class="text-sm text-gray-600">Kode: {{ $req->cabangFrom->code }}</p>
                </div>

                {{-- TO --}}
                <div class="space-y-2">
                    <p class="text-xs text-gray-500 uppercase">Cabang Tujuan</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $req->cabangTo->name }}</p>
                    <p class="text-sm text-gray-600">Kode: {{ $req->cabangTo->code }}</p>
                </div>
            </div>

        </div>

        {{-- DETAIL ITEM --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden mb-6">

            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Detail Item</h2>
                <p class="text-xs text-gray-500 mt-1">Total: {{ $req->details->count() }} item</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-700">
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Item</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3">Satuan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                    @foreach ($req->details as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $loop->iteration }}</td>
                            <td class="px-6 py-3">
                                <strong>{{ $d->item->name }}</strong>
                                <p class="text-xs text-gray-500">ID: {{ $d->item->id }}</p>
                            </td>
                            <td class="px-6 py-3 text-center font-semibold">
                                {{ number_format($d->qty, 2) }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $d->item->satuan->name }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- CATATAN --}}
        @if ($req->note)
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Catatan</h2>
            </div>
            <div class="p-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-gray-800 whitespace-pre-line">{{ $req->note }}</p>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
