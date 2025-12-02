<x-app-layout :branchCode="$branchCode">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- BREADCRUMB --}}
    <div class="mb-6">
        <a href="{{ route('branch.request.show', [$branchCode, $req->id]) }}"
           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-emerald-600">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- HEADER --}}
    <h1 class="text-3xl font-bold text-gray-900 mb-1">
        Edit Request {{ $req->trans_number }}
    </h1>
    <p class="text-sm text-gray-600 mb-8">Ubah informasi request ini.</p>

    {{-- ERRORS --}}
    @include('components.form-error')

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('branch.request.update', [$branchCode, $req->id]) }}"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- INFORMASI CABANG --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Cabang Asal --}}
                <div>
                    <label class="text-sm font-semibold text-gray-700">Cabang Asal</label>
                    <select name="cabang_from_id"
                            id="cabang_from"
                            class="w-full border-gray-300 rounded-lg"
                            required>
                        <option value="">-- Pilih Cabang Asal --</option>

                        @foreach ($branchesFrom as $b)
                            <option value="{{ $b->id }}"
                                {{ $req->cabang_id_from == $b->id ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Cabang Tujuan --}}
                <div>
                    <label class="text-sm font-semibold text-gray-700">Cabang Tujuan</label>
                    <input type="text" disabled
                           value="{{ $req->cabangTo->name }} ({{ $req->cabangTo->code }})"
                           class="w-full bg-gray-100 border-gray-300 rounded-lg">
                </div>

            </div>
        </div>

        {{-- ITEMS --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Daftar Item</h2>

                <button type="button" id="addRow"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm">
                    + Tambah Item
                </button>
            </div>

            <table class="w-full text-sm" id="itemsTable">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Item</th>
                        <th class="py-2 w-32 text-center">Qty</th>
                        <th class="py-2 w-12"></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($req->details as $i => $d)
                        <tr class="item-row border-b">

                            <td class="py-3 pr-2">
                                <select name="items[{{ $i }}][item_id]"
                                        class="item-select w-full border-gray-300 rounded-lg"
                                        required>
                                    <option value="">-- Pilih --</option>

                                    @foreach ($itemsPerBranch[$req->cabang_id_from] ?? [] as $it)
                                        <option value="{{ $it['id'] }}"
                                            {{ $d->items_id == $it['id'] ? 'selected' : '' }}>
                                            {{ $it['name'] }} ({{ $it['satuan'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="py-3 text-center">
                                <input type="number" name="items[{{ $i }}][qty]"
                                       value="{{ $d->qty }}"
                                       min="0.01" step="0.01"
                                       class="w-full border-gray-300 rounded-lg text-center"
                                       required>
                            </td>

                            <td class="py-3 text-center">
                                <button type="button"
                                        class="removeRow text-red-500 w-8 h-8">
                                    ✕
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        {{-- NOTE --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm">
            <label class="text-sm font-semibold">Catatan</label>
            <textarea name="note" rows="4"
                      class="w-full border-gray-300 rounded-lg"
                      placeholder="Tambahkan catatan...">{{ $req->note }}</textarea>
        </div>

        {{-- SUBMIT --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('branch.request.show', [$branchCode, $req->id]) }}"
               class="px-5 py-2 border rounded-lg">
                Batal
            </a>

            <button class="px-6 py-2 bg-emerald-600 text-white rounded-lg">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

{{-- JS SAMA PERSIS DGN CREATE --}}
<script>
    const cabangFrom = document.getElementById('cabang_from');
    const itemsPerBranch = @json($itemsPerBranch);

    let rowIndex = {{ $req->details->count() }};

    function refreshItemSelect() {
        const branch = cabangFrom.value;

        document.querySelectorAll('.item-select').forEach(sel => {
            sel.innerHTML = `<option value="">-- Pilih --</option>`;
            if (!branch || !itemsPerBranch[branch]) return;

            itemsPerBranch[branch].forEach(i => {
                sel.innerHTML += `<option value="${i.id}">${i.name} (${i.satuan})</option>`;
            });
        });
    }

    cabangFrom.addEventListener('change', refreshItemSelect);

    document.getElementById('addRow').addEventListener('click', () => {
        const tbody = document.querySelector('#itemsTable tbody');

        const row = document.createElement('tr');
        row.classList.add('item-row', 'border-b');

        row.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowIndex}][item_id]" class="item-select w-full border-gray-300 rounded-lg" required>
                    <option value="">-- Pilih --</option>
                </select>
            </td>
            <td class="py-3 text-center">
                <input type="number" name="items[${rowIndex}][qty]" min="0.01" step="0.01"
                       class="w-full border-gray-300 rounded-lg text-center" required>
            </td>
            <td class="py-3 text-center">
                <button type="button" class="removeRow text-red-500 w-8 h-8">✕</button>
            </td>
        `;

        tbody.appendChild(row);
        rowIndex++;
        refreshItemSelect();
    });

    document.addEventListener('click', e => {
        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
        }
    });

</script>

</x-app-layout>
