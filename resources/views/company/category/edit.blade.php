<x-app-layout>
<main class="max-w-3xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">Edit Kategori</h1>

    </div>

    {{-- FORM CARD --}}
    <div class="bg-white shadow rounded-xl p-6">

        <form action="{{ route('category.update', [$companyCode, $category->code]) }}"
              method="POST"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- NAMA --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">
                    Nama Kategori
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $category->name) }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                       required>

                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">
                    Status
                </label>

                <select name="is_active"
                        class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200">
                    <option value="1" {{ $category->is_active ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ !$category->is_active ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">

                <a href="/{{ strtolower($companyCode) }}/product/categories"
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
