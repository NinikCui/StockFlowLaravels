<x-app-layout>

<main class="min-h-screen px-6 py-10 bg-gray-50 max-w-3xl mx-auto">

    <h1 class="text-3xl font-black text-gray-900 mb-6">
        Pengaturan Perusahaan — General
    </h1>

    @if(session('success'))
        <div class="p-3 mb-5 bg-emerald-100 text-emerald-700 rounded-lg border border-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('settings.general.update', $companyCode) }}"
        class="space-y-10">

        @csrf

        {{-- LOGO --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Logo Perusahaan</h2>

            <div class="flex items-start gap-5">

                {{-- PREVIEW LOGO --}}
                <div class="flex flex-col items-center gap-2">

                    @if(isset($settings['general.logo']))
                        <img 
                            src="{{ $settings['general.logo'] }}" 
                            class="h-20 w-20 rounded-xl object-cover border shadow-sm"
                        >
                    @else
                        <div class="h-20 w-20 rounded-xl bg-gray-100 border grid place-items-center text-gray-400">
                            No Logo
                        </div>
                    @endif

                    <p class="text-xs text-gray-400">Max 2MB • JPG/PNG/WEBP</p>
                </div>

                {{-- INPUT --}}
                <div class="flex-1">
                    <input 
                        type="file" 
                        name="logo"
                        class="text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border file:bg-gray-50 
                            file:border-gray-300 hover:file:bg-gray-100 cursor-pointer"
                    >

                    {{-- ERROR MESSAGE --}}
                    @error('logo')
                        <p class="text-red-500 text-xs mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>


        {{-- CONTACT --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Kontak Perusahaan</h2>

            <div class="space-y-4">

                <div>
                    <label class="text-sm text-gray-600">Alamat Kantor</label>
                    <input type="text" name="address"
                        value="{{ $settings['general.address'] ?? '' }}"
                        class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Nomor Telepon</label>
                    <input type="text" name="phone"
                        value="{{ $settings['general.phone'] ?? '' }}"
                        class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="text" name="email"
                        value="{{ $settings['general.email'] ?? '' }}"
                        class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Website</label>
                    <input type="text" name="website"
                        value="{{ $settings['general.website'] ?? '' }}"
                        class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

            </div>
        </div>

        {{-- FOOTER TEXT --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Footer Struk</h2>
            <input type="text" name="footer_text"
                value="{{ $settings['general.footer_text'] ?? '' }}"
                placeholder="Terima kasih sudah berkunjung!"
                class="w-full mt-1 border rounded-xl px-3 py-2">
        </div>

        {{-- ESTABLISHED --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Informasi Perusahaan</h2>

            <label class="text-sm text-gray-600">Tahun Berdiri</label>
            <input type="number" name="established_year"
                value="{{ $settings['general.established_year'] ?? '' }}"
                class="w-full mt-1 border rounded-xl px-3 py-2">
        </div>

        <div class="flex justify-end">
            <button class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                Simpan Pengaturan
            </button>
        </div>

    </form>

</main>

</x-app-layout>
