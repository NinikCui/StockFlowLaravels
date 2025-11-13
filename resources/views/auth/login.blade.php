<x-guest-layout>
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-indigo-50 px-4 py-10">
    <div class="w-full max-w-md">

        {{-- BRAND --}}
        <div class="mb-8 text-center">
            <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-indigo-600 
                        text-white grid place-items-center font-semibold text-lg shadow-md">
                R
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Masuk ke Akun</h1>
            <p class="mt-2 text-sm text-gray-500">
                Gunakan email atau username beserta kata sandi Anda.
            </p>
        </div>

        {{-- FORM --}}
        <form 
            method="POST"
            action="{{ route('login') }}"
            class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5"
        >
            @csrf

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- IDENTIFIER --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email atau Username
                </label>
                <input
                    name="identifier"
                    value="{{ old('identifier') }}"
                    placeholder="contoh: nico@contoh.com atau nico_fer"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm 
                           outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                />
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>

                <div class="relative">
                    <input
                        id="password_input"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        minlength="6"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm
                               outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                    />
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-2 flex items-center text-xs text-gray-500 hover:text-gray-700 px-2"
                    >
                        Show
                    </button>
                </div>
            </div>

            {{-- REMEMBER + FORGOT --}}
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-700">
                    <input 
                        type="checkbox" 
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-400"
                    />
                    Ingat saya
                </label>

                <a href="{{ route('password.request') }}" class="text-gray-600 hover:text-emerald-700 hover:underline">
                    Lupa password?
                </a>
            </div>

            {{-- SUBMIT --}}
            <button
                type="submit"
                class="mt-3 w-full flex items-center justify-center rounded-lg bg-gradient-to-r 
                       from-emerald-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white 
                       shadow hover:opacity-90 transition"
            >
                Masuk
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
    function togglePassword() {
        const input = document.getElementById('password_input');
        const btn = event.target;

        if (input.type === "password") {
            input.type = "text";
            btn.textContent = "Hide";
        } else {
            input.type = "password";
            btn.textContent = "Show";
        }
    }
    </script>
</x-guest-layout>
