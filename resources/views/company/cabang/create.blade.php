<x-app-layout>
    @php
        $companyCode = session('role.company.code');
        $branchCode  = session('role.branch.code');
    @endphp

    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Cabang</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Isi data berikut untuk menambahkan cabang baru.
                </p>
            </div>

            <a href="/{{ $companyCode }}/cabang"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border bg-white hover:bg-gray-100 shadow-sm text-sm">
                ← Kembali
            </a>
        </div>

        {{-- FORM WRAPPER --}}
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100">
                
                <div class="px-6 py-5 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Form Cabang Baru</h2>
                    <p class="text-sm text-gray-500 mt-1">Pastikan semua data sudah benar.</p>
                </div>

                <form action="{{ route('cabang.store', $companyCode) }}" method="POST" class="px-6 py-6 space-y-6">
                    @csrf

                    {{-- NAMA --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cabang</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Contoh: Cabang Surabaya"
                               class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:ring-emerald-200 focus:border-emerald-400">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KODE --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Cabang</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="SURABAYA_01"
                               class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:ring-emerald-200 focus:border-emerald-400">
                        @error('code')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KOTA --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="Contoh: Surabaya"
                               class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:ring-emerald-200 focus:border-emerald-400">
                        @error('city')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ALAMAT --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="Alamat lengkap cabang"
                               class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:ring-emerald-200 focus:border-emerald-400">
                        @error('address')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TELEPON --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="08123456789"
                               class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:ring-emerald-200 focus:border-emerald-400">
                        @error('phone')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FOOTER --}}
                    <div class="pt-6 mt-4 border-t flex justify-end gap-3">
                        <a href="/{{ $companyCode }}/cabang"
                           class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm shadow">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>
</x-app-layout>
