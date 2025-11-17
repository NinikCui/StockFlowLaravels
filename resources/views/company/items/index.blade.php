<x-app-layout>
<main class="min-h-screen max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Produk</h1>
        <p class="text-gray-500 mt-1">Kelola Item, Kategori, dan Satuan dalam satu halaman.</p>
    </div>


    {{-- ============================ --}}
    {{--   TAB STATE (Alpine.js)     --}}
    {{-- ============================ --}}
    <div 
        x-data="{ 
            tab: @js(session('activeTab') ?? 'item')
        }"
    >

        <div class="flex gap-6 border-b mb-6 pb-1 text-sm font-medium">

            <button class="pb-2"
                :class="tab === 'item' 
                    ? 'text-emerald-600 border-b-2 border-emerald-600' 
                    : 'text-gray-500'"
                @click="tab = 'item'">
                Items
            </button>

            <button class="pb-2"
                :class="tab === 'kategori' 
                    ? 'text-emerald-600 border-b-2 border-emerald-600' 
                    : 'text-gray-500'"
                @click="tab = 'kategori'">
                Categories
            </button>

            <button class="pb-2"
                :class="tab === 'satuan' 
                    ? 'text-emerald-600 border-b-2 border-emerald-600' 
                    : 'text-gray-500'"
                @click="tab = 'satuan'">
                Units
            </button>

        </div>



        <div x-show="tab=='item'" x-transition>
            @include('company.items.partials.item')
        </div>



        <div x-show="tab=='kategori'" x-transition>
            @include('company.items.partials.kategori')

        </div>


        <div x-show="tab=='satuan'" x-transition>
            @include('company.items.partials.satuan')
        </div>

    </div>

</main>
</x-app-layout>
