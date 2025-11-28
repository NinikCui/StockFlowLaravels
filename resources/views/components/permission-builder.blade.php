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

<div class="space-y-4">

    @foreach ($permissions as $resource => $perms)
    <div class="border border-gray-200 bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition"
        data-permission-group="{{ $resource }}">

        {{-- HEADER --}}
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 uppercase tracking-wide">
                {{ ucfirst($resource) }}
            </h3>
        </div>

        {{-- TABLE --}}
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-max">
                <thead>
                    <tr class="border-b border-gray-200">
                        @foreach ($allActions as $action)
                            <th class="pb-3 text-sm font-medium text-gray-600 text-center capitalize w-32">
                                {{ $action }}
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

                            <td class="py-3 text-center">
                                @if ($hasPerm)
                                    <input type="checkbox"
                                           name="permissions[]"
                                           class="perm-{{ $action }} h-5 w-5 text-emerald-600 border-gray-300 rounded"
                                           value="{{ $code }}"
                                           {{ in_array($code, $selected) ? 'checked' : '' }}>
                                @else
                                    <span class="text-gray-400 text-xs">–</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
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
