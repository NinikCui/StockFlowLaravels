<x-app-layout :companyCode="$companyCode">
    <div class="max-w-6xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold mb-6">Laporan Penggunaan Bahan (BOM)</h1>

        <div class="bg-white border shadow rounded-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Bahan</th>
                        <th class="p-3">Total Penggunaan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($bom as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $row->bahan }}</td>
                        <td class="p-3 font-semibold">{{ $row->total_penggunaan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
