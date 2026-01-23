<div class="flex flex-col gap-2">

    {{-- ON-TIME --}}
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs text-gray-600 font-medium min-w-[60px]">On-Time</span>

        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="h-full rounded-full 
                {{ $s->kpi_on_time >= 90 ? 'bg-emerald-500' : ($s->kpi_on_time >= 70 ? 'bg-orange-500' : 'bg-red-500') }}"
                style="width: {{ min($s->kpi_on_time, 100) }}%">
            </div>
        </div>

        <span class="text-xs font-bold min-w-[45px] text-right
            {{ $s->kpi_on_time >= 90 ? 'text-emerald-600' : ($s->kpi_on_time >= 70 ? 'text-orange-600' : 'text-red-600') }}">
            {{ number_format($s->kpi_on_time, 1) }}%
        </span>
    </div>

    {{-- REJECT --}}
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs text-gray-600 font-medium min-w-[60px]">Reject</span>

        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="h-full rounded-full
                {{ $s->kpi_reject <= 5 ? 'bg-emerald-500' : ($s->kpi_reject <= 15 ? 'bg-orange-500' : 'bg-red-500') }}"
                style="width: {{ min($s->kpi_reject, 100) }}%">
            </div>
        </div>

        <span class="text-xs font-bold min-w-[45px] text-right
            {{ $s->kpi_reject <= 5 ? 'text-emerald-600' : ($s->kpi_reject <= 15 ? 'text-orange-600' : 'text-red-600') }}">
            {{ number_format($s->kpi_reject, 1) }}%
        </span>
    </div>



</div>
