<x-app-layout>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- BREADCRUMB --}}
        <div class="flex items-center text-sm text-gray-600 gap-2">
            <a href="{{ route('branch.products.index', $branchCode) }}"
               class="hover:text-emerald-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <span>/</span>
            <span class="font-medium text-gray-900">{{ $product->name }}</span>
        </div>

        {{-- HEADER PRODUK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-2xl font-bold text-gray-900">
                {{ $product->name }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <div class="text-xs font-medium text-gray-500">Kode Produk</div>
                    <div class="text-gray-900 font-semibold">{{ $product->code }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-gray-500">Kategori</div>
                    <div class="text-gray-900 font-semibold">
                        {{ $product->category->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-gray-500">Harga Dasar</div>
                    <div class="text-gray-900 font-semibold">
                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-gray-500">Status</div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full
                        {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        <span class="w-2 h-2 rounded-full
                            {{ $product->is_active ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                        {{ $product->is_active ? 'Aktif' : 'Non Aktif' }}
                    </span>
                </div>

            </div>
        </div>

        {{-- BOM SECTION --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Bill of Materials</h3>
                <p class="text-sm text-gray-600">Komposisi bahan per unit produk</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Satuan</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($bomItems as $b)
                            <tr>
                                <td class="px-6 py-3 text-gray-900 font-medium">
                                    {{ $b->item->name }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        {{ $b->item->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-mono text-sm text-gray-900">
                                    {{ rtrim(rtrim(number_format($b->qty_per_unit, 3, ',', '.'), '0'), ',') }}
                                </td>
                                <td class="px-6 py-3 text-gray-700">
                                    {{ $b->item->satuan->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-500">
                                    Produk ini belum memiliki BOM.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-app-layout>
