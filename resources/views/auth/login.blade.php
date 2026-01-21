<x-guest-layout>
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-indigo-50 px-4 py-10">
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

                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    Masuk ke Akun
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Gunakan email atau username beserta kata sandi Anda.
                </p>
            </div>

            {{-- FORM --}}
            <form method="POST"
                  action="{{ route('login') }}"
                  class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5">
                @csrf

                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <div class="font-semibold mb-1">Login gagal</div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                {{-- IDENTIFIER --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">
                        Email atau Username
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            {{-- user icon --}}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 14a4 4 0 10-8 0m8 0a4 4 0 01-8 0m8 0v1a3 3 0 01-3 3h-2a3 3 0 01-3-3v-1"/>
                            </svg>
                        </span>

                        <input
                            name="identifier"
                            value="{{ old('identifier') }}"
                            placeholder="nico@contoh.com atau nico_fer"
                            required
                            autocomplete="username"
                            class="w-full rounded-xl border border-gray-300 bg-white px-10 py-2.5 text-sm
                                   outline-none transition
                                   focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                    </div>
                </div>

                {{-- PASSWORD --}}
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
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-gray-300 bg-white px-10 pr-12 py-2.5 text-sm
                                   outline-none transition
                                   focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />

                        <button
                            id="toggle_password"
                            type="button"
                            aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-2 flex items-center justify-center rounded-lg px-2
                                   text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                        >
                            {{-- eye icon --}}
                            <svg id="eye_open" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>

                            {{-- eye off icon --}}
                            <svg id="eye_off" class="h-4 w-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3l18 18M10.477 10.48a3 3 0 104.243 4.243"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.88 4.24A9.956 9.956 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.27 10.27 0 01-4.18 5.53M6.11 6.11A10.27 10.27 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.153 0 2.26-.195 3.29-.555"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- REMEMBER + FORGOT --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-700 select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            @checked(old('remember'))
                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-400"
                        />
                        Ingat saya
                    </label>

                    <a href="{{ route('password.request') }}"
                       class="text-gray-600 hover:text-emerald-700 hover:underline">
                        Lupa password?
                    </a>
                </div>

                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="mt-2 w-full flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r
                           from-emerald-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                           shadow-lg shadow-emerald-200/40 hover:opacity-95 transition
                           focus:outline-none focus:ring-4 focus:ring-emerald-200"
                >
                    Masuk
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>

                {{-- Divider --}}
                <div class="my-5 flex items-center gap-3 text-xs text-gray-400">
                    <span class="h-px flex-1 bg-gray-200"></span>
                    atau
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                {{-- REGISTER LINK --}}
                <p class="text-center text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                       class="font-semibold bg-gradient-to-r from-emerald-600 to-indigo-600
                              bg-clip-text text-transparent hover:underline">
                        Daftar Sekarang
                    </a>
                </p>
            </form>

            {{-- FOOT NOTE --}}
            <p class="mt-5 text-center text-xs text-gray-400">
                Tips: gunakan email atau username yang terdaftar pada sistem.
            </p>

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
