<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Roles</h2>
            <p class="text-sm text-gray-500 mt-1">Atur peran dan hak akses pegawai</p>
        </div>


        <x-crud-add 
                        resource="roles"
                        :companyCode="$companyCode"
                        permissionPrefix="permission"
                    />
    </div>

    <div class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-gray-700">Role</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Scope</th>
                    <th class="px-6 py-4 text-right font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($roles as $r)
                <tr class="hover:bg-gray-50 transition-colors duration-150">

                    {{-- ROLE (pakai CODE, bukan NAME Spatie) --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($r->code, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ strtoupper($r->code) }}
                            </span>
                        </div>
                    </td>


                    {{-- SCOPE --}}
                    <td class="px-6 py-4 text-center">
                        @if(!$r->cabang_resto_id)
                            <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-emerald-50 rounded-full text-emerald-700 font-medium border border-emerald-200">
                                🌐 Universal
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-blue-50 rounded-full text-blue-700 font-medium border border-blue-200">
                                📍 {{ $r->cabangResto->name ?? 'Cabang' }}
                            </span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-6 py-4 text-right">
                        <a href="/{{ $companyCode }}/roles/{{ $r->code }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg
                                   hover:bg-emerald-100 transition-colors duration-150 font-medium text-sm">
                            👁️ Detail
                        </a>
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl">
                                    🛡️
                                </div>
                                <p class="text-gray-500 font-medium">Tidak ada role tersedia</p>
                                <p class="text-sm text-gray-400">Buat role pertama untuk mulai mengatur akses</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-app-layout>
