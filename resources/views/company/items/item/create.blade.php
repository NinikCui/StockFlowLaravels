<x-app-layout>
<main class="max-w-4xl mx-auto px-6 py-10">

    <h1 class="text-2xl font-bold mb-6">Tambah Item</h1>
@if ($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
        <strong class="font-semibold">Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form method="POST" action="{{ route('item.store', $companyCode) }}" class="space-y-6">
        @csrf

        <div>
            <label class="font-semibold block mb-1">Nama Item</label>
            <input type="text" name="name" class="w-full border rounded-lg px-4 py-2"
                   value="{{ old('name') }}" required>
        </div>

        <div>
            <label class="font-semibold block mb-1">Kategori</label>
            <select name="category_id" class="w-full border px-4 py-2 rounded-lg" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="font-semibold block mb-1">Satuan</label>
            <select name="satuan_id" class="w-full border px-4 py-2 rounded-lg" required>
                <option value="">-- Pilih Satuan --</option>
                @foreach ($satuan as $sat)
                    <option value="{{ $sat->id }}">{{ $sat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="font-semibold block mb-1">Bahan Baku Utama ?</label>
            <input type="checkbox" name="is_main_ingredient" value="1"
                class="h-4 w-4 text-emerald-600 border-gray-300 rounded">
        </div>


        <div>
            <label class="font-semibold block mb-1">Aktifkan Forecast?</label>
            <input type="checkbox" name="forecast_enabled" value="1"
                class="h-4 w-4 text-emerald-600 border-gray-300 rounded">
        </div>
        <div  class="flex items-center gap-3 pt-3">
            <button class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 
                           shadow-sm transition font-medium">
                Simpan
            </button>

            <a href="{{ route('items.index', $companyCode) }}"
               class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg 
                           hover:bg-gray-200 shadow-sm transition font-medium">
                Batal
            </a>
        </div>

        

    </form>
</main>
</x-app-layout>
