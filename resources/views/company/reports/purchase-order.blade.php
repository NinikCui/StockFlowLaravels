<x-app-layout :companyCode="$companyCode">
    <div class="max-w-7xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold mb-6">Laporan Purchase Order</h1>

        <div class="bg-white border shadow rounded-xl overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">No PO</th>
                        <th class="p-3">Supplier</th>
                        <th class="p-3">Cabang</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pos as $po)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $po->po_number }}</td>
                        <td class="p-3">{{ $po->supplier->name }}</td>
                        <td class="p-3">{{ $po->cabangResto->name }}</td>
                        <td class="p-3 font-semibold">
                            Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-600 text-xs">
                                {{ $po->status }}
                            </span>
                        </td>
                        <td class="p-3">{{ $po->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>
