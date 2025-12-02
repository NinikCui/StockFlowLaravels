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
                                class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm">
                            Accept
                        </button>

                        <button onclick="openRejectModal()"
                                class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm">
                            Tolak
                        </button>
                    @elseif(session('role.branch.id') == $req->cabang_id_to && $req->status === 'IN_TRANSIT')

                        <button onclick="openReceiveModal()"
                                class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm">
                            Terima Barang
                        </button>
                    @elseif(session('role.branch.id') == $req->cabang_id_from && $req->status === 'APPROVED')

                        <form method="POST" action="{{ route('branch.request.send', [$branchCode, $req->id]) }}">
                            @csrf
                            <button class="px-3 py-2 bg-purple-600 text-white rounded-lg text-sm">
                                Kirim
                            </button>
                        </form>
                    @elseif(session('role.branch.id') == $req->cabang_id_to && $req->status === 'RECEIVED')
                        
                    @elseif(session('role.branch.id') == $req->cabang_id_to)

                        <x-crud 
                            resource="branch.request"
                            keyField="id"
                            :companyCode="$branchCode"
                            :model="$req"
                            permissionPrefix="inventory"
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
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden">

        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl overflow-hidden">

            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Alokasikan Stok</h2>
                <p class="text-sm text-gray-600">Pilih dari gudang mana stok akan dikurangi.</p>
            </div>

            <form method="POST"
                  action="{{ route('branch.request.approve', [$branchCode, $req->id]) }}">
                @csrf

                <div class="px-6 py-4 space-y-6">

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

                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">
                                {{ $item->name }} (Butuh: {{ $detail->qty }})
                            </h3>

                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-gray-600">
                                        <th class="py-1 text-left">Code Stok</th>
                                        <th class="py-1 text-left">Gudang</th>
                                        <th class="py-1 text-left">Stok</th>
                                        <th class="py-1 text-left">Ambil</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($stocks as $s)
                                    <tr>
                                        <td class="py-1">{{ $s->code }}</td>
                                        <td class="py-1">{{ $s->warehouse->name }}</td>
                                        <td class="py-1">{{ $s->qty }}</td>
                                        <td class="py-1">
                                            <input type="number"
                                                name="alloc[{{ $item->id }}][{{ $s->id }}]"
                                                min="0"
                                                max="{{ $s->qty }}"
                                                step="0.01"
                                                class="border rounded px-2 py-1 w-24 alloc-input"
                                                data-item="{{ $item->id }}"
                                                data-needed="{{ $detail->qty }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <p id="alloc-warning-{{ $item->id }}"
                               class="text-red-600 text-xs mt-2 hidden">
                                Total alokasi belum sesuai kebutuhan!
                            </p>
                        </div>

                    @endforeach

                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button"
                            onclick="closeApproveModal()"
                            class="px-4 py-2 border rounded-lg bg-gray-100">
                        Batal
                    </button>

                    <button id="approveSubmit"
                            disabled
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                        Konfirmasi Approve
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- =====================================================
        MODAL REJECT (ALASAN WAJIB)
    ===================================================== --}}
    <div id="rejectModal"
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden">

        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">

            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Alasan Penolakan</h2>
            </div>

            <form method="POST"
                  action="{{ route('branch.request.reject', [$branchCode, $req->id]) }}">
                @csrf

                <div class="px-6 py-4 space-y-2">
                    <label class="text-sm text-gray-700">
                        Isi alasan mengapa request ini ditolak:
                    </label>

                    <textarea name="reason" rows="4" required
                              class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button"
                            onclick="closeRejectModal()"
                            class="px-4 py-2 border rounded-lg bg-gray-100">
                        Batal
                    </button>

                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>

    </div>
<div id="receiveModal"
     class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl overflow-hidden">

        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Terima Barang</h2>
            <p class="text-sm text-gray-600">Pilih gudang penyimpanan untuk setiap item.</p>
        </div>

        <form method="POST"
              action="{{ route('branch.request.receive', [$branchCode, $req->id]) }}">
            @csrf

            <div class="px-6 py-4 space-y-6">

                @foreach ($req->details as $detail)
                    @php
                        $warehouses = \App\Models\Warehouse::where('cabang_resto_id', $req->cabang_id_to)->get();
                        $item = $detail->item;
                    @endphp

                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            {{ $item->name }} (Dikirim: {{ $detail->qty }})
                        </h3>

                        <label class="text-sm">Pilih Gudang:</label>
                        <select name="receive[{{ $item->id }}][warehouse_id]"
                                class="border rounded-lg px-2 py-1 w-full mt-1" required>
                            <option value="">-- Pilih Gudang --</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>

                        <label class="text-sm mt-3 block">Qty diterima:</label>
                        <input type="number"
                               name="receive[{{ $item->id }}][qty]"
                               value="{{ $detail->qty }}"
                               min="0"
                               step="0.01"
                               class="border rounded px-2 py-1 w-24"
                               required>
                    </div>

                @endforeach

            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button"
                        onclick="closeReceiveModal()"
                        class="px-4 py-2 border rounded-lg bg-gray-100">
                    Batal
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                    Konfirmasi Terima
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- MODAL SCRIPTS --}}
    <script>
        function openReceiveModal() {
    document.getElementById('receiveModal').classList.remove('hidden');
}

function closeReceiveModal() {
    document.getElementById('receiveModal').classList.add('hidden');
}
        function openApproveModal() {
            document.getElementById('approveModal').classList.remove('hidden');
        }
        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>

    {{-- VALIDASI ALOKASI STOK --}}
    <script>
        document.querySelectorAll('.alloc-input').forEach(input => {
            input.addEventListener('input', validateAllocations);
        });

        function validateAllocations() {

            let allValid = true;

            document.querySelectorAll('.alloc-input').forEach(input => {
                const itemId = input.dataset.item;
                const needed = parseFloat(input.dataset.needed);

                let sum = 0;
                document.querySelectorAll(`.alloc-input[data-item="${itemId}"]`)
                    .forEach(i => sum += parseFloat(i.value || 0));

                const warn = document.getElementById(`alloc-warning-${itemId}`);

                if (sum !== needed) {
                    warn.classList.remove('hidden');
                    allValid = false;
                } else {
                    warn.classList.add('hidden');
                }
            });

            document.getElementById('approveSubmit').disabled = !allValid;
        }
    </script>

</x-app-layout>
