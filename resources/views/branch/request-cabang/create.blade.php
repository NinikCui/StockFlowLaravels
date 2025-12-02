<x-app-layout :branchCode="$branchCode">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- BREADCRUMB --}}
    <div class="mb-6">
        <a href="{{ route('branch.request.index', $branchCode) }}"
           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke daftar request
        </a>
    </div>

    {{-- HEADER --}}
    <div class="mb-8 flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>

        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Transfer Antar Gudang</h1>
            <p class="text-sm text-gray-600 mt-1">
                Permintaan pemindahan stok dari cabang lain ke cabang ini.
            </p>
        </div>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
            <div class="font-semibold text-red-800 mb-2">Terjadi kesalahan:</div>
            <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('branch.request.store', $branchCode) }}"
          id="mrForm"
          class="space-y-6">

        @csrf

        {{-- INFORMASI CABANG --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Cabang</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pilih cabang asal, cabang tujuan otomatis adalah cabang ini.
                </p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Cabang Asal --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Cabang Asal <span class="text-red-500">*</span>
                    </label>

                    <select name="cabang_from_id"
                            id="cabang_from"
                            class="w-full border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            required>
                        <option value="">-- Pilih Cabang Asal --</option>
                        @foreach ($branchesFrom as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>

                    <p class="text-xs text-gray-500">
                        Cabang yang mengirim stok ke cabang ini.
                    </p>
                </div>

                {{-- Cabang Tujuan (auto cabang ini) --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Cabang Tujuan
                    </label>

                    <input type="text"
                           disabled
                           value="{{ $branch->name }} ({{ $branch->code }})"
                           class="w-full px-3 py-2 bg-gray-100 border-gray-300 rounded-lg text-gray-500">

                    <input type="hidden" name="cabang_to_id" value="{{ $branch->id }}">

                    <p class="text-xs text-gray-500">
                        Cabang ini sebagai penerima stok.
                    </p>
                </div>

            </div>
        </section>

        {{-- DAFTAR ITEM --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Item</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Item akan muncul berdasarkan stok di gudang-gudang cabang asal.
                    </p>
                </div>

                <button type="button" id="addRow"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Item
                </button>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm" id="itemsTable">
                    <thead>
                    <tr class="text-left border-b-2 border-gray-200">
                        <th class="pb-3 pr-2 font-semibold text-gray-700">Item</th>
                        <th class="pb-3 w-32 text-center font-semibold text-gray-700">Qty</th>
                        <th class="pb-3 w-16 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr class="item-row border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-3 pr-2">
                            <select name="items[0][item_id]"
                                    class="w-full border-gray-300 rounded-lg item-select"
                                    required>
                                <option value="">-- Pilih Cabang Asal Dulu --</option>
                            </select>
                        </td>

                        <td class="py-3 text-center">
                            <input type="number"
                                   min="0.01"
                                   step="0.01"
                                   name="items[0][qty]"
                                   class="w-full border-gray-300 rounded-lg text-center"
                                   required>
                        </td>

                        <td class="py-3 text-center">
                            <button type="button"
                                    class="removeRow w-8 h-8 text-red-500 hover:bg-red-50 rounded-lg">
                                ✕
                            </button>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </div>

        </section>

        {{-- CATATAN --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Catatan (Opsional)</h2>
            </div>

            <div class="p-6">
                <textarea name="note" rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
        </section>

        {{-- SUBMIT --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('branch.request.index', $branchCode) }}"
               class="px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                Ajukan Request
            </button>
        </div>

    </form>
</div>
<script>
    const cabangFrom = document.getElementById('cabang_from');
    const itemsPerBranch = @json($itemsPerBranch);
    let rowIndex = 1;

    // Isi dropdown item sesuai cabang
    function fillItemSelects() {
        const branchId = cabangFrom.value;

        document.querySelectorAll('.item-select').forEach(sel => {

            if (!branchId || !itemsPerBranch[branchId]) {
                sel.innerHTML = `<option value="">-- Pilih Cabang Asal Dulu --</option>`;
                return;
            }

            let opts = `<option value="">-- Pilih Item --</option>`;

            itemsPerBranch[branchId].forEach(i => {
                opts += `<option value="${i.id}">${i.name} (${i.satuan})</option>`;
            });

            sel.innerHTML = opts;
        });
    }

    // Event ganti cabang
    cabangFrom.addEventListener('change', () => {
        document.querySelectorAll('.item-select').forEach(sel => sel.value = '');
        fillItemSelects();
    });

    // Tambah Row
    document.getElementById('addRow').addEventListener('click', () => {
        const tbody = document.querySelector('#itemsTable tbody');

        const row = document.createElement('tr');
        row.className = 'item-row border-b border-gray-100 hover:bg-gray-50 transition';

        row.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowIndex}][item_id]"
                        class="w-full border-gray-300 rounded-lg item-select"
                        required>
                    <option value="">-- Pilih Cabang Asal Dulu --</option>
                </select>
            </td>

            <td class="py-3 text-center">
                <input type="number"
                       min="0.01"
                       step="0.01"
                       name="items[${rowIndex}][qty]"
                       class="w-full border-gray-300 rounded-lg text-center"
                       required>
            </td>

            <td class="py-3 text-center">
                <button type="button"
                        class="removeRow w-8 h-8 text-red-500 hover:bg-red-50 rounded-lg">✕</button>
            </td>
        `;

        tbody.appendChild(row);

        rowIndex++;

        fillItemSelects();
    });

    // Hapus row
    document.addEventListener('click', e => {
        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    // Init default
    fillItemSelects();
</script>


</x-app-layout>
