{{-- HISTORY TABLE (BRANCH VERSION) --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden">

    {{-- TITLE --}}
    <div class="px-6 py-4 border-b bg-gray-50 flex items-center gap-3">
        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900">Riwayat Penilaian Supplier</h3>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-6 py-3">Periode</th>
                    <th class="px-6 py-3 text-center">On-Time</th>
                    <th class="px-6 py-3 text-center">Reject</th>
                    <th class="px-6 py-3 text-center">Quality</th>
                    <th class="px-6 py-3 text-center">Variance</th>
                    <th class="px-6 py-3">Catatan</th>
                    <th class="px-6 py-3">Tanggal</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($supplier->scores as $sc)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- PERIODE --}}
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::create($sc->period_year, $sc->period_month)->format('F Y') }}
                            </span>
                        </td>

                        {{-- ON TIME --}}
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-emerald-600">
                                {{ $sc->on_time_rate }}%
                            </span>
                        </td>

                        {{-- REJECT RATE --}}
                        <td class="px-6 py-4 text-center">
                            <span class="{{ $sc->reject_rate > 10 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                {{ $sc->reject_rate }}%
                            </span>
                        </td>

                        {{-- QUALITY --}}
                        <td class="px-6 py-4 text-center">
                            <span class="text-indigo-600 font-semibold">
                                {{ $sc->avg_quality }}%
                            </span>
                        </td>

                        {{-- VARIANCE --}}
                        <td class="px-6 py-4 text-center">
                            <span class="text-orange-600 font-semibold">
                                {{ $sc->price_variance }}%
                            </span>
                        </td>

                        {{-- NOTES --}}
                        <td class="px-6 py-4 max-w-xs">
                            @if ($sc->notes)
                                <p class="text-gray-700 line-clamp-2" title="{{ $sc->notes }}">
                                    {{ $sc->notes }}
                                </p>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </td>

                        {{-- DATE --}}
                        <td class="px-6 py-4 text-gray-600">
                            <div class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($sc->calculated_at)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($sc->calculated_at)->format('H:i') }}
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 font-medium">Belum ada penilaian</p>
                                <p class="text-gray-400 text-xs">Generate performance report untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
