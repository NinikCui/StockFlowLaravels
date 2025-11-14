<x-app-layout>
    @php
        $companyCode = session('role.company.code');
        $branchCode  = session('role.branch.code');
    @endphp

    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <header class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">

                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                        Cabang Restoran
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Kelola seluruh cabang dalam perusahaan Anda.
                    </p>
                </div>

                {{-- HEADER BUTTONS --}}
                <div class="flex items-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white 
                              text-gray-700 hover:bg-gray-100 text-sm shadow-sm">
                        🔄 Segarkan
                    </a>

                    <a href="/{{ strtolower($companyCode) }}/cabang/tambah"
                       class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-sm shadow">
                        + Tambah Cabang  
                    </a>
                </div>

            </div>

            {{-- FILTER BAR --}}
            <form method="GET"
                  class="mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm px-5 py-5 flex flex-col sm:flex-row 
                         gap-4 sm:items-center sm:justify-between">

                <div class="flex-1 flex flex-col sm:flex-row gap-4">

                    {{-- Search --}}
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, kode, atau kota…"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm 
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                    </div>

                    {{-- Status --}}
                    <select name="status"
                        class="rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm 
                               focus:ring-emerald-200 focus:border-emerald-500 transition">
                        <option value="ALL">Semua Status</option>
                        <option value="ACTIVE"   @selected(request('status')=='ACTIVE')>Aktif</option>
                        <option value="INACTIVE" @selected(request('status')=='INACTIVE')>Nonaktif</option>
                    </select>

                    {{-- Sort --}}
                    <select name="sort"
                        class="rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm 
                               focus:ring-emerald-200 focus:border-emerald-500 transition">
                        <option value="created_at" @selected(request('sort')=='created_at')>Terbaru</option>
                        <option value="name"       @selected(request('sort')=='name')>Nama</option>
                        <option value="code"       @selected(request('sort')=='code')>Kode</option>
                        <option value="city"       @selected(request('sort')=='city')>Kota</option>
                    </select>

                </div>

                {{-- Hidden submit on enter --}}
                <button class="hidden"></button>

            </form>
        </header>


        {{-- EMPTY STATE --}}
        @if ($cabang->count() == 0)
            <div class="rounded-2xl border border-dashed border-gray-300 p-12 bg-white text-center shadow-sm">

                <div class="mx-auto mb-5 h-14 w-14 rounded-full bg-gray-100 flex items-center justify-center text-2xl">
                    📦
                </div>

                <h3 class="text-lg font-semibold text-gray-800">Belum ada data cabang</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Tambahkan cabang baru untuk mulai mengelola restoran Anda.
                </p>

                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border border-gray-200">
                        Segarkan
                    </a>

                   <a href="/{{ strtolower($companyCode) }}/cabang/tambah"
                       class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-sm shadow">
                        + Tambah Cabang  
                    </a>
                </div>

            </div>
        @endif


        {{-- LIST GRID --}}
        @if ($cabang->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($cabang as $b)
                <div class="group rounded-2xl bg-white border border-gray-200 shadow-sm 
                            hover:shadow-md hover:border-emerald-200 hover:bg-emerald-50/30 transition-all p-6">

                    {{-- HEADER --}}
                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $b->code }} — {{ $b->name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $b->city }}</p>
                        </div>

                        {{-- STATUS BADGE --}}
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $b->is_active 
                                ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' 
                                : 'bg-gray-200 text-gray-600 border border-gray-300' }}">
                            ● {{ $b->is_active ? 'Active' : 'Inactive' }}
                        </span>

                    </div>

                    {{-- ADDRESS --}}
                    @if($b->address)
                        <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                            {{ $b->address }}
                        </p>
                    @endif

                    {{-- INFO --}}
                    <div class="mt-5 grid grid-cols-2 gap-y-3 text-xs text-gray-600">
                        <div class="font-medium">📞 Telepon</div>
                        <div>{{ $b->phone ?? '-' }}</div>

                        <div class="font-medium">🗓 Dibuat</div>
                        <div>{{ $b->created_at?->format('d M Y') }}</div>

                        <div class="font-medium">👤 Manager</div>
                        <div>{{ $b->manager_username ?? '-' }}</div>
                    </div>

                    {{-- DETAIL BUTTON --}}
                    <div class="mt-6 flex justify-end">
                        <a href="/{{ strtolower($companyCode) }}/cabang/{{ $b->code }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white 
                                  hover:bg-emerald-50 hover:border-emerald-300 text-gray-700 transition shadow-sm">
                            Detail
                        </a>
                    </div>

                </div>
                @endforeach

            </div>
        @endif

    </main>

</x-app-layout>
