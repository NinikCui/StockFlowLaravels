<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gradient-to-br from-gray-50 to-gray-100">

        <div class="max-w-4xl mx-auto">
            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Tambah Pegawai Baru</h1>
                    <p class="text-gray-600">Isi informasi berikut untuk menambahkan pegawai baru ke sistem.</p>
                </div>

                <a href="/{{ $companyCode }}/pegawai"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200 font-medium">
                    <span>←</span>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- FORM --}}
            <form method="POST" action="/{{ $companyCode }}/pegawai"
                  class="space-y-6 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">

                @csrf

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">⚠️</span>
                            <div class="flex-1">
                                <p class="font-semibold mb-2">Terdapat kesalahan:</p>
                                <ul class="list-disc ml-4 space-y-1 text-sm">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Informasi Akun --}}
                <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xl">
                            👤
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Akun</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" required value="{{ old('username') }}"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Masukkan username">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                   placeholder="email@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Telepon
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400">📱</span>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400">🔒</span>
                                <input type="password" name="password" required
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Minimal 8 karakter">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400">🔒</span>
                                <input type="password" name="password_confirmation" required
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Ulangi kata sandi">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CABANG --}}
                <section class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl">
                            🏢
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Pilih Cabang</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Cabang Restoran
                            </label>
                            <select name="branch_id" id="branchSelect"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($branches as $c)
                                    <option value="{{ $c->id }}" {{ old('branch_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                            <input type="checkbox" id="universalToggle" 
                                   class="h-5 w-5 accent-emerald-600 rounded">
                            <label for="universalToggle" class="flex items-center gap-2 text-sm font-semibold text-gray-700 cursor-pointer">
                                <span>🌐</span>
                                <span>Akses Universal (Semua Cabang)</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-1">
                            *Centang jika pegawai ini memiliki akses ke semua cabang
                        </p>
                    </div>
                </section>

                {{-- ROLE --}}
                <section id="roleSection" class="hidden bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 border border-purple-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                            🛡️
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Pilih Role</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Role Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" id="roleSelect"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white">
                            <option value="">-- Pilih Role --</option>
                        </select>

                        <div id="roleLoading" class="hidden mt-3 flex items-center gap-2 text-sm text-gray-500">
                            <div class="animate-spin h-4 w-4 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
                            <span>Memuat daftar role...</span>
                        </div>
                    </div>
                </section>

                {{-- ACTION --}}
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="/{{ $companyCode }}/pegawai"
                       class="px-6 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transition-all duration-200">
                        💾 Simpan Pegawai
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const branchSelect    = document.getElementById("branchSelect");
        const universalToggle = document.getElementById("universalToggle");
        const roleSection     = document.getElementById("roleSection");
        const roleSelect      = document.getElementById("roleSelect");
        const roleLoading     = document.getElementById("roleLoading");

        function fetchRoles() {
            let branch = branchSelect.value;
            let universal = universalToggle.checked;

            if (!branch && !universal) {
                roleSection.classList.add("hidden");
                return;
            }

            roleSection.classList.remove("hidden");
            roleLoading.classList.remove("hidden");
            roleSelect.innerHTML = `<option value="">-- Pilih Role --</option>`;

            let query = universal 
                ? `universal=true` 
                : `cabangId=${branch}`;

            fetch(`/{{ $companyCode }}/pegawai/roles-json?${query}`)
                .then(res => res.json())
                .then(res => {
                    res.data.forEach(r => {
                        roleSelect.innerHTML += 
                            `<option value="${r.id}">${r.name} (${r.code})</option>`;
                    });
                })
                .finally(() => {
                    roleLoading.classList.add("hidden");
                });
        }

        branchSelect.addEventListener("change", fetchRoles);

        universalToggle.addEventListener("change", function () {
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
    </script>

</x-app-layout>