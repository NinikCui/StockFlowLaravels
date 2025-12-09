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

        {{-- ===========================
            PRODUK GRID
        ============================ --}}
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


        {{-- ===========================
            CART SIDEBAR
        ============================ --}}
        <div class="lg:col-span-1">

            <div class="bg-white border border-gray-200 rounded-2xl shadow p-6 sticky top-10">

                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    🧺 Keranjang
                </h2>

                {{-- Empty cart --}}
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
                        <textarea
                            name="note_temp"
                            data-id="{{ $item['id'] }}"
                            rows="2"
                            placeholder="Tambahkan catatan..."
                            class="w-full text-xs border rounded-lg p-2 focus:ring-2 focus:ring-blue-400"
                        >{{ $item['note'] ?? '' }}</textarea>

                        <small class="text-gray-400 text-xs">
                            Catatan akan otomatis tersimpan saat pembayaran.
                        </small>

                        {{-- REMOVE --}}
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
                <form action="{{ route('branch.pos.order.pay', $branchCode) }}" method="POST" class="mt-6">
                    @csrf

                    {{-- hidden notes --}}
                    @foreach($cart as $item)
                        <input type="hidden" 
                               name="notes[{{ $item['id'] }}]" 
                               id="note-input-{{ $item['id'] }}">
                    @endforeach

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl shadow">
                        💰 Bayar Sekarang
                    </button>
                </form>

                @endif

            </div>

        </div>

    </div>

</div>

{{-- SYNC NOTES --}}
<script>
document.querySelectorAll('textarea[name="note_temp"]').forEach(textarea => {
    textarea.addEventListener('input', () => {
        const id = textarea.dataset.id;
        const hiddenInput = document.getElementById('note-input-' + id);
        hiddenInput.value = textarea.value;
    });

    // initialize
    const id = textarea.dataset.id;
    const hiddenInput = document.getElementById('note-input-' + id);
    hiddenInput.value = textarea.value;
});
</script>

</x-app-layout>
