<x-app-layout>
<main class="max-w-3xl mx-auto px-6 py-10">

    {{-- TITLE --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Kategori</h1>
        <p class="text-gray-500 text-sm mt-1">
            Tambahkan kategori baru untuk item.
        </p>
    </div>

    {{-- CARD --}}
    <div class="bg-white border rounded-xl p-6 shadow-sm">
        
        <form method="POST" action="{{ route('category.store', $companyCode) }}" class="space-y-6">
            @csrf

            {{-- KODE --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kode</label>
                <input type="text" name="code"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    required>
            </div>

            {{-- NAMA --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="name"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    required>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex items-center gap-3 pt-3">

                {{-- SIMPAN --}}
                <button
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 
                           shadow-sm transition font-medium">
                    Simpan
                </button>

                {{-- BATAL --}}
                <a href="{{ route('items.index', $companyCode) }}"
                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg 
                           hover:bg-gray-200 shadow-sm transition font-medium">
                    Batal
                </a>
            </div>

        </form>

    </div>

</main>
</x-app-layout>
