<x-app-layout>
<main class="mx-auto max-w-5xl p-8 min-h-screen">

    {{-- ===== HEADER ===== --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="/{{ $companyCode }}/pegawai/roles"
           class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 shadow-sm">
            ← Kembali
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Edit Role</h1>
    </div>

    <p class="text-sm text-gray-600 mb-8">
        Ubah nama, kode, dan hak akses untuk role ini.
    </p>

    {{-- ===== FORM ===== --}}
    <form action="{{ route('roles.update', [$companyCode, $role->code]) }}" 
          method="POST"
          class="space-y-8 bg-white/70 backdrop-blur p-8 rounded-2xl border shadow-sm">
        
        @csrf
        @method('PUT')

        {{-- ===== INFO ROLE ===== --}}
        <div class="grid sm:grid-cols-2 gap-6">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Role</label>
                <input name="code" value="{{ $role->code }}"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-emerald-200 focus:border-emerald-400">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Role</label>
                <input name="name" value="{{ $role->name }}"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-emerald-200 focus:border-emerald-400">
            </div>
        </div>

        {{-- ===== SCOPE ROLE ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-white p-5 shadow-sm">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Scope Role</label>

            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                <select id="cabangSelect" name="cabangRestoId"
                        class="rounded-xl border border-gray-300 px-3 py-2 text-sm flex-1">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" 
                            {{ $role->cabang_resto_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" id="universalCheck" name="isUniversal"
                           class="rounded accent-emerald-600"
                           {{ $role->cabang_resto_id === null ? "checked" : "" }}>
                    Universal (berlaku di semua cabang)
                </label>
            </div>
        </div>

        {{-- ===== PERMISSIONS ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-3">
                <div>
                    <h3 class="font-semibold text-gray-800">Hak Akses</h3>
                    <p class="text-xs text-gray-500">
                        Pilih hak akses untuk role ini.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAll(true)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 text-sm hover:bg-emerald-50">
                        Pilih semua
                    </button>

                    <button type="button" onclick="setAll(false)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 text-sm hover:bg-rose-50">
                        Hapus semua
                    </button>

                    <input id="filterBox"
                           oninput="filterPermissions()"
                           placeholder="Cari code/nama/desc…"
                           class="px-3 py-1.5 rounded-xl border border-gray-300 text-sm">
                </div>
            </div>

            {{-- Grid Permission --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2" id="permGrid">

                @foreach($permissions as $p)
                    <label class="permission-item flex items-start gap-2 rounded-xl border px-3 py-2 text-sm {{ $p['isGranted'] ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}"
                           data-key="{{ strtolower($p['code'].' '.$p['name'].' '.$p['description']) }}">
                        
                        <input type="checkbox" name="permissionIds[]" value="{{ $p['id'] }}"
                               class="mt-1 accent-emerald-600"
                               {{ $p['isGranted'] ? 'checked' : '' }}>

                        <div>
                            <div class="font-medium text-gray-800 truncate">{{ $p['name'] }}</div>
                            <div class="text-xs text-gray-500"><code>{{ $p['code'] }}</code></div>
                        </div>
                    </label>
                @endforeach

            </div>
        </div>

        {{-- ===== BUTTON SAVE ===== --}}
        <div class="flex justify-between items-center pt-3">
            <button type="submit"
                class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700 shadow">
                Simpan Perubahan
            </button>

            <span class="text-xs text-gray-500">
                Pastikan kode unik & sesuai kebijakan perusahaan.
            </span>
        </div>

    </form>
</main>

{{-- ===== JS SEARCH + SELECT ALL ===== --}}
<script>
function filterPermissions() {
    const val = document.getElementById('filterBox').value.toLowerCase();
    document.querySelectorAll('.permission-item').forEach(el => {
        const key = el.dataset.key;
        el.style.display = key.includes(val) ? '' : 'none';
    });
}

function setAll(value) {
    document.querySelectorAll('.permission-item input[type=checkbox]')
        .forEach(cb => cb.checked = value);
}

// === UNIVERSAL TOGGLE ===
document.addEventListener("DOMContentLoaded", () => {
    const universal = document.getElementById("universalCheck");
    const cabangSelect = document.getElementById("cabangSelect");

    function updateScope() {
        if (universal.checked) {
            cabangSelect.disabled = true;
            cabangSelect.value = ""; // reset cabang
        } else {
            cabangSelect.disabled = false;
        }
    }

    // First load
    updateScope();

    // When checkbox clicked
    universal.addEventListener("change", updateScope);
});
</script>

</x-app-layout>
