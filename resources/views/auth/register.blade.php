<x-guest-layout>
    <main class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-indigo-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- ================= HEADER ================= --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-indigo-600 
                            text-white grid place-items-center font-bold text-lg shadow-md">
                    R
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Buat Akun Perusahaan
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Isi data perusahaan dan admin utama untuk memulai.
                </p>
            </div>

            {{-- ================= FORM CARD ================= --}}
            <form 
                method="POST"
                action="{{ route('register') }}"
                class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-md shadow-xl p-6 space-y-5"
            >
                @csrf

                {{-- ALERT ERROR GLOBAL --}}
                @if ($errors->any())
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- ALERT SUCCESS --}}
                @if (session('success'))
                    <div class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ========== COMPANY INFO ========== --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Perusahaan
                    </label>
                    <input 
                        name="companyName"
                        value="{{ old('companyName') }}"
                        placeholder="Misal: Demo Resto Corp"
                        class="w-full rounded-lg border 
                            {{ $errors->has('companyName') ? 'border-rose-400' : 'border-gray-300' }}
                            focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                    />
                    @error('companyName')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Perusahaan <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input 
                        name="companyCode"
                        value="{{ old('companyCode') }}"
                        placeholder="Misal: DEMO123"
                        class="w-full rounded-lg border border-gray-300 uppercase 
                            focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                    />
                </div>

                {{-- ========== USER INFO ============= --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Username
                        </label>
                        <input 
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="nico_fer"
                            class="w-full rounded-lg border 
                                {{ $errors->has('username') ? 'border-rose-400' : 'border-gray-300' }}
                                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                        />
                        @error('username')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input 
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@company.com"
                            class="w-full rounded-lg border 
                                {{ $errors->has('email') ? 'border-rose-400' : 'border-gray-300' }}
                                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                        />
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Telepon
                        </label>
                        <input 
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border 
                                {{ $errors->has('phone') ? 'border-rose-400' : 'border-gray-300' }}
                                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                        />
                        @error('phone')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input 
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="w-full rounded-lg border 
                                {{ $errors->has('password') ? 'border-rose-400' : 'border-gray-300' }} 
                                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                        />
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password
                        </label>
                        <input 
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            class="w-full rounded-lg border 
                                {{ $errors->has('password_confirmation') ? 'border-rose-400' : 'border-gray-300' }}
                                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-3 py-2 transition"
                        />
                        @error('password_confirmation')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button
                    type="submit"
                    class="mt-2 w-full flex items-center justify-center rounded-lg 
                        bg-gradient-to-r from-emerald-600 to-indigo-600 text-white py-2.5 
                        text-sm font-medium shadow hover:opacity-90 transition"
                >
                    Daftar Akun
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
</x-guest-layout>
