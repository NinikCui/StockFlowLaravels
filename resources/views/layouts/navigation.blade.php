<header 
    x-data="{ open: false }"
    class="sticky top-0 z-[70] w-full border-b border-gray-200 bg-white"
>
    <div class="mx-auto max-w-screen-xl px-4 h-16 flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2 shrink-0">
            <img src="/logo.png" alt="Logo" class="h-16 w-auto" />
        </a>

        {{-- ================= DESKTOP NAV ================= --}}
        <nav class="hidden md:block">
            <ul class="flex items-center gap-6 text-[15px] font-semibold text-gray-900">

                {{-- Beranda --}}
                <li>
                    <button
                        onclick="window.location='/'"
                        class="px-4 py-1 rounded-full text-[15px] font-semibold 
                            transition hover:bg-gray-900 hover:text-white
                            {{ request()->is('/') ? 'bg-gray-900 text-white' : 'text-gray-900' }}"
                    >
                        Beranda
                    </button>
                </li>

                {{-- Aplikasi (Dropdown) --}}
                <li x-data="{ dd:false }" class="relative">
                    <button 
                        @click="dd = !dd"
                        @click.outside="dd = false"
                        class="pb-1 hover:border-b-2 border-red-300 flex items-center gap-1"
                    >
                        Aplikasi
                    </button>

                    {{-- DROPDOWN PANEL --}}
                    <div 
                        x-show="dd"
                        x-transition
                        class="absolute left-0 mt-2 bg-white border border-gray-300 shadow-lg w-[720px] p-6 grid grid-cols-3 gap-8 z-50"
                    >

                        {{-- Keuangan --}}
                        <div>
                            <h4 class="text-xs font-semibold text-green-600 border-b border-green-500 mb-2 pb-1">
                                Keuangan
                            </h4>
                            <ul class="space-y-1 text-sm">
                                <li><a href="#" class="hover:underline flex items-center gap-1">Pengelolaan</a></li>
                                <li><a href="#" class="hover:underline">Akuntansi</a></li>
                                <li><a href="#" class="hover:underline">Pemasukan</a></li>
                                <li><a href="#" class="hover:underline">Planning Keuangan</a></li>
                            </ul>
                        </div>

                        {{-- SDM --}}
                        <div>
                            <h4 class="text-xs font-semibold text-purple-600 border-b border-purple-500 mb-2 pb-1">
                                Sumber Daya Manusia
                            </h4>
                            <ul class="space-y-1 text-sm">
                                <li><a href="#" class="hover:underline">Karyawan</a></li>
                                <li><a href="#" class="hover:underline">Rekrutmen</a></li>
                                <li><a href="#" class="hover:underline">Appraisal</a></li>
                                <li><a href="#" class="hover:underline">Armada</a></li>
                            </ul>
                        </div>

                        {{-- Rantai Pasokan --}}
                        <div>
                            <h4 class="text-xs font-semibold text-blue-600 border-b border-blue-500 mb-2 pb-1">
                                Rantai Pasokan
                            </h4>
                            <ul class="space-y-1 text-sm">
                                <li><a href="#" class="hover:underline">Inventaris</a></li>
                                <li><a href="#" class="hover:underline">Manufaktur</a></li>
                                <li><a href="#" class="hover:underline">Purchase</a></li>
                                <li><a href="#" class="hover:underline">Maintenance</a></li>
                            </ul>
                        </div>

                    </div>
                </li>

            </ul>
        </nav>

        {{-- AUTH BUTTONS DESKTOP --}}
        <div class="hidden md:flex items-center gap-2">
            <a href="/login"
                class="text-[15px] font-semibold text-gray-900 rounded-full px-4 py-1 hover:bg-gray-900 hover:text-white transition">
                Login
            </a>
            <a href="/register"
                class="bg-black text-white text-[15px] font-semibold rounded-full px-4 py-1 hover:bg-gray-800 transition">
                Register
            </a>
        </div>

        {{-- ================= MOBILE BUTTON ================= --}}
        <button
            @click.stop="open = !open"
            class="md:hidden inline-flex h-9 w-9 items-center justify-center rounded-md border bg-white/70 
                   hover:bg-white z-[100] relative"
            aria-label="Buka menu"
        >
            <template x-if="!open">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path stroke-linecap="round" 
                     stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </template>
            <template x-if="open">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path stroke-linecap="round"
                     stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
        </button>

    </div>


    {{-- MOBILE PANEL --}}
    <div 
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="md:hidden fixed inset-x-0 top-16 z-[9999] border-t bg-white shadow-md"
        role="dialog"
        aria-modal="true"
    >
        <div class="mx-auto max-w-screen-xl px-4 py-4 space-y-3">
            <a href="/" class="block text-gray-900 font-semibold">Beranda</a>
            <a href="/login" class="block text-gray-900 font-semibold">Login</a>
            <a href="/register" class="block text-gray-900 font-semibold">Register</a>
        </div>
    </div>

</header>
