@php
    function row($label, $value, $full = false) {
        return '
            <div class="'.($full ? 'sm:col-span-2' : '').' space-y-1">
                <p class="text-gray-500">'.$label.'</p>
                <p class="font-semibold text-gray-800 leading-relaxed">'.($value ?? '-').'</p>
            </div>
        ';
    }
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

    {{-- INFO GRID --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
        {!! row('Nama Supplier', $supplier->name) !!}
        {!! row('PIC', $supplier->contact_name) !!}
        {!! row('Telepon', $supplier->phone) !!}
        {!! row('Email', $supplier->email) !!}
        {!! row('Kota', $supplier->city) !!}
        {!! row('Alamat', $supplier->address, true) !!}

        @if ($supplier->notes)
            {!! row('Catatan', $supplier->notes, true) !!}
        @endif
    </div>

    {{-- ACTIONS --}}
    <div class="mt-8 flex justify-end">
        <x-crud 
            resource="supplier"
            :model="$supplier"
            :companyCode="$companyCode"
            permissionPrefix="supplier"
            keyField="id"
        />
    </div>

</div>
