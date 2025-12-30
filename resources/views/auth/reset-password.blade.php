<x-guest-layout>
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-indigo-50 px-4 py-10">
        <div class="w-full max-w-md">

            {{-- BRAND --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-indigo-600 
                            text-white grid place-items-center shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Reset Password</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Buat password baru untuk akun Anda.
                </p>
            </div>

            {{-- FORM --}}
            <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5">
                
                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- EMAIL --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                placeholder="contoh: anda@email.com"
                                required
                                autofocus
                                disabled
                                autocomplete="username"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2 text-sm 
                                       outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                            />
                        </div>
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password_input"
                                type="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-16 py-2 text-sm
                                       outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('password_input')"
                                class="absolute inset-y-0 right-2 flex items-center text-xs text-gray-500 hover:text-gray-700 px-2"
                            >
                                Show
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Gunakan minimal 8 karakter dengan kombinasi huruf dan angka
                        </p>
                    </div>

                    {{-- CONFIRM PASSWORD --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input
                                id="password_confirmation_input"
                                type="password"
                                name="password_confirmation"
                                placeholder="Ketik ulang password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-16 py-2 text-sm
                                       outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation_input')"
                                class="absolute inset-y-0 right-2 flex items-center text-xs text-gray-500 hover:text-gray-700 px-2"
                            >
                                Show
                            </button>
                        </div>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-700">Kekuatan Password:</p>
                        <div class="flex gap-1.5">
                            <div id="strength-1" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                            <div id="strength-2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                            <div id="strength-3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                            <div id="strength-4" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                        </div>
                        <p id="strength-text" class="text-xs text-gray-500"></p>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r 
                               from-emerald-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white 
                               shadow hover:opacity-90 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Reset Password
                    </button>

                    {{-- Divider --}}
                    <div class="my-4 flex items-center gap-3 text-xs text-gray-400">
                        <span class="h-px flex-1 bg-gray-200"></span>
                        atau
                        <span class="h-px flex-1 bg-gray-200"></span>
                    </div>

                    {{-- BACK TO LOGIN --}}
                    <a
                        href="{{ route('login') }}"
                        class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-emerald-700 transition group"
                    >
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke halaman login
                    </a>
                </form>
            </div>

            {{-- FOOT NOTE --}}
            <p class="mt-5 text-center text-xs text-gray-400">
                Tips: gunakan kombinasi huruf besar, kecil, angka, dan simbol untuk password yang kuat.
            </p>

        </div>
    </main>

    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const btn = event.target;

        if (input.type === "password") {
            input.type = "text";
            btn.textContent = "Hide";
        } else {
            input.type = "password";
            btn.textContent = "Show";
        }
    }

    // Password Strength Checker
    document.getElementById('password_input').addEventListener('input', function(e) {
        const password = e.target.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;

        const bars = ['strength-1', 'strength-2', 'strength-3', 'strength-4'];
        const colors = ['bg-rose-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'];
        const texts = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];

        bars.forEach((bar, index) => {
            const el = document.getElementById(bar);
            el.className = 'h-1.5 flex-1 rounded-full transition-colors';
            
            if (strength === 0) {
                el.classList.add('bg-gray-200');
            } else if (index < Math.min(strength, 4)) {
                el.classList.add(colors[Math.min(strength - 1, 3)]);
            } else {
                el.classList.add('bg-gray-200');
            }
        });

        document.getElementById('strength-text').textContent = password.length > 0 ? texts[Math.min(strength, 4)] : '';
    });
    </script>
</x-guest-layout>