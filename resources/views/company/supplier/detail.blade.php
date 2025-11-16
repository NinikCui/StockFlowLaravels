@php
    $tab = request()->query('tab', 'info');
@endphp

<x-app-layout>
<main class="px-6 py-10 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Detail Supplier</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap pemasok.</p>
        </div>

        <a href="/{{ $companyCode }}/supplier"
            class="text-sm text-gray-600 hover:text-gray-900">
            ← Kembali
        </a>
    </div>

    {{-- TAB HEADER --}}
    <x-tab-header
        :tabs="[
            'info' => 'Informasi Supplier',
            'item' => 'Item yang Disuplai',
            'score' => 'Performance Supplier',
        ]"
        :active="$tab"
    />

    {{-- CONTENT --}}
    @if ($tab == 'info')
        @include('company.supplier.partials.info')
    @elseif ($tab == 'item')
        @include('company.supplier.partials.item')
    @elseif ($tab == 'score')
        @include('company.supplier.partials.performance')
    @endif

</main>
</x-app-layout>
