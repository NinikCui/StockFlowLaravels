<x-guest-layout>
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-indigo-50 px-4 py-10">
        <div class="w-full max-w-md">

            {{-- BRAND --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-indigo-600 
                            text-white grid place-items-center shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Lupa Password?</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Masukkan email Anda dan kami akan mengirimkan tautan untuk reset password.
                </p>
            </div>

            {{-- FORM --}}
            <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5">
                
                <!-- Session Status -->
                @if (session('status'))
                    <div class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 flex items-start gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-emerald-700">
                            {{ session('status') }}
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

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
                                value="{{ old('email') }}"
                                placeholder="contoh: anda@email.com"
                                required
                                autofocus
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2 text-sm 
                                       outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                            />
                        </div>

                        @if ($errors->get('email'))
                            <div class="mt-2 flex items-start gap-2 text-sm text-rose-600">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $errors->first('email') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center rounded-lg bg-gradient-to-r 
                               from-emerald-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white 
                               shadow hover:opacity-90 transition"
                    >
                        Kirim Link Reset Password
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
                Tips: pastikan email yang Anda masukkan terdaftar pada sistem.
            </p>

        </div>
    </main>
</x-guest-layout>