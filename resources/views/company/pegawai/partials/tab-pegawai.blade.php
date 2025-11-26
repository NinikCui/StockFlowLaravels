{{-- HEADER --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Pegawai</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola akun pegawai & peran mereka</p>
    </div>

    <div class="flex gap-3">
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 
                   text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all 
                   duration-200 font-medium">
            🔄 <span>Refresh</span>
        </button>

        <x-add-button 
            href="/{{ $companyCode }}/pegawai/tambah"
            text="+ Pegawai Baru"
            variant="primary"
        />
    </div>
</div>


{{-- FILTER --}}
<div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-2xl p-5 shadow-sm mb-6">

    <div class="flex flex-col sm:flex-row gap-4">

        {{-- SEARCH --}}
        <div class="relative flex-1">
            <input id="pegawaiSearch" type="text"
                class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl 
                       focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                placeholder="Cari username, telepon, atau role...">

            <span class="absolute left-4 top-3.5 text-gray-400 text-lg">🔍</span>
        </div>


        {{-- FILTER CABANG --}}
        <select id="pegawaiFilterBranch"
            class="px-4 py-3 text-sm border border-gray-200 rounded-xl bg-white font-medium 
                   focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">

            <option value="all">🏢 Semua Cabang</option>
            <option value="universal">🌐 Universal</option>

            @foreach ($roles->where('cabang_resto_id', '!=', null)->groupBy('cabang_resto_id') as $branchId => $group)
                <option value="{{ $group[0]->cabangResto->code }}">
                    📍 {{ $group[0]->cabangResto->name }}
                </option>
            @endforeach

        </select>


        {{-- FILTER STATUS --}}
        <select id="pegawaiFilterStatus"
            class="px-4 py-3 text-sm border border-gray-200 rounded-xl bg-white font-medium 
                   focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            <option value="all">📊 Semua Status</option>
            <option value="active">✅ Aktif</option>
            <option value="inactive">❌ Nonaktif</option>
        </select>

    </div>
</div>



{{-- GRID --}}
<div id="pegawaiGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($pegawai as $p)
        @include('company.pegawai.partials.pegawai-card', [
            'p' => $p,
            'companyCode' => $companyCode
        ])
    @endforeach
</div>


{{-- FILTERING SCRIPT --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("pegawaiSearch");
    const branchFilter = document.getElementById("pegawaiFilterBranch");
    const statusFilter = document.getElementById("pegawaiFilterStatus");
    const cards = document.querySelectorAll(".pegawai-card");

    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        const branch = branchFilter.value;
        const status = statusFilter.value;

        cards.forEach(card => {

            const name = card.dataset.username.toLowerCase();
            const phone = card.dataset.phone.toLowerCase();
            const role = card.dataset.rolecode.toLowerCase();
            const cardBranch = card.dataset.branchcode;
            const isActive = card.dataset.active === "1";

            let show = true;

            // Search filter
            if (search && !(
                name.includes(search) ||
                phone.includes(search) ||
                role.includes(search)
            )) {
                show = false;
            }

            // Branch filter
            if (branch !== "all") {
                if (branch === "universal" && cardBranch !== "") {
                    show = false;
                } else if (branch !== "universal" && cardBranch !== branch) {
                    show = false;
                }
            }

            // Status filter
            if (status !== "all") {
                if (status === "active" && !isActive) show = false;
                if (status === "inactive" && isActive) show = false;
            }

            card.style.display = show ? "" : "none";
        });
    }

    searchInput.addEventListener("input", applyFilters);
    branchFilter.addEventListener("change", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
});
</script>
