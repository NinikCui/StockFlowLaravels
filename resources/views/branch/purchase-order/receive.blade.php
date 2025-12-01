<x-app-layout :branchCode="$branchCode">

<div class="max-w-4xl mx-auto px-6 py-10 space-y-8">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Penerimaan Barang</h1>
        <p class="text-gray-600 mt-1">
            Purchase Order <span class="font-semibold">{{ $po->po_number }}</span>
            • {{ $po->po_date }}
        </p>
    </div>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM RECEIVE --}}
    <form method="POST"
          action="{{ route('branch.po.receive.process', [$branchCode, $po->id]) }}"
          class="space-y-6">
        @csrf

        @foreach ($po->details as $detail)

            <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-6 transition-all">

                {{-- ITEM NAME --}}
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $detail->item->name }}
                    </h2>

                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-lg border border-gray-300">
                        {{ $detail->item->satuan->name }}
                    </span>
                </div>

                {{-- ORDERED INFO --}}
                <p class="text-sm text-gray-600 mb-4">
                    Qty Dipesan:
                    <span class="font-medium text-gray-800">
                        {{ $detail->qty_ordered }}
                    </span>
                </p>

                {{-- INPUT SECTION --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- QTY RECEIVED --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Qty Diterima
                        </label>
                        <input
                            type="number"
                            class="receive-input mt-1 w-full border-gray-300 rounded-lg shadow-sm
                                   focus:ring-emerald-500 focus:border-emerald-500"
                            data-max="{{ $detail->qty_ordered }}"
                            name="receive_qty[{{ $detail->id }}]"
                            min="0"
                            max="{{ $detail->qty_ordered }}"
                            step="0.01"
                            required
                        >
                    </div>

                    {{-- QTY RETURN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Qty Return
                        </label>
                        <input
                            type="number"
                            class="return-input mt-1 w-full border-gray-300 rounded-lg shadow-sm
                                   focus:ring-rose-500 focus:border-rose-500"
                            data-max="{{ $detail->qty_ordered }}"
                            name="return_qty[{{ $detail->id }}]"
                            min="0"
                            max="{{ $detail->qty_ordered }}"
                            step="0.01"
                            required
                        >
                    </div>

                </div>

                <p class="text-xs text-gray-500 mt-3">
                    * Total diterima + return harus = {{ $detail->qty_ordered }} (NO PARTIAL)
                </p>

            </div>

        @endforeach

        {{-- ACTION BUTTONS --}}
        <div class="flex justify-between pt-4">

            <a href="{{ route('branch.po.show', [$branchCode, $po->id]) }}"
               class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg shadow hover:bg-gray-50">
                Batal
            </a>

            <button
                type="submit"
                id="submitReceiveBtn"
                class="px-6 py-3 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition disabled:opacity-50"
            >
                Simpan Penerimaan
            </button>

        </div>

    </form>
</div>

{{-- AUTO CALC + VALIDATION --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    function validateRow(receiveInput, returnInput, max) {
        let recv = parseFloat(receiveInput.value) || 0;
        let ret = parseFloat(returnInput.value) || 0;
        const total = recv + ret;

        const card = receiveInput.closest('.bg-white');

        card.classList.remove('border-red-400', 'bg-red-50');
        card.classList.remove('border-green-400', 'bg-green-50');

        if (total === max) {
            card.classList.add('border-green-400', 'bg-green-50');
            return true;
        }

        card.classList.add('border-red-400', 'bg-red-50');
        return false;
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById("submitReceiveBtn");
        const allValid = [...document.querySelectorAll('.receive-input')].every(input => {
            const max = parseFloat(input.dataset.max);
            const returnInput = input.closest('.bg-white').querySelector('.return-input');
            return validateRow(input, returnInput, max);
        });

        submitBtn.disabled = !allValid;
    }

    document.querySelectorAll('.receive-input').forEach(rcv => {
        rcv.addEventListener("input", function () {
            const max = parseFloat(this.dataset.max);
            const retInp = this.closest('.bg-white').querySelector('.return-input');

            let recv = parseFloat(this.value) || 0;
            retInp.value = Math.max(max - recv, 0).toFixed(2);

            validateRow(this, retInp, max);
            updateSubmitButton();
        });
    });

    document.querySelectorAll('.return-input').forEach(ret => {
        ret.addEventListener("input", function () {
            const max = parseFloat(this.dataset.max);
            const rcvInp = this.closest('.bg-white').querySelector('.receive-input');

            let retVal = parseFloat(this.value) || 0;
            rcvInp.value = Math.max(max - retVal, 0).toFixed(2);

            validateRow(rcvInp, this, max);
            updateSubmitButton();
        });
    });

    updateSubmitButton();
});
</script>

</x-app-layout>
