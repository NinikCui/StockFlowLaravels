<x-app-layout>

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Buat Material Request Antar Cabang</h1>
        <p class="text-sm text-gray-500 mt-1">Owner dapat mengirim permintaan dari cabang mana pun ke cabang mana pun.</p>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('request.store', $companyCode) }}" id="mrForm">
        @csrf

        {{-- ======================== --}}
        {{-- CABANG ASAL / TUJUAN --}}
        {{-- ======================== --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">

            <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Cabang</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Cabang Asal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Asal</label>
                    <select name="cabang_from_id" class="input-select w-full" required>
                        <option value="">-- Pilih Cabang Asal --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cabang Tujuan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Tujuan</label>
                    <select name="cabang_to_id" class="input-select w-full" required>
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>


        {{-- ======================== --}}
        {{-- ITEMS --}}
        {{-- ======================== --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Item yang Diminta</h2>
                <button type="button" id="addRow" class="px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg shadow hover:bg-emerald-700">
                    + Tambah Item
                </button>
            </div>

            <table class="w-full text-sm" id="itemsTable">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">Item</th>
                        <th class="py-2 w-32">Qty</th>
                        <th class="py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td class="py-2">
                            <select name="items[0][item_id]" class="input-select w-full" required>
                                <option value="">-- Pilih Item --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }} ({{ $item->satuan->code }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2">
                            <input type="number" step="0.01" min="0.01" name="items[0][qty]" class="input-text w-full" required>
                        </td>
                        <td class="py-2 text-center">
                            <button type="button" class="text-red-500 removeRow hover:text-red-700">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        {{-- ======================== --}}
        {{-- CATATAN --}}
        {{-- ======================== --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
            <textarea name="note" rows="3" class="input-text w-full"></textarea>
        </div>


        {{-- ======================== --}}
        {{-- SUBMIT --}}
        {{-- ======================== --}}
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
                Ajukan Request
            </button>
        </div>

    </form>
</div>


{{-- =============== --}}
{{-- JAVASCRIPT --}}
{{-- =============== --}}
<script>
    let rowIndex = 1;

    document.getElementById('addRow').addEventListener('click', () => {
        const tbody = document.querySelector('#itemsTable tbody');

        const row = document.createElement('tr');
        row.classList.add('item-row');

        row.innerHTML = `
            <td class="py-2">
                <select name="items[${rowIndex}][item_id]" class="input-select w-full" required>
                    <option value="">-- Pilih Item --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} ({{ $item->satuan->code }})
                        </option>
                    @endforeach
                </select>
            </td>

            <td class="py-2">
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][qty]" class="input-text w-full" required>
            </td>

            <td class="py-2 text-center">
                <button type="button" class="text-red-500 removeRow hover:text-red-700">✕</button>
            </td>
        `;

        tbody.appendChild(row);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });
</script>

</x-app-layout>
