<x-app-layout :companyCode="$companyCode" :branchCode="$branchCode">

<main class="mx-auto max-w-6xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('branch.roles.index', [$companyCode, $branchCode]) }}"
               class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm">
                ←
            </a>

            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                        ➕
                    </span>
                    Tambah Role Baru (Cabang {{ $branch->name }})
                </h1>
                <p class="text-gray-600 mt-1">Atur hak akses untuk role di cabang ini</p>
            </div>
        </div>

        {{-- ALERTS --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                <p class="font-semibold mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc ml-5 text-sm space-y-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('branch.roles.store', [ $branchCode]) }}"
              class="space-y-6 bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            @csrf

            {{-- ROLE INFO --}}
            <section class="rounded-xl p-6 bg-gradient-to-br from-gray-50 to-white border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-5">
                    <span class="h-9 w-9 rounded-lg bg-purple-600 text-white grid place-items-center">🛡️</span>
                    Informasi Role
                </h2>

                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">Kode Role *</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full px-4 py-3 uppercase font-mono rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500"
                           placeholder="KASIR, GUDANG_ADMIN, WAITER" required>
                    <p class="text-xs text-gray-500 mt-1 ml-1">
                        Contoh: KASIR — nama internal akan dibuat otomatis.
                    </p>
                </div>
            </section>


            {{-- FIXED SCOPE (BRANCH ONLY) --}}
            <section class="rounded-xl p-6 bg-gradient-to-br from-blue-50 to-white border border-blue-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-4">
                    <span class="h-9 w-9 rounded-lg bg-blue-600 text-white grid place-items-center">📍</span>
                    Scope Role
                </h2>

                <p class="text-sm text-gray-700 font-medium">
                    Role ini otomatis terikat pada cabang:
                </p>

                <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-blue-100 border border-blue-200 text-blue-800 rounded-lg text-sm">
                    📍 <strong>{{ $branch->name }}</strong> ({{ $branch->code }})
                </div>

                {{-- Hidden input supaya controller tahu cabangnya --}}
                <input type="hidden" name="cabangRestoId" value="{{ $branch->id }}">
            </section>


            {{-- PERMISSIONS --}}
            <section class="rounded-xl p-6 bg-gradient-to-br from-amber-50 to-white border border-amber-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 mb-5">
                    <span class="h-9 w-9 rounded-lg bg-amber-600 text-white grid place-items-center">🔐</span>
                    Hak Akses
                </h2>

                <x-permission-builder :permissions="$permissions" :selected="[]" />
            </section>


            {{-- FOOTER --}}
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 shadow-lg">
                    💾 Simpan Role
                </button>
            </div>
        </form>

    </div>
</main>

</x-app-layout>
