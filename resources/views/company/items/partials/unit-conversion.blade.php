{{-- SUCCESS MESSAGE --}}

<div class="max-w-5xl mx-auto mt-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6 px-2">
        <h2 class="text-xl font-semibold text-gray-900">
            Konversi Satuan
        </h2>

        <x-crud-add
            resource="unit-conversion"
            :companyCode="$companyCode"
            permissionPrefix="item"
        />
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <div class="overflow-hidden border border-gray-200 rounded-xl">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border-b text-center">Dari</th>
                        <th class="p-3 border-b text-center">Ke</th>
                        <th class="p-3 border-b text-center">Nilai</th>
                        <th class="p-3 border-b text-center w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($unitConversions as $uc)
                        <tr class="hover:bg-gray-50">

                            <td class="p-3 border-b text-center">
                                {{ $uc->fromSatuan->code }}
                            </td>

                            <td class="p-3 border-b text-center">
                                {{ $uc->toSatuan->code }}
                            </td>

                            <td class="p-3 border-b text-center">
                                <div class="font-semibold text-gray-900">
                                    1 {{ $uc->fromSatuan->code }}
                                    =
                                    {{ $uc->formatted_factor }}
                                    {{ $uc->toSatuan->code }}
                                </div>

                            </td>

                            <td class="p-3 border-b">
                                <div class="flex items-center justify-center gap-2">

                                    @if (
                                        $uc->fromSatuan->company_id !== null ||
                                        $uc->toSatuan->company_id !== null
                                    )
                                        <x-crud
                                            resource="unit-conversion"
                                            :model="$uc"
                                            keyField="id"
                                            :companyCode="$companyCode"
                                            permissionPrefix="item"
                                        />
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            Default
                                        </span>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-500">
                                Belum ada konversi satuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
