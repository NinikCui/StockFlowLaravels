<x-app-layout :branchCode="$branchCode">

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pb-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-green-600 via-green-500 to-lime-600 rounded-3xl shadow-2xl p-8 mb-8">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-white opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white opacity-10 blur-2xl"></div>
            
            <div class="relative">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border-2 border-white/30">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Riwayat Order</h1>
                            <p class="text-green-100 text-sm sm:text-base mt-1 font-medium">Shift #{{ $shift->id }}</p>
                        </div>
                    </div>

                    <a href="{{ route('branch.pos.shift.index', $branchCode) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-semibold rounded-xl border-2 border-white/30 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>

                {{-- Shift Info --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <p class="text-green-100 text-xs mb-1">Waktu Buka</p>
                        <p class="text-white font-bold text-lg">{{ \Carbon\Carbon::parse($shift->opened_at)->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <p class="text-green-100 text-xs mb-1">Waktu Tutup</p>
                        <p class="text-white font-bold text-lg">
                            {{ $shift->closed_at ? \Carbon\Carbon::parse($shift->closed_at)->format('d M Y, H:i') : 'Masih Buka' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        @if($shift->orders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Order</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $shift->orders->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 bg-gradient-to-br from-green-100 to-lime-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($shift->orders->sum(function($order) {
                                return is_array($order->receipt_items)
                                    ? collect($order->receipt_items)->sum('subtotal')
                                    : $order->details->sum(fn($d) => $d->qty * $d->price);
                            }), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Rata-rata Order</p>
                        <p class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($shift->orders->avg(function($order) {
                                return is_array($order->receipt_items)
                                    ? collect($order->receipt_items)->sum('subtotal')
                                    : $order->details->sum(fn($d) => $d->qty * $d->price);
                            }), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ORDER LIST --}}
        <div class="space-y-6">
            @forelse($shift->orders as $order)
                @php
                    $total = is_array($order->receipt_items)
                        ? collect($order->receipt_items)->sum('subtotal')
                        : $order->details->sum(fn($d) => $d->qty * $d->price);
                    $payment = $order->payments->first();
                @endphp

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-200">
                    
                    {{-- ORDER HEADER --}}
                    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b-2 border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="h-10 w-10 bg-gradient-to-br from-green-500 to-lime-600 rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                                        <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($order->order_datetime)->format('d M Y, H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-sm text-gray-500 mb-1">Total Pembayaran</p>
                                    <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('branch.pos.order.receipt', [$branchCode, $order->id]) }}" 
                                   target="_blank"
                                   class="h-10 w-10 bg-green-100 hover:bg-green-200 text-green-600 rounded-lg flex items-center justify-center transition-colors shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ORDER DETAILS --}}
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Detail Pesanan
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b-2 border-gray-200">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700 bg-gray-50">Produk</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-700 bg-gray-50">Qty</th>
                                        <th class="py-3 px-4 text-right font-semibold text-gray-700 bg-gray-50">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    {{-- MODE BARU (BUNDLE AWARE) --}}
                                    @if(is_array($order->receipt_items) && count($order->receipt_items))
                                        @foreach($order->receipt_items as $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="flex items-start gap-2">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            @if(($item['type'] ?? null) === 'BUNDLE')
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                                                                    🎁 PAKET
                                                                </span>
                                                            @endif
                                                            <span class="font-semibold text-gray-900">{{ $item['name'] }}</span>
                                                        </div>

                                                        @if(($item['type'] ?? null) === 'BUNDLE' && !empty($item['items']) && is_array($item['items']))
                                                            <div class="mt-2 ml-4 space-y-1 bg-purple-50 rounded-lg p-3 border border-purple-100">
                                                                <p class="text-xs font-semibold text-purple-700 mb-1.5">Isi Paket:</p>
                                                                @foreach($item['items'] as $bi)
                                                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                                                        <span class="text-purple-400">•</span>
                                                                        <span>{{ $bi['name'] }}</span>
                                                                        <span class="px-1.5 py-0.5 bg-purple-200 text-purple-700 rounded font-semibold">{{ $bi['qty'] }}x</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if(!empty($item['note']))
                                                            <div class="mt-2 flex items-start gap-2 text-xs bg-amber-50 rounded-lg p-2 border border-amber-100">
                                                                <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <span class="text-amber-700 italic"><strong>Catatan:</strong> {{ $item['note'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-4 text-center">
                                                <span class="inline-flex items-center justify-center h-8 w-8 bg-emerald-100 text-emerald-700 rounded-lg font-bold">
                                                    {{ $item['qty'] }}
                                                </span>
                                            </td>

                                            <td class="py-4 px-4 text-right font-semibold text-gray-900">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach

                                    {{-- FALLBACK ORDER LAMA --}}
                                    @else
                                        @foreach($order->details as $d)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="font-semibold text-gray-900 mb-1">{{ $d->product->name }}</div>
                                                @if($d->note_line)
                                                    <div class="flex items-start gap-2 text-xs bg-amber-50 rounded-lg p-2 border border-amber-100 mt-2">
                                                        <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-amber-700 italic"><strong>Catatan:</strong> {{ $d->note_line }}</span>
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="py-4 px-4 text-center">
                                                <span class="inline-flex items-center justify-center h-8 w-8 bg-emerald-100 text-emerald-700 rounded-lg font-bold">
                                                    {{ $d->qty }}
                                                </span>
                                            </td>

                                            <td class="py-4 px-4 text-right font-semibold text-gray-900">
                                                Rp {{ number_format($d->qty * $d->price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- PAYMENT SECTION --}}
                        @if($payment)
                            <div class="mt-6 bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border-2 border-gray-200">
                                <h4 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Informasi Pembayaran
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Metode Pembayaran</p>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold
                                            @if($payment->method === 'CASH')
                                                bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                            @elseif($payment->method === 'QRIS')
                                                bg-green-100 text-green-700 border-2 border-green-200
                                            @else
                                                bg-gray-100 text-gray-700 border-2 border-gray-200
                                            @endif
                                        ">
                                            @if($payment->method === 'CASH')
                                                💵
                                            @elseif($payment->method === 'QRIS')
                                                📱
                                            @else
                                                💳
                                            @endif
                                            {{ strtoupper($payment->method) }}
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold
                                            @if($payment->status === 'SUCCESS')
                                                bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                            @else
                                                bg-yellow-100 text-yellow-700 border-2 border-yellow-200
                                            @endif
                                        ">
                                            @if($payment->status === 'SUCCESS')
                                                ✓
                                            @else
                                                ⏳
                                            @endif
                                            {{ $payment->status }}
                                        </span>
                                    </div>
                                </div>

                                @if($payment->ref_number)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">Referensi Number</p>
                                        <p class="font-mono text-sm font-semibold text-gray-900 bg-gray-100 px-3 py-2 rounded-lg inline-block">
                                            {{ $payment->ref_number }}
                                        </p>
                                    </div>
                                @endif

                                @if($payment->method === 'CASH')
                                    <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Uang Dibayar</p>
                                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Kembalian</p>
                                            <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($payment->change_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-gray-50 rounded-xl border-2 border-gray-200 text-center">
                                <p class="text-sm text-gray-500 italic">Tidak ada data pembayaran</p>
                            </div>
                        @endif
                    </div>

                </div>

            @empty
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-16 text-center">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 text-lg font-semibold mb-2">Belum Ada Transaksi</p>
                    <p class="text-gray-400 text-sm">Belum ada transaksi yang tercatat pada shift ini</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

</x-app-layout>