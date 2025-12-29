<x-app-layout :companyCode="$companyCode" :branchCode="$branchCode">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Roles Cabang</h2>
            <p class="text-sm text-gray-500 mt-1">
                Atur peran dan hak akses pegawai untuk cabang {{ $branch->name }}
            </p>
        </div>

        {{-- Tombol Add --}}
        <x-crud-add 
            resource="branch.roles"
            :companyCode="$companyCode"
            :branchCode="$branchCode"
            permissionPrefix="permission"
        />
    </div>


    {{-- TABLE --}}
    <div class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-gray-700">Role</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Cabang</th>
                    <th class="px-6 py-4 text-right font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                
                @forelse($roles as $r)
                <tr class="hover:bg-gray-50 transition-colors duration-150">

                    {{-- ROLE --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 
                                        flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($r->code, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ strtoupper($r->code) }}
                            </span>
                        </div>
                    </td>

                    {{-- CABANG (Branch Scope → Selalu cabang tertentu) --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 
                                     bg-blue-50 rounded-full text-blue-700 font-medium border border-blue-200">
                            📍 {{ $branch->name }}
                        </span>
                    </td>

                    {{-- AKSI --}}
                    <td class="px-6 py-4 text-right">

                        {{-- SHOW --}}
                        

                        @if(strtoupper($r->code) !== 'OWNER')
                            <a href="{{ route('branch.roles.show', [
                                'branchCode'  => $branchCode,
                                'code'        => $r->code
                            ]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 
                                text-emerald-700 rounded-lg hover:bg-emerald-100 
                                transition-colors duration-150 font-medium text-sm">
                                👁️ Detail
                            </a>
                        @else
                            <span class="inline-flex items-center px-4 py-2 text-sm text-gray-400 italic">
                                Sistem
                            </span>
                        @endif
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl">
                                🛡️
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada role untuk cabang ini</p>
                            <p class="text-sm text-gray-400">Buat role pertama untuk mulai mengatur akses pegawai</p>
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</x-app-layout>
