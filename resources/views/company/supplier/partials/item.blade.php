<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7 space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">Item yang Disuplai</h2>

        <button onclick="modalAddItem.showModal()"
            class="inline-flex items-center gap-1 text-emerald-600 text-sm font-medium hover:text-emerald-700">
            <span class="text-lg">＋</span> Tambah Item
        </button>
    </div>

    {{-- LIST --}}
    @if ($items->isEmpty())
        <div class="p-5 rounded-xl bg-gray-50 text-center text-sm text-gray-500 border border-gray-200">
            Belum ada item yang disuplai.
        </div>
    @else
        <div class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden">

            @foreach ($items as $item)
                <div class="p-4 bg-white flex items-center justify-between hover:bg-gray-50 transition">

                    {{-- LEFT --}}
                    <div>
                        <p class="font-semibold text-gray-800 text-[15px]">{{ $item->name }}</p>

                        <div class="mt-1 text-xs text-gray-500 space-x-2">

                            <span>Harga:
                                <span class="font-semibold text-gray-700">
                                    Rp {{ number_format($item->pivot->price) }}
                                </span>
                            </span>

                            <span>•</span>

                            <span>MOQ:
                                <span class="font-semibold text-gray-700">
                                    {{ $item->pivot->min_order_qty }}
                                </span>
                            </span>

                            <span>•</span>

                            <span>Update:
                                <span class="font-semibold text-gray-700">
                                    {{ $item->pivot->last_price_update
                                        ? \Carbon\Carbon::parse($item->pivot->last_price_update)->format('d M Y')
                                        : '-' }}
                                </span>
                            </span>

                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex gap-4 items-center">

                        <button
                            onclick="openEditItem({{ $item->id }}, {{ $item->pivot->price }}, {{ $item->pivot->min_order_qty }})"
                            class="text-blue-600 text-sm hover:underline">
                            Edit
                        </button>

                        <form method="POST"
                            action="{{ route('supplier.items.destroy', [$companyCode, $supplier->id, $item->id]) }}"
                            onsubmit="return confirm('Hapus item ini dari supplier?')">

                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 text-sm hover:underline">
                                Hapus
                            </button>
                        </form>

                    </div>
                </div>
            @endforeach

        </div>
    @endif

</div>
