<script>
let index = document.querySelectorAll('.product-select').length;

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

            <button type="button"
                    onclick="removeItem(this)"
                    class="text-red-500 font-bold">
                ✕
            </button>
        </div>
    `);

    index++;
    syncProductOptions();
}

function removeItem(btn) {
    btn.parentElement.remove();
    syncProductOptions();
}

function syncProductOptions() {
    const selects = document.querySelectorAll('.product-select');
    const used = Array.from(selects).map(s => s.value).filter(v => v !== '');

    selects.forEach(select => {
        Array.from(select.options).forEach(opt => {
            opt.disabled =
                opt.value &&
                opt.value !== select.value &&
                used.includes(opt.value);
        });
    });
}

// init
syncProductOptions();
</script>
