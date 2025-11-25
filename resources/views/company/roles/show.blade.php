<x-app-layout>
<main class="mx-auto max-w-7xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    {{-- HEADER SECTION --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

            {{-- Left: Title & Back Button --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('pegawai.index', $companyCode) }}?tab=roles"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
                    ←
                </a>
                
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Role</h1>
                    <p class="text-sm text-gray-600 mt-1">Lihat informasi dan permission dari role ini</p>
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex flex-wrap gap-2">

                {{-- Refresh --}}
                <a href="{{ request()->url() }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 
                          hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200 text-sm font-medium">
                    <span>🔄</span>
                    <span>Refresh</span>
                </a>

                {{-- Edit --}}
                <a href="{{ route('roles.edit', [$companyCode, $role->code]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-blue-200 text-blue-700 
                           bg-blue-50 hover:bg-blue-100 hover:border-blue-300 shadow-sm transition-all duration-200 text-sm font-semibold">
                    <span>✎</span>
                    <span>Edit</span>
                </a>

                {{-- Delete --}}
                <button onclick="document.getElementById('delete-modal').classList.remove('hidden'); document.getElementById('delete-modal').classList.add('flex')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 text-red-700 
                           bg-red-50 hover:bg-red-100 hover:border-red-300 shadow-sm transition-all duration-200 text-sm font-semibold">
                    <span>🗑</span>
                    <span>Hapus</span>
                </button>
            </div>

        </div>
    </div>


    {{-- ROLE INFO CARD --}}
    <div class="rounded-2xl bg-white border border-gray-200 shadow-lg p-8 mb-8">

        <div class="flex flex-col lg:flex-row justify-between items-start gap-6">

            {{-- Left: Role Info --}}
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-4">
                    {{-- Icon Badge --}}
                    <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-3xl shadow-lg">
                        🛡️
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-3xl font-bold text-gray-900">{{ $role->name }}</h2>

                            @if ($role->cabangResto)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-full bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 text-blue-700 font-semibold">
                                    <span>📍</span>
                                    <span>{{ $role->cabangResto->code }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-full bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 text-emerald-700 font-semibold">
                                    <span>🌐</span>
                                    <span>Universal</span>
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-500">Kode:</span>
                            <code class="px-3 py-1.5 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 text-sm font-mono font-semibold">
                                {{ $role->code }}
                            </code>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-4 mt-6">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                        <span class="text-2xl">✔️</span>
                        <div>
                            <p class="text-xs text-emerald-600 font-medium">Total Permissions</p>
                            <p class="text-lg font-bold text-emerald-700">{{ collect($permissions)->flatten(1)->count() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                        <span class="text-2xl">📦</span>
                        <div>
                            <p class="text-xs text-blue-600 font-medium">Groups</p>
                            <p class="text-lg font-bold text-blue-700">{{ count($permissions) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Search Box --}}
            <div class="w-full lg:w-96">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Permission</label>
                <div class="relative">
                    <input id="searchBox"
                           type="text"
                           placeholder="Ketik untuk mencari..."
                           class="w-full pl-11 pr-10 py-3 text-sm rounded-xl border border-gray-200 bg-white shadow-sm 
                                  focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all duration-200">
                    <span class="absolute left-4 top-3.5 text-gray-400 text-lg">🔍</span>

                    <button onclick="document.getElementById('searchBox').value=''; filterPermissions()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-all">
                        ✕
                    </button>
                </div>
            </div>

        </div>
    </div>


    {{-- PERMISSION GROUPS --}}
    <div id="permissionContainer" class="space-y-6">

        @foreach ($permissions as $group => $rows)

        {{-- Group Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">

            {{-- Group Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-bold">
                        {{ substr($group, 0, 1) }}
                    </div>
                    <span class="font-bold text-gray-900 text-base">
                        {{ strtoupper($group) }}
                    </span>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200">
                    {{ count($rows) }} permissions
                </span>
            </div>

            {{-- Permission Items Grid --}}
            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($rows as $p)
                <div class="permission-item group flex items-start gap-3 px-4 py-4 rounded-xl border transition-all duration-200
                           {{ $p['isGranted'] 
                                ? 'bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200 hover:shadow-md hover:border-emerald-300' 
                                : 'bg-gradient-to-br from-gray-50 to-gray-100 border-gray-200 hover:shadow-md hover:border-gray-300' }}"
                    data-keywords="{{ strtolower($p['code'].' '.$p['resource'].' '.$p['action']) }}">

                    {{-- Check Icon --}}
                    <div class="h-6 w-6 rounded-lg {{ $p['isGranted'] ? 'bg-emerald-500' : 'bg-gray-400' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-white text-sm font-bold">✓</span>
                    </div>

                    {{-- Permission Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-900 text-sm leading-tight mb-1.5">
                            {{ strtoupper($p['resource']) }}
                        </div>
                        <div class="text-xs text-gray-600 mb-2">
                            {{ ucfirst($p['action']) }}
                        </div>
                        <code class="inline-block text-xs text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded-md shadow-sm font-mono">
                            {{ $p['code'] }}
                        </code>
                    </div>
                </div>
                @endforeach

            </div>

        </div>
        @endforeach

    </div>



    {{-- DELETE MODAL --}}
    <div id="delete-modal" 
         class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm items-center justify-center z-50 p-4"
         onclick="if(event.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }">

        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full" onclick="event.stopPropagation()">

            {{-- Icon --}}
            <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5">
                <span class="text-4xl">⚠️</span>
            </div>

            {{-- Content --}}
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-3">Hapus Role?</h2>

            <p class="text-gray-600 text-center mb-8 leading-relaxed">
                Apakah Anda yakin ingin menghapus role <span class="font-semibold text-gray-900">{{ $role->name }}</span>?  
                <br><span class="text-red-600 font-semibold">Tindakan ini tidak dapat dibatalkan.</span>
            </p>

            <form method="POST" action="/{{ $companyCode }}/pegawai/roles/{{ $role->code }}">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">

                    {{-- Cancel --}}
                    <button type="button"
                        onclick="document.getElementById('delete-modal').classList.add('hidden'); document.getElementById('delete-modal').classList.remove('flex')"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 
                               border border-gray-200 text-sm font-semibold shadow-sm transition-all duration-200">
                        Batal
                    </button>

                    {{-- Delete --}}
                    <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 
                               text-sm font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                        Hapus Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>

</main>

<script>
document.getElementById('searchBox').addEventListener('input', filterPermissions);

function filterPermissions() {
    const val = document.getElementById('searchBox').value.toLowerCase();
    document.querySelectorAll('.permission-item').forEach(el => {
        const keywords = el.dataset.keywords;
        el.style.display = keywords.includes(val) ? "" : "none";
    });
}
</script>

</x-app-layout>