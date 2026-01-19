<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-7">

    {{-- HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Informasi Cabang</h2>
            <p class="text-sm text-gray-500 mt-1">
                Detail identitas dan pengelolaan cabang restoran
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- CABANG UTAMA --}}
            @if ($cabang->utama)
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                             bg-amber-100 text-amber-800 border border-amber-200">
                    ⭐ Cabang Utama
                </span>
            @endif

            {{-- STATUS --}}
            <span class="px-3 py-1 text-xs font-semibold rounded-full
                {{ $cabang->is_active
                    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                    : 'bg-red-100 text-red-700 border border-red-200' }}">
                ● {{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 text-sm">

        {{-- NAMA --}}
        <div>
            <p class="text-gray-500">Nama Cabang</p>
            <p class="mt-1 font-semibold text-gray-800">
                {{ $cabang->name }}
            </p>
        </div>

        {{-- KODE --}}
        <div>
            <p class="text-gray-500">Kode Cabang</p>
            <p class="mt-1 font-mono font-semibold text-gray-800">
                {{ $cabang->code }}
            </p>
        </div>

        {{-- KOTA --}}
        <div>
            <p class="text-gray-500">Kota</p>
            <p class="mt-1 font-semibold text-gray-800">
                {{ $cabang->city }}
            </p>
        </div>

        {{-- TELEPON --}}
        <div>
            <p class="text-gray-500">Telepon</p>
            <p class="mt-1 font-semibold text-gray-800">
                {{ $cabang->phone ?? '-' }}
            </p>
        </div>

        {{-- ALAMAT --}}
        <div class="sm:col-span-2">
            <p class="text-gray-500">Alamat</p>
            <p class="mt-1 font-semibold text-gray-800 leading-relaxed">
                {{ $cabang->address ?? '-' }}
            </p>
        </div>

        {{-- MANAGER --}}
        <div class="sm:col-span-2">
            <p class="text-gray-500">Manager Cabang</p>

            @if ($cabang->manager)
                <div class="mt-2 inline-flex items-center gap-3 px-3 py-2 rounded-xl
                            bg-gray-50 border border-gray-200">
                    <span class="px-2 py-0.5 text-xs rounded-md
                                 bg-emerald-100 text-emerald-700 font-semibold">
                        MANAGER
                    </span>

                    <span class="font-semibold text-gray-800">
                        {{ $cabang->manager->username }}
                    </span>

                    <span class="text-xs text-gray-500">
                        {{ $cabang->manager->email }}
                    </span>
                </div>
            @else
                <p class="mt-1 text-sm text-gray-400">
                    Belum ditentukan
                </p>
            @endif
        </div>

    </div>

    {{-- ACTION --}}
    <div class="mt-10 flex justify-end border-t pt-6">
        <x-crud 
            resource="cabang"
            :model="$cabang"
            :companyCode="$companyCode"
            permissionPrefix="branch"
        />
    </div>

</div>
