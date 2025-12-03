<x-app-layout>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB --}}
        <a href="{{ route('products.index', $companyCode) }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke daftar produk
        </a>

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-4">

            {{-- LEFT HEADER --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $product->name }}
                </h1>

                <p class="text-gray-600 mt-1">
                    Kode:
                    <span class="font-medium">{{ $product->code }}</span>
                </p>

                {{-- STATUS BADGE --}}
                <div class="mt-2">
                    @if ($product->is_active)
                        <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">
                            Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-600">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            {{-- RIGHT HEADER (CRUD BUTTONS) --}}
            <div class="flex-shrink-0">
                <x-crud 
                    resource="products"
                    :model="$product"
                    :companyCode="$companyCode"
                    permissionPrefix="item"
                    keyField="id"
                />
            </div>
        </div>

        {{-- PRODUCT INFO CARD --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4">Informasi Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <p class="text-sm text-gray-500">Nama Produk</p>
                    <p class="font-medium">{{ $product->name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Harga Dasar (HPP)</p>
                    <p class="font-medium">
                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Kode Produk</p>
                    <p class="font-medium">{{ $product->code }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                </div>

            </div>
        </div>

        {{-- BOM SECTION --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Bill of Materials (Bahan)</h2>

                <a href="{{ route('products.bom.index', [$companyCode, $product->id]) }}"
                   class="px-4 py-2 text-sm font-medium bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                    Kelola BOM
                </a>
            </div>

            @if ($bomItems->isEmpty())
                <p class="text-sm text-gray-500 italic">Belum ada bahan untuk BOM.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($bomItems as $b)
                        <li class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex justify-between">
                            <span>{{ $b->item->name }}</span>
                            <span class="font-medium">{{ $b->qty_per_unit }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>

    </div>

</x-app-layout>
