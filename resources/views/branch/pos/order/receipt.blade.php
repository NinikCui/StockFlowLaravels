<x-app-layout :branchCode="$branchCode">

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        
        {{-- ACTION BUTTONS (Hidden on Print) --}}
        <div class="flex gap-3 mb-6 print:hidden">
            <button onclick="window.print()" 
                class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Nota
            </button>
            
            <a href="{{ route('branch.pos.order.index', $branchCode) }}" 
                class="bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 border-2 border-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        {{-- RECEIPT CONTAINER --}}
        <div id="receipt" class="bg-white rounded-2xl shadow-2xl overflow-hidden print:shadow-none print:rounded-none">
            
            {{-- HEADER --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-8 text-center print:bg-white print:text-gray-900 print:border-b-2 print:border-gray-300">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center print:bg-gray-100">
                        <svg class="w-9 h-9 text-white print:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold mb-1">{{ $branch->name ?? 'Toko Kami' }}</h1>
                <p class="text-emerald-100 text-sm print:text-gray-600">Nota Pembayaran</p>
            </div>

            <div class="p-8">
                
                {{-- ORDER INFO --}}
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 mb-6 border-2 border-gray-200 print:border print:rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs mb-1">No. Order</p>
                            <p class="font-bold text-gray-900 text-base">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Tanggal & Waktu</p>
                            <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($order->order_datetime)->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Kasir</p>
                            <p class="font-semibold text-gray-900">{{ $order->cashier->username ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-900">
                                @if($payment->method == 'CASH')
                                    💵 Tunai
                                @elseif($payment->method === 'MIDTRANS')
                                    💳 Digital
                                @else
                                    {{ $payment->method }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- DIVIDER --}}
                <div class="border-t-2 border-dashed border-gray-300 my-6"></div>

                {{-- ITEMS LIST --}}
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Detail Pesanan
                    </h3>

                    <div class="space-y-4">
                        @foreach($order->receipt_items as $item)
                            <div class="bg-gray-50 rounded-lg p-4 print:bg-white print:border print:border-gray-200">
                                
                                {{-- Item Header --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if(($item['type'] ?? '') === 'BUNDLE')
                                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-bold">PAKET</span>
                                            @endif
                                            <span class="font-bold text-gray-900">{{ $item['name'] }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="inline-flex items-center gap-1">
                                                <span class="font-semibold text-emerald-600">{{ $item['qty'] }}x</span>
                                                <span>@ Rp {{ number_format($item['price'] ?? ($item['subtotal'] / $item['qty']), 0, ',', '.') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-emerald-600">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Bundle Items --}}
                                @if(($item['type'] ?? '') === 'BUNDLE' && !empty($item['items']))
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs text-gray-500 font-semibold mb-2">Isi Paket:</p>
                                        <div class="ml-3 space-y-1">
                                            @foreach($item['items'] as $bi)
                                                <div class="text-sm text-gray-600 flex items-start gap-2">
                                                    <span class="text-emerald-500 mt-0.5">•</span>
                                                    <span>{{ $bi['name'] }} <span class="text-gray-400">({{ $bi['qty'] }}x)</span></span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Note --}}
                                @if(!empty($item['note']))
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex items-start gap-2 text-sm">
                                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-semibold mb-0.5">Catatan:</p>
                                                <p class="text-gray-700 italic">{{ $item['note'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- DIVIDER --}}
                <div class="border-t-2 border-dashed border-gray-300 my-6"></div>

                {{-- SUMMARY --}}
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format(collect($order->receipt_items)->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
                    
                    @if(isset($order->tax) && $order->tax > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Pajak</span>
                        <span class="font-semibold">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(isset($order->discount) && $order->discount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Diskon</span>
                        <span class="font-semibold">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                {{-- TOTAL --}}
                <div class="mt-6 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border-2 border-emerald-300 print:border print:border-gray-300">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-3xl font-bold text-emerald-600">
                            Rp {{ number_format(collect($order->receipt_items)->sum('subtotal'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- PAYMENT DETAILS (if cash) --}}
                @if($payment && $payment->method === 'CASH')
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Tunai Dibayar</span>
                        <span class="font-semibold">
                            Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-emerald-600">
                        <span class="font-semibold">Kembalian</span>
                        <span class="font-bold">
                            Rp {{ number_format($payment->change_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif


                {{-- FOOTER --}}
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-600 text-sm mb-2">Terima kasih atas kunjungan Anda!</p>
                    <p class="text-gray-400 text-xs">Nota ini adalah bukti pembayaran yang sah</p>
                    
                    {{-- QR Code or Barcode Space --}}
                    <div class="mt-4 flex justify-center print:block">
                        <div class="bg-gray-100 rounded-lg p-4 inline-block">
                            <p class="text-xs text-gray-500 mb-2">Order Number</p>
                            <p class="font-mono font-bold text-gray-900">{{ $order->order_number }}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- PRINT AGAIN BUTTON --}}
        <div class="mt-6 text-center print:hidden">
            <button onclick="window.print()" 
                class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Ulang
            </button>
        </div>

    </div>
</div>

{{-- PRINT STYLES --}}
<style>
@media print {
    body {
        margin: 0;
        padding: 0;
    }
    
    /* Hide everything except receipt */
    body * {
        visibility: hidden;
    }
    
    #receipt, #receipt * {
        visibility: visible;
    }
    
    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    
    /* Remove gradients for print */
    .bg-gradient-to-br,
    .bg-gradient-to-r {
        background: white !important;
        color: black !important;
    }
    
    /* Ensure proper page breaks */
    .print\:break-inside-avoid {
        break-inside: avoid;
    }
    
    /* Adjust spacing for print */
    .p-8 {
        padding: 1rem !important;
    }
    
    /* Black and white optimization */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}

/* Screen only styles */
@media screen {
    .print\:hidden {
        display: block;
    }
}
</style>

</x-app-layout>