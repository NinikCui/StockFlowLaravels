<x-app-layout>
<main class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Edit Item</h1>

        <a href="{{ route('item.index', $companyCode) }}"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
            ← Kembali
        </a>
    </div>

    {{-- FORM WRAPPER --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">

        <form action="{{ route('item.update', [$companyCode, $item->id]) }}"
              method="POST"
              class="space-y-6">

            @csrf
            @method('PUT')

            {{-- NAMA ITEM --}}
            <div>
                <label class="font-semibold text-gray-700">Nama Item</label>
                <input type="text" name="name"
                       value="{{ old('name', $item->name) }}"
                       class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500"
                       required>
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="font-semibold text-gray-700">Kategori</label>
                <select name="category_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500"
                        required>

                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ $item->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- SATUAN --}}
            <div>
                <label class="font-semibold text-gray-700">Satuan</label>
                <select name="satuan_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500"
                        required>

                    @foreach($satuan as $sat)
                        <option value="{{ $sat->id }}"
                            {{ $item->satuan_id == $sat->id ? 'selected' : '' }}>
                            {{ $sat->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- SUPPLIER --}}
            <div>
                <label class="font-semibold text-gray-700">Supplier (Opsional)</label>
                <select name="suppliers_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500">

                    <option value="">Tidak Ada</option>

                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}"
                            {{ $item->suppliers_id == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- MIN / MAX STOCK --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-gray-700">Stok Minimum</label>
                    <input type="number" name="min_stock"
                           value="{{ old('min_stock', $item->min_stock) }}"
                           class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500"
                           required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Stok Maksimum</label>
                    <input type="number" name="max_stock"
                           value="{{ old('max_stock', $item->max_stock) }}"
                           class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-blue-300 focus:border-blue-500"
                           required>
                </div>
            </div>

            {{-- MUDAH RUSAK --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="mudah_rusak" value="1"
                       {{ $item->mudah_rusak ? 'checked' : '' }}>
                <span class="text-gray-700">Mudah Rusak</span>
            </div>

            {{-- FORECAST ENABLE --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="forecast_enabled" value="1"
                       {{ $item->forecast_enabled ? 'checked' : '' }}>
                <span class="text-gray-700">Aktifkan Forecasting</span>
            </div>

            {{-- SUBMIT --}}
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>

        </form>
    </div>

</main>
</x-app-layout>
