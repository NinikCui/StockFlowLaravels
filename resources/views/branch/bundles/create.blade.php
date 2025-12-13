<x-app-layout :branchCode="$branchCode">

<div class="max-w-4xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">
        Buat Paket – {{ $branch->name }}
    </h1>

    <form method="POST"
          action="{{ route('branch.bundles.store', [$branchCode]) }}"
          class="space-y-6 bg-white p-6 rounded-xl shadow border">
        @csrf

        {{-- NAMA --}}
        <div>
            <label class="font-semibold text-sm">Nama Paket</label>
            <input type="text" name="name"
                   class="w-full border rounded-lg px-4 py-2 mt-1"
                   required>
        </div>

        {{-- HARGA --}}
        <div>
            <label class="font-semibold text-sm">Harga Paket</label>
            <input type="number" name="bundle_price"
                   min="1"
                   class="w-full border rounded-lg px-4 py-2 mt-1"
                   required>
        </div>

        {{-- ITEMS --}}
        <div>
            <label class="font-semibold text-sm mb-2 block">Isi Paket</label>

            <div id="items" class="space-y-3">
                <div class="flex gap-3">
                    <select name="items[0][product_id]"
                            class="product-select flex-1 border rounded-lg px-3 py-2"
                            onchange="syncProductOptions()">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number"
                           name="items[0][qty]"
                           min="1"
                           placeholder="Qty"
                           class="w-24 border rounded-lg px-3 py-2">
                </div>
            </div>

            <button type="button"
                    onclick="addItem()"
                    class="mt-3 text-sm font-semibold text-emerald-600">
                + Tambah Produk
            </button>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('branch.bundles.index', [$branchCode]) }}"
               class="px-4 py-2 border rounded-lg">
                Batal
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-semibold">
                Simpan
            </button>
        </div>

    </form>
</div>

@include('branch.bundles._form-script')

</x-app-layout>
