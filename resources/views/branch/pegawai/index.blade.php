<x-app-layout :branchCode="$branchCode">

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-8">

        {{-- BREADCRUMB --}}
        <div>
            <a href="{{ route('branch.dashboard', $branchCode) }}"
               class="inline-flex items-center text-sm text-gray-600 hover:text-emerald-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pegawai Cabang</h1>
                <p class="text-gray-600 mt-1">Kelola daftar pegawai untuk cabang ini</p>
            </div>

        <x-crud-add 
                        resource="branch.pegawai"
                        :companyCode="$companyCode"
                        permissionPrefix="employee"
                    />
        </div>

        {{-- SUCCESS ALERT --}}
        @if (session('success'))
            <div class="p-4 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif


        {{-- CARD LIST --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse($employees as $emp)

                <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex flex-col justify-between">

                    {{-- TOP --}}
                    <div class="space-y-2">

                        {{-- NAME --}}
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $emp->name ?? $emp->username }}
                        </h3>

                        {{-- USERNAME --}}
                        <p class="text-sm text-gray-700">Username:
                            <span class="font-medium">{{ $emp->username }}</span>
                        </p>

                        {{-- EMAIL --}}
                        <p class="text-sm text-gray-700">Email:
                            <span class="font-medium">{{ $emp->email ?? '-' }}</span>
                        </p>

                        {{-- ROLE --}}
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                            Role: {{ $emp->role_code }}
                        </span>

                        {{-- STATUS --}}
                        <div>
                            @if ($emp->is_active)
                                <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    <span class="w-2 h-2 rounded-full bg-red-600 mr-1"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- ACTIONS --}}
                    <div class="mt-5 flex items-center justify-between gap-3">

                        <x-crud 
                            resource="branch.pegawai"
                            :model="$emp"
                            :companyCode="$companyCode"
                            permissionPrefix="employee"
                            keyField="id"
                        />

                    </div>
                </div>

            @empty

                <div class="col-span-full text-center text-gray-500 py-12">
                    Belum ada pegawai di cabang ini.
                </div>

            @endforelse

        </div>

    </div>

</x-app-layout>
