<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    Detail Cabang
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap dan aktivitas cabang restoran.
                </p>
            </div>

            <a href="/{{ $companyCode }}/cabang"
               class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                ← Kembali
            </a>
        </div>

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- ========================= --}}
            {{-- 🔵 INFORMASI CABANG --}}
            {{-- ========================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

                <div class="flex items-start justify-between">
                    <h2 class="text-xl font-bold text-gray-900">
                        Informasi Cabang
                    </h2>

                    {{-- STATUS BADGE --}}
                    <span class="
                        px-3 py-1 text-xs font-semibold rounded-full
                        {{ $cabang->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}
                    ">
                        {{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">

                    <div class="space-y-1">
                        <p class="text-gray-500">Nama Cabang</p>
                        <p class="font-semibold text-gray-800">{{ $cabang->name }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Kode Cabang</p>
                        <p class="font-semibold text-gray-800">{{ $cabang->code }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Kota</p>
                        <p class="font-semibold text-gray-800">{{ $cabang->city }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Telepon</p>
                        <p class="font-semibold text-gray-800">{{ $cabang->phone ?? '-' }}</p>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-semibold text-gray-800 leading-relaxed">{{ $cabang->address }}</p>
                    </div>

                    {{-- MANAGER --}}
                    @if ($cabang->manager)
                        <div class="sm:col-span-2 space-y-1">
                            <p class="text-gray-500">Manager Cabang</p>
                            <span class="inline-flex items-center gap-2 font-semibold text-gray-800">
                                <span class="px-2 py-1 text-xs rounded-lg bg-emerald-100 text-emerald-700">
                                    MANAGER
                                </span>
                                {{ $cabang->manager->username }}
                                <span class="text-gray-400 text-xs">({{ $cabang->manager->email }})</span>
                            </span>
                        </div>
                    @endif

                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('cabang.edit', [$companyCode, $cabang->code]) }}"
                       class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium shadow hover:bg-emerald-700 transition">
                        ✎ Edit Cabang
                    </a>
                </div>
            </div>


            {{-- ========================= --}}
            {{-- 🟣 ROLE LIST --}}
            {{-- ========================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Role di Cabang Ini</h2>

                @if ($roles->isEmpty())
                    <p class="text-sm text-gray-500">Tidak ada role untuk cabang ini.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($roles as $r)
                            <li class="border border-gray-200 rounded-xl px-4 py-3 flex justify-between items-center text-sm bg-gray-50">
                                <span class="font-medium text-gray-700">{{ $r->name }}</span>
                                <a href="/{{ $companyCode }}/pegawai/roles/{{ $r->code }}"
                                   class="text-emerald-600 hover:text-emerald-700 font-medium">
                                    Detail →
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>


            {{-- ========================= --}}
            {{-- 🟤 PEGAWAI LIST --}}
            {{-- ========================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Pegawai di Cabang Ini</h2>

                @if ($pegawai->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada pegawai di cabang ini.</p>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($pegawai as $p)
                            <div class="border border-gray-200 bg-white shadow-sm rounded-xl p-4 relative">

                                {{-- Manager Badge --}}
                                @if ($cabang->manager_user_id === $p->id)
                                    <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-lg absolute top-3 right-3">
                                        Manager
                                    </span>
                                @endif

                                <p class="font-semibold text-gray-800">{{ $p->username }}</p>
                                <p class="text-sm text-gray-500">{{ $p->email }}</p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Role: {{ $p->role->name }}
                                </p>

                                <a href="/{{ $companyCode }}/pegawai/edit/{{ $p->id }}"
                                   class="text-emerald-600 text-sm mt-3 inline-block font-medium hover:text-emerald-700">
                                    Edit →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </main>
</x-app-layout>
