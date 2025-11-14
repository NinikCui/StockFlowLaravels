<x-app-layout>
    @php
        $companyCode = session('role.company.code');
        $branchCode  = session('role.branch.code');
    @endphp

    <main class="min-h-screen px-6 py-8 bg-gray-50">

        {{-- HEADER --}}
        <header class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Cabang Restoran</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Kelola dan pantau seluruh cabang dalam perusahaan Anda.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-sm shadow-sm">
                        🔄 Segarkan
                    </a>

                    <a href="/{{ $companyCode }}/cabang/tambah"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-sm shadow-sm">
                        ➕ Tambah Cabang
                    </a>
                </div>

            </div>

            {{-- FILTER BAR --}}
            <form method="GET"
                  class="mt-6 rounded-2xl border bg-white shadow-sm px-5 py-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">

                <div class="flex-1 flex flex-col sm:flex-row gap-3">

                    {{-- Search --}}
                    <div class="flex-1 relative">
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama, kode, atau kota…"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                    </div>

                    {{-- Status --}}
                    <select name="status"
                        class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-emerald-200 focus:border-emerald-500">
                        <option value="ALL">Semua Status</option>
                        <option value="ACTIVE"   {{ request('status')=='ACTIVE' ? 'selected':'' }}>Aktif</option>
                        <option value="INACTIVE" {{ request('status')=='INACTIVE' ? 'selected':'' }}>Nonaktif</option>
                    </select>

                    {{-- Sort --}}
                    <select name="sort"
                        class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-emerald-200 focus:border-emerald-500">
                        <option value="created_at" {{ request('sort')=='created_at' ? 'selected':'' }}>Terbaru</option>
                        <option value="name"       {{ request('sort')=='name' ? 'selected':'' }}>Nama</option>
                        <option value="code"       {{ request('sort')=='code' ? 'selected':'' }}>Kode</option>
                        <option value="city"       {{ request('sort')=='city' ? 'selected':'' }}>Kota</option>
                    </select>

                </div>

                <button class="hidden"></button>

            </form>
        </header>

        {{-- EMPTY STATE --}}
        @if($cabang->count() == 0)
            <div class="border border-dashed p-10 rounded-2xl bg-white text-center shadow-sm">
                <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-gray-100 flex items-center justify-center text-2xl">
                    📦
                </div>

                <h3 class="text-lg font-semibold text-gray-800">Belum ada data cabang</h3>
                <p class="text-sm text-gray-500 mt-1">Tambahkan cabang atau muat ulang data.</p>

                <div class="mt-5 flex justify-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm">
                        Segarkan
                    </a>

                    <a href="/{{ $companyCode }}/cabang/tambah"
                       class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm">
                        + Tambah Cabang
                    </a>
                </div>
            </div>
        @endif


        {{-- LIST GRID --}}
        @if($cabang->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($cabang as $b)
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all p-6">

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $b->code }} — {{ $b->name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $b->city }}</p>
                        </div>

                        {{-- STATUS BADGE --}}
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $b->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                            ● {{ $b->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($b->address)
                        <p class="mt-3 text-sm text-gray-600 leading-relaxed">{{ $b->address }}</p>
                    @endif

                    <div class="mt-5 grid grid-cols-2 gap-y-2 text-xs text-gray-600">
                        <div class="font-medium">📞 Telepon</div>
                        <div>{{ $b->phone ?? '-' }}</div>

                        <div class="font-medium">🗓 Dibuat</div>
                        <div>{{ $b->created_at?->format('d M Y') }}</div>

                        <div class="font-medium">👤 Manager</div>
                        <div>{{ $b->manager_username ?? '-' }}</div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <a href="/{{ $companyCode }}/cabang/{{ $b->code }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 hover:bg-emerald-50 hover:border-emerald-300 text-gray-700 transition">
                            Detail
                        </a>
                    </div>

                </div>
                @endforeach

            </div>
        @endif

    </main>

</x-app-layout>
