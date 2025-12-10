<x-app-layout :branchCode="$branchCode">

    <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Perubahan Stok</h1>
            <p class="text-gray-600 mt-1">
                Semua perubahan stok untuk item ini.
            </p>
        </div>

        {{-- ITEM INFO --}}
        <div class="bg-white border rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $item->name }}
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">
                        Kode Stok: <span class="font-mono">{{ $stock->code }}</span>
                    </p>
                    <p class="text-gray-500 text-sm">
                        Gudang: <span class="font-semibold">{{ $stock->warehouse->name }}</span>
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">Stok Sekarang</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ number_format($stock->qty, 0, ',', '.') }}
                        <span class="text-base text-gray-500">{{ $item->satuan->name }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- HISTORY TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Sumber</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold text-gray-700">Perubahan</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($history as $h)
                        <tr class="hover:bg-gray-50">

                            {{-- DATE --}}
                            <td class="py-3 px-4 text-gray-800">
                                {{ \Carbon\Carbon::parse($h->date)->format('d M Y H:i') }}
                            </td>

                            {{-- SOURCE BADGE --}}
                            <td class="py-3 px-4">
                                @php
                                    $color = match($h->source) {
                                        'ADJUSTMENT' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                        'IN', 'PO RECEIVE', 'TRANSFER RECEIVED' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                        'OUT', 'TRANSFER OUT', 'PRODUCTION ISSUE' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-100 text-gray-700 border border-gray-300',
                                    };
                                @endphp

                                <span class="inline-flex px-2.5 py-1 text-xs rounded-lg {{ $color }}">
                                    {{ strtoupper($h->source) }}
                                </span>
                            </td>

                            {{-- QTY CHANGE --}}
                            <td class="py-3 px-4 text-right font-semibold">
                                @if ($h->diff > 0)
                                    <span class="text-emerald-600">+{{ number_format($h->diff, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-red-600">{{ number_format($h->diff, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            {{-- USER --}}
                            <td class="py-3 px-4 text-gray-700">
                                {{ $h->user ?? '-' }}
                            </td>

                            {{-- NOTE --}}
                            <td class="py-3 px-4 text-gray-700">
                                {{ $h->note ?: '-' }}
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                Tidak ada riwayat perubahan stok.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
