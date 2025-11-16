<x-app-layout>
<main class="max-w-4xl mx-auto px-6 py-10">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Satuan</h1>

        <a href="{{ route('satuan.index', $companyCode) }}"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium">
            ← Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">

        <form action="{{ route('satuan.store', $companyCode) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nama Satuan --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Nama Satuan</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-300 focus:border-blue-500 px-4 py-2"
                       placeholder="contoh: Kilogram"
                       required>
            </div>

            {{-- Kode Satuan --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kode Satuan</label>
                <input type="text"
                       name="code"
                       value="{{ old('code') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-300 focus:border-blue-500 px-4 py-2 uppercase"
                       placeholder="contoh: KG"
                       required>

                <p class="text-xs text-gray-500 mt-1">
                    Kode satuan digunakan sebagai singkatan (misal: GR, PCS, LTR).
                </p>
            </div>

            <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">

                <a href="/{{ strtolower($companyCode) }}/product/satuan"
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
