
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

    <div class="flex items-start justify-between">
        <h2 class="text-xl font-bold text-gray-900">Informasi Supplier</h2>

        <span class="px-3 py-1 text-xs font-semibold rounded-full
            {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
            {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

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

    {{-- Actions --}}
    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('supplier.edit', [$companyCode, $supplier->id]) }}"
           class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-sm shadow hover:bg-emerald-700">
            ✎ Edit Supplier
        </a>

        <form method="POST"
              action="{{ route('supplier.destroy', [$companyCode, $supplier->id]) }}"
              onsubmit="return confirm('Hapus supplier ini?')">
            @csrf @method('DELETE')
            <button class="px-5 py-2 rounded-xl bg-red-600 text-white text-sm shadow hover:bg-red-700">
                🗑 Hapus
            </button>
        </form>
    </div>
</div>
