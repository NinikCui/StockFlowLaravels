@props(['permissions' => [], 'selected' => []])

@php
    // Ambil daftar semua action unik di seluruh permission (create/update/delete/transfer/approve/etc)
    $allActions = collect($permissions)
        ->flatten(1)
        ->pluck('action')
        ->unique()
        ->sort()
        ->values()
        ->toArray();
@endphp

<div class="space-y-6">

    @foreach ($permissions as $resource => $perms)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-200"
        data-permission-group="{{ $resource }}">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    {{ ucfirst($resource) }}
                </h3>
                <span class="text-xs font-medium text-gray-500 bg-white px-3 py-1 rounded-full">
                    {{ count($perms) }} permissions
                </span>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            @foreach ($allActions as $action)
                                <th class="pb-4 px-3 text-sm font-semibold text-gray-700 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="capitalize">{{ $action }}</span>
                                        @php
                                            $icons = [
                                                'view' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>',
                                                'create' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
                                                'update' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                                                'delete' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
                                                'approve' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                                'transfer' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>',
                                            ];
                                        @endphp
                                        @if(isset($icons[$action]))
                                            <div class="text-gray-400">{!! $icons[$action] !!}</div>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            @foreach ($allActions as $action)
                                @php
                                    $code = "$resource.$action";
                                    $hasPerm = collect($perms)->firstWhere('action', $action);
                                @endphp

                                <td class="py-4 px-3 text-center">
                                    @if ($hasPerm)
                                        <div class="flex justify-center">
                                            <label class="relative inline-flex items-center cursor-pointer group">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       class="perm-{{ $action }} sr-only peer"
                                                       value="{{ $code }}"
                                                       {{ in_array($code, $selected) ? 'checked' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 hover:shadow-md transition-all"></div>
                                            </label>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100">
                                            <span class="text-gray-300 text-sm font-medium">–</span>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endforeach

</div>

{{-- AUTO RULE SCRIPT --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("[data-permission-group]").forEach(group => {

        const view = group.querySelector(".perm-view");

        // Jika action lain dicentang → view ON
        group.querySelectorAll("input[type='checkbox']").forEach(cb => {
            cb.addEventListener("change", () => {
                if (cb.classList.contains("perm-view")) return;
                if (cb.checked && view) view.checked = true;
            });
        });

        // Jika VIEW dimatikan → semua off
        if (view) {
            view.addEventListener("change", () => {
                if (!view.checked) {
                    group.querySelectorAll("input[type='checkbox']").forEach(cb => cb.checked = false);
                }
            });
        }
    });

});
</script>