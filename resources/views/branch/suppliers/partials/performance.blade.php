<div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl shadow-lg border border-gray-200 p-8">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Performance Supplier</h1>
                    <p class="text-sm text-gray-600 mt-1">Analisis performa supplier berdasarkan PO dan penerimaan barang</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER MODE --}}
    <form method="GET" class="bg-white border border-gray-200 p-6 rounded-2xl shadow-md mb-8">

        <input type="hidden" name="tab" value="score">

        <div class="flex flex-wrap gap-5 items-end">

            <div class="flex flex-col flex-1 min-w-[200px]">
                <label class="text-sm font-semibold text-gray-700 mb-2">Mode KPI</label>
                <select name="mode" class="border border-gray-300 rounded-xl p-3 text-sm">
                    <option value="all" {{ $mode === 'all' ? 'selected' : '' }}>
                        📊 Total Keseluruhan
                    </option>
                    <option value="period" {{ $mode === 'period' ? 'selected' : '' }}>
                        📅 Berdasarkan Periode
                    </option>
                </select>
            </div>

            @if ($mode == 'period')
                <div class="flex flex-col flex-1 min-w-[150px]">
                    <label class="text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                    <select name="period_month" class="border border-gray-300 rounded-xl p-3 text-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col flex-1 min-w-[120px]">
                    <label class="text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="period_year" class="border border-gray-300 rounded-xl p-3 text-sm">
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>

    {{-- KPI NOTICE --}}
    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
        @if ($mode === 'period' && $month && $year)
            <p class="text-sm text-blue-800">
                KPI berdasarkan periode:
                <strong>{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</strong>
            </p>
        @else
            <p class="text-sm text-blue-800">
                KPI total keseluruhan transaksi supplier
            </p>
        @endif
    </div>

    {{-- KPI GRID --}}
    @include('branch.suppliers.partials._kpi-grid') 
    {{-- ➜ Jika kamu mau, aku buatkan versi branch juga --}}

    {{-- DIVIDER --}}
    <div class="my-8 border-t-2 border-gray-200"></div>

    {{-- GENERATE SCORE PER PERIOD --}}
    <div class="mb-6 p-6 bg-white border border-gray-200 rounded-2xl shadow-md">
        <div class="flex items-center justify-between gap-4 flex-wrap">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900">Generate Performance Report</h3>
                    <p class="text-xs text-gray-600">Buat penilaian performa vendor periode tertentu</p>
                </div>
            </div>

            <form method="POST"
                action="{{ route('branch.supplier.score.period', [$branchCode, $supplier->id]) }}"
                class="flex gap-3 items-center flex-wrap">

                @csrf
                <input type="hidden" name="tab" value="score">

                {{-- Month --}}
                <select name="period_month" class="border border-gray-300 rounded-xl p-3 text-sm">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>

                {{-- Year --}}
                <select name="period_year" class="border border-gray-300 rounded-xl p-3 text-sm">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl">
                    Generate
                </button>
            </form>

        </div>
    </div>

    {{-- HISTORY TABLE --}}
    @include('branch.suppliers.partials._history-table')
    {{-- ➜ Jika mau, aku buatkan versi branch dengan warna dan badge sesuai branch UI --}}
</div>
