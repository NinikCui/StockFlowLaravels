<x-app-layout :branchCode="$branchCode">

<div class="max-w-5xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Buat Purchase Order</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('branch.po.store', $branchCode) }}"
          id="poForm">

        @csrf

        {{-- ========================== --}}
        {{-- INFORMASI PO --}}
        {{-- ========================== --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Informasi Purchase Order Cabang {{ $branch->name }}
            </h2>

            <div class="grid grid-cols-3 gap-4">

                {{-- WAREHOUSE --}}
                <div>
                    <label class="text-sm font-medium">Gudang</label>
                    <select name="warehouse_id"
                        class="w-full mt-1 p-2 border rounded-lg">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SUPPLIER --}}
                <div>
                    <label class="text-sm font-medium">Supplier</label>
                    <select name="suppliers_id" id="supplierSelect"
                        class="w-full mt-1 p-2 border rounded-lg">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL PO --}}
                <div>
                    <label class="text-sm font-medium">Tanggal PO</label>
                    <input type="date" id="poDate" name="po_date"
                        class="w-full mt-1 p-2 border rounded-lg">
                </div>

                {{-- TANGGAL DIHARAPKAN --}}
                <div>
                    <label class="text-sm font-medium">Tanggal Diharapkan</label>
                    <input type="date" id="expectedDate" name="expected_delivery_date"
                        class="w-full mt-1 p-2 border rounded-lg">
                </div>

            </div>

            {{-- CATATAN --}}
            <div class="mt-4">
                <label class="text-sm font-medium">Catatan</label>
                <textarea name="note" rows="3"
                    required placeholder="Wajib Diisi"
                    class="w-full mt-1 p-2 border rounded-lg"></textarea>
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

            <button type="button" id="btnAddItem"
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


{{-- ========================== --}}
{{-- JAVASCRIPT --}}
{{-- ========================== --}}
<script>
let supplierItems = [];
let usedItems = [];
let rowIndex = 0;

const today = new Date().toISOString().split("T")[0];
$("#poDate").val(today).attr("max", today);
$("#expectedDate").attr("min", today);

// LOAD ITEMS BY SUPPLIER
$("#supplierSelect").on("change", function() {
    const supplierId = $(this).val();
    usedItems = [];
    $("#itemTable").html("");
    rowIndex = 0;

    if (!supplierId) return;

    // 🔍 DEBUG: cek URL final yang dipanggil
    let ajaxUrl = "{{ route('branch.po.ajax.supplier.items', [$branchCode, ':id']) }}"
                    .replace(':id', supplierId);

    console.log("🔗 AJAX URL:", ajaxUrl);
    console.log("📦 Fetching items for supplier ID:", supplierId);
    $.ajax({
        url: ajaxUrl,
        method: "GET",
        success: function(items) {
            console.log("✅ ITEMS LOADED:", items);
            supplierItems = items;
        },
        error: function(xhr) {
            console.error("❌ AJAX ERROR");
            console.error("Status:", xhr.status);
            console.error("Response:", xhr.responseText);

            alert("Request gagal! Cek console.");
        }
    });
});


// ADD ITEM ROW
$("#btnAddItem").on("click", function() {
    if (!supplierItems.length) {
        alert("Pilih supplier terlebih dahulu!");
        return;
    }

    const available = supplierItems.filter(i => !usedItems.includes(i.id));
    if (!available.length) {
        alert("Semua item sudah ditambahkan.");
        return;
    }

    const item = available[0];
    usedItems.push(item.id);

    let row = `
        <tr class="border-b">
            <td class="p-2">
                <select class="w-full p-2 border rounded-lg"
                        name="items[${rowIndex}][item_id]">
                    ${supplierItems.map(i => `
                        <option value="${i.id}"
                                data-price="${i.price}"
                                data-moq="${i.min_order_qty}"
                                ${i.id === item.id ? 'selected' : ''}>
                            ${i.name}
                        </option>
                    `).join("")}
                </select>
            </td>

            <td class="p-2">
                <input type="number" min="1"
                    name="items[${rowIndex}][qty_ordered]"
                    class="w-full p-2 border rounded-lg"
                    value="${item.min_order_qty}">
            </td>

            <td class="p-2">
                <input type="number" min="0"
                    name="items[${rowIndex}][unit_price]"
                    class="w-full p-2 border rounded-lg"
                    value="${item.price}">
            </td>

            <td class="p-2">
                <input type="number" min="0" max="100"
                    name="items[${rowIndex}][discount_pct]"
                    class="w-full p-2 border rounded-lg"
                    value="0">
            </td>

            <td class="p-2 text-center">
                <button type="button" class="text-red-600 btnRemove">Hapus</button>
            </td>
        </tr>
    `;

    $("#itemTable").append(row);
    rowIndex++;
});

// HAPUS ROW
$(document).on("click", ".btnRemove", function() {
    $(this).closest("tr").remove();
});
</script>

</x-app-layout>
