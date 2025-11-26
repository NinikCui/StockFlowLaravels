<x-app-layout>
<main class="mx-auto max-w-6xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    <div class="max-w-5xl mx-auto">
        {{-- ===== HEADER ===== --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('roles.show', [$companyCode, $role->code]) }}"
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
                    ←
                </a>
                
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                            ✎
                        </span>
                        Edit Role
                    </h1>
                    <p class="text-gray-600 mt-1">Ubah nama, kode, dan hak akses untuk role ini</p>
                </div>
            </div>
        </div>

        {{-- ===== FORM ===== --}}
        <form action="{{ route('roles.update', [$companyCode, $role->code]) }}" 
              method="POST"
              class="space-y-6 bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            
            @csrf
            @method('PUT')

            {{-- ERROR ALERT --}}
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

            {{-- ===== INFO ROLE ===== --}}
            <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                        🛡️
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Role</h2>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kode Role <span class="text-red-500">*</span>
                        </label>
                        <input name="code" value="{{ old('code', $role->code) }}" required
                               class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 font-mono"
                               placeholder="e.g., MANAGER">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Role <span class="text-red-500">*</span>
                        </label>
                        <input name="name" value="{{ old('name', $role->name) }}" required
                               class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="e.g., Manager Cabang">
                    </div>
                </div>
            </section>

            {{-- ===== SCOPE ROLE ===== --}}
            <section class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl">
                        📍
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Scope Role</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang Restoran</label>
                        <select id="cabangSelect" name="cabangRestoId"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangList as $c)
                                <option value="{{ $c->id }}" 
                                    {{ old('cabangRestoId', $role->cabang_resto_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                        <input type="checkbox" id="universalCheck" name="isUniversal"
                               class="h-5 w-5 accent-emerald-600 rounded"
                               {{ old('isUniversal', $role->cabang_resto_id === null) ? 'checked' : '' }}>
                        <label for="universalCheck" class="flex items-center gap-2 text-sm font-semibold text-gray-700 cursor-pointer">
                            <span>🌐</span>
                            <span>Universal (berlaku di semua cabang)</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-1">
                        *Centang jika role ini berlaku untuk semua cabang
                    </p>
                </div>
            </section>

            {{-- ===== PERMISSIONS ===== --}}
            <section class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-6 border border-amber-100">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-xl">
                            🔐
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Hak Akses</h2>
                            <p class="text-sm text-gray-600">Kelompokkan permission berdasarkan fitur</p>
                        </div>
                    </div>
                </div>

                {{-- FILTER + SELECT ALL --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="relative flex-1">
                        <input id="filterBox"
                            oninput="filterPermissions()"
                            placeholder="Cari permission..."
                            class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200">
                        <span class="absolute left-4 top-3.5 text-gray-400 text-lg">🔍</span>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="toggleAll(true)"
                                class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-all duration-200">
                            ✓ Pilih Semua
                        </button>

                        <button type="button" onclick="toggleAll(false)"
                                class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition-all duration-200">
                            ✕ Hapus Semua
                        </button>
                    </div>
                </div>

                {{-- GROUPED PERMISSION BUILDER --}}
                <x-permission-builder :permissions="$permissions" :selected="$selectedCodes" />

            </section>


            {{-- ===== BUTTON SAVE ===== --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-6 border-t border-gray-200">
                <div class="flex items-start gap-2 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <span class="text-blue-600">ℹ️</span>
                    <span>Pastikan kode role unik dan sesuai kebijakan perusahaan</span>
                </div>

                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="/{{ $companyCode }}/pegawai/roles"
                       class="flex-1 sm:flex-initial px-6 py-3 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-center">
                        Batal
                    </a>
                    
                    <button type="submit"
                        class="flex-1 sm:flex-initial px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transition-all duration-200">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>
    </div>
</main>

{{-- ===== JS SEARCH + SELECT ALL ===== --}}
<script>
function filterPermissions() {
    const val = document.getElementById('filterBox').value.toLowerCase();
    let visibleCount = 0;
    
    document.querySelectorAll('.permission-item').forEach(el => {
        const key = el.dataset.key;
        const isVisible = key.includes(val);
        el.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
}

function setAll(value) {
    document.querySelectorAll('.permission-item input[type=checkbox]').forEach(cb => {
        if (cb.closest('.permission-item').style.display !== 'none') {
            cb.checked = value;
            // Trigger visual update
            const parent = cb.parentElement;
            parent.classList.toggle('bg-gradient-to-br', value);
            parent.classList.toggle('from-emerald-50', value);
            parent.classList.toggle('to-emerald-100', value);
            parent.classList.toggle('border-emerald-200', value);
            parent.classList.toggle('bg-white', !value);
            parent.classList.toggle('border-gray-200', !value);
        }
    });
    updateCounter();
}

function updateCounter() {
    const checked = document.querySelectorAll('.permission-item input[type=checkbox]:checked').length;
    const counter = document.getElementById('selectedCount');
    if (counter) counter.textContent = checked;
}

// Update counter on checkbox change
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.permission-item input[type=checkbox]').forEach(cb => {
        cb.addEventListener('change', updateCounter);
    });
});

// === UNIVERSAL TOGGLE ===
document.addEventListener("DOMContentLoaded", () => {
    const universal = document.getElementById("universalCheck");
    const cabangSelect = document.getElementById("cabangSelect");

    function updateScope() {
        if (universal.checked) {
            cabangSelect.disabled = true;
            cabangSelect.value = "";
            cabangSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            cabangSelect.disabled = false;
            cabangSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }
    }

    // First load
    updateScope();

    // When checkbox clicked
    universal.addEventListener("change", updateScope);
});
</script>

</x-app-layout>