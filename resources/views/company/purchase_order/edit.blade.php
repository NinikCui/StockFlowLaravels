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

            <div class="grid grid-cols-3 gap-4">

                {{-- Cabang --}}
                <div>
                    <label class="text-sm font-medium">Cabang</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->cabangResto->name }}">
                </div>

                {{-- Warehouse --}}
                <div>
                    <label class="text-sm font-medium">Gudang</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->warehouse->name }}">
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="text-sm font-medium">Supplier</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->supplier->name }}">
                </div>

                {{-- Tanggal PO --}}
                <div>
                    <label class="text-sm font-medium">Tanggal PO</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->po_date }}">
                </div>

                {{-- Tanggal Expected --}}
                <div>
                    <label class="text-sm font-medium">Tanggal Diharapkan</label>
                    <input readonly class="mt-1 p-2 w-full border rounded-lg bg-gray-100"
                        value="{{ $po->expected_delivery_date }}">
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="mt-4 col-span-3">
                <label class="text-sm font-medium">Catatan</label>
                <textarea name="note" rows="3"
                    class="mt-1 p-2 w-full border rounded-lg"
                >{{ old('note', $po->note) }}</textarea>
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
                        <th class="p-2 w-20">Qty</th>
                        <th class="p-2 w-28">Harga</th>
                        <th class="p-2 w-28">Diskon (%)</th>
                        <th class="p-2 text-center w-20">Aksi</th>
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
{{-- JAVASCRIPT FINAL & AMAN --}}
{{-- ========================== --}}
<script>
let rowIndex = 0;

// Supplier → Items
const supplierItems = @json($supplierItems);

// Existing PO details
const existing = @json($po->details);

// Supplier ID
const supplierId = @json($po->suppliers_id);

// Track item yang sudah dipakai
let usedItemIds = [];


/* ======================================================
   LOAD EXISTING ITEM
====================================================== */
window.onload = function () {
    const items = supplierItems[supplierId] ?? [];

    existing.forEach(d => {
        const match = items.find(i => i.id === d.item_id);

        addRow({
            item_id: d.item_id,
            qty: d.qty_ordered,
            price: d.unit_price,
            discount: d.discount_pct,
            displayName: match ? match.name : d.item.name + " (Tidak lagi dijual)"
        });
    });
};



/* ======================================================
   ADD ROW (untuk existing atau new)
====================================================== */
function addRow(data = {}) {
    const item_id = data.item_id ?? null;
    const qty = data.qty ?? 1;
    const price = data.price ?? 0;
    const discount = data.discount ?? 0;
    const displayName = data.displayName ?? null;

    let items = supplierItems[supplierId] ?? [];

    // Jika item lama & tidak ada di supplier → tambahkan manual
    if (item_id && !items.some(i => i.id === item_id)) {
        items.push({
            id: item_id,
            name: displayName ?? "Item Lama"
        });
    }

    // Filter item yang belum dipakai
    const available = items.filter(i => !usedItemIds.includes(i.id) || i.id === item_id);

    if (available.length === 0) {
        alert("Semua item supplier sudah digunakan.");
        return;
    }

    const selectedId = item_id ?? available[0].id;

    // Build options
    let options = "";
    available.forEach(i => {
        options += `<option value="${i.id}" ${i.id === selectedId ? 'selected' : ''}>${i.name}</option>`;
    });

    // Insert Row
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
                <button type="button" onclick="removeRow(this)"
                    class="text-red-600 hover:underline">
                    Hapus
                </button>
            </td>

        </tr>
    `);

    usedItemIds.push(selectedId);
    rowIndex++;
}



/* ======================================================
   CHANGE ITEM (prevent duplicate)
====================================================== */
function changeItem(select) {
    const newId = parseInt(select.value);

    const allSelected = [...document.querySelectorAll('.itemSelect')]
        .map(s => parseInt(s.value));

    if (allSelected.filter(id => id === newId).length > 1) {
        alert("Item sudah dipilih.");
        select.value = "";
        return;
    }

    // sync ulang
    usedItemIds = allSelected;
}



/* ======================================================
   REMOVE ROW
====================================================== */
function removeRow(button) {
    const row = button.closest("tr");
    const id = parseInt(row.querySelector(".itemSelect").value);

    usedItemIds = usedItemIds.filter(x => x !== id);

    row.remove();
}
</script>

</x-app-layout>
