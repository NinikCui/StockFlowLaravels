{{-- ======================================
   MODAL: METODE PEMBAYARAN
======================================= --}}
<div id="paymentModal"
     class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-6 z-50 transition-all duration-300">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="paymentModalContent">
        
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-green-600 to-lime-600 p-6 rounded-t-2xl">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pilih Metode Pembayaran</span>
            </h2>
        </div>

        <div class="p-6 space-y-3">
            {{-- CASH --}}
            <button onclick="openCashModal()"
                class="group w-full bg-gradient-to-r from-gray-100 to-gray-50 hover:from-emerald-500 hover:to-green-500 border-2 border-gray-200 hover:border-transparent py-4 rounded-xl font-bold text-gray-700 hover:text-white transition-all duration-200 flex items-center justify-center gap-3 hover:shadow-lg hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Cash / Tunai</span>
            </button>

            {{-- MIDTRANS QRIS --}}
            <button onclick="payMidtrans()"
                class="group w-full bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white py-4 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>QRIS / E-Wallet</span>
            </button>

            <button onclick="closePaymentModal()"
                class="w-full text-red-500 hover:text-red-700 py-3 font-bold transition-colors duration-200 hover:bg-red-50 rounded-xl">
                Batalkan
            </button>
        </div>
    </div>

</div>

{{-- ======================================
   MODAL: CASH PAYMENT INPUT
======================================= --}}
<div id="cashModal"
     class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-6 z-50">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl" id="cashModalContent">
        
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-emerald-500 to-green-500 p-6 rounded-t-2xl">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pembayaran Cash</span>
            </h2>
        </div>

        <div class="p-6 space-y-5">
            <div class="p-4 bg-green-50 border-2 border-green-200 rounded-xl">
                <p class="text-sm text-gray-600 mb-1">Total yang harus dibayar:</p>
                <p class="text-3xl font-bold text-green-600">
                    Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}
                </p>
            </div>

            <div>
                <label class="text-sm text-gray-700 font-bold mb-2 block">Uang yang diterima:</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input type="number" id="cashPaid"
                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-4 text-lg font-bold focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all duration-200"
                           placeholder="0"
                           oninput="calcChange()">
                </div>
            </div>

            <div id="changeInfo" class="p-4 rounded-xl border-2 min-h-[60px] flex items-center justify-center"></div>

            <form id="cashForm" method="POST" action="{{ route('branch.pos.order.pay', $branchCode) }}" class="space-y-3">
                @csrf
                <input type="hidden" name="payment_method" value="CASH">
                <input type="hidden" name="paid_amount" id="paidAmountField">

                <button type="submit"
                    class="w-full bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white py-4 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Selesaikan Pembayaran</span>
                </button>

                <button type="button" onclick="closeCashModal()"
                    class="w-full text-red-500 hover:text-red-700 py-3 font-bold transition-colors duration-200 hover:bg-red-50 rounded-xl">
                    Batalkan
                </button>
            </form>
        </div>
    </div>

</div>