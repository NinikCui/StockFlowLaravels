<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Material Request Antar Cabang</h1>
                    <p class="text-sm text-gray-600">Kelola dan pantau semua permintaan transfer bahan antar cabang.</p>
                </div>
            </div>

            <x-crud-add 
                        resource="request"
                        :companyCode="$companyCode"
                        permissionPrefix="transfer"
                    />
        </div>
    </div>

    {{-- FILTER UNTUK OWNER --}}
    <form method="GET" class="mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Filter Cabang Asal --}}
                <div>
                    <label class="text-xs font-medium text-gray-600">Cabang Asal</label>
                    <select name="from" class="mt-1 w-full border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('from') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Cabang Tujuan --}}
                <div>
                    <label class="text-xs font-medium text-gray-600">Cabang Tujuan</label>
                    <select name="to" class="mt-1 w-full border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('to') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div>
                    <label class="text-xs font-medium text-gray-600">Status</label>
                    <select name="status" class="mt-1 w-full border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        <option value="REQUESTED" {{ request('status')=='REQUESTED' ? 'selected' : '' }}>REQUESTED</option>
                        <option value="APPROVED" {{ request('status')=='APPROVED' ? 'selected' : '' }}>APPROVED</option>
                        <option value="IN_TRANSIT" {{ request('status')=='IN_TRANSIT' ? 'selected' : '' }}>IN TRANSIT</option>
                        <option value="RECEIVED" {{ request('status')=='RECEIVED' ? 'selected' : '' }}>RECEIVED</option>
                        <option value="CANCELLED" {{ request('status')=='CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                    </select>
                </div>

                {{-- Date --}}
                <div>
                    <label class="text-xs font-medium text-gray-600">Tanggal</label>
                    <input type="date" name="date"
                           value="{{ request('date') }}"
                           class="mt-1 w-full border-gray-300 rounded-lg">
                </div>

            </div>

            <div class="flex justify-end mt-4">
                <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-black">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    {{-- TABLE CARD --}}
    @include('company.request-cabang.partials.table', ['requests' => $requests])

</div>
</x-app-layout>
