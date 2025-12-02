<x-app-layout :branchCode="$branchCode">

<div x-data="{ tab: @js(request('tab') ?? 'receiver') }">

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                {{-- LEFT SECTION --}}
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 
                                flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">Transfer Antar Gudang</h1>
                        <p class="text-sm text-gray-600">Kelola dan pantau perpindahan stok antar gudang dalam cabang ini.</p>
                    </div>
                </div>

                {{-- ADD BUTTON – ONLY SHOW ON RECEIVER TAB --}}
                <div x-show="tab === 'receiver'" x-transition>
                    <x-crud-add 
                        resource="branch.request"
                        :companyCode="$branchCode"
                        permissionPrefix="inventory"
                    />
                </div>

            </div>
        </div>

        {{-- TAB HEADER --}}
        <div class="flex gap-6 border-b mb-6 pb-1 text-sm font-semibold">

            <button class="pb-2"
                :class="tab === 'receiver'
                    ? 'text-emerald-600 border-b-2 border-emerald-600'
                    : 'text-gray-500'"
                @click="tab = 'receiver'">
                Sebagai Penerima
            </button>

            <button class="pb-2"
                :class="tab === 'sender'
                    ? 'text-emerald-600 border-b-2 border-emerald-600'
                    : 'text-gray-500'"
                @click="tab = 'sender'">
                Sebagai Pengirim
            </button>

        </div>

        {{-- FILTERS --}}
        @include('branch.request-cabang.partials.filters', [
    'branches' => $branches,
    'branch' => $branch
])

        {{-- TABLES --}}
        <div x-show="tab === 'receiver'">
            @include('branch.request-cabang.partials.table', [
                'requests' => $asReceiver,
                'branchCode' => $branchCode
            ])
        </div>

        <div x-show="tab === 'sender'">
            @include('branch.request-cabang.partials.table', [
                'requests' => $asSender,
                'branchCode' => $branchCode
            ])
        </div>

    </div>

</div>

</x-app-layout>
