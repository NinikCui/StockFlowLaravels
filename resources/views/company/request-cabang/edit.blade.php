<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB --}}
        <a href="{{ route('request.show', [$companyCode, $req->id]) }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <h1 class="text-2xl font-bold text-gray-900 mb-4">
            Edit Request {{ $req->trans_number }}
        </h1>

        {{-- ERROR SERVER SIDE --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
                <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('request.update', [$companyCode, $req->id]) }}"
              id="mrForm">
            @csrf
            @method('PUT')

            {{-- INFORMASI CABANG --}}
            <section class="bg-white border rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Cabang</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Cabang Asal --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1">Cabang Asal</label>
                        <select id="cabang_from" name="cabang_from_id"
                                class="input-select w-full" required>
                            <option value="">-- Pilih Cabang Asal --</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}"
                                    {{ $b->id == $req->warehouseFrom->cabang_resto_id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cabang Tujuan --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1">Cabang Tujuan</label>
                        <select id="cabang_to" name="cabang_to_id"
                                class="input-select w-full" required>
                            <option value="">-- Pilih Cabang Tujuan --</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}"
                                    {{ $b->id == $req->warehouseTo->cabang_resto_id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </section>

            {{-- ITEM LIST --}}
            <section class="bg-white border rounded-xl shadow-sm p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Daftar Item</h2>
                        <p class="text-xs text-gray-500">Item tidak boleh duplikat.</p>
                    </div>

                    <button type="button" id="addRow"
                        class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 text-sm">
                        + Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="itemsTable">
                        <thead class="border-b text-gray-500">
                          <tr>
                              <th class="p-2 text-left">Item</th>
                              <th class="p-2 w-28 text-center">Qty</th>
                              <th class="p-2 w-10 text-center">Aksi</th>
                          </tr>
                        </thead>

                        <tbody>
                        @foreach ($req->details as $i => $d)
                            <tr class="item-row">
                                <td class="py-2 pr-2">
                                    <select name="items[{{ $i }}][item_id]"
                                            class="input-select w-full item-select"
                                            data-current-id="{{ $d->items_id ?? $d->item->id }}"
                                            required>
                                        <option value="">-- Pilih Item --</option>
                                        {{-- Options diisi via JS --}}
                                    </select>
                                </td>

                                <td class="py-2">
                                    <input type="number" name="items[{{ $i }}][qty]"
                                           class="input-text w-full text-right qty-input"
                                           value="{{ $d->qty }}"
                                           min="0.01" step="0.01" required>
                                </td>

                                <td class="py-2 text-center">
                                    <button type="button"
                                        class="text-red-500 hover:text-red-700 text-lg removeRow">✕</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </section>

            {{-- CATATAN --}}
            <section class="bg-white border rounded-xl shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="note" rows="3"
                          class="input-text w-full">{{ $req->note }}</textarea>
            </section>

            {{-- SUBMIT --}}
            <div class="flex justify-end mt-6 gap-4">
                <a href="{{ route('request.show', [$companyCode, $req->id]) }}"
                   class="px-4 py-2 rounded-lg border text-sm text-gray-700 hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow text-sm hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
    <script>
        const itemsPerBranch = @json($itemsPerBranch);
        const cabangFrom = document.getElementById('cabang_from');
        const cabangTo   = document.getElementById('cabang_to');
        let rowIndex = {{ count($req->details) }};
        let itemsData = [];

        function updateCabangOptions() {
            const fromVal = cabangFrom.value;
            const toVal   = cabangTo.value;

            [...cabangFrom.options].forEach(o => o.hidden = false);
            [...cabangTo.options].forEach(o => o.hidden = false);

            if (fromVal) {
                const opt = cabangTo.querySelector(`option[value='${fromVal}']`);
                if (opt) opt.hidden = true;
            }

            if (toVal) {
                const opt = cabangFrom.querySelector(`option[value='${toVal}']`);
                if (opt) opt.hidden = true;
            }
        }

        function resetItemsTable() {
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.innerHTML = `
                <tr class="item-row">
                    <td class="py-2 pr-2">
                        <select name="items[0][item_id]"
                                class="input-select w-full item-select" required>
                            <option value="">-- Pilih Item --</option>
                        </select>
                    </td>
                    <td class="py-2">
                        <input type="number" min="0.01" step="0.01"
                               name="items[0][qty]"
                               class="input-text w-full text-right qty-input" required>
                    </td>
                    <td class="py-2 text-center">
                        <button type="button"
                                class="text-red-500 hover:text-red-700 text-lg removeRow">✕</button>
                    </td>
                </tr>
            `;
            rowIndex = 1;
            removeError('item-duplicate-error');
        }

        function getSelectedItems(isInitial = false) {
            return [...document.querySelectorAll('.item-select')]
                .map(sel => isInitial ? (sel.dataset.currentId || '') : sel.value)
                .filter(v => v !== '');
        }

        function updateAllDropdowns(isInitial = false) {
            const selected = getSelectedItems(isInitial);

            document.querySelectorAll('.item-select').forEach(sel => {
                const current = isInitial
                    ? (sel.dataset.currentId || '')
                    : sel.value;

                sel.innerHTML = `<option value="">-- Pilih Item --</option>`;

                itemsData.forEach(i => {
                    // tampilkan item kecuali sudah dipakai di row lain,
                    // kecuali ini adalah item yang sedang terpilih di row ini
                    if (!selected.includes(String(i.id)) || String(i.id) === String(current)) {
                        sel.innerHTML += `
                            <option value="${i.id}">
                                ${i.name} (${i.satuan})
                            </option>`;
                    }
                });

                if (current) {
                    sel.value = current;
                }
            });
        }

        function loadItemsForBranch(isInitial = false) {
            const branchId = cabangFrom.value;
            itemsData = itemsPerBranch[branchId] ?? [];
            updateAllDropdowns(isInitial);
        }

        function validateDuplicate() {
            const selected = getSelectedItems(false);
            const hasDuplicate = new Set(selected).size !== selected.length;

            if (hasDuplicate) {
                showError('item-duplicate-error', 'Item tidak boleh duplikat. Gunakan satu baris per item.');
                return false;
            }
            removeError('item-duplicate-error');
            return true;
        }

        function showError(id, message) {
            let box = document.getElementById(id);
            if (!box) {
                box = document.createElement('div');
                box.id = id;
                box.className =
                    'mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700';
                const table = document.getElementById('itemsTable');
                table.parentNode.insertBefore(box, table);
            }
            box.innerText = message;
        }

        function removeError(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        cabangFrom.addEventListener('change', () => {
            updateCabangOptions();
            resetItemsTable();
            loadItemsForBranch(false);
        });

        cabangTo.addEventListener('change', updateCabangOptions);

        document.getElementById('addRow').addEventListener('click', () => {
            const tbody = document.querySelector('#itemsTable tbody');

            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.innerHTML = `
                <td class="py-2 pr-2">
                    <select name="items[${rowIndex}][item_id]"
                            class="input-select w-full item-select" required>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </td>
                <td class="py-2">
                    <input type="number" min="0.01" step="0.01"
                           name="items[${rowIndex}][qty]"
                           class="input-text w-full text-right qty-input" required>
                </td>
                <td class="py-2 text-center">
                    <button type="button"
                        class="text-red-500 hover:text-red-700 text-lg removeRow">✕</button>
                </td>
            `;

            tbody.appendChild(row);
            rowIndex++;

            updateAllDropdowns(false);
            validateDuplicate();
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
                updateAllDropdowns(false);
                validateDuplicate();
            }
        });

        document.addEventListener('change', e => {
            if (e.target.classList.contains('item-select') ||
                e.target.classList.contains('qty-input')) {
                updateAllDropdowns(false);
                validateDuplicate();
            }
        });

        document.getElementById('mrForm').addEventListener('submit', e => {
            const okDup = validateDuplicate();

            if (!okDup) {
                e.preventDefault();
                document.getElementById('itemsTable')
                    .scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        updateCabangOptions();
        loadItemsForBranch(true); 

    </script>

</x-app-layout>
