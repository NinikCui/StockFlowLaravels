<x-app-layout>
<main class="min-h-screen px-6 py-10 bg-gradient-to-br from-gray-50 to-gray-100">

    <div class="max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Tambah Pegawai Baru</h1>
                <p class="text-gray-600">Isi informasi berikut untuk menambahkan pegawai ke sistem.</p>
            </div>

               <a href={{ route('pegawai.index', ['companyCode' => $companyCode]) }}
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 
                      hover:bg-gray-50 hover:border-gray-300 shadow-sm transition">
                ← Kembali
            </a>
        </div>

        {{-- FORM --}}
        <form method="POST" action="/{{ $companyCode }}/pegawai"
              class="space-y-6 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">

            @csrf

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <p class="font-semibold mb-2">Terdapat kesalahan:</p>
                    <ul class="list-disc ml-4 text-sm space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ============================
                INFORMASI AKUN
            ============================ --}}
            <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-5">
                    👤 Informasi Akun
                </h2>

                <div class="grid sm:grid-cols-2 gap-5">

                    {{-- username --}}
                    <div>
                        <label class="text-sm font-semibold mb-2 block">Username *</label>
                        <input type="text" name="username" required value="{{ old('username') }}"
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm 
                                      focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- email --}}
                    <div>
                        <label class="text-sm font-semibold mb-2 block">Email *</label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm 
                                      focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- phone --}}
                    <div>
                        <label class="text-sm font-semibold mb-2 block">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm 
                                      focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- password --}}
                    <div>
                        <label class="text-sm font-semibold mb-2 block">Password *</label>
                        <input type="password" name="password" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm 
                                      focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- confirm --}}
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold mb-2 block">Konfirmasi Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm 
                                      focus:ring-2 focus:ring-emerald-500">
                    </div>

                </div>
            </section>

            {{-- ============================
                CABANG
            ============================ --}}
            <section class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-5">
                    🏢 Cabang
                </h2>

                <div class="space-y-4">

                    {{-- Select Branch --}}
                    <div>
                        <label class="text-sm font-semibold mb-2 block">Cabang Restoran</label>
                        <select name="branch_id" id="branchSelect"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm bg-white 
                                   focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach ($branches as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Universal --}}
                    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <input type="checkbox" id="universalToggle" class="h-5 w-5 accent-emerald-600">
                        <label for="universalToggle" class="text-sm font-semibold cursor-pointer">
                            🌐 Universal (Akses semua cabang)
                        </label>
                    </div>
                </div>
            </section>

            {{-- ============================
                ROLE
            ============================ --}}
            <section id="roleSection"
                     class="hidden bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 border border-purple-100">

                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-5">
                    🛡️ Pilih Role
                </h2>

                <label class="text-sm font-semibold mb-2 block">Role Pegawai *</label>

                <select name="role_id" id="roleSelect"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm bg-white 
                           focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Role --</option>
                </select>

                <div id="roleLoading"
                     class="hidden mt-3 flex items-center gap-2 text-sm text-gray-500">
                    <div class="animate-spin h-4 w-4 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
                    Memuat role...
                </div>
            </section>

            {{-- ============================
                ACTION
            ============================ --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                   <a href={{ route('pegawai.index', ['companyCode' => $companyCode]) }}

                   class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold 
                               hover:bg-emerald-700 shadow-lg transition">
                    💾 Simpan Pegawai
                </button>
            </div>

        </form>
    </div>
</main>

<script>
const branchSelect = document.getElementById("branchSelect");
const universalToggle = document.getElementById("universalToggle");
const roleSection = document.getElementById("roleSection");
const roleSelect = document.getElementById("roleSelect");
const roleLoading = document.getElementById("roleLoading");

// Fetch roles
function loadRoles() {
    const branch = branchSelect.value;
    const universal = universalToggle.checked;

    if (!branch && !universal) {
        roleSection.classList.add("hidden");
        return;
    }

    roleSection.classList.remove("hidden");
    roleLoading.classList.remove("hidden");
    roleSelect.innerHTML = `<option value="">-- Pilih Role --</option>`;

    let query = universal
        ? "universal=true"
        : `cabangId=${branch}`;

    fetch(`/{{ $companyCode }}/pegawai/roles-json?${query}`)
        .then(res => res.json())
        .then(res => {
            if (res.ok) {
                res.data.forEach(r => {
                    roleSelect.innerHTML += `<option value="${r.id}">${r.code}</option>`;
                });
            }
        })
        .finally(() => roleLoading.classList.add("hidden"));
}

// Events
branchSelect.addEventListener("change", loadRoles);
universalToggle.addEventListener("change", () => {
    branchSelect.disabled = universalToggle.checked;
    branchSelect.classList.toggle("bg-gray-100", universalToggle.checked);
    branchSelect.value = "";
    loadRoles();
});
</script>

</x-app-layout>
