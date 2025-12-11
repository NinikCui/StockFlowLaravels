<x-app-layout :companyCode="$companyCode">
    <div class="max-w-7xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold mb-6">Laporan Stok Global</h1>

        <div class="bg-white shadow border rounded-xl overflow-hidden">

            <table class="w-full text-left">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-3">Item</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Total Qty</th>
                        <th class="p-3">Cabang Terbanyak</th>
                        <th class="p-3">Cabang Tersedikit</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($stocks as $itemId => $group)
                        @php
                            $total = $group->sum('qty');
                            $max = $group->sortByDesc('qty')->first();
                            $min = $group->sortBy('qty')->first();
                        @endphp

                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $group[0]->item->name }}</td>
                            <td class="p-3">{{ $group[0]->item->kategori->name }}</td>
                            <td class="p-3 font-semibold">{{ $total }}</td>
                            <td class="p-3">{{ $max->warehouse->cabangResto->name }} ({{ $max->qty }})</td>
                            <td class="p-3">{{ $min->warehouse->cabangResto->name }} ({{ $min->qty }})</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>
