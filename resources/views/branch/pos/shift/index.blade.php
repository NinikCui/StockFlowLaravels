<x-app-layout :companyCode="$companyCode" :branchCode="$branchCode">

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-green-500 to-teal-600 rounded-3xl shadow-2xl p-8 mb-8">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-white opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white opacity-10 blur-2xl"></div>
            
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border-2 border-white/30">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Manajemen Shift</h1>
                        <p class="text-emerald-100 text-sm sm:text-base mt-1 font-medium">{{ $branch->name }}</p>
                    </div>
                </div>

                {{-- ACTION BUTTON --}}
                <div>
                    @if($activeShift)
                        <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $activeShift->id]) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-red-500/90 backdrop-blur-sm hover:bg-red-600 text-white font-bold rounded-xl border-2 border-white/30 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Tutup Shift</span>
                        </a>
                    @else
                        <a href="{{ route('branch.pos.shift.openForm', [ $branchCode]) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold rounded-xl border-2 border-white/30 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span>Buka Shift Baru</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- NOTIFICATIONS --}}
        @if(session('success'))
            <div class="mb-6 p-5 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-700 flex items-start gap-3 shadow-sm animate-fade-in">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold mb-1">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-5 rounded-2xl bg-red-50 border-2 border-red-200 text-red-700 flex items-start gap-3 shadow-sm animate-fade-in">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold mb-1">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- ACTIVE SHIFT CARD --}}
        @if($activeShift)
        <div class="mb-8 bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-2xl shadow-xl border-2 border-amber-200 overflow-hidden">
            
            {{-- Header Badge --}}
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-3">
                <div class="flex items-center gap-2 text-white">
                    <div class="h-8 w-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center animate-pulse">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg">Shift Sedang Aktif</span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- SHIFT INFO --}}
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                                <span class="h-10 w-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-md">
                                    #
                                </span>
                                Shift #{{ $activeShift->id }}
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 border-2 border-amber-200 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Dibuka Oleh</p>
                                        <p class="font-bold text-gray-900">{{ $activeShift->openedBy->username ?? 'User' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-4 border-2 border-amber-200 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Modal Kas</p>
                                        <p class="font-bold text-emerald-600 text-lg">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-4 border-2 border-amber-200 shadow-sm sm:col-span-2">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Waktu Buka</p>
                                        <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($activeShift->opened_at)->format('d M Y, H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('branch.pos.order.index', $branchCode) }}"
                           class="flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Masuk ke POS</span>
                        </a>

                        <a href="{{ route('branch.pos.shift.history', [$branchCode, $activeShift->id]) }}"
                           class="flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Lihat Riwayat Order</span>
                        </a>

                        <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $activeShift->id]) }}"
                           class="flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Tutup Shift</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
        @endif

        {{-- SHIFT HISTORY TABLE --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            
            {{-- Table Header --}}
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b-2 border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Shift
                </h2>
                <p class="text-sm text-gray-500 mt-1">Semua shift yang pernah berjalan di cabang ini</p>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Shift ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kasir</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($shifts as $s)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- SHIFT ID --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="h-10 w-10 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                        <span class="text-emerald-600 font-bold">#</span>
                                    </div>
                                    <span class="font-bold text-gray-900 text-lg">{{ $s->id }}</span>
                                </div>
                            </td>

                            {{-- KASIR --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $s->openedByUser->username ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- WAKTU --}}
                            <td class="px-6 py-5">
                                <div class="space-y-1 text-sm">
                                    <div class="flex items-center gap-2 text-emerald-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($s->opened_at)->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 {{ $s->closed_at ? 'text-red-600' : 'text-gray-400' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <span class="font-medium">{{ $s->closed_at ? \Carbon\Carbon::parse($s->closed_at)->format('d M Y, H:i') : 'Belum ditutup' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- KAS --}}
                            <td class="px-6 py-5">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500">Modal:</span>
                                        <span class="font-semibold text-emerald-600">Rp {{ number_format($s->opening_cash, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500">Tutup:</span>
                                        <span class="font-semibold {{ $s->closing_cash ? 'text-blue-600' : 'text-gray-400' }}">
                                            {{ $s->closing_cash ? 'Rp '.number_format($s->closing_cash,0,',','.') : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-5 text-center">
                                @if($s->status === 'OPEN')
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-lg bg-amber-100 text-amber-700 border-2 border-amber-200 shadow-sm">
                                        <div class="h-2 w-2 bg-amber-500 rounded-full animate-pulse"></div>
                                        OPEN
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-lg bg-emerald-100 text-emerald-700 border-2 border-emerald-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        CLOSED
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-6 py-5 text-right">
                                @if($s->status === 'OPEN')
                                    <a href="{{ route('branch.pos.shift.closeForm', [ $branchCode, $s->id]) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 font-bold transition-all shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Tutup Shift
                                    </a>
                                @else
                                    <a href="{{ route('branch.pos.shift.history', [$branchCode, $s->id]) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold transition-all shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Lihat Riwayat
                                    </a>
                                @endif
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20">
                                <div class="text-center">
                                    <div class="w-24 h-24 mx-auto mb-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 text-lg font-semibold mb-1">Belum Ada Shift</p>
                                    <p class="text-gray-400 text-sm">Belum ada shift yang tercatat pada cabang ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

</x-app-layout>