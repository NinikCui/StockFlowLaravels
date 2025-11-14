<x-app-layout>
<main class="mx-auto max-w-6xl px-6 py-10 min-h-screen bg-gray-50">

    {{-- HEADER BUTTON GROUP --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-3">

        <a href="/{{ $companyCode }}/pegawai/roles"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 
                  hover:bg-gray-100 shadow-sm transition text-sm">
            ← Kembali
        </a>

        <div class="flex gap-2">
            <a href="{{ request()->url() }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 
                      hover:bg-gray-100 shadow-sm transition text-sm">
                ⟳ Refresh
            </a>

            <a href="{{ route('roles.edit', [$companyCode, $role->code]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-300 bg-white text-blue-600 hover:bg-blue-50 shadow-sm transition text-sm">
                ✎ Edit
            </a>

            <button onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-rose-300 bg-white text-rose-600
                       hover:bg-rose-50 shadow-sm transition text-sm">
                🗑 Hapus
            </button>
        </div>

    </div>


    {{-- ROLE DETAIL CARD --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-md p-6 mb-10">

        <div class="flex flex-wrap justify-between items-start gap-6">

            {{-- Role name --}}
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                    {{ $role->name }}

                    @if ($role->cabangResto)
                        <span class="px-3 py-0.5 text-xs rounded-full bg-blue-100 border border-blue-200 text-blue-700 font-medium">
                            {{ $role->cabangResto->code }}
                        </span>
                    @else
                        <span class="px-3 py-0.5 text-xs rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 font-medium">
                            Universal
                        </span>
                    @endif
                </h1>

                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <span>Kode:</span>
                    <code class="px-2 py-1 rounded bg-gray-100 border text-gray-800 shadow-inner text-xs">
                        {{ $role->code }}
                    </code>
                </div>
            </div>

            {{-- Search --}}
            <div class="relative w-full sm:w-80">
                <input id="searchBox"
                       type="text"
                       placeholder="Cari permission..."
                       class="w-full px-10 py-2.5 text-sm rounded-xl border border-gray-300 bg-white shadow-sm 
                              focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>

                <button onclick="document.getElementById('searchBox').value=''; filterPermissions()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-500">
                    ✕
                </button>
            </div>
        </div>
    </div>


    {{-- PERMISSION GROUPS --}}
    <div id="permissionContainer" class="space-y-8">

        @foreach ($permissions as $group => $rows)

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            {{-- Group Header --}}
            <div class="flex items-center justify-between px-6 py-3 bg-gray-100 border-b border-gray-200">
                <span class="font-semibold text-gray-800 text-sm flex items-center gap-2">
                    🔐 {{ strtoupper($group) }}
                </span>
                <span class="text-xs text-gray-500">{{ count($rows) }} permission</span>
            </div>

            {{-- Permission List --}}
            <ul class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($rows as $p)
                <li class="permission-item flex items-start gap-3 px-4 py-3 rounded-xl border shadow-sm transition-all
                           {{ $p['isGranted'] 
                                ? 'bg-emerald-50/60 border-emerald-200 hover:bg-emerald-100' 
                                : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}"
                    data-keywords="{{ strtolower($p['code'].' '.$p['resource'].' '.$p['action']) }}">

                    <span class="text-purple-600 mt-0.5">✔</span>

                    <div class="min-w-0">
                        <div class="font-medium text-gray-900 truncate">
                            {{ strtoupper($p['resource']) }} — {{ $p['action'] }}
                        </div>

                        <div class="text-xs text-gray-500 truncate">
                            <code class="px-1 bg-white border rounded shadow-sm text-[10px]">
                                {{ $p['code'] }}
                            </code>
                        </div>
                    </div>
                </li>
                @endforeach

            </ul>

        </div>
        @endforeach

    </div>


    {{-- DELETE MODAL --}}
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">

            <h2 class="text-lg font-semibold text-gray-900 mb-2">Hapus Role?</h2>

            <p class="text-gray-600 mb-5">
                Apakah yakin ingin menghapus role <b>{{ $role->name }}</b>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <form method="POST" action="/{{ $companyCode }}/pegawai/roles/{{ $role->code }}">
                @csrf
                @method('DELETE')

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                        onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 
                            border border-gray-200 text-sm shadow-sm transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-500 text-red hover:bg-rose-600 
                            text-sm shadow-sm transition">
                        Hapus
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
