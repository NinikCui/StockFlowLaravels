<x-app-layout :companyCode="$companyCode" :branchCode="$branchCode">

<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('branch.pos.shift.index', [$companyCode, $branchCode]) }}"
           class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white 
                  text-gray-700 hover:bg-gray-50 shadow-sm transition">
            ←
        </a>

        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-red-400 to-red-600 
                             flex items-center justify-center text-white text-xl">
                    🔒
                </span>
                Tutup Shift
            </h1>
            <p class="text-gray-600 mt-1">Shift #{{ $shift->id }} — Cabang {{ $branch->name }}</p>
        </div>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <p class="font-semibold mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc ml-5 space-y-1 text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- FORM --}}
    <form action="{{ route('branch.pos.shift.close', [ $branchCode, $shift->id]) }}"
          method="POST"
          class="bg-white border border-gray-200 rounded-2xl shadow p-8 space-y-8">
        @csrf

        {{-- SHIFT SUMMARY --}}
        <section class="rounded-xl p-6 bg-gradient-to-br from-gray-50 to-white border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <span class="h-9 w-9 rounded-lg bg-yellow-500 text-white grid place-items-center">📊</span>
                Ringkasan Shift
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">

                {{-- Opening Cash --}}
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <p class="text-sm text-blue-700 font-medium mb-1">Modal Kas Awal</p>
                    <p class="font-bold text-xl text-blue-800">
                        Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Expected Cash --}}
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <p class="text-sm text-emerald-700 font-medium mb-1">Kas Seharusnya</p>
                    <p class="font-bold text-xl text-emerald-800">
                        Rp {{ number_format($expectedCash, 0, ',', '.') }}
                    </p>
                </div>

            </div>
        </section>


        {{-- INPUT ACTUAL CASH --}}
        <section class="rounded-xl p-6 bg-gradient-to-br from-red-50 to-white border border-red-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <span class="h-9 w-9 rounded-lg bg-red-500 text-white grid place-items-center">💵</span>
                Kas Aktual & Penyesuaian
            </h2>

            {{-- INPUT --}}
            <label class="text-sm font-semibold text-gray-700 mb-2 block">
                Kas Aktual (uang fisik di laci)
            </label>

            <input type="number"
                   step="0.01"
                   min="0"
                   name="closing_cash"
                   id="closingCash"
                   value="{{ old('closing_cash') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 
                          text-lg font-semibold text-gray-900"
                   placeholder="0.00"
                   required>

            {{-- SELISIH --}}
            <div class="mt-5 p-4 rounded-xl border bg-white" id="selisihBox">
                <p class="text-sm font-semibold text-gray-700">Selisih Kas</p>
                <p id="selisihText" class="text-lg font-bold mt-1">Rp 0</p>
            </div>
        </section>


        {{-- NOTE --}}
        <section class="rounded-xl p-6 bg-gradient-to-br from-gray-50 to-white border border-gray-100">

            <label class="text-sm font-semibold text-gray-700 mb-2 block">
                Catatan (opsional)
            </label>

            <textarea name="note"
                      class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-gray-400"
                      rows="3"
                      placeholder="Contoh: ada selisih karena uang kembalian tertinggal..."
            >{{ old('note') }}</textarea>

        </section>


        {{-- ACTION BUTTONS --}}
        <div class="flex justify-between items-center border-t pt-6 border-gray-200">

            <a href="{{ route('branch.pos.shift.index', [$companyCode, $branchCode]) }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                class="px-6 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 shadow-lg">
                🔒 Tutup Shift
            </button>
        </div>

    </form>

</div>


{{-- SELISIH JS --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const closing = document.getElementById('closingCash');
    const selisihText = document.getElementById('selisihText');
    const selisihBox = document.getElementById('selisihBox');
    const expected = {{ $expectedCash }};

    function updateSelisih() {
        const val = parseFloat(closing.value || 0);
        const diff = val - expected;

        selisihText.textContent = "Rp " + diff.toLocaleString('id-ID');

        if (diff === 0) {
            selisihBox.className = "mt-5 p-4 rounded-xl border bg-green-50 border-green-300 text-green-700";
        } else if (diff > 0) {
            selisihBox.className = "mt-5 p-4 rounded-xl border bg-blue-50 border-blue-300 text-blue-700";
        } else {
            selisihBox.className = "mt-5 p-4 rounded-xl border bg-red-50 border-red-300 text-red-700";
        }
    }

    closing.addEventListener('input', updateSelisih);
    updateSelisih();
});
</script>

</x-app-layout>
