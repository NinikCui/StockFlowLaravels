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
                <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 
                             flex items-center justify-center text-white text-xl">
                    ➕
                </span>
                Buka Shift Baru
            </h1>
            <p class="text-gray-600 mt-1">Shift baru akan dibuka untuk cabang {{ $branch->name }}</p>
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


    {{-- OPEN SHIFT FORM --}}
    <form action="{{ route('branch.pos.shift.open', [ $branchCode]) }}"
          method="POST"
          class="bg-white border border-gray-200 rounded-2xl shadow p-8 space-y-8">
        @csrf

        {{-- CASH INPUT --}}
        <section class="rounded-xl p-6 bg-gradient-to-br from-gray-50 to-white border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <span class="h-9 w-9 rounded-lg bg-emerald-500 text-white grid place-items-center">💵</span>
                Modal Kas Awal
            </h2>

            <label class="text-sm font-semibold text-gray-700 block mb-2">
                Jumlah Uang Modal <span class="text-red-500">*</span>
            </label>

            <input type="number"
                   step="0.01"
                   min="0"
                   name="opening_cash"
                   value="{{ old('opening_cash') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 
                          text-lg font-semibold text-gray-900"
                   placeholder="0.00"
                   required>

            <p class="text-xs text-gray-500 mt-2">
                Modal kas awal ini digunakan untuk menerima pembayaran tunai selama shift.
            </p>
        </section>


        {{-- INFO --}}
        <section class="rounded-xl p-6 bg-blue-50 border border-blue-200">
            <p class="text-blue-700 text-sm">
                Setelah shift dibuka, kasir akan dapat melakukan transaksi POS hingga shift ditutup.
            </p>
        </section>


        {{-- ACTIONS --}}
        <div class="flex justify-between items-center border-t pt-6 border-gray-200">

            <a href="{{ route('branch.pos.shift.index', [$companyCode, $branchCode]) }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold 
                       hover:bg-emerald-700 shadow-lg">
                🚀 Buka Shift
            </button>
        </div>

    </form>

</div>

</x-app-layout>
