<x-app-layout :branchCode="$branchCode">

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Paket Pembelian</h1>
            <p class="text-sm text-gray-500">
                Cabang: {{ $branch->name }}
            </p>
        </div>

        <x-crud-add 
                resource="branch.bundles"
                :companyCode="$branchCode"
                permissionPrefix="item"
                :routeParams="[$branchCode]" 
            />
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Nama Paket</th>
                    <th class="px-4 py-3">Isi Paket</th>
                    <th class="px-4 py-3 text-right">Harga</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($bundles as $bundle)
                <tr>
                    <td class="px-4 py-3 font-semibold">
                        {{ $bundle->name }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        <ul class="list-disc list-inside">
                            @foreach($bundle->items as $item)
                                <li>
                                    {{ $item->product->name }} × {{ $item->qty }}
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    <td class="px-4 py-3 text-right font-bold text-emerald-600">
                        Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            {{ $bundle->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $bundle->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center space-x-2">
                        
                        <x-crud 
                            resource="branch.bundles"
                            keyField="id"
                            :companyCode="$branchCode"
                            :model="$bundle"
                            permissionPrefix="item"
                        />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5"
                        class="px-4 py-10 text-center text-gray-500">
                        Belum ada paket di cabang ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</x-app-layout>
