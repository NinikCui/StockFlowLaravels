<x-app-layout :companyCode="$companyCode">

<div class="max-w-6xl mx-auto px-6 py-10">

    <h1 class="text-2xl font-bold mb-6">Performa Cabang</h1>

    <div class="bg-white shadow border rounded-xl overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Cabang</th>
                    <th class="p-3">Request Keluar</th>
                    <th class="p-3">Request Masuk</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($cabang as $c)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $c->name }}</td>

                    <td class="p-3">
                        {{ $requestKeluar[$c->id]->total_keluar ?? 0 }}
                    </td>

                    <td class="p-3">
                        {{ $requestMasuk[$c->id]->total_masuk ?? 0 }}
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

</x-app-layout>
