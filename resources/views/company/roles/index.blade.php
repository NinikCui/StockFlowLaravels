<x-app-layout>
<main class="mx-auto max-w-6xl p-6 min-h-screen">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Roles</h1>
            <p class="text-gray-500 mt-1">Kelola role karyawan, universal maupun khusus cabang.</p>
        </div>

        <div class="flex gap-2">
            {{-- Refresh --}}
            <a href="{{ route('roles.index', $companyCode) }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm hover:bg-gray-50 transition">
                <span class="text-gray-600">🔄</span> Muat Ulang
            </a>

            {{-- Tambah --}}
            <a href="{{ route('roles.create', $companyCode) }}"
                class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-emerald-700 transition shadow-sm">
                <span>➕</span> Tambah Role
            </a>
        </div>
    </div>

    <hr class="border-gray-200 mb-6" />

    {{-- FILTER BAR --}}
    <div class="bg-white border rounded-2xl shadow-sm p-4 mb-6 space-y-4">

        <form method="GET"
            class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">

            {{-- SEARCH --}}
            <div class="relative">
                <label class="text-sm text-gray-600 mb-1 block">Pencarian</label>
                <input
                    name="q"
                    value="{{ request('q') }}"
                    class="w-full rounded-xl border border-gray-200 pl-10 py-2 text-sm bg-gray-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition"
                    placeholder="Cari nama role atau kode..."
                />
                <span class="absolute left-3 top-9 transform text-gray-400">🔍</span>
                @if(request('q'))
                    <a href="{{ route('roles.index', $companyCode) }}"
                        class="absolute right-3 top-9 transform text-gray-400 text-xs">✕</a>
                @endif
            </div>

            {{-- FILTER CABANG --}}
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Filter Scope</label>
                <select name="filterCabang"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition">
                    <option value="all">Semua Scope</option>
                    <option value="universal" {{ request('filterCabang')=='universal'?'selected':'' }}>
                        Universal
                    </option>

                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" {{ request('filterCabang')==$c->id?'selected':'' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SORT --}}
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Urutkan</label>
                <select name="sortKey"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition">
                    <option value="name" {{ $sortKey=='name'?'selected':'' }}>Nama (A-Z)</option>
                    <option value="code" {{ $sortKey=='code'?'selected':'' }}>Kode (A-Z)</option>
                </select>

                <input type="hidden" name="sortDir" value="{{ $sortDir }}">
            </div>

            {{-- APPLY BUTTON --}}
            <div class="sm:col-span-3 flex justify-end gap-2">
                <a href="{{ route('roles.index', $companyCode) }}"
                    class="px-4 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50">
                    Clear Filters
                </a>

                <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700 shadow">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- COUNT --}}
    <div class="text-xs text-gray-500 mb-3">
        <span class="font-semibold">{{ $roles->count() }}</span> role ditemukan
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-6 py-3 cursor-pointer">
                        <a href="?sortKey=name&sortDir={{ $sortDir=='asc'?'desc':'asc' }}" class="flex items-center gap-1">
                            Nama
                            @if($sortKey=='name')
                                <span>{{ $sortDir=='asc'?'▲':'▼' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-6 py-3 cursor-pointer">
                        <a href="?sortKey=code&sortDir={{ $sortDir=='asc'?'desc':'asc' }}" class="flex items-center gap-1">
                            Kode
                            @if($sortKey=='code')
                                <span>{{ $sortDir=='asc'?'▲':'▼' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-6 py-3">Scope</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($roles as $idx => $r)
                <tr class="{{ $idx%2==0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-emerald-50/40 transition">
                    <td class="px-6 py-3 font-medium">{{ $r->name }}</td>

                    <td class="px-6 py-3">
                        <span class="rounded-md bg-gray-100 px-2 py-1 text-gray-700 font-mono">
                            {{ $r->code }}
                        </span>
                    </td>

                    <td class="px-6 py-3">
                        @if(!$r->cabang_resto_id)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                ● Universal
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                ● {{ $r->cabangResto->name }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('roles.show', [$companyCode, $r->code]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 hover:text-emerald-700 transition">
                            👁 Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr class="bg-white">
                    <td colspan="4" class="text-center py-8 text-gray-500">
                        Tidak ada data role ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</main>
</x-app-layout>
