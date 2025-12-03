<x-app-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- PAGE HEADER --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Produk</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Daftar produk dan resep BOM.
                </p>
            </div>
            <x-crud-add 
                        resource="products"
                        :companyCode="$companyCode"
                        permissionPrefix="item"
                    />
        </div>

        {{-- ALERT MESSAGE --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

{{-- PRODUCT LIST as CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    @forelse ($products as $p)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex flex-col justify-between">

            {{-- HEADER --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $p->name }}
                    </h3>

                    {{-- STATUS --}}
                    @if ($p->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-600">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 mb-2">
                    Kode: <span class="font-medium">{{ $p->code }}</span>
                </p>

                <p class="text-sm text-gray-700 font-semibold">
                    Rp {{ number_format($p->base_price, 0, ',', '.') }}
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="mt-5 flex justify-between gap-2">
                {{-- ACTION BUTTONS --}}
<div class="mt-5 flex justify-end">
    <a href="{{ route('products.show', [$companyCode, $p->id]) }}"
       class="w-full text-center px-3 py-2 text-xs font-semibold bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
        Detail
    </a>
</div>
            </div>

        </div>
    @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            Belum ada produk.
        </div>
    @endforelse

</div>


    </div>

</x-app-layout>
