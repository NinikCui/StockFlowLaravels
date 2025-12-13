<x-app-layout :branchCode="$branchCode">

<div class="min-h-screen pb-10">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- HEADER --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-green-600 via-lime-600 to-yellow-600 rounded-2xl shadow-2xl p-6 mb-6">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-24 w-24 rounded-full bg-white opacity-10"></div>
            
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg border border-white/30">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Point of Sale</h1>
                        <p class="text-green-100 text-sm sm:text-base mt-1">{{ $branch->name }}</p>
                    </div>
                </div>

                <a href="{{ route('branch.pos.shift.index', $branchCode) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-semibold rounded-xl border border-white/30 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Shift</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ======================================
                 PRODUK GRID
            ======================================= --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-lime-500 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Menu Produk</h2>
                                <p class="text-xs text-gray-500">Pilih produk untuk ditambahkan</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 bg-green-100 text-lime-700 text-sm font-semibold rounded-full">
                            {{ count($products) }} Items
                        </span>
                    </div>

                    {{-- Product Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($products as $p)
                            @php
                                $disabled = !$p->is_available;
                                $isRecommended = in_array($p->id, $recommendedProductIds);
                            @endphp

                            <form 
                                action="{{ $disabled ? '#' : route('branch.pos.order.add', $branchCode) }}" 
                                method="POST"
                                class="{{ $disabled ? 'pointer-events-none' : '' }} relative"
                            >
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $p->id }}">

                                {{-- ⭐ BADGE REKOMENDASI MENU (Fixed Position) --}}
                                @if($isRecommended)
                                    <div class="absolute -top-2 -right-2 z-10">
                                        <span class="inline-flex items-center gap-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg border-2 border-white animate-pulse">
                                            ⭐ Hot
                                        </span>
                                    </div>
                                @endif

                                <button type="{{ $disabled ? 'button' : 'submit' }}"
                                    class="group w-full rounded-2xl transition-all duration-200 p-4 text-left shadow-md border-2
                                        {{ $disabled 
                                            ? 'bg-gray-50 border-gray-200 opacity-60 cursor-not-allowed' 
                                            : ($isRecommended 
                                                ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-yellow-300 hover:border-orange-400 hover:shadow-xl hover:scale-105 active:scale-100'
                                                : 'bg-white border-transparent hover:border-green-400 hover:shadow-xl hover:scale-105 active:scale-100')
                                        }}"
                                >
                                    {{-- Product Icon --}}
                                    <div class="mb-3 h-16 w-16 mx-auto rounded-2xl flex items-center justify-center text-3xl
                                        {{ $disabled 
                                            ? 'bg-gray-100' 
                                            : ($isRecommended 
                                                ? 'bg-gradient-to-br from-yellow-100 to-orange-100 group-hover:from-yellow-200 group-hover:to-orange-200'
                                                : 'bg-gradient-to-br from-green-100 to-lime-100 group-hover:from-green-200 group-hover:to-lime-200')
                                        }}
                                        transition-colors duration-200">
                                        🍽️
                                    </div>

                                    {{-- NAMA PRODUK --}}
                                    <div class="text-sm font-bold text-gray-900 mb-2 line-clamp-2 text-center min-h-[2.5rem]">
                                        {{ $p->name }}
                                    </div>

                                    {{-- HARGA --}}
                                    <div class="text-center mb-3">
                                        <span class="text-lg font-bold {{ $isRecommended ? 'text-orange-600' : 'text-green-600' }}">
                                            Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- BADGE STATUS --}}
                                    @if($disabled)
                                        <div class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-full font-semibold text-center flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Stok Habis</span>
                                        </div>
                                    @else
                                        <div class="bg-gradient-to-r {{ $isRecommended ? 'from-orange-500 to-yellow-500 group-hover:from-orange-600 group-hover:to-yellow-600' : 'from-green-500 to-lime-500 group-hover:from-green-600 group-hover:to-lime-600' }} text-white text-xs px-3 py-1.5 rounded-full font-semibold text-center flex items-center justify-center gap-1 transition-all duration-200">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Tambah</span>
                                        </div>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- ======================================
                 CART SIDEBAR
            ======================================= --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 sticky top-6">
                    
                    {{-- Cart Header --}}
                    <div class="bg-gradient-to-r from-emerald-500 to-green-500 p-6 rounded-t-2xl">
                        <div class="flex items-center gap-3 text-white">
                            <div class="h-12 w-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Keranjang</h2>
                                <p class="text-emerald-100 text-sm">
                                    {{ empty($cart) ? 'Kosong' : count($cart) . ' Item' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- EMPTY CART --}}
                        @if(empty($cart))
                            <div class="text-center py-12">
                                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Keranjang masih kosong</p>
                                <p class="text-gray-400 text-xs mt-1">Tambahkan produk dari menu</p>
                            </div>
                        @else
                            {{-- CART LIST --}}
                            <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                                @foreach($cart as $item)
                                <div class="group bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border-2 border-gray-100 hover:border-emerald-300 transition-all duration-200 hover:shadow-md">
                                    
                                    {{-- TITLE BAR --}}
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1 pr-2">
                                            <p class="font-bold text-gray-900 mb-1 leading-tight">{{ $item['name'] }}</p>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-semibold">
                                                    {{ $item['qty'] }}x
                                                </span>
                                                <span>Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <p class="font-bold text-base text-emerald-600 whitespace-nowrap">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- NOTE --}}
                                    <div class="mb-3">
                                        <textarea
                                            class="w-full text-xs border-2 border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all duration-200 resize-none"
                                            placeholder="Tambahkan catatan khusus..."
                                            rows="2"
                                            oninput="updateNote({{ $item['id'] }}, this.value)"
                                        >{{ $item['note'] ?? '' }}</textarea>
                                    </div>

                                    {{-- REMOVE BUTTON --}}
                                    <form method="POST" action="{{ route('branch.pos.order.remove', $branchCode) }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <button type="submit" class="flex items-center gap-1.5 text-red-500 text-xs font-semibold hover:text-red-700 transition-colors group">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="group-hover:underline">Hapus Item</span>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>

                            {{-- TOTAL SECTION --}}
                            <div class="mt-6 pt-6 border-t-2 border-gray-200">
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Subtotal</span>
                                        <span class="font-semibold">Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border-2 border-emerald-200">
                                    <span class="text-lg font-bold text-gray-900">Total Bayar</span>
                                    <span class="text-2xl font-bold text-emerald-600">
                                        Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- PAY BUTTON --}}
                            <button type="button"
                                onclick="openPaymentModal()"
                                class="group w-full bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 mt-6 flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Proses Pembayaran</span>
                            </button>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

