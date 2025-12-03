<x-app-layout>

    <div class="max-w-3xl mx-auto px-6 py-10">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
            <p class="text-gray-600 mt-1 text-sm">Masukkan data produk baru perusahaan</p>
        </div>

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('products.store', $companyCode) }}"
              class="space-y-6">
            @csrf

            {{-- Nama Produk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: Nasi Goreng" required>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_id"
                        class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                @if ($categories->count() === 0)
                    <p class="mt-1 text-xs text-red-600">
                        Belum ada kategori — buat kategori terlebih dahulu.
                    </p>
                @endif
            </div>

            {{-- Kode Produk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" value="{{ old('code') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 uppercase"
                       placeholder="Contoh: NSGR001" required>
            </div>

            {{-- Harga Dasar --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Harga Dasar <span class="text-red-500">*</span>
                </label>
                <input type="number" min="0" step="0.01" name="base_price"
                       value="{{ old('base_price') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: 15000" required>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status Produk
                </label>
                <select name="is_active"
                        class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="1" {{ old('is_active') == "1" ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == "0" ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex justify-end gap-3 pt-6 border-t">

                <a href="{{ route('products.index', $companyCode) }}"
                   class="px-4 py-2 rounded-lg border text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 shadow">
                    Simpan Produk
                </button>

            </div>

        </form>

    </div>

</x-app-layout>
