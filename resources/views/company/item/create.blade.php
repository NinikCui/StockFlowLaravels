<x-app-layout>
<main class="max-w-5xl mx-auto px-6 py-10">

    <div class="flex justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Item</h1>

        <a href="{{ route('item.index', $companyCode) }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            ← Kembali
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border">

        <form action="{{ route('item.store', $companyCode) }}"
              method="POST" class="space-y-6">
            @csrf

            {{-- NAMA --}}
            <div>
                <label class="font-semibold text-gray-700">Nama Item</label>
                <input type="text" name="name"
                       class="w-full mt-1 px-4 py-2 border rounded-lg"
                       required>
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="font-semibold text-gray-700">Kategori</label>
                <select name="category_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SATUAN --}}
            <div>
                <label class="font-semibold text-gray-700">Satuan</label>
                <select name="satuan_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg" required>
                    @foreach($satuan as $sat)
                        <option value="{{ $sat->id }}">{{ $sat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SUPPLIER --}}
            <div>
                <label class="font-semibold text-gray-700">Supplier (Opsional)</label>
                <select name="suppliers_id"
                        class="w-full mt-1 px-4 py-2 border rounded-lg">
                    <option value="">- Tidak Ada -</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- MIN MAX --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-gray-700">Stok Minimum</label>
                    <input type="number" name="min_stock"
                           class="w-full mt-1 px-4 py-2 border rounded-lg" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Stok Maksimum</label>
                    <input type="number" name="max_stock"
                           class="w-full mt-1 px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            {{-- MUDAH RUSAK --}}
            <label class="flex items-center gap-2">
                <input type="checkbox" name="mudah_rusak" value="1">
                <span class="text-gray-700">Mudah Rusak</span>
            </label>

            {{-- FORECAST --}}
            <label class="flex items-center gap-2">
                <input type="checkbox" name="forecast_enabled" value="1">
                <span class="text-gray-700">Aktifkan Forecasting</span>
            </label>

             <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">

                <a  href="{{ route('item.index', $companyCode) }}"
                    class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 
                         hover:bg-gray-100 text-sm shadow-sm transition">
                        Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 
                                text-sm shadow-sm transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</main>
</x-app-layout>
