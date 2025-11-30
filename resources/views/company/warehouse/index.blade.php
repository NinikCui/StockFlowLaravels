<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10" x-data="{ tab: 'warehouses' }">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Warehouse</h1>
        
    </div>

    {{-- TAB MENU --}}
    <div class="flex gap-6 border-b mb-6 pb-1">
        <button class="pb-2 text-sm font-medium"
            :class="tab === 'warehouses' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
            @click="tab = 'warehouses'">
            List Warehouse
        </button>

        <button class="pb-2 text-sm font-medium"
            :class="tab === 'types' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
            @click="tab = 'types'">
            Warehouse Types
        </button>
    </div>

    {{-- TAB CONTENT --}}
    <div x-show="tab === 'warehouses'">
        @include('company.warehouse.partials.list')
    </div>

    <div x-show="tab === 'types'">
        @include('company.warehouse.partials.types')
    </div>

</main>
</x-app-layout>
