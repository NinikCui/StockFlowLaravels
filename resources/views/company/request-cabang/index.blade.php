<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Material Request Antar Cabang</h1>
                    <p class="text-sm text-gray-600">Kelola dan pantau semua permintaan transfer bahan antar cabang dalam satu tempat.</p>
                </div>
            </div>

            <a href="{{ route('request.create', $companyCode) }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg shadow-lg shadow-emerald-500/30 hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-150 font-medium whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Request
            </a>
        </div>
    </div>

    {{-- ERROR --}}
    @isset($error)
        <div class="mb-6 rounded-xl border-l-4 border-red-500 bg-gradient-to-r from-red-50 to-red-50/50 px-5 py-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-red-100 flex items-center justify-center mt-0.5">
                    <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
            </div>
        </div>
    @endisset

    {{-- TABLE CARD --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        
        {{-- Table Header Info --}}
        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Daftar Request</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Total: {{ $requests->count() }} request</p>
                    </div>
                </div>
                
                {{-- Optional: Filter/Search placeholder --}}
                <div class="hidden sm:flex items-center gap-2">
                    <span class="text-xs text-gray-500">Status:</span>
                    <div class="flex items-center gap-1">
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">Semua</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 border-b-2 border-gray-200">
                    <tr class="text-left text-gray-700">
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Transaksi</th>
                        <th class="px-6 py-4 font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                Cabang Asal
                            </div>
                        </th>
                        <th class="px-6 py-4 font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                Cabang Tujuan
                            </div>
                        </th>
                        <th class="px-6 py-4 font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Tanggal
                            </div>
                        </th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">{{ $req->trans_number }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">Request #{{ $req->id }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-900">{{ $req->warehouseFrom?->cabangResto?->name ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-900">{{ $req->warehouseTo?->cabangResto?->name ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ date('d M Y', strtotime($req->trans_date)) }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
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

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $statusConfig['icon'] !!}
                                    </svg>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('request.show', [$companyCode, $req->id]) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-medium hover:bg-black transition-all duration-150 shadow-sm hover:shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-900 font-semibold mb-1">Belum ada request</p>
                                        <p class="text-sm text-gray-500">Klik tombol "Buat Request" untuk membuat request baru</p>
                                    </div>
                                    <a href="{{ route('request.create', $companyCode) }}"
                                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Buat Request Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer (Optional: Pagination placeholder) --}}
        @if($requests->count() > 0)
        <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Menampilkan {{ $requests->count() }} dari {{ $requests->count() }} request</span>
                {{-- Pagination placeholder --}}
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">Halaman 1 dari 1</span>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
</x-app-layout>