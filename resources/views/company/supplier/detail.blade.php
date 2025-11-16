<x-app-layout>
    <main class="min-h-screen px-6 py-10 bg-gray-50">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    Detail Supplier
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap pemasok beserta item yang disuplai.
                </p>
            </div>

            <a href="/{{ $companyCode }}/supplier"
               class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                ← Kembali
            </a>
        </div>

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- ===================================== --}}
            {{-- 🔵 INFORMASI SUPPLIER --}}
            {{-- ===================================== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

                <div class="flex items-start justify-between">
                    <h2 class="text-xl font-bold text-gray-900">
                        Informasi Supplier
                    </h2>

                    {{-- Status --}}
                    <span class="
                        px-3 py-1 text-xs font-semibold rounded-full
                        {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}
                    ">
                        {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">

                    <div class="space-y-1">
                        <p class="text-gray-500">Nama Supplier</p>
                        <p class="font-semibold text-gray-800">{{ $supplier->name }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">PIC / Kontak</p>
                        <p class="font-semibold text-gray-800">{{ $supplier->contact_name ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Telepon</p>
                        <p class="font-semibold text-gray-800">{{ $supplier->phone ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Email</p>
                        <p class="font-semibold text-gray-800">{{ $supplier->email ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-gray-500">Kota</p>
                        <p class="font-semibold text-gray-800">{{ $supplier->city ?? '-' }}</p>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-semibold text-gray-800 leading-relaxed">{{ $supplier->address ?? '-' }}</p>
                    </div>

                    @if ($supplier->notes)
                        <div class="sm:col-span-2 space-y-1">
                            <p class="text-gray-500">Catatan</p>
                            <p class="font-semibold text-gray-800 leading-relaxed">{{ $supplier->notes }}</p>
                        </div>
                    @endif

                </div>

                {{-- ACTIONS --}}
                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('supplier.edit', [$companyCode, $supplier->id]) }}"
                       class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium shadow hover:bg-emerald-700 transition">
                        ✎ Edit Supplier
                    </a>

                    <form action="{{ route('supplier.destroy', [$companyCode, $supplier->id]) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus supplier ini?')">
                        @csrf
                        @method('DELETE')
                        <button
                            class="px-5 py-2 rounded-xl bg-red-600 text-white text-sm font-medium shadow hover:bg-red-700 transition">
                            🗑 Hapus
                        </button>
                    </form>
                </div>

            </div>



            {{-- ===================================== --}}
            {{-- 🟣 ITEM YANG DISUPLAI --}}
            {{-- ===================================== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Item yang Disuplai</h2>

                    <a href="#" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                        + Tambah Item
                    </a>
                </div>

                <div class="mt-5">
                    @if ($supplier->items->isEmpty())
                        <p class="text-sm text-gray-500">Supplier belum memiliki item.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($supplier->items as $si)
                                <li class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $si->item->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Harga: Rp {{ number_format($si->price) }}  
                                            • MOQ: {{ $si->min_order_qty }}  
                                            • Update: {{ $si->last_price_update }}
                                        </p>
                                    </div>

                                    <div class="flex gap-3">
                                        <a href="#" class="text-blue-600 text-sm hover:text-blue-800">Edit</a>
                                        <a href="#" class="text-red-600 text-sm hover:text-red-800">Hapus</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>



            {{-- ===================================== --}}
            {{-- 🟤 PERFORMANCE SUPPLIER --}}
            {{-- ===================================== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Performance Supplier</h2>

                    <a href="#" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                        + Tambah Penilaian
                    </a>
                </div>

                <div class="mt-5">
                    @if ($supplier->scores->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada penilaian.</p>
                    @else
                        <div class="overflow-hidden border border-gray-200 rounded-xl">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 border-b text-gray-600">
                                    <tr>
                                        <th class="py-2 px-4">On-Time</th>
                                        <th class="px-4">Reject Rate</th>
                                        <th class="px-4">Quality</th>
                                        <th class="px-4">Variance</th>
                                        <th class="px-4">Notes</th>
                                        <th class="px-4">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplier->scores as $sc)
                                        <tr class="border-b">
                                            <td class="py-2 px-4">{{ $sc->on_time_rate }}%</td>
                                            <td class="px-4">{{ $sc->reject_rate }}%</td>
                                            <td class="px-4">{{ $sc->avg_quality }}</td>
                                            <td class="px-4">{{ $sc->price_variance }}%</td>
                                            <td class="px-4">{{ $sc->notes ?? '-' }}</td>
                                            <td class="px-4">{{ $sc->calculated_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </main>
</x-app-layout>
