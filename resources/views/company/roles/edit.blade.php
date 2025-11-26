<x-app-layout>
<main class="mx-auto max-w-6xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('roles.show', [$companyCode, $role->code]) }}"
               class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition">
                ←
            </a>

            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                        ✎
                    </span>
                    Edit Role
                </h1>
                <p class="text-gray-600 mt-1">Ubah kode role, scope dan permissions</p>
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route('roles.update', [$companyCode, $role->code]) }}" method="POST"
              class="space-y-6 bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            @csrf
            @method('PUT')

            {{-- ERROR ALERT --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <p class="font-semibold mb-2">Terdapat kesalahan:</p>
                    <ul class="list-disc ml-4 space-y-1 text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- INFO ROLE --}}
            <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                        🛡️
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Role</h2>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode Role <span class="text-red-500">*</span>
                    </label>

                    <input name="code"
                           value="{{ old('code', $role->code) }}"
                           required
                           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 font-mono"
                           placeholder="e.g., MANAGER">
                </div>

                <p class="text-xs mt-2 text-gray-500 pl-1">
                    Nama role akan di-generate otomatis oleh sistem
                </p>
            </section>

            {{-- SCOPE --}}
            <section class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl">
                        📍
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Scope Role</h2>
                </div>

                <div class="space-y-4">

                    {{-- UNIVERSAL TOGGLE --}}
                    <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <input type="checkbox" id="universalCheck" name="isUniversal"
                               class="h-5 w-5 accent-emerald-600"
                               {{ old('isUniversal', $role->cabang_resto_id === null) ? 'checked' : '' }}>
                        <label for="universalCheck" class="text-sm font-semibold text-gray-700">
                            🌐 Universal (berlaku untuk semua cabang)
                        </label>
                    </div>

                    {{-- CABANG SELECT --}}
                    <select id="cabangSelect" name="cabangRestoId"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Cabang --</option>

                        @foreach($cabangList as $c)
                            <option value="{{ $c->id }}"
                                {{ old('cabangRestoId', $role->cabang_resto_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>

                </div>
            </section>

            {{-- PERMISSIONS --}}
            <section class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-6 border border-amber-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-xl">
                        🔐
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Hak Akses</h2>
                </div>

                <x-permission-builder :permissions="$permissions" :selected="$selected" />
            </section>

            {{-- ACTION BUTTONS --}}
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <a href="/{{ $companyCode }}/pegawai?tab=roles"
                   class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 shadow-lg">
                    💾 Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const universal = document.getElementById("universalCheck");
    const cabangSelect = document.getElementById("cabangSelect");

    function syncScope() {
        if (universal.checked) {
            cabangSelect.disabled = true;
            cabangSelect.value = "";
            cabangSelect.classList.add("bg-gray-100", "cursor-not-allowed");
        } else {
            cabangSelect.disabled = false;
            cabangSelect.classList.remove("bg-gray-100", "cursor-not-allowed");
        }
    }

    universal.addEventListener("change", syncScope);
    syncScope();
});
</script>

</x-app-layout>
