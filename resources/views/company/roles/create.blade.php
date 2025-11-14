<x-app-layout>
    <main class="mx-auto max-w-5xl p-8 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                Tambah Role Baru
            </h1>
            <p class="text-gray-500 mt-1 text-sm">
                Atur akses dan hak pegawai berdasarkan jabatan atau cabang.
            </p>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-700 border border-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- CARD --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            {{-- CARD HEADER --}}
            <div class="bg-gray-50 px-6 py-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    Detail Role
                </h2>
                <p class="text-gray-500 text-sm">
                    Masukkan informasi dasar dan hak akses untuk role baru.
                </p>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('roles.store', $companyCode) }}">
                @csrf

                <div class="p-6 sm:p-8 space-y-10">

                    {{-- === INFO ROLE === --}}
                    <section class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="text-gray-700 font-medium">Nama Role</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="mis. Supervisor Gudang"
                                class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2
                                       focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 transition"
                            >
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Kode Role</label>
                            <input
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                placeholder="mis. WAREHOUSE_SUPV"
                                class="mt-1 w-full uppercase rounded-xl border border-gray-300 bg-white px-3 py-2
                                       focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 transition"
                            >
                        </div>
                    </section>

                    {{-- === CABANG === --}}
                    <section class="space-y-3">
                        <label class="text-gray-700 font-medium">Cabang Resto</label>

                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                            <select
                                name="cabangRestoId"
                                id="cabangRestoId"
                                class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 transition"
                                @if(old('isUniversal', true)) disabled @endif
                            >
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangList as $c)
                                    <option
                                        value="{{ $c->id }}"
                                        {{ old('cabangRestoId') == $c->id ? 'selected':'' }}
                                    >
                                        {{ $c->name }} ({{ $c->code }})
                                    </option>
                                @endforeach
                            </select>

                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    name="isUniversal"
                                    value="1"
                                    class="rounded text-emerald-600 focus:ring-emerald-500"
                                    onchange="document.getElementById('cabangRestoId').disabled = this.checked"
                                    {{ old('isUniversal', true) ? 'checked' : '' }}
                                >
                                Berlaku untuk semua cabang
                            </label>
                        </div>
                    </section>

                    {{-- === PERMISSIONS === --}}
                    <section x-data="{ expanded: {} }" class="space-y-5">

                        {{-- Header --}}
                        <div class="flex justify-between items-center">
                            <h3 class="text-gray-800 font-medium">Hak Akses</h3>
                            <div class="flex gap-2">
                                <button type="button"
                                    onclick="checkAllPermissions(true)"
                                    class="px-3 py-1.5 rounded-xl border border-gray-300 text-gray-600 text-sm hover:bg-gray-100">
                                    Pilih Semua
                                </button>

                                <button type="button"
                                    onclick="checkAllPermissions(false)"
                                    class="px-3 py-1.5 rounded-xl border border-gray-300 text-gray-600 text-sm hover:bg-gray-100">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>

                        {{-- GROUPS --}}
                        <div class="space-y-4">
@php
                                $PERMISSIONS = [
                                    [
                                        'key' => 'cabang',
                                        'label' => 'Cabang',
                                        'items' => [
                                            ['key' => 'branch.view', 'label' => 'Lihat cabang'],
                                            ['key' => 'branch.create', 'label' => 'Tambah cabang'],
                                            ['key' => 'branch.update', 'label' => 'Edit cabang'],
                                            ['key' => 'branch.deactivate', 'label' => 'Nonaktifkan cabang'],
                                        ]
                                    ],
                                    [
                                        'key' => 'gudang',
                                        'label' => 'Gudang & Stok',
                                        'items' => [
                                            ['key' => 'warehouse.view', 'label' => 'Lihat gudang'],
                                            ['key' => 'warehouse.manage', 'label' => 'Kelola gudang'],
                                            ['key' => 'inventory.view', 'label' => 'Lihat persediaan'],
                                            ['key' => 'inventory.adjust', 'label' => 'Penyesuaian persediaan'],
                                            ['key' => 'inventory.transfer', 'label' => 'Transfer antar gudang'],
                                        ]
                                    ],
                                    [
                                        'key' => 'pegawai',
                                        'label' => 'Pegawai',
                                        'items' => [
                                            ['key' => 'employee.view', 'label' => 'Lihat pegawai'],
                                            ['key' => 'employee.create', 'label' => 'Tambah pegawai'],
                                            ['key' => 'employee.update', 'label' => 'Edit pegawai'],
                                            ['key' => 'role.manage', 'label' => 'Kelola role & akses'],
                                        ]
                                    ],
                                    [
                                        'key' => 'pos',
                                        'label' => 'POS & Penjualan',
                                        'items' => [
                                            ['key' => 'pos.open_shift', 'label' => 'Buka shift'],
                                            ['key' => 'pos.order', 'label' => 'Transaksi penjualan'],
                                            ['key' => 'pos.refund', 'label' => 'Refund'],
                                            ['key' => 'pos.close_shift', 'label' => 'Tutup shift'],
                                        ]
                                    ],
                                    [
                                        'key' => 'analytics',
                                        'label' => 'Analytics',
                                        'items' => [
                                            ['key' => 'analytics.view', 'label' => 'Lihat dashboard'],
                                            ['key' => 'analytics.export', 'label' => 'Export laporan'],
                                        ]
                                    ],
                                ];
                            @endphp
                            @foreach($PERMISSIONS as $group)
                                <div x-data="{ open:false }"
                                     class="rounded-xl border border-gray-200 bg-white hover:shadow-sm transition">

                                    {{-- Group Header --}}
                                    <div class="flex items-center justify-between px-4 py-3 cursor-pointer"
                                         @click="open = !open">
                                        <span class="font-medium text-gray-800">
                                            {{ $group['label'] }}
                                        </span>

                                        <svg class="h-4 w-4 text-gray-500 transition-transform duration-200"
                                            :class="open ? 'rotate-180':''"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 9l6 6 6-6" />
                                        </svg>
                                    </div>

                                    {{-- Items --}}
                                    <div x-show="open" x-collapse
                                        class="border-t border-gray-200 px-4 py-3 bg-gray-50 grid gap-3 sm:grid-cols-2 md:grid-cols-3">

                                        @foreach($group['items'] as $item)
                                            <label class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm hover:bg-emerald-50 transition">
                                                <input type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $item['key'] }}"
                                                    class="rounded text-emerald-600 focus:ring-emerald-500"
                                                    {{ in_array($item['key'], old('permissions', [])) ? 'checked':'' }}
                                                >
                                                <span class="text-gray-800">{{ $item['label'] }}</span>

                                                <code class="ml-auto text-xs text-gray-500 bg-white px-1 py-0.5 border rounded">
                                                    {{ $item['key'] }}
                                                </code>
                                            </label>
                                        @endforeach

                                    </div>
                                </div>
                            @endforeach

                        </div>

                    </section>

                </div>

                {{-- FOOTER --}}
                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <a href="/{{ $companyCode }}/pegawai/roles"
                        class="px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm shadow hover:bg-emerald-700">
                        Simpan
                    </button>
                </div>

            </form>

        </div>

    </main>

    <script>
        function checkAllPermissions(check) {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = check);
        }
    </script>

</x-app-layout>
