<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Buat Purchase Order</h1>

    {{-- ERROR ALERT SERVER --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('po.store', $companyCode) }}" id="poForm">
        @csrf

        {{-- ========================== --}}
        {{-- INFORMASI PO --}}
        {{-- ========================== --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi PO</h2>

            <div class="grid grid-cols-3 gap-4">
                
                {{-- CABANG --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Cabang</label>
                    <select name="cabang_resto_id"
                        class="w-full mt-1 p-2 border rounded-lg"
                    >
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- WAREHOUSE --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Gudang</label>
                    <select id="warehouseSelect" name="warehouse_id"
                        class="w-full mt-1 p-2 border rounded-lg"
                    >
                        <option value="">-- Pilih Cabang Terlebih Dahulu --</option>
                    </select>
                </div>

                {{-- SUPPLIER --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Supplier</label>
                    <select id="supplierSelect" name="suppliers_id"
                        class="w-full mt-1 p-2 border rounded-lg"
                        onchange="onSupplierChange()"
                    >
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL PO --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal PO</label>
                    <input type="date" id="poDate" name="po_date"
                        class="w-full mt-1 p-2 border rounded-lg">
                    <p id="poDateError" class="text-xs text-red-600 hidden mt-1">
                        Tanggal tidak boleh melebihi hari ini.
                    </p>
                </div>

                {{-- TANGGAL DIHARAPKAN --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal Diharapkan</label>
                    <input type="date" id="expectedDate" name="expected_delivery_date"
                        class="w-full mt-1 p-2 border rounded-lg">
                    <p id="expectedDateError" class="text-xs text-red-600 hidden mt-1">
                        Tanggal harus lebih dari hari ini.
                    </p>
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="mt-4 col-span-3">
                <label class="text-sm font-medium text-gray-700">Catatan</label>
                <textarea name="note" rows="3"
                    required placeholder="Wajib Diisi"
                    class="w-full mt-1 p-2 border rounded-lg"
                ></textarea>
            </div>
        </div>

        {{-- ========================== --}}
        {{-- ITEM PEMBELIAN --}}
        {{-- ========================== --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Item Pembelian</h2>

            <table class="w-full border rounded-lg text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2">Item</th>
                        <th class="p-2 w-24">Qty</th>
                        <th class="p-2 w-32">Harga</th>
                        <th class="p-2 w-32">Diskon (%)</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemTable"></tbody>
            </table>

            <button type="button" onclick="addRow()"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                + Tambah Item
            </button>
        </div>

        {{-- ========================== --}}
        {{-- SUBMIT --}}
        {{-- ========================== --}}
        <button type="submit"
            class="mt-6 px-6 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            Simpan PO
        </button>

    </form>
</div>

{{-- ========================== --}}
{{-- JAVASCRIPT --}}
{{-- ========================== --}}
<script>
let rowIndex = 0;
const supplierItems = @json($supplierItems);
const warehouses = @json($warehouses);
let usedItemIds = [];

// DEFAULT DATE
const today = new Date().toISOString().split("T")[0];
document.getElementById("poDate").value = today;
document.getElementById("poDate").max = today;
document.getElementById("expectedDate").min = today;

document.getElementById("poDate").addEventListener("change", function() {
    document.getElementById("poDateError").classList.toggle("hidden", !(this.value > today));
});

document.getElementById("expectedDate").addEventListener("change", function() {
    document.getElementById("expectedDateError").classList.toggle("hidden", !(this.value <= today));
});


document.querySelector("[name='cabang_resto_id']").addEventListener("change", function () {
    const cabangId = parseInt(this.value);
    const warehouseSelect = document.getElementById("warehouseSelect");

    warehouseSelect.innerHTML = '<option value="">-- Pilih Gudang --</option>';

    if (!cabangId) return;

    const filtered = warehouses.filter(w => w.cabang_resto_id === cabangId);

    filtered.forEach(w => {
        warehouseSelect.insertAdjacentHTML("beforeend",
            `<option value="${w.id}">${w.name}</option>`
        );
    });

    if (filtered.length === 0) {
        warehouseSelect.innerHTML = '<option value="">Tidak ada gudang untuk cabang ini</option>';
    }
});


function onSupplierChange() {
    document.getElementById("itemTable").innerHTML = "";
    usedItemIds = [];
    rowIndex = 0;
}

function addRow() {
    const supplierId = document.getElementById("supplierSelect").value;

    if (!supplierId) {
        alert("Pilih supplier terlebih dahulu.");
        return;
    }

    const items = supplierItems[supplierId];
    const available = items.filter(i => !usedItemIds.includes(i.id));

    if (available.length === 0) {
        alert("Semua item supplier sudah dimasukkan.");
        return;
    }

    const firstItem = available[0];

    usedItemIds.push(firstItem.id);

    let options = "";
    available.forEach(i => {
        options += `<option value="${i.id}"
                        data-price="${i.price}"
                        data-moq="${i.min_order_qty}">
                        ${i.name}
                    </option>`;
    });

    document.getElementById("itemTable").insertAdjacentHTML("beforeend", `
        <tr class="border-b" data-row="${rowIndex}">
            
            <td class="p-2">
                <select name="items[${rowIndex}][item_id]" 
                        class="w-full border rounded-lg p-2"
                        onchange="changeItem(this, ${rowIndex})">
                    ${options}
                </select>
            </td>

            <td class="p-2">
                <input type="number" min="1"
                       value="${firstItem.min_order_qty}"
                       name="items[${rowIndex}][qty_ordered]"
                       class="w-full border rounded-lg p-2 qty-input-${rowIndex}">
            </td>

            <td class="p-2">
                <input type="number" min="0"
                       value="${firstItem.price}"
                       name="items[${rowIndex}][unit_price]"
                       class="w-full border rounded-lg p-2 price-input-${rowIndex}">
            </td>

            <td class="p-2">
                <input type="number" min="0" max="100" value="0"
                       name="items[${rowIndex}][discount_pct]"
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2 text-center">
                <button type="button" 
                        onclick="removeRow(this, ${firstItem.id})"
                        class="text-red-600 hover:underline">
                    Hapus
                </button>
            </td>

        </tr>
    `);

    rowIndex++;
}


function removeRow(button, id) {
    button.closest("tr").remove();
    usedItemIds = usedItemIds.filter(x => x !== id);
}


function changeItem(select, index) {

    const newId = parseInt(select.value);

    if (usedItemIds.includes(newId)) {
        alert("Item sudah dipilih.");
        select.value = usedItemIds[index];
        return;
    }

    usedItemIds[index] = newId;

    const option = select.selectedOptions[0];
    const price = option.getAttribute("data-price");
    const moq = option.getAttribute("data-moq");

    document.querySelector(`.qty-input-${index}`).value = moq;
    document.querySelector(`.price-input-${index}`).value = price;
}

</script>

</x-app-layout>
