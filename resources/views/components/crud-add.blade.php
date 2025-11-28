@props([
    'resource',
    'companyCode',
    'permissionPrefix',
])

@php
    $companyCode = strtolower($companyCode);
@endphp

@if (\App\Support\Access::can("$permissionPrefix.create"))
    <a href="{{ route("$resource.create", $companyCode) }}"
       class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm">
        + Tambah {{ ucfirst($resource) }}
    </a>
@endif
