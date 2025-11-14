<x-app-layout>
<main class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Roles</h1>
            <p class="text-gray-500 mt-1">Kelola role karyawan, baik universal maupun khusus cabang.</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('roles.index', $companyCode) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border bg-white text-gray-700 hover:bg-gray-50 shadow">
                🔄 <span>Refresh</span>
            </a>

            <a href="{{ route('roles.create', $companyCode) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                ➕ <span>Tambah Role</span>
            </a>
        </div>
    </div>


    {{-- FILTER BAR --}}
    <div class="bg-white border border-black-100 rounded-2xl shadow-sm p-5 mb-8">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            {{-- SEARCH --}}
            <div>
                <label class="text-sm text-gray-600 font-medium">Pencarian</label>
                <div class="relative mt-1">
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama role atau kode..."
                        class="w-full rounded-xl pl-10 py-2.5 bg-gray-50 border border-gray-200 text-sm shadow-sm focus:bg-white focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                    />
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>

                    @if(request('q'))
                    <a href="{{ route('roles.index', $companyCode) }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">✕</a>
                    @endif
                </div>
            </div>

            {{-- FILTER SCOPE --}}
            <div>
                <label class="text-sm text-gray-600 font-medium">Filter Scope</label>
                <select name="filterCabang"
                    class="w-full mt-1 rounded-xl py-2.5 px-3 text-sm bg-gray-50 border border-gray-200 shadow-sm focus:bg-white focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                    <option value="all">Semua Scope</option>
                    <option value="universal" {{ request('filterCabang')=='universal'?'selected':'' }}>Universal</option>

                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" {{ request('filterCabang')==$c->id?'selected':'' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SORT --}}
            <div>
                <label class="text-sm text-gray-600 font-medium">Urutkan</label>
                <select name="sortKey"
                    class="w-full mt-1 rounded-xl py-2.5 px-3 text-sm bg-gray-50 border border-gray-200 shadow-sm focus:bg-white focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                    <option value="name" {{ $sortKey=='name'?'selected':'' }}>Nama (A–Z)</option>
                    <option value="code" {{ $sortKey=='code'?'selected':'' }}>Kode (A–Z)</option>
                </select>
                <input type="hidden" name="sortDir" value="{{ $sortDir }}">
            </div>

            {{-- BUTTONS --}}
            <div class="sm:col-span-3 flex justify-end gap-3 mt-2">
                <a href="{{ route('roles.index', $companyCode) }}"
                    class="px-4 py-2 rounded-xl border border-gray-300 bg-white text-sm hover:bg-gray-50">
                    Reset Filter
                </a>

                <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700 shadow">
                    Terapkan
                </button>
            </div>
        </form>
    </div>


    {{-- COUNT --}}
    <div class="text-xs text-gray-500 mb-2">
        <span class="font-semibold">{{ $roles->count() }}</span> role ditemukan
    </div>


    {{-- TABLE --}}
    <div class="overflow-hidden bg-white border border-black-100 rounded-2xl shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium">Kode</th>
                    <th class="px-6 py-3 font-medium">Scope</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($roles as $idx => $r)
                <tr class="{{ $idx%2==0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-emerald-50/40 transition">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $r->name }}</td>

                    <td class="px-6 py-3">
                        <span class="px-2 py-1 rounded-md bg-gray-100 font-mono text-gray-700 text-xs">
                            {{ $r->code }}
                        </span>
                    </td>

                    <td class="px-6 py-3">
                        @if(!$r->cabang_resto_id)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                ● Universal
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                ● {{ $r->cabangResto->name }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('roles.show', [$companyCode, $r->code]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700 hover:bg-gray-100 hover:text-emerald-700">
                            👁 Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">Tidak ada data role ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</main>
</x-app-layout>
