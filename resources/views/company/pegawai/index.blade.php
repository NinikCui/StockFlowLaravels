<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-8">

 

    {{-- TAB WRAPPER --}}
    <div x-data="{ tab: '{{ request()->get('tab','pegawai') }}' }" class="mb-8">

       

        {{-- TAB PEGAWAI --}}
        <div x-show="tab==='pegawai'" x-transition>
            @include('company.pegawai.partials.tab-pegawai', [
                'companyCode' => $companyCode,
                'pegawai' => $pegawai
            ])
        </div>


    </div>

</div>


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