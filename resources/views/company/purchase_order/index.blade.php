<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Order</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua transaksi pembelian.</p>
        </div>

        <a href="{{ route('po.create', $companyCode) }}"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            + Buat PO
        </a>
    </div>

    <div class="bg-white border rounded-xl shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left">PO Number</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($po as $row)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium text-gray-800">{{ $row->po_number }}</td>
                    <td class="p-3">{{ $row->supplier->name }}</td>
                    <td class="p-3">{{ $row->po_date }}</td>

                    <td class="p-3">
                        @php
                            $colors = [
                                'DRAFT' => 'bg-gray-100 text-gray-700',
                                'APPROVED' => 'bg-blue-100 text-blue-700',
                                'PARTIAL' => 'bg-yellow-100 text-yellow-700',
                                'RECEIVED' => 'bg-emerald-100 text-emerald-700',
                                'CANCELLED' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $colors[$row->status] }}">
                            {{ $row->status }}
                        </span>
                    </td>

                    <td class="p-3 text-right">
                        <a href="{{ route('po.show', [$companyCode, $row->id]) }}"
                           class="text-emerald-600 hover:underline">
                           Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">Belum ada PO.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $po->links() }}
    </div>

</div>
</x-app-layout>
