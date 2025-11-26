@php
    $tab = request()->query('tab', 'info');
@endphp

<x-app-layout>
<main class="min-h-screen px-6 py-10 bg-gray-50">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                Detail Cabang — {{ $cabang->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Informasi lengkap dan aktivitas cabang restoran.
            </p>
        </div>

        <a href="/{{ strtolower($companyCode) }}/cabang"
           class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
            ← Kembali
        </a>
    </div>


    <div class="max-w-5xl mx-auto">

        {{-- ================= TAB HEADER ================= --}}
        <x-tab-header
            :tabs="[
                'info'    => 'Informasi Cabang',
                'roles'   => 'Role Cabang',
                'pegawai' => 'Pegawai Cabang'
            ]"
            :active="$tab"
            baseUrl="/{{ strtolower($companyCode) }}/cabang/{{ strtolower($cabang->code) }}"
        />

        {{-- ================= TAB CONTENT ================= --}}
        @switch($tab)

            @case('info')
                @include('company.cabang.partials.info')
                @break

            @case('roles')
                @include('company.cabang.partials.roles')
                @break

            @case('pegawai')
                @include('company.cabang.partials.pegawai')
                @break

            @default
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    Tab tidak dikenal.
                </div>

        @endswitch

    </div>

</main>
</x-app-layout>
