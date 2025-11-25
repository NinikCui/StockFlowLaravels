<x-app-layout>
    <main class="mx-auto max-w-6xl px-6 py-10 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

        <div class="max-w-5xl mx-auto">
            {{-- HEADER --}}
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('pegawai.index', $companyCode) }}?tab=roles"
                       class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
                        ←
                    </a>
                    
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                                ➕
                            </span>
                            Tambah Role Baru
                        </h1>
                        <p class="text-gray-600 mt-1">Atur akses dan hak pegawai berdasarkan jabatan atau cabang</p>
                    </div>
                </div>
            </div>

            {{-- SUCCESS --}}
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">✅</span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- ERROR --}}
            @if($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
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

            {{-- FORM --}}
            <form method="POST" action="{{ route('roles.store', $companyCode) }}"
                  class="space-y-6 bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
                @csrf

                {{-- === INFO ROLE === --}}
                <section class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xl">
                            🛡️
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Role</h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="mis. Supervisor Gudang"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kode Role <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                placeholder="mis. WAREHOUSE_SUPV"
                                required
                                class="w-full uppercase rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 font-mono"
                            >
                        </div>
                    </div>
                </section>

                {{-- === CABANG === --}}
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
                            <select
                                name="cabangRestoId"
                                id="cabangRestoId"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
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
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                            <input
                                type="checkbox"
                                name="isUniversal"
                                id="isUniversal"
                                value="1"
                                class="h-5 w-5 rounded text-emerald-600 focus:ring-emerald-500"
                                onchange="toggleCabangSelect()"
                                {{ old('isUniversal', true) ? 'checked' : '' }}
                            >
                            <label for="isUniversal" class="flex items-center gap-2 text-sm font-semibold text-gray-700 cursor-pointer">
                                <span>🌐</span>
                                <span>Universal (berlaku untuk semua cabang)</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-1">
                            *Centang jika role ini berlaku untuk semua cabang
                        </p>
                    </div>
                </section>

                {{-- === PERMISSIONS === --}}
                <section x-data="{ expanded: {} }" class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-6 border border-amber-100">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-xl">
                                🔐
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Hak Akses</h2>
                                <p class="text-sm text-gray-600">Pilih permission untuk role ini</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button"
                                onclick="checkAllPermissions(true)"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-all duration-200">
                                <span>✓</span>
                                <span>Pilih Semua</span>
                            </button>

                            <button type="button"
                                onclick="checkAllPermissions(false)"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition-all duration-200">
                                <span>✕</span>
                                <span>Hapus Semua</span>
                            </button>
                        </div>
                    </div>

                    {{-- GROUPS --}}
                    <div class="space-y-3">
@php
                        $PERMISSIONS = [
                            [
                                'key' => 'cabang',
                                'label' => 'Cabang',
                                'icon' => '🏢',
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
                                'icon' => '📦',
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
                                'icon' => '👥',
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
                                'icon' => '💰',
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
                                'icon' => '📊',
                                'items' => [
                                    ['key' => 'analytics.view', 'label' => 'Lihat dashboard'],
                                    ['key' => 'analytics.export', 'label' => 'Export laporan'],
                                ]
                            ],
                        ];
                    @endphp
                        @foreach($PERMISSIONS as $group)
                            <div x-data="{ open: false }"
                                 class="rounded-xl border border-gray-200 bg-white hover:shadow-sm transition-shadow duration-200 overflow-hidden">

                                {{-- Group Header --}}
                                <div class="flex items-center justify-between px-5 py-4 cursor-pointer bg-gradient-to-r from-gray-50 to-white hover:from-gray-100 hover:to-gray-50 transition-colors duration-200"
                                     @click="open = !open">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">{{ $group['icon'] }}</span>
                                        <span class="font-bold text-gray-900">{{ $group['label'] }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-medium">
                                            {{ count($group['items']) }} items
                                        </span>
                                    </div>

                                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                        :class="open ? 'rotate-180':''"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M6 9l6 6 6-6" />
                                    </svg>
                                </div>

                                {{-- Items --}}
                                <div x-show="open" 
                                     x-collapse
                                     class="border-t border-gray-200 p-4 bg-gray-50">
                                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($group['items'] as $item)
                                            <label class="group flex items-start gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm hover:border-emerald-200 hover:bg-emerald-50 transition-all duration-200 cursor-pointer">
                                                <input type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $item['key'] }}"
                                                    class="mt-0.5 h-5 w-5 rounded text-emerald-600 focus:ring-emerald-500"
                                                    {{ in_array($item['key'], old('permissions', [])) ? 'checked':'' }}
                                                >
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-semibold text-gray-900 leading-tight">
                                                        {{ $item['label'] }}
                                                    </div>
                                                    <code class="text-xs text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-md inline-block mt-1">
                                                        {{ $item['key'] }}
                                                    </code>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Counter --}}
                    <div class="mt-5 p-4 bg-white rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold text-gray-900" id="selectedCount">0</span> 
                            permission dipilih
                        </p>
                    </div>
                </section>

                {{-- FOOTER --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-6 border-t border-gray-200">
                    <div class="flex items-start gap-2 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <span class="text-blue-600">ℹ️</span>
                        <span>Pastikan kode role unik dan sesuai kebijakan perusahaan</span>
                    </div>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('pegawai.index', $companyCode) }}?tab=roles"
                            class="flex-1 sm:flex-initial px-6 py-3 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-center">
                            Batal
                        </a>

                        <button type="submit"
                            class="flex-1 sm:flex-initial px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transition-all duration-200">
                            💾 Simpan Role
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </main>

    <script>
        function checkAllPermissions(check) {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = check);
            updateCounter();
        }

        function toggleCabangSelect() {
            const isUniversal = document.getElementById('isUniversal').checked;
            const cabangSelect = document.getElementById('cabangRestoId');
            
            cabangSelect.disabled = isUniversal;
            if (isUniversal) {
                cabangSelect.value = "";
                cabangSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                cabangSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }

        function updateCounter() {
            const checked = document.querySelectorAll('input[name="permissions[]"]:checked').length;
            const counter = document.getElementById('selectedCount');
            if (counter) counter.textContent = checked;
        }

        // Initialize counter on page load and update on checkbox change
        document.addEventListener('DOMContentLoaded', () => {
            updateCounter();
            
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.addEventListener('change', updateCounter);
            });

            // Initialize cabang select state
            toggleCabangSelect();
        });
    </script>

</x-app-layout>