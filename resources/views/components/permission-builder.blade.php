@props(['permissions' => [], 'selected' => []])

<div class="space-y-4">
    @foreach ($permissions as $resource => $actions)
        <div class="border border-gray-200 bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
            data-permission-group="{{ $resource }}">
            
            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-800 uppercase tracking-wide">
                    {{ $resource }}
                </h3>
            </div>

            {{-- TABLE --}}
            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-gray-200">
                            <th class="pb-3 text-sm font-medium text-gray-600 w-1/4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </div>
                            </th>
                            <th class="pb-3 text-sm font-medium text-gray-600 w-1/4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create
                                </div>
                            </th>
                            <th class="pb-3 text-sm font-medium text-gray-600 w-1/4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Update
                                </div>
                            </th>
                            <th class="pb-3 text-sm font-medium text-gray-600 w-1/4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{-- VIEW --}}
                            <td class="py-4 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer group">
                                    <input type="checkbox"
                                        class="perm-view h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-all"
                                        name="permissions[]"
                                        value="{{ $resource }}.view"
                                        {{ in_array("$resource.view", $selected) ? 'checked' : '' }}>
                                </label>
                            </td>

                            {{-- CREATE --}}
                            <td class="py-4 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer group">
                                    <input type="checkbox"
                                        class="perm-create h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-2 focus:ring-green-500 focus:ring-offset-0 cursor-pointer transition-all"
                                        name="permissions[]"
                                        value="{{ $resource }}.create"
                                        {{ in_array("$resource.create", $selected) ? 'checked' : '' }}>
                                </label>
                            </td>

                            {{-- UPDATE --}}
                            <td class="py-4 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer group">
                                    <input type="checkbox"
                                        class="perm-update h-5 w-5 text-yellow-600 rounded border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-0 cursor-pointer transition-all"
                                        name="permissions[]"
                                        value="{{ $resource }}.update"
                                        {{ in_array("$resource.update", $selected) ? 'checked' : '' }}>
                                </label>
                            </td>

                            {{-- DELETE --}}
                            <td class="py-4 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer group">
                                    <input type="checkbox"
                                        class="perm-delete h-5 w-5 text-red-600 rounded border-gray-300 focus:ring-2 focus:ring-red-500 focus:ring-offset-0 cursor-pointer transition-all"
                                        name="permissions[]"
                                        value="{{ $resource }}.delete"
                                        {{ in_array("$resource.delete", $selected) ? 'checked' : '' }}>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

{{-- =============================================
    AUTO RULE SCRIPT
============================================== --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-permission-group]").forEach(group => {
        const view    = group.querySelector(".perm-view");
        const creates = group.querySelectorAll(".perm-create");
        const updates = group.querySelectorAll(".perm-update");
        const deletes = group.querySelectorAll(".perm-delete");

        // RULE 1 → Jika create/update/delete dicentang → view otomatis ON
        [...creates, ...updates, ...deletes].forEach(input => {
            input.addEventListener("change", () => {
                if (input.checked) {
                    view.checked = true;
                }
            });
        });

        // RULE 2 → Jika VIEW di-uncheck → semua mati
        view.addEventListener("change", () => {
            if (!view.checked) {
                [...creates, ...updates, ...deletes].forEach(i => i.checked = false);
            }
        });
    });
});
</script>