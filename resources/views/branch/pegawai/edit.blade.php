<x-app-layout :branchCode="$branchCode">

    <div class="max-w-4xl mx-auto px-6 py-10 space-y-10">

        {{-- BREADCRUMB --}}
        <div>
            <a href="{{ route('branch.pegawai.index', $branchCode) }}"
               class="inline-flex items-center text-sm text-gray-600 hover:text-emerald-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pegawai
            </a>
        </div>

        {{-- HEADER --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Pegawai Cabang</h1>
            <p class="text-gray-600 mt-1">Ubah informasi pegawai untuk cabang ini</p>
        </div>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-300">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM CARD --}}
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-8">

            <form method="POST" action="{{ route('branch.pegawai.update', [$branchCode, $pegawai->id]) }}" class="space-y-6">
                @csrf
                @method('PUT')

          

                {{-- USERNAME --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username', $pegawai->username) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $pegawai->email) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                {{-- PHONE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $pegawai->phone) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role Pegawai</label>

                    <select name="role_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">

                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ (old('role_id', $currentRole->id ?? null) == $role->id) ? 'selected' : '' }}>
                                {{ $role->code }} — {{ $role->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pegawai</label>
                    <select name="is_active"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">

                        <option value="1" {{ $pegawai->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$pegawai->is_active ? 'selected' : '' }}>Tidak Aktif</option>

                    </select>
                </div>

                {{-- PASSWORD (optional) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password Baru (opsional)
                        </label>
                        <input type="password" name="password"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <input type="password" name="password_confirmation"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                </div>

                {{-- BUTTONS --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">

                    <a href="{{ route('branch.pegawai.index', $branchCode) }}"
                       class="px-5 py-2.5 border rounded-lg text-sm text-gray-700 border-gray-300 hover:bg-gray-50">
                        Batal
                    </a>

                    <button class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 shadow">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
