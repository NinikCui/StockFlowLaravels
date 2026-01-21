<x-app-layout>
<main class="min-h-screen max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Produk</h1>
        <p class="text-gray-500 mt-1">Kelola Item, Kategori, dan Satuan dalam satu halaman.</p>
    </div>


    {{-- ============================ --}}
    {{--   TAB STATE (Alpine.js)     --}}
    {{-- ============================ --}}
    @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
        <div class="font-semibold text-red-700 mb-1">
            Terjadi kesalahan
        </div>
        <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('success'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="font-semibold text-emerald-700 mb-1">
            Berhasil
        </div>
        <p class="text-sm text-emerald-600">
            {{ session('success') }}
        </p>
    </div>
@endif

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
             @include('company.items.partials.unit-conversion')
        </div>

    </div>

</main>
</x-app-layout>
