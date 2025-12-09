<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <span class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl grid place-items-center text-white">
                🛒
            </span>
            POS – {{ $branch->name }}
        </h1>

        <a href="{{ route('branch.pos.shift.index', $branchCode) }}"
           class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800">
            ⬅ Kembali ke Shift
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ======================================
             PRODUK GRID
        ======================================= --}}
        <div class="lg:col-span-2">

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($products as $p)
                <form action="{{ route('branch.pos.order.add', $branchCode) }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">

                    <button type="submit"
                        class="w-full bg-white border border-gray-200 rounded-xl shadow hover:shadow-md transition p-4 text-left">

                        <div class="text-lg font-semibold text-gray-800 mb-1">
                            {{ $p->name }}
                        </div>

                        <div class="text-sm text-gray-500 mb-3">
                            Rp {{ number_format($p->base_price, 0, ',', '.') }}
                        </div>

                        <div class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full inline-block">
                            + Tambah
                        </div>
                    </button>
                </form>
                @endforeach
            </div>

        </div>



        {{-- ======================================
             CART SIDEBAR
        ======================================= --}}
        <div class="lg:col-span-1">

            <div class="bg-white border border-gray-200 rounded-2xl shadow p-6 sticky top-10">

                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    🧺 Keranjang
                </h2>

                {{-- EMPTY CART --}}
                @if(empty($cart))
                    <p class="text-gray-500 text-sm text-center py-10">
                        Belum ada item ditambahkan.
                    </p>
                @else


                {{-- CART LIST --}}
                <div class="space-y-4">

                    @foreach($cart as $item)
                    <div class="border border-gray-200 rounded-xl p-4 flex flex-col gap-2">

                        {{-- TITLE BAR --}}
                        <div class="flex justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="font-semibold text-gray-800">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </p>
                        </div>


                        {{-- NOTE --}}
                        <div>
                            <textarea
    class="w-full text-xs border rounded-lg p-2 focus:ring-2 focus:ring-blue-400"
    placeholder="Catatan..."
    oninput="updateNote({{ $item['id'] }}, this.value)"
>{{ $item['note'] ?? '' }}</textarea>
                        </div>


                        {{-- REMOVE BUTTON --}}
                        <form method="POST" action="{{ route('branch.pos.order.remove', $branchCode) }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                            <button type="submit" class="text-red-500 text-xs hover:underline">
                                Hapus
                            </button>
                        </form>

                    </div>
                    @endforeach

                </div>


                {{-- TOTAL --}}
                <div class="mt-6 border-t pt-4">
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>
                            Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>


                {{-- PAY BUTTON --}}
                <button type="button"
                    onclick="openPaymentModal()"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl shadow mt-6">
                    💰 Bayar Sekarang
                </button>

                @endif

            </div>

        </div>

    </div>

</div>



{{-- ======================================
   MODAL: METODE PEMBAYARAN
======================================= --}}
<div id="paymentModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-6 z-50">

    <div class="bg-white w-full max-w-md rounded-xl p-6 shadow-xl space-y-4">

        <h2 class="text-xl font-bold mb-3">Pilih Metode Pembayaran</h2>

        {{-- CASH --}}
        <button onclick="openCashModal()"
            class="w-full bg-gray-200 py-3 rounded-lg font-semibold hover:bg-gray-300">
            💵 Cash
        </button>

        {{-- MIDTRANS QRIS --}}
        <button onclick="payMidtrans()"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
            📱 QRIS / E-Wallet
        </button>

        <button onclick="closePaymentModal()"
            class="w-full text-red-500 py-2 font-semibold">
            Batal
        </button>
    </div>

</div>



{{-- ======================================
   MODAL: CASH PAYMENT INPUT
======================================= --}}
<div id="cashModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-6 z-50">

    <div class="bg-white w-full max-w-md rounded-xl p-6 shadow-xl space-y-4">

        <h2 class="text-xl font-bold">Pembayaran Cash</h2>

        <p class="text-gray-600">
            Total:
            <strong>Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}</strong>
        </p>

        <label class="text-sm text-gray-700 font-semibold">Uang dibayar pelanggan:</label>
        <input type="number" id="cashPaid"
               class="w-full border rounded-lg p-3"
               placeholder="Masukkan nominal..."
               oninput="calcChange()">

        <p id="changeInfo" class="text-sm text-gray-700 font-semibold"></p>

        <form id="cashForm" method="POST" action="{{ route('branch.pos.order.pay', $branchCode) }}">
            @csrf
            <input type="hidden" name="payment_method" value="CASH">
            <input type="hidden" name="paid_amount" id="paidAmountField">

            <button type="submit"
                class="w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold hover:bg-emerald-700">
                ✔ Selesaikan Pembayaran
            </button>
        </form>

        <button onclick="closeCashModal()"
            class="text-red-500 font-semibold w-full mt-2">
            Batalkan
        </button>
    </div>

</div>






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
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function openCashModal() {
    closePaymentModal();
    document.getElementById('cashModal').classList.remove('hidden');
}

function closeCashModal() {
    document.getElementById('cashModal').classList.add('hidden');
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

    if (change < 0) {
        document.getElementById("changeInfo").innerHTML =
            "<span class='text-red-600'>Uang kurang Rp " + Math.abs(change).toLocaleString() + "</span>";

        document.getElementById("paidAmountField").value = "";
        return;
    }

    document.getElementById("changeInfo").innerHTML =
        "Kembalian: <strong>Rp " + change.toLocaleString() + "</strong>";

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
