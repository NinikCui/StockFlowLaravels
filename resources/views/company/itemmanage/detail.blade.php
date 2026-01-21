<x-app-layout :companyCode="$companyCode">
    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <div class="bg-white border rounded-xl p-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
            <p class="text-sm text-gray-600">
                Forecast Prediksi Berdasarkan Histori {{ $monthsBack }} bulan terakhir
            </p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total Stok (All Cabang)</p>
                    <p class="text-2xl font-bold">{{ $overall['stock_qty'] }}
</p>
                </div>
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Forecast Pemakaian (All Cabang)</p>
                    <p class="text-2xl font-bold">
                        {{ $overall['forecast_next'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-xl overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-gray-900">Per Cabang</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">Cabang</th>
                            <th class="text-right px-4 py-3">Total Stok</th>
                            <th class="text-right px-4 py-3">Forecast Pemakaian (Next)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($rows as $r)
                            <tr>
                                <td class="px-4 py-3">{{ $r['branch']->nama_cabang ?? $r['branch']->name }}</td>
                                <td class="px-4 py-3 text-right">{{ $r['total_qty'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    {{ $r['forecast_next'] === null ? '-' : $r['forecast_next'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
