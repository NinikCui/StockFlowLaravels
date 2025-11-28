<x-app-layout>
<main class="max-w-3xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Kategori</h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui informasi kategori.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 shadow-sm">
        
        <form method="POST" action="{{ route('category.update', [$companyCode, $category->code]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kode</label>
                <input type="text" name="code"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    value="{{ $category->code }}" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="name"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    value="{{ $category->name }}" required>
            </div>

            <div class="flex items-center gap-3 pt-3">

                <button
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 
                           shadow-sm transition font-medium">
                    Update
                </button>

                {{-- CANCEL --}}
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
