<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10" 
      x-data="{ tab: '{{ request("tab", "overview") }}' }">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $warehouse->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Detail lengkap dan informasi operasional warehouse.
            </p>
        </div>

        <a href="{{ route('warehouse.index', $companyCode) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    </div>

    {{-- TAB MENU --}}
    <div class="flex gap-8 border-b mb-6 pb-1">
        <button class="pb-2 text-sm font-semibold"
            :class="tab === 'overview' 
                ? 'text-emerald-600 border-b-2 border-emerald-600' 
                : 'text-gray-500'"
            @click="tab = 'overview'">
            Overview
        </button>

        <button class="pb-2 text-sm font-semibold"
            :class="tab === 'stock' 
                ? 'text-emerald-600 border-b-2 border-emerald-600' 
                : 'text-gray-500'"
            @click="tab = 'stock'">
            Stok Barang
        </button>

        <button class="pb-2 text-sm font-semibold"
            :class="tab === 'mutations' 
                ? 'text-emerald-600 border-b-2 border-emerald-600' 
                : 'text-gray-500'"
            @click="tab = 'mutations'">
            Mutasi
        </button>

        <button class="pb-2 text-sm font-semibold"
            :class="tab === 'activity' 
                ? 'text-emerald-600 border-b-2 border-emerald-600' 
                : 'text-gray-500'"
            @click="tab = 'activity'">
            Aktivitas
        </button>
    </div>

    {{-- TAB CONTENT --}}
    {{-- OVERVIEW --}}
    <div x-show="tab === 'overview'">
        <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-6">

            <h2 class="text-lg font-semibold">Informasi Warehouse</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                
                <div>
                    <div class="text-gray-500">Nama Warehouse</div>
                    <div class="font-semibold text-gray-900">
                        {{ $warehouse->name }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Kode</div>
                    <div class="font-semibold text-gray-900">
                        {{ $warehouse->code }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Cabang Restoran</div>
                    <div class="font-semibold text-gray-900">
                        {{ $warehouse->cabangResto->name }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Tipe Warehouse</div>
                    <div class="font-semibold text-gray-900">
                        {{ $warehouse->type->name ?? '-' }}
                    </div>
                </div>

              


            </div>

        </div>
    </div>

    {{-- STOK --}}
    <div x-show="tab === 'stock'">
        <div class="bg-white p-6 rounded-2xl border shadow-sm text-sm">
            <h2 class="text-lg font-semibold mb-4">Stok Barang</h2>

            <p class="text-gray-500">
                (Nanti di sini ada tabel stok + tombol tambah stok + update stok)
            </p>
        </div>
    </div>

    {{-- MUTATIONS --}}
    <div x-show="tab === 'mutations'">
        <div class="bg-white p-6 rounded-2xl border shadow-sm text-sm">
            <h2 class="text-lg font-semibold mb-4">Mutasi Barang</h2>

            <p class="text-gray-500">
                (Nanti di sini ada riwayat mutasi + create mutasi + approve mutasi)
            </p>
        </div>
    </div>

    {{-- ACTIVITY --}}
    <div x-show="tab === 'activity'">
        <div class="bg-white p-6 rounded-2xl border shadow-sm text-sm">
            <h2 class="text-lg font-semibold mb-4">Aktivitas / Log Gudang</h2>

            <p class="text-gray-500">
                (Nanti di sini ada semua aktivitas warehouse)
            </p>
        </div>
    </div>

</main>
</x-app-layout>
