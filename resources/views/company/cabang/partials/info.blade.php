<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

    <div class="flex items-start justify-between">
        <h2 class="text-xl font-bold text-gray-900">Informasi Cabang</h2>

        <span class="px-3 py-1 text-xs font-semibold rounded-full
            {{ $cabang->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
            {{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">

        <div class="space-y-1">
            <p class="text-gray-500">Nama Cabang</p>
            <p class="font-semibold text-gray-800">{{ $cabang->name }}</p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-500">Kode Cabang</p>
            <p class="font-semibold text-gray-800">{{ $cabang->code }}</p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-500">Kota</p>
            <p class="font-semibold text-gray-800">{{ $cabang->city }}</p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-500">Telepon</p>
            <p class="font-semibold text-gray-800">{{ $cabang->phone ?? '-' }}</p>
        </div>

        <div class="sm:col-span-2 space-y-1">
            <p class="text-gray-500">Alamat</p>
            <p class="font-semibold text-gray-800 leading-relaxed">{{ $cabang->address }}</p>
        </div>

        @if ($cabang->manager)
            <div class="sm:col-span-2 space-y-1">
                <p class="text-gray-500">Manager Cabang</p>
                <span class="inline-flex items-center gap-2 font-semibold text-gray-800">
                    <span class="px-2 py-1 text-xs rounded-lg bg-emerald-100 text-emerald-700">
                        MANAGER
                    </span>
                    {{ $cabang->manager->username }}
                    <span class="text-gray-400 text-xs">({{ $cabang->manager->email }})</span>
                </span>
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('cabang.edit', [$companyCode, $cabang->code]) }}"
           class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium shadow hover:bg-emerald-700">
            ✎ Edit Cabang
        </a>
    </div>
</div>
