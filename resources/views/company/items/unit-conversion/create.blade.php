<x-app-layout>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="font-semibold text-red-700 mb-1">
                Terjadi kesalahan
            </div>
            <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('unit-conversion.store', $companyCode) }}"
        x-data="{
            from: '{{ old('from_satuan_id') }}',
            to: '{{ old('to_satuan_id') }}'
        }"
    >
        @csrf

        <div class="space-y-4">

            {{-- DARI SATUAN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Dari Satuan
                </label>
                <select
                    name="from_satuan_id"
                    x-model="from"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                >
                    <option value="">-- Pilih Satuan --</option>
                    @foreach ($satuan as $sat)
                        <option
                            value="{{ $sat->id }}"
                            :disabled="to == '{{ $sat->id }}'"
                        >
                            {{ $sat->code }} - {{ $sat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- KE SATUAN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ke Satuan
                </label>
                <select
                    name="to_satuan_id"
                    x-model="to"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                >
                    <option value="">-- Pilih Satuan --</option>
                    @foreach ($satuan as $sat)
                        <option
                            value="{{ $sat->id }}"
                            :disabled="from == '{{ $sat->id }}'"
                        >
                            {{ $sat->code }} - {{ $sat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- NILAI KONVERSI --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nilai Konversi
                </label>
                <input
                    type="number"
                    name="factor"
                    step="0.000001"
                    min="0.000001"
                    value="{{ old('factor') }}"
                    placeholder="Contoh: 1000"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                >

                <p class="text-xs text-gray-500 mt-1">
                    Contoh: 1 KG = 1000 GR
                </p>
            </div>

        </div>

        {{-- ACTION --}}
        <div class="mt-6 flex justify-end gap-2">

            <a
                href="{{ route('items.index', $companyCode) }}"
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
            >
                Batal
            </a>

            <button
                type="submit"
                class="px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                :disabled="!from || !to || from === to"
                :class="from === to ? 'opacity-50 cursor-not-allowed' : ''"
            >
                Simpan
            </button>

        </div>
    </form>

</x-app-layout>
