<x-app-layout>
    @php
        $companyCode = strtolower(session('role.company.code'));
    @endphp

    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('cabang.index', ['companyCode' => $companyCode]) }}"
                   class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                    ← Kembali
                </a>

                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    Tambah Cabang
                </h1>
            </div>

            <p class="text-sm text-gray-500 ml-[54px]">
                Isi data berikut untuk menambahkan cabang baru.
            </p>
        </div>

        {{-- FORM CARD --}}
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">

                {{-- CARD HEADER --}}
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Form Cabang Baru</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Mohon lengkapi seluruh data dengan benar.
                    </p>
                </div>

                {{-- FORM --}}
                <form action="{{ route('cabang.store', $companyCode) }}"
                      method="POST"
                      class="px-6 py-7 space-y-6">
                    @csrf

                    {{-- INFO CABANG UTAMA SAAT INI --}}
                    @if ($cabangUtama)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            <p class="font-semibold flex items-center gap-2">
                                ⭐ Cabang Utama Saat Ini
                            </p>
                            <p class="mt-1">
                                {{ $cabangUtama->name }} ({{ $cabangUtama->code }}) – {{ $cabangUtama->city }}
                            </p>
                            <p class="text-xs text-amber-700 mt-1">
                                Menjadikan cabang baru sebagai cabang utama akan menggantikan cabang ini.
                            </p>
                        </div>
                    @endif

                    {{-- NAMA --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Nama Cabang</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5"
                               placeholder="Contoh: Cabang Surabaya">
                        @error('name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KODE --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Kode Cabang</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 uppercase"
                               placeholder="SURABAYA_01">
                        @error('code')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KOTA --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5"
                               placeholder="Surabaya">
                        @error('city')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ALAMAT --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5"
                               placeholder="Alamat lengkap cabang">
                    </div>

                    {{-- TELEPON --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5"
                               placeholder="08123456789">
                    </div>

                    {{-- CABANG UTAMA --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-300 bg-gray-50">
                            <input type="checkbox"
                                   name="utama"
                                   value="1"
                                   @checked(old('utama'))
                                   class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Jadikan sebagai Cabang Utama
                                </p>
                            </div>
                        </label>
                    </div>

                    {{-- FOOTER --}}
                    <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('cabang.index', ['companyCode' => $companyCode]) }}"
                           class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>
</x-app-layout>
