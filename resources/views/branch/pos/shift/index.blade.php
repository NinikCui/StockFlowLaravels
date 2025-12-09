<x-app-layout :companyCode="$companyCode" :branchCode="$branchCode">

<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="h-10 w-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl grid place-items-center text-white">
                    💼
                </span>
                POS Shift – {{ $branch->name }}
            </h1>
            <p class="text-gray-600 mt-1">Kelola shift kasir pada cabang ini</p>
        </div>

        {{-- TOMBOL OPEN / CLOSE --}}
        <div>
            @if($activeShift)
                <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $activeShift->id]) }}"
                   class="px-5 py-3 rounded-xl bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                    🔒 Tutup Shift
                </a>
            @else
                <a href="{{ route('branch.pos.shift.openForm', [ $branchCode]) }}"
                   class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700 transition">
                    ➕ Buka Shift Baru
                </a>
            @endif
        </div>
    </div>


    {{-- NOTIFICATION --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- ACTIVE SHIFT CARD --}}
    @if($activeShift)
    <div class="mb-10 p-6 bg-gradient-to-br from-yellow-50 to-white border border-yellow-200 
                rounded-2xl shadow flex items-center justify-between">

        <div>
            <p class="text-sm text-yellow-700 font-semibold">Shift Sedang Berjalan</p>
            <h3 class="text-xl font-bold text-gray-900 mt-1">Shift #{{ $activeShift->id }}</h3>

            <div class="mt-3 text-gray-700 text-sm space-y-1">
                <p>👤 Dibuka oleh: 
                    <span class="font-semibold">{{ $activeShift->openedBy->username ?? 'User' }}</span>
                </p>
                <p>⏰ Dibuka pada: {{ $activeShift->opened_at }}</p>
                <p>💵 Modal Kas: 
                    <span class="font-semibold">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex flex-col items-end gap-3">

            {{-- 🔥 BUTTON MASUK POS --}}
            <a href="{{ route('branch.pos.order.index', $branchCode) }}"
            class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">
                🛒 Masuk ke POS
            </a>
            <a href="{{ route('branch.pos.shift.history', [$branchCode, $activeShift->id]) }}"
            class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
                📄 Lihat Order Hari Ini
            </a>
            {{-- 🔒 BUTTON TUTUP SHIFT --}}
            <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $activeShift->id]) }}"
            class="px-5 py-3 rounded-xl bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                🔒 Tutup Shift
            </a>

        </div>
    </div>
    @endif



    {{-- HISTORY TABLE --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Shift</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Kasir</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Waktu</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Kas</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($shifts as $s)
                <tr class="hover:bg-gray-50 transition">

                    {{-- ID SHIFT --}}
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-900">#{{ $s->id }}</span>
                    </td>

                    {{-- USER --}}
                    <td class="px-6 py-4">
                        {{ $s->openedByUser->username ?? '-' }}
                    </td>

                    {{-- WAKTU --}}
                    <td class="px-6 py-4">
                        <div class="text-gray-700">
                            <div>Open: {{ $s->opened_at }}</div>
                            <div>Close: {{ $s->closed_at ?? '—' }}</div>
                        </div>
                    </td>

                    {{-- KAS --}}
                    <td class="px-6 py-4">
                        <div class="text-gray-700">
                            <div>Modal: Rp {{ number_format($s->opening_cash, 0, ',', '.') }}</div>
                            <div>Tutup: 
                                {{ $s->closing_cash ? 'Rp '.number_format($s->closing_cash,0,',','.') : '—' }}
                            </div>
                        </div>
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4 text-center">
                        @if($s->status === 'OPEN')
                            <span class="px-3 py-1.5 text-xs rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 font-semibold">
                                OPEN
                            </span>
                        @else
                            <span class="px-3 py-1.5 text-xs rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold">
                                CLOSED
                            </span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-6 py-4 text-right">
                        @if($s->status === 'OPEN')
                            <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $s->id]) }}"
                               class="text-red-600 font-semibold hover:underline">
                                Tutup Shift
                            </a>
                        @else
                            <a href="{{ route('branch.pos.shift.history', [$branchCode, $s->id]) }}"
                            class="text-indigo-600 font-semibold hover:underline">
                                📜 History
                            </a>
                        @endif
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-500">
                        Belum ada shift yang tercatat.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

</x-app-layout>
