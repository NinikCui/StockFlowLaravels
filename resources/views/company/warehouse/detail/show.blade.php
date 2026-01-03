<x-app-layout>

<main class="max-w-6xl mx-auto px-6 py-10"
      x-data="tabState()"
      x-init="init()">

    {{-- HEADER --}}
    <div class="mb-8">
        <div>
            {{ Breadcrumbs::render(
                'company.warehouse.detail',
                $companyCode,$warehouse

            ) }}
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Detail Penyimpanan</h1>
        <p class="text-gray-500 text-sm mt-1">Informasi lengkap mengenai penyimpanan & seluruh aktivitas stok.</p>
    </div>

    {{-- TAB MENU --}}
    <div class="flex gap-6 border-b mb-6 pb-1 text-sm font-medium">

        <button @click="changeTab('info')"
                :class="tab === 'info' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
                class="pb-2">
            Info Penyimpanan
        </button>

        <button @click="changeTab('stock')"
                :class="tab === 'stock' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
                class="pb-2">
            Stok
        </button>

        <button @click="changeTab('mutasi')"
                :class="tab === 'mutasi' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500'"
                class="pb-2">
            Mutasi Stok
        </button>

    </div>


    {{-- TAB PANEL: INFO --}}
    <div x-show="tab === 'info'" x-transition>
        @include('company.warehouse.detail.partials.warehouse-info')
    </div>

    {{-- TAB PANEL: STOCK --}}
    <div x-show="tab === 'stock'" x-transition>
        @include('company.warehouse.detail.partials.warehouse-stock')
    </div>

    {{-- TAB PANEL: MUTASI --}}
    <div x-show="tab === 'mutasi'" x-transition>
        @include('company.warehouse.detail.partials.mutasi-gudang')
    </div>

</main>

</x-app-layout>


{{-- Alpine + URL Sync --}}
<script>
function tabState() {
    return {
        // default tab diambil dari URL
        tab: new URLSearchParams(window.location.search).get('tab') || 'info',

        init() {
            // Jika filter aktif, paksa tab = mutasi
            const url = new URL(window.location.href);
            if (url.searchParams.get('from') || url.searchParams.get('to') || url.searchParams.get('issue')) {
                this.changeTab('mutasi', false);
            }
        },

        changeTab(value, updateUrl = true) {
            this.tab = value;

            if (updateUrl) {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', value);
                window.history.replaceState({}, '', url);
            }
        }
    }
}
</script>
