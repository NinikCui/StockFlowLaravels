<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Cabang</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi cabang restoran.</p>
            </div>

            <a href="{{ route('cabang.detail', [$companyCode, $cabang->code]) }}"
               class="text-sm text-gray-600 hover:text-gray-800 underline">
                ← Kembali
            </a>
        </div>

        <div class="max-w-3xl mx-auto bg-white border shadow-sm rounded-2xl p-6">
            <form method="POST" action="{{ route('cabang.update', [$companyCode, $cabang->code]) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- Nama Cabang --}}
                    <div>
                        <label class="text-gray-600 text-sm">Nama Cabang</label>
                        <input type="text" name="name"
                               class="mt-1 w-full rounded-xl border-gray-300"
                               value="{{ old('name', $cabang->name) }}" required>
                    </div>

                    {{-- Kode Cabang --}}
                    <div>
                        <label class="text-gray-600 text-sm">Kode Cabang</label>
                        <input type="text" name="code"
                               class="mt-1 w-full rounded-xl border-gray-300 uppercase"
                               value="{{ old('code', $cabang->code) }}" required>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="text-gray-600 text-sm">Kota</label>
                        <input type="text" name="city"
                               class="mt-1 w-full rounded-xl border-gray-300"
                               value="{{ old('city', $cabang->city) }}">
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="text-gray-600 text-sm">Telepon</label>
                        <input type="text" name="phone"
                               class="mt-1 w-full rounded-xl border-gray-300"
                               value="{{ old('phone', $cabang->phone) }}">
                    </div>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm">Alamat</label>
                        <textarea name="address"
                                  class="mt-1 w-full rounded-xl border-gray-300"
                                  rows="3">{{ old('address', $cabang->address) }}</textarea>
                    </div>

                    {{-- Status --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm">Status Cabang</label>
                        <select name="is_active"
                                class="mt-1 w-full rounded-xl border-gray-300">
                            <option value="1" {{ $cabang->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$cabang->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Manager Cabang --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm">Manager Cabang</label>
                        <select name="manager_user_id"
                                class="mt-1 w-full rounded-xl border-gray-300">
                            <option value="">-- Tidak Ada --</option>

                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id }}"
                                    {{ $cabang->manager_user_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->username }} — {{ $p->role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

    </main>
</x-app-layout>
