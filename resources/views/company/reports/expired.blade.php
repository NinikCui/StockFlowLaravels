<x-app-layout :companyCode="$companyCode">
    <div class="max-w-6xl mx-auto px-6 py-10">
        
        <h1 class="text-2xl font-bold mb-6">Laporan Stok Hampir Expired</h1>

        <div class="bg-white shadow border rounded-xl overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Item</th>
                        <th class="p-3">Cabang</th>
                        <th class="p-3">Qty</th>
                        <th class="p-3">Expired</th>
                        <th class="p-3">Sisa Hari</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($expiredSoon as $row)
                        @php
                            $days = \Carbon\Carbon::now()->diffInDays($row->expired_at);
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $row->item->name }}</td>
                            <td class="p-3">{{ $row->warehouse->cabangResto->name }}</td>
                            <td class="p-3">{{ $row->qty }}</td>
                            <td class="p-3">
                                {{ \Carbon\Carbon::parse($row->expired_at)->format('d M Y') }}
                            </td>                            
                            <td class="p-3 {{ $days <= 3 ? 'text-red-600 font-bold' : '' }}">
                                {{ $days }} hari
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>
