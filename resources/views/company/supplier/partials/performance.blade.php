<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">Performance Supplier</h2>

        <a href="#" class="text-emerald-600 text-sm hover:text-emerald-700">
            + Tambah Penilaian
        </a>
    </div>

    <div class="mt-5">
        @if ($supplier->scores->isEmpty())
            <p class="text-sm text-gray-500">Belum ada penilaian.</p>
        @else
            <div class="overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b text-gray-600">
                        <tr>
                            <th class="py-2 px-4">On-Time</th>
                            <th class="px-4">Reject Rate</th>
                            <th class="px-4">Quality</th>
                            <th class="px-4">Variance</th>
                            <th class="px-4">Notes</th>
                            <th class="px-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplier->scores as $sc)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $sc->on_time_rate }}%</td>
                                <td class="px-4">{{ $sc->reject_rate }}%</td>
                                <td class="px-4">{{ $sc->avg_quality }}</td>
                                <td class="px-4">{{ $sc->price_variance }}%</td>
                                <td class="px-4">{{ $sc->notes ?? '-' }}</td>
                                <td class="px-4">{{ $sc->calculated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
