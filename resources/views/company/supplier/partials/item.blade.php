<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">Item yang Disuplai</h2>

        <a href="#" class="text-emerald-600 text-sm hover:text-emerald-700">
            + Tambah Item
        </a>
    </div>

    <div class="mt-5">
        @if ($supplier->items->isEmpty())
            <p class="text-sm text-gray-500">Belum ada item.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach ($supplier->items as $si)
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $si->item->name }}</p>
                            <p class="text-xs text-gray-500">
                                Harga: Rp {{ number_format($si->price) }}
                                • MOQ: {{ $si->min_order_qty }}
                                • Update: {{ $si->last_price_update }}
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <a href="#" class="text-blue-600 text-sm">Edit</a>
                            <a href="#" class="text-red-600 text-sm">Hapus</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
