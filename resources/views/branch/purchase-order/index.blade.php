<x-app-layout :branchCode="$branch->code">

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- ======================= --}}
    {{-- HEADER --}}
    {{-- ======================= --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Order Cabang</h1>
            <p class="text-gray-600 mt-2">
                Kelola transaksi pembelian untuk cabang {{ $branch->name }}
            </p>
        </div>

         <x-crud-add 
                resource="branch.po"
                :companyCode="$companyCode"
                permissionPrefix="purchase"
                :routeParams="[$branchCode]" 
            />
    </div>


    {{-- ======================= --}}
    {{-- FILTER --}}
    {{-- ======================= --}}
    <form method="GET" class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 mb-8">

        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">Filter Pencarian</h2>
            </div>

            <a href="{{ route('branch.po.index', $branch->code) }}"
                class="text-sm text-gray-600 hover:text-emerald-600 font-medium flex items-center gap-2">
                Reset Filter
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- PO NUMBER --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-2">PO Number</label>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nomor PO / Supplier..."
                       class="w-full px-4 py-3 border rounded-xl">
            </div>

            {{-- STATUS --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-3 border rounded-xl bg-white">
                    <option value="">Semua Status</option>
                    @foreach(['DRAFT','APPROVED','RECEIVED','CANCELLED'] as $s)
                        <option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit"
                    class="px-8 py-3 bg-emerald-600 text-white rounded-xl shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>


    {{-- ======================= --}}
    {{-- TABLE --}}
    {{-- ======================= --}}
    <div class="bg-white rounded-2xl shadow-md border overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-emerald-50 border-b">
                <tr class="text-left text-gray-700 font-bold uppercase">
                    <th class="p-4">PO Number</th>
                    <th class="p-4">Supplier</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pos as $po)
                    <tr class="border-b hover:bg-gray-50">

                        <td class="p-4 font-semibold">{{ $po->po_number }}</td>

                        <td class="p-4">{{ $po->supplier->name ?? '-' }}</td>

                        <td class="p-4">{{ $po->po_date }}</td>

                        <td class="p-4">
                            @php
                                $bgs = [
                                    'DRAFT' => 'bg-gray-100 text-gray-700',
                                    'APPROVED' => 'bg-blue-100 text-blue-700',
                                    'RECEIVED' => 'bg-emerald-100 text-emerald-700',
                                    'CANCELLED' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-xl text-xs font-semibold {{ $bgs[$po->status] ?? '' }}">
                                {{ $po->status }}
                            </span>
                        </td>

                        <td class="p-4 text-center">
                            <a href="{{ route('branch.po.show', [$branch->code, $po->id]) }}"
                               class="text-emerald-600 hover:underline">
                                Detail
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Tidak ada Purchase Order.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div class="mt-6">
        {{ $pos->links() }}
    </div>

</div>

</x-app-layout>
