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

        {{-- ERROR --}}
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

            {{-- PANEL CABANG --}}
            <section class="bg-white border rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Cabang</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Cabang Asal --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1">Cabang Asal</label>
                        <select id="cabang_from" name="cabang_id_from"
                                class="input-select w-full" required>
                            <option value="">-- Pilih Cabang Asal --</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ $b->id == $req->cabang_id_from ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cabang Tujuan --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1">Cabang Tujuan</label>
                        <select id="cabang_to" name="cabang_id_to"
                                class="input-select w-full" required>
                            <option value="">-- Pilih Cabang Tujuan --</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ $b->id == $req->cabang_id_to ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </section>

            {{-- PANEL ITEMS --}}
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
                                    <select
                                        name="items[{{ $i }}][item_id]"
                                        class="input-select w-full item-select"
                                        data-current-id="{{ $d->item_id }}"
                                        required>
                                        <option value="">-- Pilih Item --</option>
                                    </select>
                                </td>

                                <td class="py-2">
                                    <input type="number"
                                           name="items[{{ $i }}][qty]"
                                           class="input-text w-full text-right qty-input"
                                           value="{{ $d->qty }}"
                                           min="0.01" step="0.01" required>
                                </td>

                                <td class="py-2 text-center">
                                    <button type="button"
                                            class="text-red-500 hover:text-red-700 text-lg removeRow">
                                        ✕
                                    </button>
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
                <textarea name="note" rows="3" class="input-text w-full">{{ $req->note }}</textarea>
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
        (function () {
            const itemsPerBranch = @json($itemsPerBranch);

            const form = document.getElementById('mrForm');
            const cabangFrom = document.getElementById('cabang_from');
            const cabangTo = document.getElementById('cabang_to');
            const addRowBtn = document.getElementById('addRow');
            const itemsTableBody = document.querySelector('#itemsTable tbody');

            let rowIndex = {{ count($req->details) }};
            let itemsData = [];

            // ---------- Helpers ----------
            function getItemSelects() {
                return Array.from(document.querySelectorAll('.item-select'));
            }

            function getQtyInputs() {
                return Array.from(document.querySelectorAll('.qty-input'));
            }

            function getSelectedItems(exceptSelect = null) {
                return getItemSelects()
                    .filter(s => s !== exceptSelect)
                    .map(s => s.value)
                    .filter(v => v !== "");
            }

            function hasDuplicateItems() {
                const selected = getItemSelects()
                    .map(s => s.value)
                    .filter(v => v !== "");

                return selected.length !== new Set(selected).size;
            }

            function showValidationMessage(message, type = 'error') {
                const existing = document.getElementById('validation-alert');
                if (existing) existing.remove();

                const alertClass = type === 'error'
                    ? 'border-red-200 bg-red-50 text-red-700'
                    : 'border-amber-200 bg-amber-50 text-amber-700';

                const alert = document.createElement('div');
                alert.id = 'validation-alert';
                alert.className = `mb-6 rounded-xl border ${alertClass} px-4 py-3 text-sm`;
                alert.innerHTML = `<div class="font-semibold">${message}</div>`;

                form.insertAdjacentElement('afterbegin', alert);
                alert.scrollIntoView({behavior: 'smooth', block: 'nearest'});

                setTimeout(() => alert.remove(), 4000);
            }

            // ---------- Items Handling ----------
            function refreshItemsData() {
                const branchId = cabangFrom.value;
                itemsData = branchId ? (itemsPerBranch[branchId] || []) : [];
            }

            function renderOptionsForSelect(select, currentValue, selectedItems) {

                select.innerHTML = `<option value="">-- Pilih Item --</option>`;

                itemsData.forEach(item => {

                    // Item yg sedang dipakai oleh row ini TIDAK boleh di-disable
                    const isUsedByOtherRow = selectedItems.includes(item.id.toString());
                    const isCurrentItem = item.id.toString() === currentValue;

                    const disabledAttr = (!isCurrentItem && isUsedByOtherRow) ? 'disabled' : '';
                    const disabledText = (!isCurrentItem && isUsedByOtherRow) ? ' [Sudah dipilih]' : '';

                    select.innerHTML += `
                        <option value="${item.id}" ${disabledAttr}>
                            ${item.name} (${item.satuan})${disabledText}
                        </option>
                    `;
                });

                // === SET VALUE SETELAH SELESAI RENDER ===
                if (currentValue) {
                    select.value = currentValue;
                }
            }

            function loadItemsForBranch(initial = false) {
                refreshItemsData();

                if (!itemsData.length) {
                    // kosongkan semua select
                    getItemSelects().forEach(sel => {
                        sel.innerHTML = `<option value="">-- Pilih Item --</option>`;
                    });
                    return;
                }

                getItemSelects().forEach(sel => {
                    const currentValue = initial ? sel.dataset.currentId : sel.value;
                    const selectedItems = getSelectedItems(sel);
                    renderOptionsForSelect(sel, currentValue, selectedItems);
                });
            }

            function addItemRow() {
                if (!cabangFrom.value) {
                    showValidationMessage('⚠️ Pilih Cabang Asal terlebih dahulu', 'error');
                    cabangFrom.focus();
                    return;
                }

                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
                    <td class="py-2 pr-2">
                        <select name="items[${rowIndex}][item_id]"
                                class="input-select w-full item-select" required>
                            <option value="">-- Pilih Item --</option>
                        </select>
                    </td>
                    <td class="py-2">
                        <input type="number" min="0.01" step="0.01"
                               name="items[${rowIndex}][qty]"
                               class="input-text w-full text-right qty-input"
                               placeholder="0.00" required>
                    </td>
                    <td class="py-2 text-center">
                        <button type="button"
                            class="text-red-500 hover:text-red-700 text-lg removeRow"
                            title="Hapus baris">✕</button>
                    </td>
                `;

                itemsTableBody.appendChild(newRow);
                rowIndex++;

                // load option untuk row baru
                loadItemsForBranch(false);
                newRow.querySelector('.item-select').focus();
            }

            function removeItemRow(button) {
                const rows = document.querySelectorAll('.item-row');

                if (rows.length === 1) {
                    showValidationMessage('⚠️ Minimal harus ada 1 item', 'error');
                    return;
                }

                button.closest('tr').remove();
                loadItemsForBranch(false);
            }

            function resetItemsOnBranchChange() {
                itemsTableBody.innerHTML = `
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
                                   class="input-text w-full text-right qty-input"
                                   placeholder="0.00" required>
                        </td>
                        <td class="py-2 text-center">
                            <button type="button"
                                class="text-red-500 hover:text-red-700 text-lg removeRow">✕</button>
                        </td>
                    </tr>
                `;
                rowIndex = 1;
                loadItemsForBranch(false);
            }

            // ---------- Branch Sync ----------
            function syncBranchSelections() {
                const fromVal = cabangFrom.value;
                const toVal = cabangTo.value;

                Array.from(cabangFrom.options).forEach(o => o.hidden = false);
                Array.from(cabangTo.options).forEach(o => o.hidden = false);

                if (fromVal) {
                    const optTo = cabangTo.querySelector(`option[value='${fromVal}']`);
                    if (optTo) optTo.hidden = true;
                    if (cabangTo.value === fromVal) {
                        cabangTo.value = "";
                    }
                }

                if (toVal) {
                    const optFrom = cabangFrom.querySelector(`option[value='${toVal}']`);
                    if (optFrom) optFrom.hidden = true;
                    if (cabangFrom.value === toVal) {
                        cabangFrom.value = "";
                    }
                }
            }

            function handleBranchFromChange() {
                if (!cabangFrom.value) {
                    refreshItemsData();
                    resetItemsOnBranchChange();
                    syncBranchSelections();
                    return;
                }

                const hasData = getItemSelects().length > 0 &&
                    getItemSelects().some(s => s.value);

                if (hasData) {
                    const confirmReset = confirm(
                        'Mengubah cabang asal akan mereset semua item. Lanjutkan?'
                    );
                    if (!confirmReset) {
                        // batal, kembalikan option sync
                        syncBranchSelections();
                        return;
                    }
                }

                resetItemsOnBranchChange();
                syncBranchSelections();
            }

            // ---------- Validation ----------
            function validateForm(e) {
                if (cabangFrom.value && cabangTo.value && cabangFrom.value === cabangTo.value) {
                    e.preventDefault();
                    showValidationMessage('❌ Cabang Asal dan Cabang Tujuan tidak boleh sama', 'error');
                    return;
                }

                if (hasDuplicateItems()) {
                    e.preventDefault();
                    showValidationMessage('❌ Ada item yang duplikat. Setiap item hanya boleh dipilih sekali.', 'error');
                    return;
                }

                const itemSelects = getItemSelects();
                if (itemSelects.length === 0) {
                    e.preventDefault();
                    showValidationMessage('❌ Minimal harus ada 1 item', 'error');
                    return;
                }

                const hasEmptyItem = itemSelects.some(s => !s.value);
                if (hasEmptyItem) {
                    e.preventDefault();
                    showValidationMessage('❌ Semua item harus dipilih', 'error');
                    return;
                }

                const quantities = getQtyInputs();
                const hasInvalidQty = quantities.some(q => !q.value || parseFloat(q.value) <= 0);
                if (hasInvalidQty) {
                    e.preventDefault();
                    showValidationMessage('❌ Semua quantity harus diisi dengan nilai lebih dari 0', 'error');
                    return;
                }
            }

            // ---------- Events ----------
            // Inisialisasi awal
            loadItemsForBranch(true);
            syncBranchSelections();

            // Perubahan cabang asal/tujuan
            cabangFrom.addEventListener('change', () => {
                handleBranchFromChange();
            });

            cabangTo.addEventListener('change', () => {
                syncBranchSelections();
            });

            // Tambah baris
            addRowBtn.addEventListener('click', addItemRow);

            // Delegasi klik (hapus row)
            document.addEventListener('click', e => {
                if (e.target.classList.contains('removeRow')) {
                    removeItemRow(e.target);
                }
            });

            // Delegasi change (item-select)
            document.addEventListener('change', e => {
                if (e.target.classList.contains('item-select')) {
                    loadItemsForBranch(false);
                }
            });

            // Validasi submit
            form.addEventListener('submit', validateForm);

            // Auto-format qty
            document.addEventListener('blur', e => {
                if (e.target.classList.contains('qty-input') && e.target.value) {
                    const value = parseFloat(e.target.value);
                    if (!isNaN(value)) {
                        e.target.value = value.toFixed(2);
                    }
                }
            }, true);

            // Keyboard shortcut
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    form.requestSubmit();
                }
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'i') {
                    e.preventDefault();
                    addItemRow();
                }
            });

        })();
    </script>

</x-app-layout>
