<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Purchase Order</h1>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('po.store', $companyCode) }}">
        @csrf

        {{-- INFORMASI PO --}}
        <div class="bg-white p-6 rounded-xl border shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi PO</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Cabang</label>
                    <select name="cabang_resto_id"
                        class="w-full border rounded-lg p-2 mt-1">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Supplier</label>
                    <select id="supplierSelect" name="suppliers_id"
                        class="w-full border rounded-lg p-2 mt-1"
                        onchange="onSupplierChange()">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal PO</label>
                    <input type="date" name="po_date"
                        class="w-full border rounded-lg p-2 mt-1">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal Diharapkan</label>
                    <input type="date" name="expected_delivery_date"
                        class="w-full border rounded-lg p-2 mt-1">
                </div>
            </div>

            <div class="mt-4">
                <label class="text-sm font-medium">Catatan</label>
                <textarea name="note" class="w-full border rounded-lg p-2 mt-1"
                    rows="3"></textarea>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Item Pembelian</h2>

            <table class="w-full text-sm border rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2">Item</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Harga</th>
                        <th class="p-2">Diskon (%)</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemTable"></tbody>
            </table>

            <button type="button"
                onclick="addRow()"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                + Tambah Item
            </button>
        </div>

        <button type="submit"
            class="mt-6 px-6 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            Simpan PO
        </button>

    </form>

</div>


<script>
let rowIndex = 0;

// Data item per supplier (AMAN — sudah disiapkan controller)
const supplierItems = @json($supplierItems);

function onSupplierChange() {
    document.getElementById("itemTable").innerHTML = "";
    rowIndex = 0;
}

function addRow() {
    const supplierId = document.getElementById("supplierSelect").value;

    if (!supplierId) {
        alert("Pilih supplier terlebih dahulu.");
        return;
    }

    const items = supplierItems[supplierId];

    if (!items || items.length === 0) {
        alert("Supplier ini tidak memiliki item.");
        return;
    }

    let options = "";
    items.forEach(i => {
        options += `<option value="${i.id}">${i.name}</option>`;
    });

    const tbody = document.getElementById("itemTable");

    tbody.insertAdjacentHTML('beforeend', `
        <tr class="border-b">
            <td class="p-2">
                <select name="items[${rowIndex}][item_id]" 
                        class="w-full border rounded-lg p-2">
                    ${options}
                </select>
            </td>

            <td class="p-2">
                <input type="number" min="0" 
                       name="items[${rowIndex}][qty_ordered]" 
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2">
                <input type="number" min="0" 
                       name="items[${rowIndex}][unit_price]" 
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2">
                <input type="number" min="0"
                       name="items[${rowIndex}][discount_pct]" 
                       class="w-full border rounded-lg p-2">
            </td>

            <td class="p-2 text-center">
                <button type="button" 
                    onclick="this.closest('tr').remove()" 
                    class="text-red-600 hover:underline">
                    Hapus
                </button>
            </td>
        </tr>
    `);

    rowIndex++;
}
</script>


</x-app-layout>
