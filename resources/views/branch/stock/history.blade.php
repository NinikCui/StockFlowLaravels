<x-app-layout :branchCode="$branchCode">

    <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Penyesuaian Stok</h1>
            <p class="text-gray-600 mt-1">
                Riwayat perubahan stok untuk item berikut pada cabang ini.
            </p>
        </div>

        {{-- ITEM INFO --}}
        <div class="bg-white border rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $item->name }}
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">
                        Kode Stok: <span class="font-mono">{{ $stock->code }}</span>
                    </p>
                    <p class="text-gray-500 text-sm">
                        Gudang: <span class="font-semibold">{{ $stock->warehouse->name }}</span>
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">Stok Sekarang</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ number_format($stock->qty, 0, ',', '.') }}
                        <span class="text-base text-gray-500">{{ $item->satuan->name }}</span>
                    </p>
                </div>

            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-xl shadow-sm border">

            {{-- ISSUE --}}
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Kategori Issue</label>
                <select name="issue"
                    class="w-full border px-3 py-2 rounded-lg">
                    <option value="">Semua Issue</option>
                    @foreach ($categoriesIssues as $issue)
                        <option value="{{ $issue->name }}"
                            @selected(request('issue') == $issue->name)>
                            {{ $issue->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- USER --}}
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">User</label>
                <select name="user"
                    class="w-full border px-3 py-2 rounded-lg">
                    <option value="">Semua User</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}"
                            @selected(request('user') == $u->id)>
                            {{ $u->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DATE FROM --}}
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}"
                    class="w-full border px-3 py-2 rounded-lg">
            </div>

            {{-- DATE TO --}}
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}"
                    class="w-full border px-3 py-2 rounded-lg">
            </div>

            <div class="col-span-1 lg:col-span-4">
                <button 
                    class="w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700">
                    Terapkan Filter
                </button>
            </div>

        </form>

        {{-- HISTORY TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Issue</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold text-gray-700">Qty</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($history as $h)
                        <tr class="hover:bg-gray-50">

                            {{-- DATE --}}
                            <td class="py-3 px-4 text-gray-800">
                                {{ \Carbon\Carbon::parse($h->date)->format('d M Y H:i') }}
                            </td>

                            {{-- ISSUE --}}
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-lg
                                        bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $h->issue_name }}
                                </span>
                            </td>

                            {{-- QTY --}}
                            <td class="py-3 px-4 text-right font-semibold">

                                {{-- BEFORE/AFTER --}}
                                <div class="text-sm text-gray-500">
                                    {{ number_format($h->prev_qty, 0, ',', '.') }}
                                    →
                                    {{ number_format($h->after_qty, 0, ',', '.') }}
                                </div>

                                {{-- DIFF --}}
                                @if ($h->diff > 0)
                                    <span class="text-emerald-600 font-bold">
                                        +{{ number_format($h->diff, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-red-600 font-bold">
                                        {{ number_format($h->diff, 0, ',', '.') }}
                                    </span>
                                @endif

                            </td>

                            {{-- USER --}}
                            <td class="py-3 px-4 text-gray-700">
                                {{ $h->created_by_name ?? '-' }}
                            </td>

                            {{-- NOTE --}}
                            <td class="py-3 px-4 text-gray-700">
                                {{ $h->note ?: '-' }}
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                Tidak ada riwayat penyesuaian stok.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
