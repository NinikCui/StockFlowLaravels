<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Buat Purchase Order</h1>

    {{-- ERROR ALERT SERVER (JIKA KEBUTUHAN) --}}
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

        {{-- INFORMASI PO --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi PO</h2>

            <div class="grid grid-cols-2 gap-4">
                {{-- CABANG --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Cabang</label>
                    <select name="cabang_resto_id" class="w-full mt-1 p-2 border rounded-lg">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SUPPLIER --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">Supplier</label>
                    <select id="supplierSelect" name="suppliers_id"
                        class="w-full mt-1 p-2 border rounded-lg"
                        onchange="onSupplierChange()">
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
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-700">Catatan</label>
                <textarea name="note" rows="3"
                    class="w-full mt-1 p-2 border rounded-lg"></textarea>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Item Pembelian</h2>

            <table class="w-full border rounded-lg text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2">Item</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Harga</th>
                        <th class="p-2">Diskon (%)</th>
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

        {{-- SUBMIT --}}
        <button type="submit"
            class="mt-6 px-6 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            Simpan PO
        </button>

    </form>
</div>

<script>
let rowIndex = 0;
const supplierItems = @json($supplierItems);
let usedItemIds = []; // Prevent duplicate item

// ============================
// TANGGAL DEFAULT & VALIDASI
// ============================
const today = new Date().toISOString().split("T")[0];
document.getElementById("poDate").value = today;
document.getElementById("poDate").max = today;

document.getElementById("expectedDate").min = today;

// Validasi tanggal PO
document.getElementById("poDate").addEventListener("change", function() {
    const err = document.getElementById("poDateError");
    if (this.value > today) err.classList.remove("hidden");
    else err.classList.add("hidden");
});

// Validasi tanggal expected
document.getElementById("expectedDate").addEventListener("change", function() {
    const err = document.getElementById("expectedDateError");
    if (this.value <= today) err.classList.remove("hidden");
    else err.classList.add("hidden");
});

// ============================
// CHANGE SUPPLIER
// ============================
function onSupplierChange() {
    document.getElementById("itemTable").innerHTML = "";
    rowIndex = 0;
    usedItemIds = [];
}

// ============================
// ADD ROW ITEM
// ============================
function addRow() {
    const supplierId = document.getElementById("supplierSelect").value;

    if (!supplierId) {
        alert("Pilih supplier terlebih dahulu.");
        return;
    }

    const items = supplierItems[supplierId];

    // Filter item yang belum dipakai
    const availableItems = items.filter(i => !usedItemIds.includes(i.id));

    if (availableItems.length === 0) {
        alert("Semua item supplier sudah ditambahkan.");
        return;
    }

    let options = "";
    availableItems.forEach(i => {
        options += `<option value="${i.id}">${i.name}</option>`;
    });

    const newItemId = availableItems[0].id;
    usedItemIds.push(newItemId); // Tandai sebagai sudah digunakan

    const tbody = document.getElementById("itemTable");

    tbody.insertAdjacentHTML("beforeend", `
        <tr class="border-b">
            <td class="p-2">
                <select name="items[${rowIndex}][item_id]"
                        class="w-full border rounded-lg p-2 itemSelect"
                        onchange="changeItem(this, ${rowIndex})">
                    ${options}
                </select>
            </td>

            <td class="p-2">
                <input type="number" min="1"
                       name="items[${rowIndex}][qty_ordered]"
                       value="1"
                       class="w-full border rounded-lg p-2 qtyInput"
                       oninput="validateQty(this)">
                <p class="text-xs text-red-600 hidden">Minimal 1.</p>
            </td>

            <td class="p-2">
                <input type="number" min="0"
                       name="items[${rowIndex}][unit_price]"
                       value="0"
                       class="w-full border rounded-lg p-2 priceInput"
                       oninput="validatePrice(this)">
                <p class="text-xs text-red-600 hidden">Harga tidak valid.</p>
            </td>

            <td class="p-2">
                <input type="number" min="0" max="100"
                       name="items[${rowIndex}][discount_pct]"
                       value="0"
                       class="w-full border rounded-lg p-2 discountInput"
                       oninput="validateDiscount(this)">
                <p class="text-xs text-red-600 hidden">Diskon 0–100.</p>
            </td>

            <td class="p-2 text-center">
                <button type="button" onclick="removeRow(this, ${newItemId})"
                    class="text-red-600 hover:underline">
                    Hapus
                </button>
            </td>
        </tr>
    `);

    rowIndex++;
}

// ============================
// VALIDASI INPUT
// ============================
function validateQty(el) {
    const err = el.nextElementSibling;
    if (el.value < 1) err.classList.remove("hidden");
    else err.classList.add("hidden");
}

function validatePrice(el) {
    const err = el.nextElementSibling;
    if (el.value < 0) err.classList.remove("hidden");
    else err.classList.add("hidden");
}

function validateDiscount(el) {
    const err = el.nextElementSibling;
    if (el.value < 0 || el.value > 100) err.classList.remove("hidden");
    else err.classList.add("hidden");
}

// ============================
// REMOVE ROW
// ============================
function removeRow(button, itemId) {
    button.closest("tr").remove();
    usedItemIds = usedItemIds.filter(id => id !== itemId);
}

// ============================
// CHANGE ITEM (PREVENT DUPLICATE)
// ============================
function changeItem(select, index) {
    const oldId = usedItemIds[index];
    const newId = parseInt(select.value);

    if (usedItemIds.includes(newId)) {
        alert("Item sudah dipilih.");
        select.value = oldId;
        return;
    }

    usedItemIds[index] = newId;
}
</script>

</x-app-layout>
