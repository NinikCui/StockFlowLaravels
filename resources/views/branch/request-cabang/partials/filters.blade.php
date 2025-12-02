<form method="GET" 
      class="bg-white border rounded-xl shadow-sm p-4 mb-6"
      x-data="{
            tab: @js(request('tab') ?? 'receiver'),
            branchId: {{ $branch->id }}
      }">

    <input type="hidden" name="tab" x-model="tab">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- CABANG ASAL --}}
        <div>
            <label class="text-xs font-semibold text-gray-600">Cabang Asal</label>

            <select name="from" class="input-select w-full"
                :disabled="tab === 'sender'">

                {{-- LOOP BLADE --}}
                @foreach ($branches as $b)

                    {{-- JIKA RECEIVER: hide cabang ini --}}
                    <option value="{{ $b->id }}"
                        x-show="tab === 'sender' || (tab === 'receiver' && {{ $b->id }} != branchId)"

                        {{-- jika sender → selected = cabang ini --}}
                        x-bind:selected="tab === 'sender' && {{ $b->id }} == branchId"

                        {{-- jika receiver → gunakan request --}}
                        @if(request('from') == $b->id) selected @endif
                    >
                        {{ $b->name }}
                        @if ($b->id == $branch->id)
                            (CABANG INI)
                        @endif
                    </option>

                @endforeach
            </select>
        </div>

        {{-- CABANG TUJUAN --}}
        <div>
            <label class="text-xs font-semibold text-gray-600">Cabang Tujuan</label>

            <select name="to" class="input-select w-full"
                :disabled="tab === 'receiver'">

                @foreach ($branches as $b)

                    {{-- JIKA SENDER: hide cabang ini --}}
                    <option value="{{ $b->id }}"
                        x-show="tab === 'receiver' || (tab === 'sender' && {{ $b->id }} != branchId)"

                        {{-- jika receiver → selected = cabang ini --}}
                        x-bind:selected="tab === 'receiver' && {{ $b->id }} == branchId"

                        {{-- jika sender → gunakan request --}}
                        @if(request('to') == $b->id) selected @endif
                    >
                        {{ $b->name }}
                        @if ($b->id == $branch->id)
                            (CABANG INI)
                        @endif
                    </option>

                @endforeach
            </select>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="text-xs font-semibold text-gray-600">Status</label>
            <select name="status" class="input-select w-full">
                <option value="">Semua</option>
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

    <div class="flex justify-end mt-4">
        <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-black">
            Terapkan Filter
        </button>
    </div>
</form>
