<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto">

            {{-- HEADER --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                        <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl">
                            ✎
                        </span>
                        Edit Pegawai
                    </h1>
                    <p class="text-gray-600">Perbarui informasi pegawai {{ $pegawai->username }}</p>
                </div>

                <a href="/{{ strtolower($companyCode) }}/pegawai"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
                    ← <span>Kembali</span>
                </a>
            </div>

            {{-- FORM --}}
            <form method="POST"
                  action="{{ route('pegawai.update', [strtolower($companyCode), $pegawai->id]) }}"
                  class="space-y-6 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">

                @csrf
                @method('PUT')

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                        <strong class="block mb-2">Terdapat kesalahan:</strong>
                        <ul class="list-disc ml-4 text-sm space-y-1">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===================== INFORMASI AKUN ===================== --}}
                <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xl">👤</div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Akun</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">

                        {{-- Username --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Username *</label>
                            <input type="text" required
                                name="username" value="{{ old('username', $pegawai->username) }}"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500"
                                placeholder="Username">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                            <input type="email" required
                                name="email" value="{{ old('email', $pegawai->email) }}"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500"
                                placeholder="email@example.com">
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Telepon</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400">📱</span>
                                <input type="text"
                                    name="phone" value="{{ old('phone', $pegawai->phone) }}"
                                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="sm:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <span class="text-blue-700 text-sm">
                                Kosongkan password jika tidak ingin mengubah.
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Opsional">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Opsional">
                        </div>

                    </div>
                </section>

                {{-- ===================== CABANG ===================== --}}
                <section class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">

                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl">🏢</div>
                        <h2 class="text-xl font-bold text-gray-900">Pilih Cabang</h2>
                    </div>

                    @php
                        $currentBranchId = $pegawai->roles->first()->cabang_resto_id ?? null;
                        $currentRoleId   = $pegawai->roles->first()->id ?? null;
                    @endphp

                    <div class="space-y-4">

                        {{-- Dropdown cabang --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang Restoran</label>
                            <select id="branchSelect" name="branch_id"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm bg-white {{ $isUniversal ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                {{ $isUniversal ? 'disabled' : '' }}>
                                <option value="">-- Pilih Cabang --</option>

                                @foreach ($branches as $c)
                                    <option value="{{ $c->id }}" {{ $currentBranchId == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Universal checkbox --}}
                        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <input type="checkbox" id="universalToggle" name="is_universal"
                                class="h-5 w-5 accent-emerald-600"
                                {{ $isUniversal ? 'checked' : '' }}>
                            <label for="universalToggle" class="text-sm font-semibold text-gray-700 cursor-pointer">
                                🌐 Akses Universal (Semua Cabang)
                            </label>
                        </div>

                    </div>
                </section>

                {{-- ===================== ROLE ===================== --}}
                <section class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 border border-purple-100">

                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">🛡️</div>
                        <h2 class="text-xl font-bold text-gray-900">Pilih Role</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role Pegawai *</label>

                        <select id="roleSelect" name="role_id"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm bg-white">

                            <option value="">-- Pilih Role --</option>

                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}" {{ $currentRoleId == $r->id ? 'selected' : '' }}>
                                    {{ $r->code }}
                                </option>
                            @endforeach

                        </select>

                        <div id="roleLoading" class="hidden mt-3 flex items-center gap-2 text-gray-500 text-sm">
                            <div class="animate-spin h-4 w-4 border-2 border-purple-500 border-t-transparent rounded-full"></div>
                            Memuat role...
                        </div>

                    </div>
                </section>

                {{-- ===================== STATUS ===================== --}}
                <section class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-6 border border-amber-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center text-xl">⚡</div>
                        <h2 class="text-xl font-bold text-gray-900">Status Akun</h2>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-200">
                        <input type="checkbox" id="statusToggle" name="is_active"
                               class="h-5 w-5 accent-emerald-600"
                               {{ $pegawai->is_active ? 'checked' : '' }}>
                        <label for="statusToggle" class="text-sm font-semibold text-gray-700 cursor-pointer">
                            {{ $pegawai->is_active ? 'Akun Aktif' : 'Akun Nonaktif' }}
                        </label>
                    </div>
                </section>

                {{-- ===================== ACTION BUTTONS ===================== --}}
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="/{{ strtolower($companyCode) }}/pegawai"
                        class="px-6 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 shadow-lg">
                        💾 Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </main>

    {{-- ===================== JAVASCRIPT ===================== --}}
    <script>
        const universalToggle = document.getElementById('universalToggle');
        const branchSelect = document.getElementById('branchSelect');
        const roleSelect = document.getElementById('roleSelect');
        const roleLoading = document.getElementById('roleLoading');
        const CURRENT_ROLE_ID = "{{ $currentRoleId }}";

        function fetchRoles() {
            let universal = universalToggle.checked;
            let branch = branchSelect.value;

            roleLoading.classList.remove("hidden");
            roleSelect.innerHTML = `<option value="">-- Pilih Role --</option>`;

            fetch(`/{{ strtolower($companyCode) }}/pegawai/roles-json?${universal ? 'universal=true' : 'cabangId=' + branch}`)
                .then(r => r.json())
                .then(res => {
                    res.data.forEach(role => {
                        roleSelect.innerHTML += `
                            <option value="${role.id}" ${role.id == CURRENT_ROLE_ID ? 'selected' : ''}>
                                ${role.code}
                            </option>
                        `;
                    });
                })
                .finally(() => roleLoading.classList.add("hidden"));
        }

        // Universal toggle
        universalToggle.addEventListener('change', () => {
            if (universalToggle.checked) {
                branchSelect.disabled = true;
                branchSelect.value = "";
                branchSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                branchSelect.disabled = false;
                branchSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
            fetchRoles();
        });

        // Branch change
        branchSelect.addEventListener('change', fetchRoles);
    </script>

</x-app-layout>
