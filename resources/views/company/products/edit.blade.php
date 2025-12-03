<x-app-layout>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB --}}
        <a href="{{ route('products.show', [$companyCode, $product]) }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        {{-- HEADER --}}
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            Edit Produk: {{ $product->name }}
        </h1>

        {{-- ERROR ALERT --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('products.update', [$companyCode, $product->id]) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- NAME --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $product->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            {{-- CODE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Produk</label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $product->code) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            {{-- BASE PRICE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar (HPP)</label>
                <input type="number"
                       step="0.01"
                       name="base_price"
                       value="{{ old('base_price', $product->base_price) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            {{-- STATUS --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>

                <select name="is_active"
                        class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="1" {{ $product->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- BUTTONS --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('products.show', [$companyCode, $product]) }}"
                   class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</x-app-layout>
