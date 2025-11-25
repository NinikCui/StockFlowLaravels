<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Pegawai & Roles</h1>
        <p class="text-gray-600">
            Kelola seluruh pegawai dan role dalam satu halaman terpadu.
        </p>
    </div>

    {{-- TAB WRAPPER --}}
    <div x-data="{ tab: '{{ request()->get('tab','pegawai') }}' }" class="mb-8">

        {{-- TAB BUTTONS - Enhanced --}}
        <div class="flex gap-2 bg-gray-100 p-1.5 rounded-xl mb-8 w-fit">
            <button @click="tab='pegawai'"
                class="px-6 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200"
                :class="tab==='pegawai'
                    ? 'bg-white text-emerald-700 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'">
                <span class="flex items-center gap-2">
                    <span>👥</span>
                    <span>Pegawai</span>
                </span>
            </button>

            <button @click="tab='roles'"
                class="px-6 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200"
                :class="tab==='roles'
                    ? 'bg-white text-emerald-700 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'">
                <span class="flex items-center gap-2">
                    <span>🛡️</span>
                    <span>Roles</span>
                </span>
            </button>
        </div>

        {{-- TAB PEGAWAI --}}
        <div x-show="tab==='pegawai'" x-transition>
            @include('company.pegawai.partials.tab-pegawai', [
                'companyCode' => $companyCode,
                'pegawai' => $pegawai
            ])
        </div>

        {{-- TAB ROLES --}}
        <div x-show="tab==='roles'" x-cloak x-transition>
            @include('company.pegawai.partials.tab-roles', [
                'companyCode' => $companyCode,
                'roles' => $roles
            ])
        </div>

    </div>

</div>


{{-- DELETE MODAL --}}
@include('company.pegawai.partials.modal-delete', ['companyCode' => $companyCode])

<script>
    const pegawaiSearch = document.getElementById("pegawaiSearch");
const pegawaiFilterBranch = document.getElementById("pegawaiFilterBranch");
const pegawaiFilterStatus = document.getElementById("pegawaiFilterStatus");

function applyPegawaiFilters() {
    const s = pegawaiSearch.value.toLowerCase();
    const b = pegawaiFilterBranch.value;
    const st = pegawaiFilterStatus.value;

    document.querySelectorAll('.pegawai-item').forEach(el => {
        const match =
            el.dataset.search.includes(s) &&
            (b === "all" || el.dataset.branch === b) &&
            (st === "all" || el.dataset.status === st);

        el.style.display = match ? "" : "none";
    });
}

if (pegawaiSearch) pegawaiSearch.oninput = applyPegawaiFilters;
if (pegawaiFilterBranch) pegawaiFilterBranch.onchange = applyPegawaiFilters;
if (pegawaiFilterStatus) pegawaiFilterStatus.onchange = applyPegawaiFilters;

function openDeletePegawai(id) {
    document.getElementById("pegawaiDeleteForm").action =
        `${window.location.pathname}/${id}`;
    document.getElementById("pegawaiDeleteModal").classList.remove("hidden");
    document.getElementById("pegawaiDeleteModal").classList.add("flex");
}

function closeDeletePegawai() {
    document.getElementById("pegawaiDeleteModal").classList.add("hidden");
    document.getElementById("pegawaiDeleteModal").classList.remove("flex");
}
</script>

</x-app-layout>