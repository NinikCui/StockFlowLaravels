<x-app-layout>
    <main class="min-h-screen px-6 py-10">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    Edit Pegawai
                </h1>
                <p class="text-sm text-gray-500 mt-1">Ubah informasi pegawai.</p>
            </div>

            <a href="/{{ strtolower($companyCode) }}/pegawai"
               class="text-sm text-gray-600 hover:text-gray-800 underline">
                ← Kembali
            </a>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('pegawai.update', [strtolower($companyCode), $pegawai->id]) }}"
              class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

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
                        <input type="text" name="username" value="{{ $pegawai->username }}" required
                            class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ $pegawai->email }}" required
                            class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Telepon</label>
                        <input type="text" name="phone" value="{{ $pegawai->phone }}"
                            class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />
                    </div>

                    
                </div>
            </section>

            {{-- CABANG --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Pilih Cabang</h2>

                <div class="flex items-center gap-3">
                    <select name="branch_id" id="branchSelect"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 {{ $isUniversal ? 'bg-gray-100' : '' }}"
                        {{ $isUniversal ? 'disabled' : '' }}>
                        <option value="">-- Pilih Cabang --</option>

                        @foreach ($branches as $c)
                            <option value="{{ $c->id }}"
                                {{ optional($pegawai->role->cabangResto)->id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_universal" id="universalToggle"
                            class="h-4 w-4 accent-emerald-600"
                            {{ $isUniversal ? 'checked' : '' }}>
                        Universal
                    </label>
                </div>
            </section>

            {{-- ROLE --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Pilih Role</h2>

                <select name="role_id" id="roleSelect"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                    <option value="">-- Pilih Role --</option>

                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}" {{ $pegawai->roles_id == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} ({{ $r->code }})
                        </option>
                    @endforeach
                </select>

                <p id="roleLoading" class="text-sm text-gray-500 hidden">Memuat role...</p>
            </section>

            {{-- STATUS --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Status Akun</h2>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active"
                        class="h-4 w-4 accent-emerald-600"
                        {{ $pegawai->is_active ? 'checked' : '' }}>
                    Aktifkan Pegawai
                </label>
            </section>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="/{{ strtolower($companyCode) }}/pegawai"
                    class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </main>

    {{-- AJAX --}}
    <script>
        const universalToggle = document.getElementById('universalToggle');
        const branchSelect = document.getElementById('branchSelect');
        const roleSelect = document.getElementById('roleSelect');
        const roleLoading = document.getElementById('roleLoading');

        function fetchRoles() {
            let universal = universalToggle.checked;
            let branch = branchSelect.value;

            roleLoading.classList.remove("hidden");
            roleSelect.innerHTML = `<option value="">-- Pilih Role --</option>`;

            fetch(`/{{ strtolower($companyCode) }}/pegawai/roles-json?${universal ? 'universal=true' : 'cabangId=' + branch}`)
                .then(res => res.json())
                .then(res => {
                    res.data.forEach(r => {
                        roleSelect.innerHTML += `<option value="${r.id}">${r.name} (${r.code})</option>`;
                    });
                })
                .finally(() => roleLoading.classList.add("hidden"));
        }

        branchSelect.addEventListener('change', fetchRoles);

        universalToggle.addEventListener('change', () => {
            branchSelect.disabled = universalToggle.checked;
            if (universalToggle.checked) branchSelect.value = "";
            fetchRoles();
        });
    </script>
</x-app-layout>
