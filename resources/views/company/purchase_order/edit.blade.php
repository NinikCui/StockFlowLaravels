<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Purchase Order</h1>

    {{-- ALERT --}}
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-700 rounded-xl shadow-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('po.update', [$companyCode, $po->id]) }}" id="poForm">
        @csrf
        @method('PUT')

        {{-- ========================== --}}
        {{-- INFORMASI PO (READONLY) --}}
        {{-- ========================== --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi PO</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Cabang</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->cabangResto->name }}">
                </div>

                <div>
                    <label class="text-sm font-medium">Supplier</label>
                    <input readonly id="supplierSelect"
                        class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->supplier->name }}">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal PO</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->po_date }}">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal Diharapkan</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->expected_delivery_date }}">
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="mt-4">
                <label class="text-sm font-medium">Catatan</label>
                <textarea name="note" rows="3"
                    class="mt-1 p-2 w-full border rounded-lg">{{ old('note', $po->note) }}</textarea>
            </div>
        </div>


        {{-- ========================== --}}
        {{-- ITEM TABLE --}}
        {{-- ========================== --}}
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

        <button type="submit"
            class="mt-6 px-6 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            Simpan Perubahan
        </button>

    </form>
</div>


{{-- ========================== --}}
{{-- JAVASCRIPT FINAL FIX --}}
{{-- ========================== --}}
<script>
let rowIndex = 0;

// Data supplier items
const supplierItems = @json($supplierItems);

// Existing PO items
const existing = @json($po->details);

// Track used IDs (prevent duplicate)
let usedItemIds = [];

// =======================
// LOAD EXISTING 
// =======================
window.onload = function () {
    const supplierId = @json($po->suppliers_id);
    const items = supplierItems[supplierId] ?? [];

    existing.forEach(d => {
        let existingItem = items.find(i => i.id === d.item_id);

        let itemName = existingItem
            ? existingItem.name
            : d.item.name + " (Tidak lagi dijual)";

        addRow(d.item_id, d.qty_ordered, d.unit_price, d.discount_pct, itemName);
    });
};


// =======================
// ADD ROW (SAFE VERSION)
// =======================
function addRow(itemId = null, qty = 1, price = 0, discount = 0, legacyName = null) {

    const supplierId = @json($po->suppliers_id);
    let items = supplierItems[supplierId] ?? [];

    // Add legacy item if needed
    if (itemId && !items.some(i => i.id === itemId)) {
        items.push({
            id: itemId,
            name: legacyName ?? "Item Lama"
        });
    }

    // Filter available items
    const available = items.filter(i => !usedItemIds.includes(i.id) || i.id === itemId);

    if (available.length === 0) {
        alert("Semua item supplier sudah dipakai.");
        return;
    }

    const selectedId = itemId ?? available[0].id;

    let options = "";
    available.forEach(i => {
        options += `<option value="${i.id}" ${i.id === selectedId ? 'selected' : ''}>${i.name}</option>`;
    });

    document.getElementById("itemTable").insertAdjacentHTML("beforeend", `
        <tr class="border-b">

            <td class="p-2">
                <select name="items[${rowIndex}][item_id]" 
                        class="itemSelect w-full border rounded-lg p-2"
                        onchange="changeItem(this)">
                    ${options}
                </select>
            </td>

            <td class="p-2">
                <input type="number" min="1" value="${qty}"
                       name="items[${rowIndex}][qty_ordered]"
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2">
                <input type="number" min="0" value="${price}"
                       name="items[${rowIndex}][unit_price]"
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2">
                <input type="number" min="0" max="100" value="${discount}"
                       name="items[${rowIndex}][discount_pct]"
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2 text-center">
                <button type="button" class="text-red-600 hover:underline"
                        onclick="removeRow(this)">
                    Hapus
                </button>
            </td>

        </tr>
    `);

    usedItemIds.push(selectedId);
    rowIndex++;
}
// =======================
// CHANGE ITEM SAFE
// =======================
function changeItem(select) {
    const newId = parseInt(select.value);

    // Ambil semua value itemSelect 
    const allValues = [...document.querySelectorAll('.itemSelect')]
        .map(s => parseInt(s.value));

    const duplicates = allValues.filter(v => v === newId).length > 1;

    if (duplicates) {
        alert("Item sudah dipilih.");
        select.value = "";
        return;
    }
}


// =======================
// REMOVE ROW
// =======================
function removeRow(btn) {
    const tr = btn.closest("tr");
    const itemId = parseInt(tr.querySelector(".itemSelect").value);

    usedItemIds = usedItemIds.filter(id => id !== itemId);

    tr.remove();
}
</script>

</x-app-layout>
