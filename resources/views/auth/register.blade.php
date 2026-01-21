<x-guest-layout>
    <main class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-indigo-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- BRAND --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex justify-center">
                    <img
                        src="{{ asset('logo.png') }}"
                        alt="Logo"
                        class="h-14 w-auto object-contain drop-shadow-sm"
                    />
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                                Buat Akun Perusahaan
                            </h1>
                            <p class="mt-2 text-sm text-gray-500">
                                Isi data perusahaan dan admin utama untuk memulai.
                            </p>
            </div>
            {{-- ================= FORM CARD ================= --}}
            <form method="POST"
                  action="{{ route('register') }}"
                  class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5">
                @csrf

                {{-- ALERT ERROR GLOBAL --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <div class="font-semibold mb-1">Registrasi gagal</div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                {{-- ALERT SUCCESS --}}
                @if (session('success'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <div class="font-semibold mb-1">Berhasil</div>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                {{-- ========== COMPANY INFO ========== --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">
                        Nama Perusahaan
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            {{-- building icon --}}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 21h18M6 21V7a2 2 0 012-2h8a2 2 0 012 2v14M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01"/>
                            </svg>
                        </span>

                        <input
                            name="companyName"
                            value="{{ old('companyName') }}"
                            placeholder="Misal: Demo Resto Corp"
                            required
                            autocomplete="organization"
                            class="w-full rounded-xl border bg-white px-10 py-2.5 text-sm outline-none transition
                                   {{ $errors->has('companyName') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                   focus:ring-4"
                        />
                    </div>

                    @error('companyName')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">
                        Kode Perusahaan <span class="text-gray-400">(opsional)</span>
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            {{-- tag icon --}}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0L21 13V3H11L3 11z"/>
                            </svg>
                        </span>

                        <input
                            name="companyCode"
                            value="{{ old('companyCode') }}"
                            placeholder="Misal: DEMO123"
                            autocomplete="off"
                            class="w-full rounded-xl border border-gray-300 bg-white px-10 py-2.5 text-sm outline-none transition uppercase
                                   focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                    </div>
                </div>

                {{-- ========== USER INFO ============= --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Username --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- user icon --}}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 14a4 4 0 10-8 0m8 0a4 4 0 01-8 0m8 0v1a3 3 0 01-3 3h-2a3 3 0 01-3-3v-1"/>
                                </svg>
                            </span>

                            <input
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="nico_fer"
                                required
                                autocomplete="username"
                                class="w-full rounded-xl border bg-white px-10 py-2.5 text-sm outline-none transition
                                       {{ $errors->has('username') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                       focus:ring-4"
                            />
                        </div>
                        @error('username')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                          {{--  --}}n --}}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l9 6 9-6M5 6h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                </svg>
                            </span>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@company.com"
                                required
                                autocomplete="email"
                                class="w-full rounded-xl border bg-white px-10 py-2.5 text-sm outline-none transition
                                       {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                       focus:ring-4"
                            />
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Telepon</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- phone icon --}}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H7c1 3.866 4.134 7 8 8v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-1C10.85 21 3 13.15 3 6V5z"/>
                                </svg>
                            </span>

                            <input
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="08xxxxxxxxxx"
                                autocomplete="tel"
                                class="w-full rounded-xl border bg-white px-10 py-2.5 text-sm outline-none transition
                                       {{ $errors->has('phone') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                       focus:ring-4"
                            />
                        </div>
                        @error('phone')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- lock icon --}}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2zM8 9V7a4 4 0 118 0v2"/>
                                </svg>
                            </span>

                            <input
                                id="password_input"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                class="w-full rounded-xl border bg-white px-10 pr-12 py-2.5 text-sm outline-none transition
                                       {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                       focus:ring-4"
                            />

                            <button id="toggle_password" type="button"
                                    class="absolute inset-y-0 right-2 flex items-center justify-center rounded-lg px-2
                                           text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                                    aria-label="Tampilkan password">
                                <svg id="eye_open" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                                </svg>
                                <svg id="eye_off" class="h-4 w-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3l18 18M10.477 10.48a3 3 0 104.243 4.243"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.88 4.24A9.956 9.956 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.27 10.27 0 01-4.18 5.53M6.11 6.11A10.27 10.27 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.153 0 2.26-.195 3.29-.555"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- shield check icon --}}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4M12 3l7 4v6c0 5-3 9-7 9s-7-4-7-9V7l7-4z"/>
                                </svg>
                            </span>

                            <input
                                id="password_confirm"
                                type="password"
                                name="password_confirmation"
                                placeholder="••••••••"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                class="w-full rounded-xl border bg-white px-10 py-2.5 text-sm outline-none transition
                                       {{ $errors->has('password_confirmation') ? 'border-rose-400 focus:ring-rose-100 focus:border-rose-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100' }}
                                       focus:ring-4"
                            />
                        </div>
                        @error('password_confirmation')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button type="submit"
                        class="mt-2 w-full flex items-center justify-center gap-2 rounded-xl
                               bg-gradient-to-r from-emerald-600 to-indigo-600 text-white py-2.5
                               text-sm font-semibold shadow-lg shadow-emerald-200/40 hover:opacity-95 transition
                               focus:outline-none focus:ring-4 focus:ring-emerald-200">
                    Daftar Akun
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                       class="font-semibold bg-gradient-to-r from-emerald-600 to-indigo-600
                              bg-clip-text text-transparent hover:underline">
                        Masuk di sini
                    </a>
                </p>
            </form>
        </div>
    </main>

    <script>
        (function () {
            const input = document.getElementById('password_input');
            const btn = document.getElementById('toggle_password');
            const eyeOpen = document.getElementById('eye_open');
            const eyeOff = document.getElementById('eye_off');

            if (!input || !btn) return;

            btn.addEventListener('click', () => {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';

                eyeOpen.classList.toggle('hidden', !isHidden);
                eyeOff.classList.toggle('hidden', isHidden);

                btn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();
    </script>
</x-guest-layout>
