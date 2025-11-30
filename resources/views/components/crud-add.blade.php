@props([
    'resource',         // ex: branch.stock
    'companyCode',
    'permissionPrefix', // ex: stock
])

@php
    $companyCode = strtolower($companyCode);

    // Ambil label paling belakang → 'stock'
    $segments = explode('.', $resource);
    $label = ucfirst(end($segments));

    // Generate nama route → 'branch.stock.create'
    $routeName = "$resource.create";
@endphp

@if (\App\Support\Access::can("$permissionPrefix.create"))
    <a href="{{ route($routeName, $companyCode) }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 group">

       <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M12 4v16m8-8H4"></path>
       </svg>

       <span>Tambah {{ $label }}</span>
    </a>
@endif
