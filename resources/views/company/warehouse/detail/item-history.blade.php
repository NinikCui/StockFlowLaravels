<x-app-layout>
<main class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Mutasi Item</h1>
        <p class="text-gray-600 text-sm mt-1">
            Gudang: <strong>{{ $warehouse->name }}</strong> <br>
            Kode Stok: <strong>{{ $stock->code }}</strong> <br>
            Item: <strong>{{ $item->name }}</strong>
        </p>
    </div>

    <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}"
       class="inline-flex items-center px-4 py-2 mb-6 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">
        ← Kembali
    </a>


    {{-- TABLE UNIVERSAL --}}
    <div class="bg-white border rounded-xl shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Sumber</th>
                        <th class="p-3 text-right">Perubahan</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($history as $h)

                        @php
                            $color = match($h->source) {
                                'ADJUSTMENT' => 'blue',
                                'PO RECEIVE', 'IN', 'TRANSFER RECEIVED' => 'emerald',
                                'OUT', 'PRODUCTION ISSUE', 'TRANSFER OUT' => 'red',
                                default => 'gray',
                            };
                        @endphp

                        <tr class="hover:bg-gray-50">

                            {{-- DATE --}}
                            <td class="p-3">
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($h->date)->format('H:i') }}
                                </div>
                            </td>

                            {{-- SOURCE BADGE --}}
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-md
                                    bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
                                    {{ strtoupper($h->source) }}
                                </span>
                            </td>

                            {{-- DIFF --}}
                            <td class="p-3 text-right font-semibold
                                @if($h->diff > 0) text-emerald-700
                                @elseif($h->diff < 0) text-red-600
                                @else text-gray-500 @endif">
                                {{ number_format($h->diff, 2) }}
                            </td>

                            {{-- USER --}}
                            <td class="p-3 font-medium text-gray-800">
                                {{ $h->user ?? '-' }}
                            </td>

                            {{-- NOTE --}}
                            <td class="p-3 text-gray-700 max-w-xs line-clamp-2">
                                {{ $h->note ?: '-' }}
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-500">
                                Tidak ada mutasi item.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>
    </div>

</main>
</x-app-layout>