@include("branch.pos.order.modal")

<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Modal Animation */
#paymentModal.show #paymentModalContent,
#cashModal.show #cashModalContent {
    transform: scale(1);
    opacity: 1;
}

/* Line Clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

{{-- ======================================
        SCRIPT AJAX NOTE
======================================= --}}
<script>
function updateNote(productId, note) {
    fetch("{{ route('branch.pos.order.note', $branchCode) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            product_id: productId,
            note: note,
        }),
    });
}
</script>

{{-- ======================================
       CONTROL MODAL
======================================= --}}
<script>
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.style.transform = 'scale(1)';
        content.style.opacity = '1';
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    content.style.transform = 'scale(0.95)';
    content.style.opacity = '0';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openCashModal() {
    closePaymentModal();
    setTimeout(() => {
        const modal = document.getElementById('cashModal');
        modal.classList.remove('hidden');
    }, 300);
}

function closeCashModal() {
    document.getElementById('cashModal').classList.add('hidden');
    document.getElementById('cashPaid').value = '';
    document.getElementById('changeInfo').innerHTML = '';
}
</script>

{{-- ======================================
       CASH PAYMENT LOGIC
======================================= --}}
<script>
function calcChange() {
    let paid = parseFloat(document.getElementById("cashPaid").value || 0);
    let total = {{ collect($cart)->sum('subtotal') }};

    let change = paid - total;
    const changeInfo = document.getElementById("changeInfo");

    if (paid === 0) {
        changeInfo.className = "p-4 rounded-xl border-2 border-gray-200 bg-gray-50 min-h-[60px] flex items-center justify-center";
        changeInfo.innerHTML = "<span class='text-gray-400 text-sm'>Masukkan nominal uang...</span>";
        document.getElementById("paidAmountField").value = "";
        return;
    }

    if (change < 0) {
        changeInfo.className = "p-4 rounded-xl border-2 border-red-200 bg-red-50 min-h-[60px] flex flex-col items-center justify-center";
        changeInfo.innerHTML = `
            <svg class="w-6 h-6 text-red-500 mb-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class='text-red-600 font-bold'>Uang kurang Rp ${Math.abs(change).toLocaleString('id-ID')}</span>
        `;
        document.getElementById("paidAmountField").value = "";
        return;
    }

    changeInfo.className = "p-4 rounded-xl border-2 border-emerald-200 bg-emerald-50 min-h-[60px] flex flex-col items-center justify-center";
    changeInfo.innerHTML = `
        <span class='text-sm text-gray-600 mb-1'>Kembalian</span>
        <span class='text-2xl font-bold text-emerald-600'>Rp ${change.toLocaleString('id-ID')}</span>
    `;
    document.getElementById("paidAmountField").value = paid;
}
</script>

{{-- ======================================
       MIDTRANS SNAP
======================================= --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function payMidtrans() {
    fetch("{{ route('branch.pos.order.midtrans', $branchCode) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        snap.pay(data.snap_token, {
            onSuccess: function(result){
                finishPayment(result);
            },
            onPending: function(result){
                finishPayment(result);
            },
            onError: function(){
                alert("Pembayaran gagal.");
            },
        });
    });
}

function finishPayment(result) {
    fetch("{{ route('branch.pos.order.pay', $branchCode) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            payment_method: "MIDTRANS",
            midtrans_result: result,
        }),
    })
    .then(() => window.location.reload());
}
</script>

</x-app-layout>