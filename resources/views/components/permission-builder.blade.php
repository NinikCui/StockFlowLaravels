@props(['permissions' => [], 'selected' => []])

<div class="max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Kelola Permissions</h2>
                <p class="text-sm text-gray-500">Atur hak akses untuk setiap resource</p>
            </div>
            <div class="flex gap-3">
                <button type="button"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-sm font-semibold hover:from-emerald-600 hover:to-emerald-700 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2"
                    onclick="toggleAllGlobal(true)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Aktifkan Semua
                </button>

                <button type="button"
                    class="px-5 py-2.5 rounded-xl bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 flex items-center gap-2"
                    onclick="toggleAllGlobal(false)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Matikan Semua
                </button>
            </div>
        </div>
    </div>

    {{-- Permissions Grid --}}
    <div class="grid gap-6">
        @foreach ($permissions as $resource => $perms)
        @php
            $actions = collect($perms)->pluck('action')->unique()->sort()->values();
        @endphp
        
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300"
             data-permission-group="{{ $resource }}"
             data-resource="{{ $resource }}">

            {{-- Card Header --}}
            <div class="bg-gradient-to-br from-slate-50 via-gray-50 to-slate-50 px-8 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ ucfirst($resource) }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Resource permissions</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100">
                            {{ count($perms) }} permissions
                        </span>
                    </div>
                </div>
            </div>

            {{-- Permissions Table --}}
            <div class="p-8">
                <div class="overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-100">
                                @foreach ($actions as $action)
                                    <th class="pb-6 px-4 text-sm font-bold text-gray-700 text-center first:text-left">
                                        <div class="flex flex-col items-center gap-2">
                                            @php
                                                $icons = [
                                                    'view' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                                    'create' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>', 'color' => 'text-green-500', 'bg' => 'bg-green-50'],
                                                    'update' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50'],
                                                    'delete' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>', 'color' => 'text-red-500', 'bg' => 'bg-red-50'],
                                                    'approve' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                                    'transfer' => ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50'],
                                                ];
                                                $iconData = $icons[$action] ?? ['icon' => '', 'color' => 'text-gray-400', 'bg' => 'bg-gray-50'];
                                            @endphp
                                            <div class="w-10 h-10 rounded-xl {{ $iconData['bg'] }} flex items-center justify-center">
                                                <div class="{{ $iconData['color'] }}">{!! $iconData['icon'] !!}</div>
                                            </div>
                                            <span class="capitalize text-xs font-semibold">{{ $action }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @foreach ($actions as $action)
                                    @php
                                        $code = "$resource.$action";
                                        $hasPerm = collect($perms)->firstWhere('action', $action);
                                    @endphp

                                    <td class="py-6 px-4 text-center">
                                        @if ($hasPerm)
                                            <div class="flex justify-center">
                                                <label class="relative inline-flex items-center cursor-pointer group">
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           class="perm-{{ $action }} sr-only peer"
                                                           value="{{ $code }}"
                                                           {{ in_array($code, $selected) ? 'checked' : '' }}>
                                                    <div class="w-14 h-7 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-100 peer-checked:after:translate-x-7 rtl:peer-checked:after:-translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-emerald-600 shadow-sm hover:shadow-md transition-all duration-200"></div>
                                                </label>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 border border-gray-100">
                                                <span class="text-gray-300 text-sm font-medium">—</span>
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

function toggleAllGlobal(state) {
    document
        .querySelectorAll('[data-permission-group]')
        .forEach(group => {
            if (group.style.display === 'none') return;
            group.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = state;
            });
        });
}
</script>