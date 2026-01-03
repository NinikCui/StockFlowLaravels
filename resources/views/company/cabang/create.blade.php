<x-app-layout>
    @php
        $companyCode = strtolower(session('role.company.code'));
    @endphp

    <main class="min-h-screen px-4 sm:px-6 lg:px-8 py-8 bg-gradient-to-br from-gray-50 via-white to-gray-50">

        {{-- HEADER --}}
        <div class="max-w-3xl mx-auto mb-8">
            {{-- Breadcrumb --}}
            <div class="mb-4">
                {{ Breadcrumbs::render('company.cabang.create', $companyCode) }}
            </div>

            <div class="flex items-center gap-4 mb-3">
                <a href="{{ route('cabang.index', ['companyCode' => $companyCode]) }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                        Tambah Cabang Baru
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Lengkapi formulir di bawah untuk menambahkan cabang baru
                    </p>
                </div>
            </div>
        </div>


        {{-- FORM CARD --}}
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">

                {{-- FORM --}}
                <form action="{{ route('cabang.store', $companyCode) }}"
                      method="POST"
                      class="p-8 space-y-6">
                    @csrf

                    {{-- Grid Layout for better organization --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- NAMA --}}
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Nama Cabang
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Contoh: Cabang Surabaya"
                                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-white
                                          focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all
                                          placeholder:text-gray-400 text-gray-900">
                            @error('name')
                                <p class="flex items-center gap-1 text-xs text-red-600 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- KODE --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Kode Cabang
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code') }}"
                                   placeholder="SURABAYA_01"
                                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-white
                                          uppercase focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all
                                          placeholder:text-gray-400 text-gray-900 font-mono">
                            @error('code')
                                <p class="flex items-center gap-1 text-xs text-red-600 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- KOTA --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Kota
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   placeholder="Contoh: Surabaya"
                                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-white
                                          focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all
                                          placeholder:text-gray-400 text-gray-900">
                            @error('city')
                                <p class="flex items-center gap-1 text-xs text-red-600 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ALAMAT --}}
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Alamat Lengkap
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" rows="3"
                                      placeholder="Masukkan alamat lengkap cabang..."
                                      class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-white
                                             focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all
                                             placeholder:text-gray-400 text-gray-900 resize-none">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="flex items-center gap-1 text-xs text-red-600 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- TELEPON --}}
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Nomor Telepon
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-white
                                          focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all
                                          placeholder:text-gray-400 text-gray-900">
                            @error('phone')
                                <p class="flex items-center gap-1 text-xs text-red-600 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    {{-- INFO BOX --}}
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-emerald-900 mb-1">Informasi Penting</h4>
                                <p class="text-xs text-emerald-700 leading-relaxed">
                                    Pastikan semua data yang dimasukkan sudah benar. Kode cabang harus unik dan tidak boleh sama dengan cabang lain.
                                </p>
                            </div>
                        </div>
                    </div>


                    {{-- FOOTER ACTIONS --}}
                    <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end gap-3">

                        <a href="{{ route('cabang.index', ['companyCode' => $companyCode]) }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                  border-2 border-gray-300 bg-white text-gray-700 font-medium
                                  hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                       bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-medium
                                       hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg 
                                       shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Cabang
                        </button>

                    </div>

                </form>
            </div>
        </div>

    </main>
</x-app-layout>