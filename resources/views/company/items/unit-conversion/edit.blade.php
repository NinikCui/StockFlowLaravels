<x-app-layout>
    <main class="min-h-screen max-w-xl mx-auto px-6 py-10">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                Edit Konversi Satuan
            </h1>
            <p class="text-gray-500 mt-1">
                Perbarui nilai konversi antar satuan.
            </p>
        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

            <form
                method="POST"
                action="{{ route('unit-conversion.update', [$companyCode, $conversion->id]) }}"
                class="space-y-4"
            >
                @csrf
                @method('PUT')

                {{-- DARI SATUAN (READONLY) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Dari Satuan
                    </label>
                    <input
                        type="text"
                        value="{{ $conversion->fromSatuan->code }} - {{ $conversion->fromSatuan->name }}"
                        disabled
                        class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700 text-sm"
                    >
                </div>

                {{-- KE SATUAN (READONLY) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ke Satuan
                    </label>
                    <input
                        type="text"
                        value="{{ $conversion->toSatuan->code }} - {{ $conversion->toSatuan->name }}"
                        disabled
                        class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700 text-sm"
                    >
                </div>

                {{-- NILAI KONVERSI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nilai Konversi
                    </label>

                    <div class="text-sm text-gray-500 mb-1">
                        1 {{ $conversion->fromSatuan->code }}
                        =
                        {{ $conversion->toSatuan->code }}
                    </div>

                    <input
                        type="number"
                        name="factor"
                        step="0.000001"
                        min="0.000001"
                        value="{{ old('factor', $conversion->factor) }}"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    >

                    <p class="text-xs text-gray-500 mt-1">
                        Contoh: 1000 (artinya 1 {{ $conversion->fromSatuan->code }} = 1000 {{ $conversion->toSatuan->code }})
                    </p>

                    @error('factor')
                        <p class="text-xs text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ACTION --}}
                <div class="pt-4 flex justify-end gap-2">

                    <a
                        href="{{ route('items.index', $companyCode) }}"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </main>
</x-app-layout>
