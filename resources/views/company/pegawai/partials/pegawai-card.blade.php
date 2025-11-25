<div class="pegawai-item bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-emerald-200 transition-all duration-300 group"
    data-search="{{ strtolower($p['username'].' '.$p['phone'].' '.$p['role_name'].' '.$p['branch_name']) }}"
    data-branch="{{ $p['branch_code'] ?? 'universal' }}"
    data-status="{{ $p['is_active'] ? 'active' : 'inactive' }}">

    {{-- Header with Avatar --}}
    <div class="flex items-start gap-4 mb-4">
        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xl font-bold shadow-sm group-hover:scale-105 transition-transform duration-200">
            {{ substr($p['username'], 0, 1) }}
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900 text-lg truncate">{{ $p['username'] }}</h3>
            <p class="text-sm text-gray-500 flex items-center gap-1.5 mt-0.5">
                <span>📱</span>
                <span>{{ $p['phone'] ?: 'Tidak ada nomor' }}</span>
            </p>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="space-y-3 mb-4">
        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
            <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Role</p>
            <p class="text-sm font-semibold text-gray-900">{{ $p['role_name'] ?? '-' }}</p>
        </div>

        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
            <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Cabang</p>
            <p class="text-sm font-semibold text-gray-900">{{ $p['branch_name'] ?? '🌐 Universal' }}</p>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
            {{ $p['is_active'] 
                ? 'bg-gradient-to-r from-emerald-50 to-emerald-100 text-emerald-700 border border-emerald-200' 
                : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-600 border border-gray-300' }}">
            <span>{{ $p['is_active'] ? '✅' : '❌' }}</span>
            <span>{{ $p['is_active'] ? 'Aktif' : 'Nonaktif' }}</span>
        </span>

        <div class="flex gap-2">
            <a href="{{ route('pegawai.edit', [$companyCode, $p['id']]) }}" 
                class="h-9 w-9 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 flex items-center justify-center transition-all duration-200 border border-emerald-200 hover:border-emerald-300"
                title="Edit Pegawai">
                <span class="text-lg">✎</span>
            </a>
            <button onclick="openDeletePegawai({{ $p['id'] }})" 
                class="h-9 w-9 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 flex items-center justify-center transition-all duration-200 border border-red-200 hover:border-red-300"
                title="Hapus Pegawai">
                <span class="text-lg">🗑</span>
            </button>
        </div>
    </div>
</div>