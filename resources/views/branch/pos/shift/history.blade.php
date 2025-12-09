<x-app-layout :branchCode="$branchCode">

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <span class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl
                       grid place-items-center text-white">📜</span>
            Riwayat Order – Shift #{{ $shift->id }}
        </h1>

        <p class="text-gray-600 mt-2 text-sm leading-5">
            <strong>Dibuka:</strong> {{ $shift->opened_at }} <br>
            <strong>Ditutup:</strong> {{ $shift->closed_at ?? '—' }}
        </p>
    </div>

    {{-- LIST ORDER --}}
    <div class="bg-white rounded-xl border shadow divide-y">

        @forelse($shift->orders as $order)
        @php
            $total = $order->details->sum(fn($d) => $d->qty * $d->price);
            $payment = $order->payments->first();
        @endphp

        <div class="p-6">

            {{-- ORDER HEADER --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Order #{{ $order->order_number }}
                    </h2>

                    <p class="text-xs text-gray-600">
                        {{ $order->order_datetime }}
                    </p>
                </div>

                <div class="text-right font-semibold text-emerald-700 text-lg">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </div>
            </div>

            {{-- ORDER DETAILS --}}
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Produk</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->details as $d)
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-semibold text-gray-900">
                                    {{ $d->product->name }}
                                </div>

                                @if($d->note_line)
                                    <div class="text-xs text-gray-600 italic">
                                        Catatan: "{{ $d->note_line }}"
                                    </div>
                                @endif
                            </td>

                            <td class="py-2 text-center">
                                {{ $d->qty }}
                            </td>

                            <td class="py-2 text-right">
                                Rp {{ number_format($d->qty * $d->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- PAYMENT SECTION --}}
            @if($payment)
                <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm">

                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-700">Metode Pembayaran</span>

                        <span class="
                            px-3 py-1 rounded-full text-xs font-semibold
                            @if($payment->method === 'CASH')
                                bg-green-100 text-green-700 border border-green-200
                            @elseif($payment->method === 'QRIS')
                                bg-blue-100 text-blue-700 border border-blue-200
                            @else
                                bg-gray-100 text-gray-700 border border-gray-200
                            @endif
                        ">
                            {{ strtoupper($payment->method) }}
                        </span>
                    </div>

                    {{-- STATUS --}}
                    <div class="mt-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="
                            font-semibold
                            @if($payment->status === 'SUCCESS')
                                text-emerald-600
                            @else
                                text-yellow-600
                            @endif
                        ">
                            {{ $payment->status }}
                        </span>
                    </div>

                    {{-- REF NUMBER (MIDTRANS) --}}
                    @if($payment->ref_number)
                        <div class="mt-1 text-gray-600 text-xs">
                            Ref: <span class="font-mono">{{ $payment->ref_number }}</span>
                        </div>
                    @endif

                    {{-- CASH PAID + CHANGE --}}
                    @if($payment->method === 'CASH')
                        <div class="mt-3 border-t pt-2 text-xs text-gray-700">
                            <div>Dibayar: <strong>Rp {{ number_format($payment->paid_amount ?? $total, 0, ',', '.') }}</strong></div>

                            @if(isset($payment->change_amount))
                            <div>Kembalian: 
                                <strong class="text-emerald-700">
                                    Rp {{ number_format($payment->change_amount, 0, ',', '.') }}
                                </strong>
                            </div>
                            @endif
                        </div>
                    @endif

                </div>
            @else
                <div class="text-xs text-gray-500 italic mt-2">
                    Tidak ada data pembayaran.
                </div>
            @endif

        </div>

        @empty
            <div class="p-10 text-center text-gray-500">
                Belum ada transaksi pada shift ini.
            </div>
        @endforelse

    </div>

</div>

</x-app-layout>
