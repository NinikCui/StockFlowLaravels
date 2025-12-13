<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Rekomendasi Promosi Menu
            </h1>
            <p class="text-sm text-gray-600">
                Berdasarkan risiko kadaluarsa bahan baku utama
            </p>
        </div>
    </div>

    {{-- EMPTY STATE --}}
    @if($recommendations->isEmpty())
        <div class="bg-white border rounded-xl p-6 text-center text-gray-500">
            Tidak ada rekomendasi menu hari ini.
        </div>
    @else

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Menu</th>
                    <th class="px-4 py-3 text-left">Bahan Utama</th>
                    <th class="px-4 py-3 text-center">Sisa Hari</th>
                    <th class="px-4 py-3 text-right">Potensi Pemakaian</th>
                    <th class="px-4 py-3 text-center">Risk Score</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($recommendations as $rec)
                <tr>
                    <td class="px-4 py-3 font-semibold">
                        {{ $rec->product->name }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $rec->item->name }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $rec->days_to_expired <= 3
                                ? 'bg-red-100 text-red-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $rec->days_to_expired }} hari
                        </span>
                    </td>

                    <td class="px-4 py-3 text-right">
                        {{ number_format($rec->potential_usage, 2) }}
                    </td>

                    <td class="px-4 py-3 text-center font-bold">
                        {{ $rec->risk_score }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $rec->status === 'NEW'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-gray-100 text-gray-600' }}">
                            {{ $rec->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>

</x-app-layout>
