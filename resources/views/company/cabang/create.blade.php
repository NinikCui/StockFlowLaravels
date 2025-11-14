<x-app-layout>
    @php
        $companyCode = strtolower(session('role.company.code'));
    @endphp

    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <a href="/{{ strtolower($companyCode) }}/cabang"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white 
                        hover:bg-gray-100 text-sm shadow-sm transition">
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
                    <p class="text-sm text-gray-500 mt-1">Mohon lengkapi seluruh data dengan benar.</p>
                </div>

                {{-- FORM --}}
                <form action="{{ route('cabang.store', $companyCode) }}"
                      method="POST"
                      class="px-6 py-7 space-y-6">
                    @csrf


                    {{-- NAMA --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Nama Cabang</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Contoh: Cabang Surabaya"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 bg-white shadow-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        @error('name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KODE --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Kode Cabang</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="SURABAYA_01"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 bg-white shadow-sm
                                      uppercase focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        @error('code')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KOTA --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="Contoh: Surabaya"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 bg-white shadow-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        @error('city')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ALAMAT --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="Alamat lengkap cabang"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 bg-white shadow-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        @error('address')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TELEPON --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="08123456789"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 bg-white shadow-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        @error('phone')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- FOOTER --}}
                    <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">

                        <a href="/{{ strtolower($companyCode) }}/cabang"
                           class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 
                                  hover:bg-gray-100 text-sm shadow-sm transition">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 
                                       text-sm shadow-sm transition">
                            Simpan
                        </button>

                    </div>

                </form>
            </div>
        </div>

    </main>
</x-app-layout>
