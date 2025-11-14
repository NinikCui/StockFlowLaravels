<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Pegawai Baru</h1>
                <p class="text-sm text-gray-500">Isi informasi berikut untuk menambahkan pegawai baru.</p>
            </div>

            <a href="/{{ $companyCode }}/pegawai"
                class="text-sm text-gray-600 hover:text-gray-800 underline">
                ← Kembali
            </a>
        </div>

        {{-- FORM --}}
        <form method="POST" action="/{{ $companyCode }}/pegawai"
              class="space-y-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

            @csrf

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="rounded-lg border border-rose-300 bg-rose-50 p-3 text-rose-700 text-sm">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Informasi Akun --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Informasi Akun</h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium">Username</label>
                        <input type="text" name="username" required
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" required
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Telepon</label>
                        <input type="text" name="phone"
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Kata Sandi</label>
                        <input type="password" name="password" required
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Konfirmasi Sandi</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>
                </div>
            </section>

            {{-- CABANG --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Pilih Cabang</h2>

                <div class="flex items-center gap-3">
                    <select name="branch_id" id="branchSelect"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach ($branches as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" id="universalToggle"
                               class="h-4 w-4 accent-emerald-600">
                        Universal
                    </label>
                </div>
            </section>

            {{-- ROLE --}}
            <section id="roleSection" class="hidden">
                <h2 class="text-lg font-semibold mb-3">Pilih Role</h2>

                <select name="role_id" id="roleSelect"
                    class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    <option value="">-- Pilih Role --</option>
                </select>

                <p id="roleLoading" class="text-sm text-gray-500 hidden mt-2">
                    Memuat role...
                </p>
            </section>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="/{{ $companyCode }}/pegawai"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                    Simpan Pegawai
                </button>
            </div>
        </form>
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
            branchSelect.disabled = universalToggle.checked;
            branchSelect.value = "";
            fetchRoles();
        });
    </script>

</x-app-layout>
