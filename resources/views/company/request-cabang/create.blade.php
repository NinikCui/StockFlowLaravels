<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB + HEADER --}}
        <div class="mb-8">
            <a href="{{ route('request.index', $companyCode) }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors mb-3">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke daftar request
            </a>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Buat Material Request Antar Cabang</h1>
                    <p class="text-sm text-gray-600">
                        Owner dapat mengajukan permintaan stok dari satu cabang ke cabang lain dalam satu perusahaan.
                    </p>
                </div>
            </div>
        </div>

        {{-- ERROR SERVER SIDE --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-red-50/50 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-red-100 flex items-center justify-center mt-0.5">
                        <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-red-800 mb-2">Terjadi kesalahan:</div>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm text-red-700 flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('request.store', $companyCode) }}" id="mrForm" class="space-y-6">
            @csrf

            {{-- INFORMASI CABANG --}}
            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Informasi Cabang</h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Pilih cabang asal dan cabang tujuan transfer
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Cabang Asal --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Cabang Asal
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <select id="cabang_from" name="cabang_from_id"
                                        class="input-select w-full pl-10 pr-4 py-2.5 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    <option value="">-- Pilih Cabang Asal --</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-gray-500">Cabang pengirim stok</p>
                        </div>

                        {{-- Cabang Tujuan --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Cabang Tujuan
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <select id="cabang_to" name="cabang_to_id"
                                        class="input-select w-full pl-10 pr-4 py-2.5 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    <option value="">-- Pilih Cabang Tujuan --</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-gray-500">Cabang penerima stok</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs text-blue-800">
                                Cabang asal dan cabang tujuan tidak boleh sama. Item yang tersedia akan mengikuti gudang dari cabang asal.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ITEM LIST --}}
            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Daftar Item yang Diminta</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Tambahkan item yang ingin di-request</p>
                            </div>
                        </div>

                        <button type="button" id="addRow"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 text-sm font-medium shadow-sm hover:shadow transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="itemsTable">
                            <thead>
                            <tr class="text-left border-b-2 border-gray-200">
                                <th class="pb-3 pr-2 font-semibold text-gray-700">Item</th>
                                <th class="pb-3 w-32 text-center font-semibold text-gray-700">Quantity</th>
                                <th class="pb-3 text-center w-16 font-semibold text-gray-700">Aksi</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr class="item-row border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 pr-2">
                                    <select name="items[0][item_id]" class="input-select w-full item-select border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                        <option value="">-- Pilih Cabang Asal Dulu --</option>
                                    </select>
                                </td>

                                <td class="py-3 px-2">
                                    <input type="number" min="0.01" step="0.01"
                                           name="items[0][qty]"
                                           class="input-text w-full text-right qty-input border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                           placeholder="0.00"
                                           required>
                                </td>

                                <td class="py-3 text-center">
                                    <button type="button"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors removeRow">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- CATATAN --}}
            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </div>
                        <div>
                            <label class="text-lg font-semibold text-gray-900">Catatan</label>
                            <p class="text-xs text-gray-500 mt-0.5">Tambahkan catatan atau instruksi khusus (opsional)</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <textarea name="note" rows="4" 
                              class="input-text w-full resize-none border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Contoh: Kirim sebelum weekend, prioritas tinggi, atau instruksi khusus lainnya..."></textarea>
                </div>
            </section>

            {{-- SUBMIT --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('request.index', $companyCode) }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 text-white hover:from-emerald-700 hover:to-emerald-800 active:from-emerald-800 active:to-emerald-900 text-sm font-semibold shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Ajukan Request
                </button>
            </div>

        </form>
    </div>

    <script>
        const cabangFrom = document.getElementById('cabang_from');
        const cabangTo   = document.getElementById('cabang_to');

        const itemsPerBranch = @json($itemsPerBranch);
        let itemsData = [];
        let rowIndex = 1;

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

        cabangFrom.addEventListener('change', () => {
            updateCabangOptions();
            resetItemsTable();
            loadItemsForBranch();
        });

        cabangTo.addEventListener('change', updateCabangOptions);
        updateCabangOptions();


        function resetItemsTable() {
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.innerHTML = `
                <tr class="item-row border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-2">
                        <select name="items[0][item_id]"
                                class="input-select w-full item-select border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="">-- Pilih Cabang Asal Dulu --</option>
                        </select>
                    </td>
                    <td class="py-3 px-2">
                        <input type="number" min="0.01" step="0.01"
                               name="items[0][qty]"
                               class="input-text w-full text-right qty-input border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="0.00" required>
                    </td>
                    <td class="py-3 text-center">
                        <button type="button"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors removeRow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            rowIndex = 1;
            removeError('item-duplicate-error');
        }


        function loadItemsForBranch() {
            const branchId = cabangFrom.value;
            if (!branchId || !itemsPerBranch[branchId]) {
                clearItemsDropdowns();
                return;
            }

            itemsData = itemsPerBranch[branchId];
            updateAllDropdowns();
        }


        function clearItemsDropdowns() {
            document.querySelectorAll('.item-select').forEach(sel => {
                sel.innerHTML = `<option value="">-- Pilih Cabang Asal Dulu --</option>`;
            });
        }


        function getAllSelectedItems() {
            return [...document.querySelectorAll('.item-select')]
                .map(s => s.value)
                .filter(v => v !== "");
        }


        function updateAllDropdowns() {
            const selected = getAllSelectedItems();

            document.querySelectorAll('.item-select').forEach(sel => {

                const cur = sel.value;

                sel.innerHTML = `<option value="">-- Pilih Item --</option>`;

                itemsData.forEach(i => {
                    if (!selected.includes(String(i.id)) || String(i.id) === cur) {
                        sel.innerHTML += `
                            <option value="${i.id}">
                                ${i.name} (${i.satuan})
                            </option>
                        `;
                    }
                });

                sel.value = cur;
            });
        }


        document.getElementById('addRow').addEventListener('click', () => {
            const tbody = document.querySelector('#itemsTable tbody');

            const row = document.createElement('tr');
            row.classList.add('item-row', 'border-b', 'border-gray-100', 'hover:bg-gray-50', 'transition-colors');

            row.innerHTML = `
                <td class="py-3 pr-2">
                    <select name="items[${rowIndex}][item_id]"
                            class="input-select w-full item-select border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </td>

                <td class="py-3 px-2">
                    <input type="number" min="0.01" step="0.01"
                           name="items[${rowIndex}][qty]"
                           class="input-text w-full text-right qty-input border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0.00" required>
                </td>

                <td class="py-3 text-center">
                    <button type="button"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors removeRow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            rowIndex++;

            updateAllDropdowns();
            validateDuplicate();
        });


        document.addEventListener('click', e => {
            if (e.target.closest('.removeRow')) {
                e.target.closest('tr').remove();
                updateAllDropdowns();
                validateDuplicate();
            }
        });


        function validateDuplicate() {
            const selected = getAllSelectedItems();
            const hasDuplicate = new Set(selected).size !== selected.length;

            if (hasDuplicate) {
                showError('item-duplicate-error', 'Item tidak boleh duplikat.');
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
                box.className = 'mb-4 rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 shadow-sm';
                box.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-red-800">${message}</p>
                    </div>
                `;

                const table = document.getElementById('itemsTable');
                table.parentNode.insertBefore(box, table);
            }
        }

        function removeError(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }


        document.getElementById('mrForm').addEventListener('submit', e => {
            if (!validateDuplicate()) {
                e.preventDefault();
                document.getElementById('itemsTable').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });

    </script>

</x-app-layout>