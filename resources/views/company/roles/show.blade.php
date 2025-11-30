<x-app-layout>
<main class="mx-auto max-w-7xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

            {{-- Back --}}
            <div class="flex items-center gap-4">
                <a href={{ route('roles.index', ['companyCode' => $companyCode]) }}
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm">
                    ←
                </a>

                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Role</h1>
                    <p class="text-sm text-gray-600 mt-1">Informasi lengkap role & permission</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ request()->url() }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border bg-white text-gray-700 shadow-sm">
                    🔄 Refresh
                </a>
            <x-crud 
                resource="roles"
                :model="$role"
                :companyCode="$companyCode"
                permissionPrefix="permission"
            />
                
            </div>
        </div>
    </div>

    {{-- ROLE HEADER CARD --}}
    <div class="rounded-2xl bg-white border border-gray-200 shadow-lg p-8 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-6">

            {{-- Role Info --}}
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-4">

                    <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-3xl shadow-lg">
                        🛡️
                    </div>

                    <div>
                        {{-- Visible identity → CODE --}}
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-3xl font-bold text-gray-900 tracking-wide">
                                {{ strtoupper($role->code) }}
                            </h2>

                            @if($role->cabangResto)
                                <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    📍 {{ $role->cabangResto->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    🌐 Universal
                                </span>
                            @endif
                        </div>

                        {{-- INTERNAL NAME (optional) --}}
                        <p class="text-xs text-gray-500 font-mono">
                            Internal name:
                            <span class="font-semibold text-gray-700">{{ $role->name }}</span>
                        </p>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-4 mt-6">
                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-xl border border-emerald-200">
                        <span class="text-2xl">✔️</span>
                        <div>
                            <p class="text-xs text-emerald-600 font-medium">Total Permissions</p>
                            <p class="text-lg font-bold text-emerald-700">
                                {{ collect($permissions)->flatten(1)->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-xl border border-blue-200">
                        <span class="text-2xl">📦</span>
                        <div>
                            <p class="text-xs text-blue-600 font-medium">Groups</p>
                            <p class="text-lg font-bold text-blue-700">{{ count($permissions) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search Box --}}
            <div class="w-full lg:w-96">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Permission</label>
                <div class="relative">
                    <input id="searchBox" type="text"
                           placeholder="Ketik permission..."
                           oninput="filterPermissions()"
                           class="w-full pl-11 pr-10 py-3 text-sm rounded-xl border border-gray-200 shadow-sm">

                    <span class="absolute left-4 top-3.5 text-gray-400 text-lg">🔍</span>

                    <button onclick="clearSearch()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500">
                        ✕
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PERMISSION GROUPS --}}
    <div id="permissionContainer" class="space-y-6">

        @foreach ($permissions as $group => $rows)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            {{-- Group Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500 text-white grid place-items-center font-bold">
                        {{ strtoupper(substr($group, 0, 1)) }}
                    </div>
                    <span class="font-bold text-gray-900">{{ strtoupper($group) }}</span>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs border border-emerald-200">
                    {{ count($rows) }} permissions
                </span>
            </div>

            {{-- Items --}}
            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($rows as $p)
                <div class="permission-item px-4 py-4 rounded-xl border bg-gray-50 hover:shadow-md transition"
                     data-keywords="{{ strtolower($p['code'].' '.$p['resource'].' '.$p['action']) }}">

                    <div class="flex items-start gap-3">

                        <div class="h-6 w-6 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-sm font-bold">
                            ✓
                        </div>

                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm mb-1">
                                {{ strtoupper($p['resource']) }}
                            </div>

                            <div class="text-xs text-gray-600 mb-2">
                                {{ ucfirst($p['action']) }}
                            </div>

                            <code class="text-xs text-gray-500 bg-white border px-2 py-1 rounded-md shadow-sm">
                                {{ $p['code'] }}
                            </code>
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>
        @endforeach

    </div>

    

</main>

<script>
function filterPermissions() {
    const val = searchBox.value.toLowerCase();
    document.querySelectorAll(".permission-item").forEach(el => {
        el.style.display = el.dataset.keywords.includes(val) ? "" : "none";
    });
}
function clearSearch() {
    searchBox.value = "";
    filterPermissions();
}
</script>

</x-app-layout>
