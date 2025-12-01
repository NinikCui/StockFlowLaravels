@php
    $tab = request()->query('tab', 'info');
@endphp

<x-app-layout :branchCode="$branchCode">
<main class="px-6 py-10 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Detail Supplier</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap pemasok cabang.</p>
        </div>

        {{-- BACK --}}
        <a href="{{ route('branch.supplier.index', $branchCode) }}"
            class="text-sm text-gray-600 hover:text-gray-900">
            ← Kembali
        </a>
    </div>

    {{-- TAB HEADER --}}
    <x-tab-header
        :tabs="[
            'info' => 'Informasi Supplier',
            'item' => 'Item yang Disuplai',
            'score' => 'Performance Supplier',
        ]"
        :active="$tab"
    />

    {{-- CONTENT --}}
    @if ($tab == 'info')
        @include('branch.suppliers.partials.info')
    @elseif ($tab == 'item')
        @include('branch.suppliers.partials.item')
    @elseif ($tab == 'score')
        @include('branch.suppliers.partials.performance')
    @endif

    <dialog id="modalAddItem" class="modal backdrop:bg-black/40">

        <form method="POST"
            action="{{ route('branch.supplier.item.store', [$branchCode, $supplier->id]) }}"
            class="bg-white p-6 rounded-xl w-[420px] shadow-xl space-y-5">
            @csrf

            <input type="hidden" name="modal" value="add">

            <h2 class="text-lg font-bold text-gray-900">Tambah Item ke Supplier</h2>

            {{-- ITEM --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Pilih Item</label>
                <select name="items_id"
                        class="w-full mt-1 px-3 py-2 border rounded-lg text-sm
                        @error('items_id') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    <option value="">— Pilih Item —</option>

                    @foreach ($allItems as $it)
                        <option value="{{ $it->id }}" {{ old('items_id') == $it->id ? 'selected' : '' }}>
                            {{ $it->name }}
                        </option>
                    @endforeach
                </select>

                @error('items_id')
                    <p class="text-xs text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- HARGA --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Harga Beli</label>
                <input type="number" name="price" min="0" step="1"
                    class="w-full mt-1 px-3 py-2 border rounded-lg text-sm
                        @error('price') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                    value="{{ old('price') }}"
                    placeholder="Contoh: 15000">

                @error('price')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- MOQ --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Minimum Order (Qty)</label>
                <input type="number" min="0" step="1" name="min_order_qty"
                    class="w-full mt-1 px-3 py-2 border rounded-lg text-sm
                        @error('min_order_qty') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                    value="{{ old('min_order_qty', 0) }}">

                @error('min_order_qty')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="modalAddItem.close()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                    Batal
                </button>

                <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
                    Simpan
                </button>
            </div>
        </form>
    </dialog>

    @if ($errors->any() && session('modal') === 'add')
        <script> modalAddItem.showModal(); </script>
    @endif


    <dialog id="modalEditItem" class="modal backdrop:bg-black/40">

        <form method="POST" id="formEditItem"
            class="bg-white p-6 rounded-xl w-[420px] shadow-xl space-y-5">
            @csrf
            @method('PUT')

            <input type="hidden" name="modal" value="edit">

            <h2 class="text-lg font-bold text-gray-900">Edit Item Supplier</h2>

            {{-- HARGA --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Harga Beli</label>
                <input id="editPrice" name="price" type="number" min="0"
                    class="w-full mt-1 px-3 py-2 border rounded-lg text-sm border-gray-300">
            </div>

            {{-- MOQ --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Minimum Order (Qty)</label>
                <input id="editMinOrder" name="min_order_qty" type="number" min="0"
                    class="w-full mt-1 px-3 py-2 border rounded-lg text-sm border-gray-300">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="modalEditItem.close()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                    Batal
                </button>

                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </dialog>

    @if ($errors->any() && session('modal') === 'edit')
        <script> modalEditItem.showModal(); </script>
    @endif

<dialog id="modalDeleteSupplier" class="modal backdrop:bg-black/40">

    <form method="POST"
          action="{{ route('branch.supplier.destroy', [$branchCode, $supplier->id]) }}"
          class="bg-white p-6 rounded-xl w-[400px] shadow-xl space-y-5">
        
        @csrf
        @method('DELETE')

        <h2 class="text-lg font-bold text-gray-900">
            Hapus Supplier?
        </h2>

        <p class="text-sm text-gray-600">
            Tindakan ini tidak dapat dibatalkan. Semua data supplier akan terhapus dari cabang ini.
        </p>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button"
                onclick="modalDeleteSupplier.close()"
                class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                Batal
            </button>

            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                Hapus
            </button>
        </div>

    </form>

</dialog>
@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg text-emerald-700">
    {{ session('success') }}
</div>
@endif

    <script>
    function openEditItem(itemId, price, minOrderQty) {

        document.getElementById('editPrice').value = price ?? 0;
        document.getElementById('editMinOrder').value = minOrderQty ?? 0;

        // SET ROUTE
        const url = `{{ url('branch/' . $branchCode . '/supplier/' . $supplier->id . '/item') }}/${itemId}`;
        document.getElementById('formEditItem').action = url;

        modalEditItem.showModal();
    }
    </script>

</main>
</x-app-layout>
