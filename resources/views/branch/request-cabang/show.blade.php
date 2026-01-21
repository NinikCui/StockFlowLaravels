<x-app-layout :branchCode="$branchCode">
    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- BREADCRUMB --}}
        <div class="mb-8">
            <a href="{{ route('branch.request.index', $branchCode) }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-emerald-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke daftar request
            </a>
        </div>

        {{-- ================= ERROR HANDLER ================= --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <div>
                        <p class="font-semibold text-red-700">
                            Terjadi kesalahan
                        </p>
                        <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="text-sm font-semibold text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                {{-- LEFT --}}
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="current-currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">
                            {{ $req->trans_number }}
                        </h1>

                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Diajukan pada {{ $req->trans_date->format('d M Y') }}
                        </div>
                    </div>
                </div>

                {{-- STATUS + ACTION --}}
                <div class="flex items-center gap-3">
                    @php
                        $statusConfig = match($req->status) {
                            'REQUESTED' => ['bg'=>'bg-yellow-100','text'=>'text-yellow-700','border'=>'border-yellow-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>','label'=>'REQUESTED'],
                            'APPROVED' => ['bg'=>'bg-blue-100','text'=>'text-blue-700','border'=>'border-blue-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>','label'=>'APPROVED'],
                            'IN_TRANSIT' => ['bg'=>'bg-purple-100','text'=>'text-purple-700','border'=>'border-purple-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>','label'=>'IN TRANSIT'],
                            'RECEIVED' => ['bg'=>'bg-emerald-100','text'=>'text-emerald-700','border'=>'border-emerald-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>','label'=>'RECEIVED'],
                            'CANCELLED','REJECTED' => ['bg'=>'bg-red-100','text'=>'text-red-700','border'=>'border-red-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>','label'=>$req->status],
                            default => ['bg'=>'bg-gray-100','text'=>'text-gray-700','border'=>'border-gray-200',
                                'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>','label'=>$req->status],
                        };
                    @endphp

                    {{-- BADGE --}}
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border
                        {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}
                        text-sm font-semibold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                            {!! $statusConfig['icon'] !!}
                        </svg>
                        {{ $statusConfig['label'] }}
                    </span>

                    {{-- ACTION BUTTONS --}}
                    @if(session('role.branch.id') == $req->cabang_id_from && $req->status === 'REQUESTED')

                        <button onclick="openApproveModal()"
                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition">
                            Accept
                        </button>

                        <button onclick="openRejectModal()"
                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">
                            Tolak
                        </button>

                    @elseif(session('role.branch.id') == $req->cabang_id_to && $req->status === 'IN_TRANSIT')

                        <button onclick="openReceiveModal()"
                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition">
                            Terima Barang
                        </button>

                    @elseif(session('role.branch.id') == $req->cabang_id_from && $req->status === 'APPROVED')

                        <form method="POST" action="{{ route('branch.request.send', [$branchCode, $req->id]) }}">
                            @csrf
                            <button class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition">
                                Kirim
                            </button>
                        </form>

                    @elseif(session('role.branch.id') == $req->cabang_id_to && $req->status === 'RECEIVED')

                        {{-- kosong --}}

                    @elseif(session('role.branch.id') == $req->cabang_id_to)

                        <x-crud
                            resource="branch.request"
                            keyField="id"
                            :companyCode="$branchCode"
                            :model="$req"
                            permissionPrefix="transfer"
                        />

                    @endif
                </div>
            </div>
        </div>

        {{-- INFORMASI CABANG --}}
        <div class="bg-white shadow-sm rounded-2xl border mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">
                    Informasi Transfer
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Cabang Asal</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $req->cabangFrom->name }}</p>
                    <p class="text-sm text-gray-600">Kode: {{ $req->cabangFrom->code }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">Cabang Tujuan</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $req->cabangTo->name }}</p>
                    <p class="text-sm text-gray-600">Kode: {{ $req->cabangTo->code }}</p>
                </div>
            </div>
        </div>

        {{-- REASON --}}
        @if ($req->reason)
            <div class="bg-white shadow-sm rounded-2xl border mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Alasan Request / Penolakan
                    </h2>
                </div>

                <div class="p-6">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $req->reason }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- DETAIL ITEM --}}
        <div class="bg-white shadow-sm rounded-2xl border mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Detail Item</h2>
                <p class="text-xs text-gray-500">Total: {{ $req->details->count() }} item</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-700">
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Item</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3">Satuan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($req->details as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3">
                                    <strong>{{ $d->item->name }}</strong>
                                    <p class="text-xs text-gray-500">ID: {{ $d->item->id }}</p>
                                </td>
                                <td class="px-6 py-3 text-center font-semibold">
                                    {{ number_format($d->qty, 2) }}
                                </td>
                                <td class="px-6 py-3">
                                    {{ $d->item->satuan->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CATATAN --}}
        @if ($req->note)
            <div class="bg-white shadow-sm rounded-2xl border">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Catatan</h2>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $req->note }}</p>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- =====================================================
        MODAL APPROVE (ALLOCATE STOK)
    ===================================================== --}}
    <div id="approveModal"
         class="fixed inset-0 z-50 hidden">
        {{-- overlay --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeApproveModal()"></div>

        {{-- wrapper (biar modal ga nempel pinggir di mobile) --}}
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl
                        max-h-[90vh] flex flex-col overflow-hidden border border-gray-100">

                {{-- header --}}
                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Alokasikan Stok</h2>
                            <p class="text-sm text-gray-500">Tentukan gudang asal untuk memenuhi permintaan.</p>
                        </div>
                        <button type="button" onclick="closeApproveModal()"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 text-gray-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="POST"
                      class="flex flex-col flex-1 min-h-0"
                      action="{{ route('branch.request.approve', [$branchCode, $req->id]) }}">
                    @csrf

                    {{-- content (scroll) --}}
                    <div class="px-6 py-4 space-y-6 overflow-y-auto flex-1 min-h-0">

                        @foreach ($req->details as $detail)
                            @php
                                $item = $detail->item;

                                $warehouseIds = \App\Models\Warehouse::where('cabang_resto_id', $req->cabang_id_from)
                                    ->pluck('id');

                                $stocks = \App\Models\Stock::with('warehouse')
                                    ->whereIn('warehouse_id', $warehouseIds)
                                    ->where('item_id', $item->id)
                                    ->get();
                            @endphp

                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-base truncate">
                                            {{ $item->name }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            ID Item: {{ $item->id }} • Satuan: {{ $item->satuan->name ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-gray-200">
                                        <span class="text-xs text-gray-500">Kebutuhan</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($detail->qty, 2) }}</span>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm border-separate border-spacing-y-2">
                                        <thead>
                                            <tr class="text-gray-600 text-xs uppercase tracking-wide">
                                                <th class="py-1 text-left">Code Stok</th>
                                                <th class="py-1 text-left">Gudang</th>
                                                <th class="py-1 text-left">Stok</th>
                                                <th class="py-1 text-left">Ambil</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($stocks as $s)
                                                <tr class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                                    <td class="py-3 px-3 rounded-l-lg font-medium text-gray-900">
                                                        {{ $s->code }}
                                                    </td>
                                                    <td class="py-3 px-3 text-gray-700">
                                                        {{ $s->warehouse->name }}
                                                    </td>
                                                    <td class="py-3 px-3 text-gray-700">
                                                        {{ number_format($s->qty, 2) }}
                                                    </td>
                                                    <td class="py-3 px-3 rounded-r-lg">
                                                        <input type="number"
                                                               name="alloc[{{ $item->id }}][{{ $s->id }}]"
                                                               min="0"
                                                               max="{{ $s->qty }}"
                                                               step="0.01"
                                                               placeholder="0.00"
                                                               class="w-28 text-right border border-gray-300 rounded-lg
                                                                      px-2 py-2 bg-white
                                                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                                                                      alloc-input"
                                                               data-item="{{ $item->id }}"
                                                               data-needed="{{ $detail->qty }}">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-3 text-sm text-gray-500">
                                                        Tidak ada stok untuk item ini di gudang cabang asal.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <p id="alloc-warning-{{ $item->id }}"
                                   class="text-red-600 text-xs mt-2 font-medium hidden">
                                    Total alokasi belum memenuhi kebutuhan
                                </p>
                            </div>
                        @endforeach

                    </div>

                    {{-- footer --}}
                     <div class="px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                        <p class="text-xs text-gray-500">
                            Tips: Isi qty “Ambil” sampai minimal sama dengan kebutuhan item.
                        </p>

                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    onclick="closeApproveModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 font-semibold transition">
                                Batal
                            </button>

                            <button
                                id="approveSubmit"
                                disabled
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition
                                       bg-gray-300 text-gray-500 cursor-not-allowed
                                       disabled:opacity-100">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- =====================================================
        MODAL REJECT (ALASAN WAJIB)
    ===================================================== --}}
    <div id="rejectModal"
         class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRejectModal()"></div>

        <div class="relative w-full h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md
                        max-h-[85vh] flex flex-col overflow-hidden border border-gray-100">

                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Alasan Penolakan</h2>
                            <p class="text-sm text-gray-500">Wajib isi alasan agar tercatat jelas.</p>
                        </div>
                        <button type="button" onclick="closeRejectModal()"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 text-gray-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="POST"
                      class="flex flex-col flex-1"
                      action="{{ route('branch.request.reject', [$branchCode, $req->id]) }}">
                    @csrf

                    <div class="px-6 py-5 space-y-2 overflow-y-auto">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            Alasan penolakan
                        </label>

                        <textarea name="reason" rows="5" required
                                  class="w-full border-gray-300 rounded-xl p-3
                                         focus:ring-2 focus:ring-red-500 focus:border-red-500
                                         placeholder:text-gray-400"
                                  placeholder="Contoh: stok tidak mencukupi / prioritas operasional / dll..."></textarea>

                        <p class="text-xs text-gray-500">
                            Catatan akan tampil pada detail request.
                        </p>
                    </div>

                    <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 shrink-0">
                        <button type="button"
                                onclick="closeRejectModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 font-semibold transition">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- =====================================================
        MODAL RECEIVE (TERIMA BARANG)
    ===================================================== --}}
    <div id="receiveModal"
         class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeReceiveModal()"></div>

        <div class="relative w-full h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl
                        max-h-[90vh] flex flex-col overflow-hidden border border-gray-100">

                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Terima Barang</h2>
                            <p class="text-sm text-gray-500">Pilih gudang penyimpanan & qty yang diterima.</p>
                        </div>
                        <button type="button" onclick="closeReceiveModal()"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 text-gray-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="POST"
                      class="flex flex-col flex-1 min-h-0"
                      action="{{ route('branch.request.receive', [$branchCode, $req->id]) }}">
                    @csrf

                    <div class="px-6 py-4 space-y-6 overflow-y-auto flex-1 min-h-0">
                        @foreach ($req->details as $detail)
                            @php
                                $warehouses = \App\Models\Warehouse::where('cabang_resto_id', $req->cabang_id_to)->get();
                                $item = $detail->item;
                            @endphp

                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-base truncate">
                                            {{ $item->name }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            Dikirim: {{ number_format($detail->sended ?? 0, 2) }} • Default terima: {{ number_format($detail->qty, 2) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Gudang tujuan
                                        </label>
                                        <select name="receive[{{ $item->id }}][warehouse_id]"
                                                class="mt-1 w-full border-gray-300 rounded-xl px-3 py-2 bg-white
                                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                required>
                                            <option value="">-- Pilih Gudang --</option>
                                            @foreach ($warehouses as $w)
                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Qty diterima
                                        </label>
                                        <input type="number"
                                               name="receive[{{ $item->id }}][qty]"
                                               value="{{ $detail->qty }}"
                                               min="0"
                                               step="0.01"
                                               class="mt-1 w-full text-right border border-gray-300 rounded-xl px-3 py-2 bg-white
                                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                               required>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Expired Date
                                        </label>
                                        <input type="date"
                                               name="receive[{{ $item->id }}][expired_at]"
                                               class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                               required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                        <button type="button"
                                onclick="closeReceiveModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 font-semibold transition">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition">
                            Konfirmasi Terima
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- MODAL SCRIPTS --}}
    <script>
        // ========= modal helpers (lock scroll + esc close) =========
        const MODALS = {
            approve: 'approveModal',
            reject: 'rejectModal',
            receive: 'receiveModal',
        };

        function lockBodyScroll() {
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
        }

        function unlockBodyScroll() {
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function isAnyModalOpen() {
            return Object.values(MODALS).some(id => !document.getElementById(id).classList.contains('hidden'));
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            lockBodyScroll();
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            if (!isAnyModalOpen()) unlockBodyScroll();
        }

        function openApproveModal(){ openModal(MODALS.approve); }
        function closeApproveModal(){ closeModal(MODALS.approve); }

        function openRejectModal(){ openModal(MODALS.reject); }
        function closeRejectModal(){ closeModal(MODALS.reject); }

        function openReceiveModal(){ openModal(MODALS.receive); }
        function closeReceiveModal(){ closeModal(MODALS.receive); }

        // ESC to close top-most open modal
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;

            // priority close (approve -> receive -> reject) terserah, yang penting nutup yang kebuka
            if (!document.getElementById(MODALS.approve).classList.contains('hidden')) return closeApproveModal();
            if (!document.getElementById(MODALS.receive).classList.contains('hidden')) return closeReceiveModal();
            if (!document.getElementById(MODALS.reject).classList.contains('hidden')) return closeRejectModal();
        });
    </script>

    {{-- VALIDASI ALOKASI STOK --}}
    <script>
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('alloc-input')) {
                validateAllocations();
            }
        });

        function validateAllocations() {
            let allValid = true;

            const itemIds = [...new Set(
                Array.from(document.querySelectorAll('.alloc-input'))
                    .map(i => i.dataset.item)
            )];

            itemIds.forEach(itemId => {
                const inputs = document.querySelectorAll(`.alloc-input[data-item="${itemId}"]`);
                const needed = parseFloat(inputs[0]?.dataset.needed ?? 0);
                let sum = 0;
                let hasInput = false;

                inputs.forEach(i => {
                    const val = parseFloat(i.value);
                    if (!isNaN(val) && val > 0) {
                        sum += val;
                        hasInput = true;
                    }
                });

                const warn = document.getElementById(`alloc-warning-${itemId}`);

                if (!hasInput || sum < needed) {
                    warn.textContent = `Total alokasi minimal ${needed}`;
                    warn.classList.remove('hidden');
                    allValid = false;
                } else {
                    warn.classList.add('hidden');
                }
            });

            const btn = document.getElementById('approveSubmit');

            if (allValid) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btn.classList.add('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'cursor-pointer');
            } else {
                btn.disabled = true;
                btn.classList.remove('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'cursor-pointer');
                btn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            }
        }
    </script>

</x-app-layout>
