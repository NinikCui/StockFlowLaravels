<x-app-layout>

<main class="max-w-6xl mx-auto px-6 py-10" x-data="{ tab: 'info' }">

    {{-- HEADER GUDANG --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detail Gudang</h1>
        <p class="text-gray-500 text-sm mt-1">Informasi lengkap gudang & stok.</p>
    </div>

    {{-- TAB MENU --}}
    <div class="flex gap-6 border-b mb-6 pb-1 text-sm font-medium">
        <button @click="tab='info'"
            :class="tab==='info' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
            class="pb-2">
            Info Gudang
        </button>

        <button @click="tab='stock'"
            :class="tab==='stock' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
            class="pb-2">
            Stok
        </button>

        <button @click="tab='mutasi'"
            :class="tab==='mutasi' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
            class="pb-2">
            Mutasi Stok
        </button>
    </div>

    {{-- PARTIALS --}}
    <div x-show="tab === 'info'" x-transition>
        @include('company.warehouse.detail.partials.warehouse-info')
    </div>

    <div x-show="tab === 'stock'" x-transition>
        @include('company.warehouse.detail.partials.warehouse-stock')
    </div>

    <div x-show="tab === 'mutasi'" x-transition>
        @include('company.warehouse.detail.partials.warehouse-movements')
    </div>

</main>
</x-app-layout>
