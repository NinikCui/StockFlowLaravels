<x-app-layout :branchCode="$branchCode">

<div class="max-w-6xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">Daftar Item Cabang</h1>

    <table class="w-full bg-white rounded-xl shadow text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Item</th>
                <th class="px-4 py-2 text-center">Total Stok</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($items as $item)
            <tr class="border-b">
                <td class="px-4 py-3">{{ $item->name }}</td>

                <td class="px-4 py-3 text-center">
                    {{ $item->total_qty ?? 0 }}
                </td>

                <td class="px-4 py-3 text-center">
                    <a href="{{ route('branch.item.show', [$branchCode, $item->id]) }}"
                       class="text-emerald-600 hover:underline">
                        Detail
                    </a>
                    {{-- HISTORY --}}
    <a href="{{ route('branch.item.history', [$branchCode, $item->id]) }}"
       class="text-blue-600 hover:underline">
        Riwayat
    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

</x-app-layout>
