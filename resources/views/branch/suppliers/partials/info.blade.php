@php
    
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

    {{-- HEADER --}}
    <div class="flex items-start justify-between">
        <h2 class="text-xl font-bold text-gray-900">Informasi Supplier</h2>

        <span class="px-3 py-1 text-xs font-semibold rounded-full
            {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
            {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

    {{-- GRID INFO --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
        <div class="space-y-1">
            <p class="text-gray-500">Nama Supplier</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->name ?? '-' }}
            </p>
        </div>
        <div class="space-y-1">
            <p class="text-gray-500">Nama Kontak Supplier</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->contact_name ?? '-' }}
            </p>
        </div>
        <div class="space-y-1">
            <p class="text-gray-500">Telepon Supplier</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->phone ?? '-' }}
            </p>
        </div>
        <div class="space-y-1">
            <p class="text-gray-500">Email Supplier</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->email ?? '-' }}
            </p>
        </div>
        <div class="space-y-1">
            <p class="text-gray-500">Kota</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->city ?? '-' }}
            </p>
        </div>

        <div class="sm:col-span-2 space-y-1">
            <p class="text-gray-500">Alamat</p>
            <p class="font-semibold text-gray-800 leading-relaxed">
                {{ $supplier->address ?? '-' }}
            </p>
        </div>


        @if ($supplier->notes)
            <div class="space-y-1">
                <p class="text-gray-500">Catatan</p>
                <p class="font-semibold text-gray-800 leading-relaxed">
                    {{ $supplier->notes }}
                </p>
            </div>
        @endif
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="mt-8 flex justify-end">
        
    </div>

</div>
