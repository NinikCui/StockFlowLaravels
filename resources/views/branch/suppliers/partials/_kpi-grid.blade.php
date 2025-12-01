{{-- KPI GRID (BRANCH VERSION) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

    {{-- ON TIME DELIVERY --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">On-Time Delivery</p>
                <p class="text-xs text-gray-500">Ketepatan waktu pengiriman</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-emerald-600">{{ $onTimeRate }}%</p>
        <p class="text-xs text-gray-500 mt-2">{{ $totalOrders }} total PO</p>
    </div>

    {{-- LEAD TIME --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Rata-rata Lead Time</p>
                <p class="text-xs text-gray-500">Durasi PO sampai barang datang</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-sky-600">{{ $avgLead }} <span class="text-lg">hari</span></p>
    </div>

    {{-- REJECT RATE --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M3.34 16c-.77 1.333.192 3 1.732 3h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Reject Rate</p>
                <p class="text-xs text-gray-500">Barang tidak sesuai standar</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-red-600">{{ $rejectRate }}%</p>
    </div>

    {{-- PRICE VARIANCE --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Price Variance</p>
                <p class="text-xs text-gray-500">Fluktuasi perubahan harga</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-orange-600">{{ $priceVariance }}%</p>
    </div>

    {{-- QUANTITY ACCURACY --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5h6m-3 14l4-4H5a2 2 0 01-2-2V5" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Quantity Accuracy</p>
                <p class="text-xs text-gray-500">Ketepatan jumlah diterima</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-indigo-600">{{ $qtyAccuracy }}%</p>
    </div>

    {{-- LATE DELIVERY --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg transition-all">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Late Deliveries</p>
                <p class="text-xs text-gray-500">Jumlah PO terlambat</p>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-rose-600">{{ $late }}</p>
        <p class="text-xs text-gray-500 mt-2">Dari {{ $totalOrders }} PO</p>
    </div>

</div>
