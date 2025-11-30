@props([
    'resource',
    'model',
    'companyCode',
    'permissionPrefix',
    'keyField' => 'code',
    'routeParams' => null,
])

@php
    $companyCode = strtolower($companyCode);
    $param = $model->{$keyField};
    $params = $routeParams ?: [$companyCode, $param];
    $showRoute   = "$resource.show";
    $editRoute   = "$resource.edit";
    $deleteRoute = "$resource.destroy";
@endphp

<div class="flex items-center gap-2">

    @if (\App\Support\Access::can("$permissionPrefix.update"))
        <a href="{{ route($editRoute, $params) }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 focus:ring-4 focus:ring-amber-200 transition-all duration-150 group">
            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Edit</span>
        </a>
    @endif

    @if (\App\Support\Access::can("$permissionPrefix.delete"))
        <form method="POST"
              action="{{ route($deleteRoute, $params) }}"
              onsubmit="return confirm('⚠️ Apakah Anda yakin ingin menghapus data ini?\n\nData yang dihapus tidak dapat dikembalikan.')"
              class="inline-block">

            @csrf
            @method('DELETE')

            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 focus:ring-4 focus:ring-red-200 transition-all duration-150 group">
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Hapus</span>
            </button>
        </form>
    @endif

</div>