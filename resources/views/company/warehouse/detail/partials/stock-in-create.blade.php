<x-app-layout>
<main class="max-w-xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Stok Masuk</h1>
        <p class="text-gray-500 text-sm mt-1">
            Tambahkan jumlah stok untuk gudang <strong>{{ $warehouse->name }}</strong>.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 shadow-sm">

        {{-- FLASH SUCCESS --}}
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- FLASH ERROR --}}
        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- VALIDATION ERROR GLOBAL --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                <ul class="list-disc ml-4 text-sm">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" 
              action="{{ route('stock.in.store', [$companyCode, $warehouse->id]) }}" 
              class="space-y-6">
            @csrf

            {{-- ITEM --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Item</label>
                <select name="item_id"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 
                           focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                    <option value="">-- Pilih Item --</option>

                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" 
                                {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->satuan->name }})
                        </option>
                    @endforeach
                </select>

                @error('item_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- QTY --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Jumlah</label>
                <input type="number" step="0.01" name="qty"
                    value="{{ old('qty') }}"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 
                           focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                @error('qty')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NOTES --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 
                           focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
            </div>

            {{-- BUTTON --}}
            <div class="flex items-center gap-3">
                <button 
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg 
                           hover:bg-emerald-700 shadow-sm transition font-medium">
                    Simpan
                </button>

                <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}"
                   class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg 
                          hover:bg-gray-200 shadow-sm transition">
                    Batal
                </a>
            </div>

        </form>

    </div>

</main>
</x-app-layout>
