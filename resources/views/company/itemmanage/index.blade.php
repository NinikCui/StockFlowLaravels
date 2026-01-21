<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ===============================
            HEADER
        =============================== --}}
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        Daftar Item (Company)
                    </h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </span>
                        <span class="font-medium">
                            Pantau stok item lintas seluruh cabang
                        </span>
                    </p>
                </div>

                <x-crud-add
                    resource="item"
                    :companyCode="$companyCode"
                    permissionPrefix="item"
                />
            </div>
        </div>

        {{-- ===============================
            FILTER CABANG
        =============================== --}}
        <form method="GET" class="mb-8">
            <div class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-2xl shadow border">
                <div class="text-sm font-bold text-gray-700">Filter Cabang</div>

                <select name="branch_id"
                    class="rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($selectedBranch == $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>

                <button
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                    Terapkan
                </button>
            </div>
        </form>

        {{-- ===============================
            TABLE
        =============================== --}}
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900">
                            <th class="px-6 py-5 text-left text-xs font-black text-white uppercase">Item</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-white uppercase">Status Stok</th>
                            <th class="px-6 py-5 text-center text-xs font-black text-white uppercase">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $item)
                            <tr class="hover:bg-gray-50 transition group">

                                {{-- ITEM --}}
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-16 w-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center text-white font-black text-xl">
                                            {{ strtoupper(substr($item->name, 0, 2)) }}
                                        </div>

                                        <div>
                                            <div class="font-bold text-gray-900 text-lg">
                                                {{ $item->name }}
                                            </div>

                                            @if (!is_null($item->days_to_expire) && $item->days_to_expire <= 7)
                                                <div
                                                    class="inline-flex items-center gap-2 px-3 py-1 mt-1 bg-red-600 text-white rounded-xl text-xs font-bold">
                                                    Kadaluarsa {{ ceil($item->days_to_expire) }} hari
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- STATUS STOK + SATUAN + TOOLTIP --}}
                                <td class="px-6 py-6">
                                    <div class="inline-flex items-center gap-1 relative group">

                                        <span
                                            class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl text-sm font-black
                                            {{ $item->is_low_stock
                                                ? 'bg-red-600 text-white'
                                                : ($item->is_near_low_stock
                                                    ? 'bg-yellow-500 text-white'
                                                    : 'bg-emerald-600 text-white') }}"
                                        >
                                            Stok:
                                            {{ 
                                                ($item->total_qty ?? 0) == floor($item->total_qty ?? 0)
                                                    ? number_format($item->total_qty ?? 0, 0, ',', '.')
                                                    : number_format($item->total_qty ?? 0, 2, ',', '.')
                                            }}
                                            {{ $item->satuan->code ?? '' }}
                                        </span>

                                        @if (
                                            isset($unitConversions[$item->satuan_id]) &&
                                            $unitConversions[$item->satuan_id]->count()
                                        )
                                            <span
                                                class="ml-1 w-4 h-4 text-xs font-bold flex items-center justify-center
                                                       rounded-full bg-white/80 text-gray-700 cursor-pointer border">
                                                ?
                                            </span>

                                            <div
                                                class="absolute left-0 top-full mt-2 w-56
                                                       hidden group-hover:block
                                                       bg-white border border-gray-200 shadow-xl
                                                       rounded-xl p-3 text-xs text-gray-700 z-50">
                                                <div class="font-semibold text-gray-900 mb-1">
                                                    Konversi Satuan
                                                </div>

                                                <ul class="space-y-1">
                                                    @foreach ($unitConversions[$item->satuan_id] as $conv)
                                                        <li>
                                                           {{
                                                                (($item->total_qty ?? 0) * $conv->factor)
                                                                    == floor(($item->total_qty ?? 0) * $conv->factor)
                                                                        ? number_format(($item->total_qty ?? 0) * $conv->factor, 0, ',', '.')
                                                                        : number_format(($item->total_qty ?? 0) * $conv->factor, 3, ',', '.')
                                                            }}
                                                            {{ $conv->toSatuan->code }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-6">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('stockmanage.index', [
                                                'companyCode' => $companyCode,
                                                'item' => $item->id
                                            ]) }}"
                                            class="px-4 py-2 text-xs font-bold bg-gray-100 rounded-xl hover:bg-gray-200">
                                            Detail
                                        </a>

                                        <a href="{{ route('itemmanage.history', [$companyCode, $item->id]) }}"
                                            class="px-4 py-2 text-xs font-bold bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200">
                                            Riwayat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-20 text-center text-gray-500 font-bold">
                                    Belum ada item
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
