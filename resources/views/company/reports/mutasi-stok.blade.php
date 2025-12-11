<x-app-layout :companyCode="$companyCode">
    <div class="max-w-7xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold mb-6">Laporan Mutasi Stok</h1>

        <div class="bg-white border shadow rounded-xl overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Cabang</th>
                        <th class="p-3">Gudang</th>
                        <th class="p-3">Item</th>
                        <th class="p-3">Qty</th>
                        <th class="p-3">Jenis</th>
                        <th class="p-3">Reference</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($mutasi as $m)
                    <tr class="border-b hover:bg-gray-50">
                        
                        {{-- Tanggal --}}
                        <td class="p-3">{{ $m->created_at->format('d M Y H:i') }}</td>

                        {{-- Cabang --}}
                        <td class="p-3">
                            {{ $m->warehouse->cabangResto->name ?? '-' }}
                        </td>

                        {{-- Gudang --}}
                        <td class="p-3">
                            {{ $m->warehouse->name ?? '-' }}
                        </td>

                        {{-- Item --}}
                        <td class="p-3">
                            {{ $m->item->name ?? '-' }}
                        </td>

                        {{-- Qty --}}
                        <td class="p-3">
                            {{ $m->qty }}
                        </td>

                        {{-- Jenis --}}
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-600 text-xs">
                                {{ $m->type }}
                            </span>
                        </td>

                        {{-- Reference --}}
                        <td class="p-3">
                            {{ $m->reference ?? '-' }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>
</x-app-layout>
