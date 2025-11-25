{{-- HEADER --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Pegawai</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola akun pegawai & peran mereka</p>
    </div>

    <div class="flex gap-3">
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200 font-medium">
            <span>🔄</span>
            <span>Refresh</span>
        </button>

        <x-add-button 
            href="/pegawai/tambah"
            text="+ Pegawai Baru"
            variant="primary"
        />
    </div>
</div>

{{-- FILTER --}}
<div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-2xl p-5 shadow-sm mb-6">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <input id="pegawaiSearch" type="text"
                class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                placeholder="Cari nama, telepon, atau role...">
            <span class="absolute left-4 top-3.5 text-gray-400 text-lg">🔍</span>
        </div>

        <select id="pegawaiFilterBranch" 
            class="px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white font-medium">
            <option value="all">🏢 Semua Cabang</option>
            <option value="universal">🌐 Universal</option>
            @foreach ($pegawai->where('branch_code','!=',null)->groupBy('branch_code') as $code => $items)
                <option value="{{ $code }}">📍 {{ $items[0]['branch_name'] }}</option>
            @endforeach
        </select>

        <select id="pegawaiFilterStatus" 
            class="px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white font-medium">
            <option value="all">📊 Semua Status</option>
            <option value="active">✅ Aktif</option>
            <option value="inactive">❌ Nonaktif</option>
        </select>
    </div>
</div>

{{-- GRID --}}
<div id="pegawaiGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($pegawai as $p)
        @include('company.pegawai.partials.pegawai-card', ['p' => $p, 'companyCode' => $companyCode])
    @endforeach
</div>