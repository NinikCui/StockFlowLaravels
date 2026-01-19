<x-app-layout>
    @php
        $companyCode = session('role.company.code');
        $branchCode  = session('role.branch.code');
    @endphp

    <main class="min-h-screen px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-gradient-to-br from-gray-50 via-emerald-50/30 to-gray-50">

        {{-- HEADER --}}
        <header class="mb-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">
                                Cabang Restoran
                            </h1>
                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Kelola seluruh cabang dalam perusahaan
                            </p>
                        </div>
                    </div>
                </div>

                {{-- HEADER BUTTONS --}}
                <div class="flex items-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm
                              text-gray-700 hover:bg-white hover:border-emerald-300 hover:shadow-md text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Segarkan
                    </a>

                    <x-crud-add 
                        resource="cabang"
                        :companyCode="$companyCode"
                        permissionPrefix="branch"
                    />
                </div>

            </div>

            {{-- FILTER BAR --}}
            <form method="GET"
                  class="mt-8 rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-sm shadow-lg shadow-gray-200/50 p-6 
                         flex flex-col lg:flex-row gap-4 lg:items-end">

                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    {{-- Search --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Pencarian</label>
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama, kode, atau kota…"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-11 text-sm 
                                       focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all">
                            <svg class="absolute left-3.5 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm 
                                   focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all">
                            <option value="ALL">Semua Status</option>
                            <option value="ACTIVE"   @selected(request('status')=='ACTIVE')>✓ Aktif</option>
                            <option value="INACTIVE" @selected(request('status')=='INACTIVE')>○ Nonaktif</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Urutkan</label>
                        <select name="sort"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm 
                                   focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all">
                            <option value="created_at" @selected(request('sort')=='created_at')>📅 Terbaru</option>
                            <option value="name"       @selected(request('sort')=='name')>🔤 Nama</option>
                            <option value="code"       @selected(request('sort')=='code')>🔢 Kode</option>
                            <option value="city"       @selected(request('sort')=='city')>📍 Kota</option>
                        </select>
                    </div>

                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold
                               hover:from-emerald-700 hover:to-emerald-800 active:scale-95 transition-all duration-200 shadow-lg shadow-emerald-600/30
                               flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Terapkan Filter
                </button>

            </form>
        </header>


        {{-- EMPTY STATE --}}
        @if ($cabang->count() == 0)
            <div class="rounded-3xl border-2 border-dashed border-gray-300 p-16 bg-white/80 backdrop-blur-sm text-center shadow-xl">

                <div class="mx-auto mb-6 h-20 w-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-4xl shadow-inner">
                    🏪
                </div>

                <h3 class="text-xl font-bold text-gray-800">Belum ada data cabang</h3>
                <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
                    Mulai dengan menambahkan cabang pertama Anda untuk mengelola operasional perusahaan dengan lebih baik.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 
                              hover:bg-gray-200 text-sm font-medium border border-gray-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Segarkan
                    </a>

                    <x-crud-add 
                        resource="cabang"
                        :companyCode="$companyCode"
                        permissionPrefix="branch"
                    />
                </div>

            </div>
        @endif


        {{-- LIST GRID --}}
        @if ($cabang->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($cabang as $b)
                <div class="group rounded-2xl bg-white border border-gray-200 shadow-md
                            hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1
                            transition-all duration-300 p-6 relative overflow-hidden">
                    
                    {{-- Decorative gradient --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    {{-- MAIN BRANCH BADGE --}}
                    @if ($b->utama)
                        <span class="absolute top-4 left-4 px-3 py-1.5 rounded-xl text-xs font-bold
                                    bg-gradient-to-r from-amber-400 to-amber-500 text-white border border-amber-300 shadow-lg shadow-amber-500/30
                                    flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Utama
                        </span>
                    @endif

                    {{-- STATUS BADGE --}}
                    <span class="absolute top-4 right-4 px-3 py-1.5 rounded-xl text-xs font-semibold shadow-md
                        flex items-center gap-1.5
                        {{ $b->is_active 
                            ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' 
                            : 'bg-gray-200 text-gray-600 border border-gray-300' }}">
                        <span class="w-2 h-2 rounded-full {{ $b->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>

                    {{-- HEADER --}}
                    <div class="mt-8 relative z-10">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">
                            {{ $b->name }}
                        </h3>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                {{ $b->code }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $b->city }}
                            </span>
                        </div>
                    </div>

                    {{-- ADDRESS --}}
                    @if($b->address)
                        <div class="mt-4 p-3 rounded-xl bg-gray-50 border border-gray-200">
                            <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">
                                {{ $b->address }}
                            </p>
                        </div>
                    @endif

                    {{-- INFO GRID --}}
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-blue-50 to-transparent border border-blue-100">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Telepon
                            </div>
                            <span class="text-xs text-gray-900 font-medium">{{ $b->phone ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-purple-50 to-transparent border border-purple-100">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Manager
                            </div>
                            <div>
                                @if ($b->manager)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                        {{ $b->manager->username }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum ada</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-orange-50 to-transparent border border-orange-100">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Dibuat
                            </div>
                            <span class="text-xs text-gray-900 font-medium">{{ $b->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- DETAIL BUTTON --}}
                    <div class="mt-6">
                        <a href="{{ route('cabang.detail', ['companyCode' => $companyCode, 'code' => $b->code]) }}"
                           class="w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold rounded-xl 
                                  border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 to-white
                                  hover:from-emerald-600 hover:to-emerald-700 hover:text-white hover:border-emerald-600
                                  text-emerald-700 shadow-sm hover:shadow-lg
                                  transition-all duration-300 group/btn">
                            <span>Lihat Detail</span>
                            <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>
                @endforeach

            </div>

            
        @endif

    </main>
</x-app-layout>