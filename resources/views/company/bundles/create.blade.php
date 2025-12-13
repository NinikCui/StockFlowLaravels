<x-app-layout>

<div class="max-w-4xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">Buat Paket Pembelian</h1>

    <form method="POST"
          action="{{ route('bundles.store', [$companyCode]) }}"
          class="space-y-6 bg-white p-6 rounded-xl shadow border">
        @csrf

        {{-- =====================
             NAMA PAKET
        ====================== --}}
        <div>
            <label class="font-semibold text-sm">Nama Paket</label>
            <input type="text"
                   name="name"
                   class="w-full border rounded-lg px-4 py-2 mt-1"
                   required>
        </div>

        {{-- =====================
             HARGA
        ====================== --}}
        <div>
            <label class="font-semibold text-sm">Harga Paket</label>
            <input type="number"
                   name="bundle_price"
                   min="1"
                   class="w-full border rounded-lg px-4 py-2 mt-1"
                   required>
        </div>

        {{-- =====================
             ITEMS
        ====================== --}}
        <div>
            <label class="font-semibold text-sm mb-2 block">Isi Paket</label>

            <div id="items" class="space-y-3">

                {{-- ITEM ROW --}}
                <div class="flex gap-3 items-center">
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
                           placeholder="Qty"
                           min="1"
                           class="w-24 border rounded-lg px-3 py-2">
                </div>

            </div>

            <button type="button"
                    onclick="addItem()"
                    class="mt-3 text-sm font-semibold text-emerald-600">
                + Tambah Produk
            </button>
        </div>

        {{-- =====================
             ACTION
        ====================== --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('bundles.index', [$companyCode]) }}"
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

{{-- =========================================================
     SCRIPT: ADD ITEM
========================================================= --}}
<script>
let index = 1;

function addItem() {
    const wrapper = document.getElementById('items');

    wrapper.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 items-center">
            <select name="items[${index}][product_id]"
                    class="product-select flex-1 border rounded-lg px-3 py-2"
                    onchange="syncProductOptions()">
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>

            <input type="number"
                   name="items[${index}][qty]"
                   min="1"
                   placeholder="Qty"
                   class="w-24 border rounded-lg px-3 py-2">
        </div>
    `);

    index++;
    syncProductOptions();
}
</script>

{{-- =========================================================
     SCRIPT: PREVENT DUPLICATE PRODUCT (REAL TIME)
========================================================= --}}
<script>
function syncProductOptions() {
    const selects = document.querySelectorAll('.product-select');

    let selectedValues = Array.from(selects)
        .map(s => s.value)
        .filter(v => v !== "");

    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            if (
                option.value !== "" &&
                option.value !== select.value &&
                selectedValues.includes(option.value)
            ) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
}
</script>

{{-- =========================================================
     SCRIPT: FINAL SUBMIT VALIDATION
========================================================= --}}
<script>
document.querySelector('form').addEventListener('submit', function (e) {
    let errors = [];

    const name = document.querySelector('input[name="name"]').value.trim();
    const price = document.querySelector('input[name="bundle_price"]').value;

    if (!name) errors.push('Nama paket wajib diisi');
    if (!price || Number(price) <= 0) errors.push('Harga paket harus lebih dari 0');

    const selects = document.querySelectorAll('.product-select');
    const qtys = document.querySelectorAll('input[name^="items"][name$="[qty]"]');

    if (selects.length === 0) {
        errors.push('Minimal 1 produk harus dipilih');
    }

    let used = [];

    selects.forEach((select, i) => {
        if (!select.value) {
            errors.push(`Produk pada baris ${i + 1} belum dipilih`);
        }

        if (!qtys[i].value || qtys[i].value < 1) {
            errors.push(`Qty pada baris ${i + 1} minimal 1`);
        }

        if (used.includes(select.value)) {
            errors.push(`Produk pada baris ${i + 1} duplikat`);
        }

        used.push(select.value);
    });

    if (errors.length > 0) {
        e.preventDefault();
        alert('❌ Gagal menyimpan paket:\n\n- ' + errors.join('\n- '));
    }
});
</script>

</x-app-layout>
