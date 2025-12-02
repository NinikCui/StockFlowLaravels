<form method="GET" 
      class="bg-white border rounded-xl shadow-sm p-4 mb-6"
      x-data="{
            get currentTab() {
                // Ambil dari URL parameter atau default ke receiver
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('tab') || 'receiver';
            }
      }">

    {{-- Hidden input tab menggunakan getter --}}
    <input type="hidden" name="tab" :value="currentTab">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- CABANG ASAL - Hanya muncul di tab RECEIVER --}}
        <div x-show="currentTab === 'receiver'" x-transition>
            <label class="text-xs font-semibold text-gray-600">Cabang Asal</label>

            <select name="from" class="input-select w-full">
                <option value="">Semua Cabang</option>
                @foreach ($branches as $b)
                    @if ($b->id != $branch->id)
                        <option value="{{ $b->id }}"
                            {{ request('from') == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        {{-- CABANG TUJUAN - Hanya muncul di tab SENDER --}}
        <div x-show="currentTab === 'sender'" x-transition>
            <label class="text-xs font-semibold text-gray-600">Cabang Tujuan</label>

            <select name="to" class="input-select w-full">
                <option value="">Semua Cabang</option>
                @foreach ($branches as $b)
                    @if ($b->id != $branch->id)
                        <option value="{{ $b->id }}"
                            {{ request('to') == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="text-xs font-semibold text-gray-600">Status</label>
            <select name="status" class="input-select w-full">
                <option value="">Semua Status</option>
                <option value="REQUESTED" {{ request('status')=='REQUESTED'?'selected':'' }}>REQUESTED</option>
                <option value="APPROVED" {{ request('status')=='APPROVED'?'selected':'' }}>APPROVED</option>
                <option value="IN_TRANSIT" {{ request('status')=='IN_TRANSIT'?'selected':'' }}>IN TRANSIT</option>
                <option value="RECEIVED" {{ request('status')=='RECEIVED'?'selected':'' }}>RECEIVED</option>
                <option value="CANCELLED" {{ request('status')=='CANCELLED'?'selected':'' }}>CANCELLED</option>
            </select>
        </div>

        {{-- DATE --}}
        <div>
            <label class="text-xs font-semibold text-gray-600">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="input-text w-full">
        </div>

    </div>

    <div class="flex justify-end gap-3 mt-4">
        {{-- Reset Button --}}
        <a href="{{ route('branch.request.index', $branchCode) }}?tab={{ request('tab') ?? 'receiver' }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
            Reset Filter
        </a>

        {{-- Apply Filter Button --}}
        <button type="submit" 
                class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-black">
            Terapkan Filter
        </button>
    </div>
</form>