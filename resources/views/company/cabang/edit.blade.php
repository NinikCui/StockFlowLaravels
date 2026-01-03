<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gradient-to-br from-gray-50 to-gray-100">

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

        {{-- CARD --}}
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 shadow-sm rounded-2xl p-6">

            {{-- GLOBAL ERROR --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                    <ul class="list-disc ml-4 text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cabang.update', [$companyCode, $cabang->code]) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- Nama Cabang --}}
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Nama Cabang *</label>
                        <input type="text" name="name"
                               value="{{ old('name', $cabang->name) }}"
                               class="mt-1 w-full rounded-xl border-gray-300"
                               required>
                    </div>

                    {{-- Kode Cabang --}}
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Kode Cabang *</label>
                        <input type="text" name="code"
                               value="{{ old('code', $cabang->code) }}"
                               class="mt-1 w-full rounded-xl border-gray-300 uppercase"
                               required>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Kota</label>
                        <input type="text" name="city"
                               value="{{ old('city', $cabang->city) }}"
                               class="mt-1 w-full rounded-xl border-gray-300">
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Telepon</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $cabang->phone) }}"
                               class="mt-1 w-full rounded-xl border-gray-300">
                    </div>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm font-semibold">Alamat</label>
                        <textarea name="address" rows="3"
                                  class="mt-1 w-full rounded-xl border-gray-300">{{ old('address', $cabang->address) }}</textarea>
                    </div>

                    {{-- Status --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm font-semibold">Status Cabang</label>
                        <select name="is_active"
                                class="mt-1 w-full rounded-xl border-gray-300">
                            <option value="1" {{ $cabang->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$cabang->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Manager --}}
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 text-sm font-semibold">Manager Cabang</label>
                        <select name="manager_user_id" class="mt-1 w-full rounded-xl border-gray-300">
                            <option value="">-- Tidak Ada --</option>

                            @foreach ($pegawai as $p)
                                <option value="{{ $p['id'] }}"
                                        {{ $cabang->manager_user_id == $p['id'] ? 'selected' : '' }}>
                                    {{ $p['username'] }} — {{ $p['role_code'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- BUTTON --}}
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
