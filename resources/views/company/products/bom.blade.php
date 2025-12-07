<x-app-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- BREADCRUMB --}}
        <nav class="flex items-center text-sm text-gray-600 gap-2">
            <a href="{{ route('products.index', $companyCode) }}"
               class="inline-flex items-center gap-1.5 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Daftar Produk
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-900">{{ $product->name }}</span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-500">Bill of Materials</span>
        </nav>

        {{-- HEADER SECTION --}}
        <div class="bg-gradient-to-br from-emerald-50 via-white to-blue-50 border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

                {{-- LEFT: PRODUCT INFO --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold mb-3">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Bill of Materials
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                        <p class="text-sm text-gray-600 mt-2">
                            Kelola komposisi bahan baku untuk produk ini
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                            <div class="text-xs font-medium text-gray-500 mb-1">Kode Produk</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $product->code ?? '-' }}</div>
                        </div>

                        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                            <div class="text-xs font-medium text-gray-500 mb-1">Kategori</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $product->category->name ?? '-' }}</div>
                        </div>

                        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                            <div class="text-xs font-medium text-gray-500 mb-1">Status</div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                {{ $product->is_active ? 'Aktif' : 'Non Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: ACTIONS --}}
                <div class="lg:w-64 flex gap-2">
                    <a href="{{ route('products.show', [$companyCode, $product->id]) }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>

                    <button type="button" onclick="openModal('add')"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-600 shadow-lg shadow-emerald-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Bahan
                    </button>
                </div>
            </div>
        </div>

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Terdapat kesalahan:</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Daftar Bahan Baku</h2>
                        <p class="text-sm text-gray-600 mt-0.5">Komposisi bahan per unit produk</p>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-full">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">{{ $bomItems->count() }} Item</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Bahan Baku</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Qty / Unit</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-36">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($bomItems as $bom)
                        <tr class="hover:bg-emerald-50/30 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-100 to-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $bom->item->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-xs font-medium text-gray-700">
                                    {{ $bom->item->kategori->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="font-mono font-semibold text-gray-900">
                                    {{ rtrim(rtrim(number_format($bom->qty_per_unit, 3, ',', '.'), '0'), ',') }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ $bom->item->satuan->name ?? '-' }}</span>
                            </td>

<td class="px-6 py-4">
    <div class="flex items-center justify-center gap-2">

        {{-- EDIT --}}
        <button type="button"
            onclick="openModal('edit', {
                id: '{{ $bom->id }}',
                action: '{{ route('products.bom.update',  [$companyCode, $product->id, $bom->id]) }}',
                item_id: '{{ $bom->item_id }}',
                qty: '{{ $bom->qty_per_unit }}'
            })"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-blue-200 text-blue-700 hover:bg-blue-50 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </button>

        {{-- DELETE --}}
        <form method="POST"
              action="{{ route('products.bom.destroy', [$companyCode, $product->id, $bom->id]) }}"
              onsubmit="return confirm('Yakin ingin menghapus bahan ini dari BOM?');">
            @csrf
            @method('DELETE')
            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border-2 border-red-200 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
            </button>
        </form>

    </div>
</td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Bahan</h3>
                                    <p class="text-sm text-gray-600 mb-4">Tambahkan bahan baku untuk membuat BOM produk ini</p>
                                    <button onclick="openModal('add')" 
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Tambah Bahan Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    {{-- MODAL BACKDROP --}}
    <div id="modalBackdrop" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40" onclick="closeModal()"></div>

    {{-- MODAL CONTENT --}}
    <div id="modalContent" class="hidden fixed inset-0 flex items-center justify-center z-50 p-4">
        <div onclick="event.stopPropagation()"
             class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all">

            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-5">
                <h2 id="modalTitle" class="text-xl font-bold text-white">Tambah Bahan BOM</h2>
                <p class="text-emerald-100 text-sm mt-1">Masukkan detail bahan baku untuk BOM</p>
            </div>

            <form id="bomForm" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                {{-- ITEM --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Bahan Baku
                        </span>
                    </label>
                    <select id="itemField" name="item_id" required
                            class="w-full border-gray-300 rounded-xl text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                        <option value="">-- Pilih Bahan --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} — {{ $item->satuan->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- QTY --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Qty per Unit
                        </span>
                    </label>
                    <input type="number" id="qtyField" step="0.001" min="0" name="qty_per_unit" required
                           placeholder="0.000"
                           class="w-full border-gray-300 rounded-xl text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    <p class="mt-1.5 text-xs text-gray-500">Jumlah bahan yang dibutuhkan per unit produk</p>
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 text-sm font-medium border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>

                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-emerald-600 shadow-lg shadow-emerald-200 transition-all">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal(mode, data = {}) {
            const modalBackdrop = document.getElementById('modalBackdrop');
            const modalContent = document.getElementById('modalContent');
            const modalTitle = document.getElementById('modalTitle');
            const bomForm = document.getElementById('bomForm');
            const methodField = document.getElementById('methodField');
            const itemField = document.getElementById('itemField');
            const qtyField = document.getElementById('qtyField');

            if (mode === 'add') {
                modalTitle.textContent = 'Tambah Bahan BOM';
                bomForm.action = '{{ route('products.bom.store', [$companyCode, $product->id]) }}';
                methodField.value = 'POST';
                itemField.value = '';
                qtyField.value = '';
            } else if (mode === 'edit') {
                modalTitle.textContent = 'Edit Bahan BOM';
                bomForm.action = data.action;
                methodField.value = 'PUT';
                itemField.value = data.item_id;
                qtyField.value = data.qty;
            }

            modalBackdrop.classList.remove('hidden');
            modalContent.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modalBackdrop = document.getElementById('modalBackdrop');
            const modalContent = document.getElementById('modalContent');
            
            modalBackdrop.classList.add('hidden');
            modalContent.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</x-app-layout>