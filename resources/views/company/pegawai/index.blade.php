<x-app-layout>
    <main class="p-8 min-h-screen">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Pegawai</h1>
                <p class="text-sm text-gray-500">Kelola data pegawai beserta peran dan cabangnya.</p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Refresh --}}
                <button onclick="window.location.reload()"
                    class="px-4 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 shadow-sm">
                    🔄 Muat Ulang
                </button>

                <x-add-button 
                        href="/pegawai/tambah"
                        text="+ Tambah Pegawai"
                        variant="primary"
                    />
            </div>
        </div>

        {{-- SEARCH & FILTER BAR --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">

            {{-- Search --}}
            <div class="relative flex-1 w-full">
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                <input
                    id="searchBox"
                    type="text"
                    placeholder="Cari nama, telepon, role, atau cabang..."
                    class="w-full pl-10 pr-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-200">
            </div>

            {{-- Filter Cabang --}}
            <select id="filterBranch"
                class="rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                <option value="all">Semua Cabang</option>
                <option value="universal">Universal</option>

                @foreach ($pegawai->where('branch_code','!=',null)->groupBy('branch_code') as $code => $items)
                    <option value="{{ $code }}">
                        {{ $items[0]['branch_name'] }} ({{ $code }})
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select id="filterStatus"
                class="rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>

        {{-- EMPTY STATE --}}
        @if ($pegawai->count() == 0)
            <div class="rounded-2xl border border-dashed bg-white/80 shadow-sm p-10 text-center text-gray-500 text-sm">
                Tidak ada pegawai.
            </div>
        @endif

        {{-- GRID LIST --}}
        <div id="pegawaiGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($pegawai as $p)
                <div class="pegawai-item bg-white border rounded-2xl shadow-sm p-5 transition-all"
                    data-search="{{ strtolower($p['username'].' '.$p['phone'].' '.$p['role_name'].' '.$p['branch_name']) }}"
                    data-branch="{{ $p['branch_code'] ?? 'universal' }}"
                    data-status="{{ $p['is_active'] ? 'active' : 'inactive' }}">

                    {{-- HEADER --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <span class="text-emerald-700">👤</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $p['username'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $p['phone'] ?: '-' }}</p>
                        </div>
                    </div>

                    {{-- ROLE --}}
                    <div class="mb-3">
                        <p class="text-xs text-gray-500">Peran</p>

                        @if ($p['role_name'])
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-medium">{{ $p['role_name'] }}</span>
                                <code class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-xs">{{ $p['role_code'] }}</code>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">Belum ditentukan</span>
                        @endif
                    </div>

                    {{-- CABANG --}}
                    <div class="mb-3">
                        <p class="text-xs text-gray-500">Cabang</p>

                        @if ($p['branch_name'])
                            <span class="text-sm">{{ $p['branch_name'] }}</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                Universal
                            </span>
                        @endif
                    </div>

                    {{-- STATUS + ACTION --}}
                    <div class="flex justify-between items-center mt-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $p['is_active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $p['is_active'] ? 'Aktif' : 'Nonaktif' }}
                        </span>

                        <div class="flex items-center gap-2">
                            {{-- EDIT --}}
                            <a href="/{{ strtolower($companyCode) }}/pegawai/edit/{{ $p['id'] }}"
                                class="text-emerald-600 hover:text-emerald-700">✎</a>

                            {{-- DELETE --}}
                            <button onclick="openDelete({{ $p['id'] }})"
                                class="text-red-600 hover:text-red-700">🗑</button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>


        {{-- DELETE MODAL --}}
        <div id="deleteModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-lg">
                <h2 class="font-semibold text-lg mb-2">Hapus Pegawai?</h2>
                <p class="text-gray-500 mb-5">Aksi ini tidak dapat dibatalkan.</p>

                <div class="flex justify-end gap-3">
                    <button onclick="closeDelete()"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Batal</button>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-rose-600 text-white hover:bg-rose-700">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>


    {{-- SCRIPT --}}
    <script>
        // === SEARCH + FILTER LOGIC ===
        const searchBox = document.getElementById("searchBox");
        const filterBranch = document.getElementById("filterBranch");
        const filterStatus = document.getElementById("filterStatus");
        const items = document.querySelectorAll(".pegawai-item");

        function applyFilters() {
            const search = searchBox.value.toLowerCase();
            const branch = filterBranch.value;
            const status = filterStatus.value;

            items.forEach(el => {
                const matchSearch = el.dataset.search.includes(search);
                const matchBranch = branch === "all" || el.dataset.branch === branch;
                const matchStatus = status === "all" || el.dataset.status === status;

                el.style.display = (matchSearch && matchBranch && matchStatus) ? "" : "none";
            });
        }

        searchBox.addEventListener("input", applyFilters);
        filterBranch.addEventListener("change", applyFilters);
        filterStatus.addEventListener("change", applyFilters);


        // === DELETE MODAL ===
        let deleteId = null;

        function openDelete(id) {
            deleteId = id;
            document.getElementById("deleteForm").action =
                `/{{ strtolower($companyCode) }}/pegawai/${id}`;
            document.getElementById("deleteModal").classList.remove("hidden");
        }

        function closeDelete() {
            deleteId = null;
            document.getElementById("deleteModal").classList.add("hidden");
        }
    </script>

</x-app-layout>
