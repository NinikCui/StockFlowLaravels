<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">

    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-gray-900">Informasi Gudang</h2>

      
        <x-crud 
            resource="warehouse"
            :model="$warehouse"
            :companyCode="$companyCode"
            permissionPrefix="warehouse"
            keyField="id"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

        {{-- Nama Gudang --}}
        <div class="flex flex-col gap-1">
            <p class="text-gray-500">Nama Gudang</p>
            <p class="font-semibold text-gray-800">{{ $warehouse->name }}</p>
        </div>

        {{-- Kode Gudang --}}
        <div class="flex flex-col gap-1">
            <p class="text-gray-500">Kode</p>
            <p>
                <span class="px-2 py-1 bg-gray-100 border border-gray-300 rounded-md 
                             text-gray-800 text-xs font-semibold">
                    {{ $warehouse->code }}
                </span>
            </p>
        </div>

        {{-- Tipe Gudang --}}
        <div class="flex flex-col gap-1">
            <p class="text-gray-500">Tipe Gudang</p>
            <p class="font-semibold text-gray-800">
                {{ $warehouse->type->name ?? '-' }}
            </p>
        </div>

        {{-- Cabang --}}
        <div class="flex flex-col gap-1">
            <p class="text-gray-500">Cabang</p>
            <p class="font-semibold text-gray-800">
                {{ $warehouse->cabangResto->name ?? '-' }}
            </p>
        </div>

        

    </div>

</div>
