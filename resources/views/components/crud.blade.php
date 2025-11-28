@props([
    'resource',
    'model',
    'companyCode',
    'permissionPrefix',
    'keyField' => 'code',
])

@php
    $companyCode = strtolower($companyCode);
    $param = $model->{$keyField};

    $showRoute   = "$resource.show";
    $editRoute   = "$resource.edit";
    $deleteRoute = "$resource.destroy";
@endphp

<div class="flex items-center gap-3">

    @if (\App\Support\Access::can("$permissionPrefix.update"))
        <a href="{{ route($editRoute, [$companyCode, $param]) }}"
           class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-medium shadow hover:bg-amber-600 transition">
            ✎ Edit
        </a>
    @endif

    @if (\App\Support\Access::can("$permissionPrefix.delete"))
        <form method="POST"
              action="{{ route($deleteRoute, [$companyCode, $param]) }}"
              onsubmit="return confirm('Hapus data ini?')">

            @csrf
            @method('DELETE')

            <button class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-medium shadow hover:bg-red-700 transition">
                🗑 Hapus
            </button>
        </form>
    @endif

</div>
