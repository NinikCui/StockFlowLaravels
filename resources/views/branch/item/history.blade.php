<x-app-layout :branchCode="$branchCode">

<div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Riwayat Stok — {{ $item->name }}
        </h1>
        <p class="text-gray-600 mt-1">
            Menampilkan semua riwayat perubahan stok dari seluruh gudang untuk item ini.
        </p>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="bg-white p-4 rounded-xl shadow-sm border grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- DATE FROM --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
        </div>

        {{-- DATE TO --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
        </div>

        {{-- USER --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1">Pengguna</label>
            <select name="user"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Pengguna</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(request('user') == $u->id)>
                        {{ $u->username }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ISSUE CATEGORY --}}
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-1">Kategori Issue</label>
            <select name="issue"
                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach ($categoriesIssues as $ci)
                    <option value="{{ $ci->name }}" @selected(request('issue') == $ci->name)>
                        {{ $ci->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-full flex justify-end">
            <button class="bg-emerald-600 text-white px-6 py-2 rounded-lg shadow hover:bg-emerald-700">
                Terapkan Filter
            </button>
        </div>

    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Gudang</th>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Stock Code</th>
                    <th class="px-4 py-3 font-semibold text-right text-gray-700">Qty Sebelum</th>
                    <th class="px-4 py-3 font-semibold text-right text-gray-700">Qty Sesudah</th>
                    <th class="px-4 py-3 font-semibold text-right text-gray-700">Selisih</th>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Kategori</th>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Catatan</th>
                    <th class="px-4 py-3 font-semibold text-left text-gray-700">Pengguna</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($history as $row)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->date)->format('d M Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->warehouse_name }}
                        </td>

                        <td class="px-4 py-3 font-mono">
                            {{ $row->stock_code }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $row->prev_qty + 0 }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $row->after_qty + 0 }}
                        </td>

                        <td class="px-4 py-3 text-right font-bold
                            {{ $row->diff > 0 ? 'text-green-600' : ($row->diff < 0 ? 'text-red-600' : 'text-gray-600') }}">
                            {{ $row->diff + 0 }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->issue_name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->note ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->created_by_name ?? '-' }}
                        </td>

                    </tr>
                @empty

                    <tr>
                        <td colspan="9" class="text-center py-10 text-gray-500">
                            Tidak ada riwayat ditemukan.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</x-app-layout>
