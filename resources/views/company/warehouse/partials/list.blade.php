<div class="bg-white p-6 rounded-2xl border shadow-sm">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Warehouse</h2>

        <x-crud-add 
            resource="warehouse"
            :companyCode="$companyCode"
            permissionPrefix="warehouse"
        />
    </div>

    {{-- FILTER CABANG --}}
    <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- SEARCH --}}
        <input type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama / kode warehouse"
            class="px-4 py-2 border rounded-lg text-sm">

        {{-- FILTER CABANG --}}
        <select name="cabang" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Cabang</option>
            @foreach($cabangs as $c)
                <option value="{{ $c->id }}" @selected(request('cabang') == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>

        {{-- FILTER TIPE --}}
        <select name="type" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Tipe</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}" @selected(request('type') == $t->id)>
                    {{ $t->name }}
                </option>
            @endforeach
        </select>

        {{-- SORT --}}
        <select name="sort" class="px-4 py-2 border rounded-lg text-sm">
            <option value="name_asc"  @selected(request('sort')=='name_asc')>Nama A–Z</option>
            <option value="name_desc" @selected(request('sort')=='name_desc')>Nama Z–A</option>
            <option value="latest"    @selected(request('sort')=='latest')>Terbaru</option>
        </select>

        {{-- BUTTON --}}
        <div class="flex gap-2 col-span-full">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm">
                Terapkan
            </button>

            @if(request()->hasAny(['q','cabang','type','sort']))
                <a href="{{ route('warehouse.index', $companyCode) }}"
                class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
                    Reset
                </a>
            @endif
        </div>

    </form>


    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                    <th class="px-4 py-3 text-left font-semibold">Cabang</th>
                    <th class="px-4 py-3 text-right font-semibold w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($warehouses as $w)
                    <tr class="hover:bg-gray-50 transition border-b">

                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $w->name }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-md bg-gray-100 border text-gray-800 text-xs">
                                {{ $w->code }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $w->type->name ?? '-' }}
                        </td>

                        {{-- CABANG --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $w->cabangResto->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">

                                <a href="{{ route('warehouse.show', [$companyCode, $w->id]) }}"
                                   class="text-blue-600 hover:underline text-sm font-medium">
                                    Detail
                                </a>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Belum ada warehouse.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
