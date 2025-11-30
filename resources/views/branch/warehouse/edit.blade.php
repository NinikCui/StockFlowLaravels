<x-app-layout :branchCode="$branchCode">

    <div class="max-w-3xl mx-auto px-6 py-10">

        {{-- TITLE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Gudang</h1>
            <p class="text-gray-600 text-sm mt-1">
                Perbarui informasi gudang milik cabang ini.
            </p>
        </div>

        {{-- CARD --}}
        <div class="bg-white border rounded-2xl p-6 shadow-sm">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ml-4 text-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST"
                  action="{{ route('branch.warehouse.update', [$branchCode, $warehouse->id]) }}"
                  class="space-y-6">
                @csrf

                {{-- NAME --}}
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">
                        Nama Gudang
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $warehouse->name) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-2 
                                  focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           required>
                </div>

                {{-- CODE --}}
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">
                        Kode Gudang
                    </label>
                    <input type="text" name="code"
                           value="{{ old('code', $warehouse->code) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-2 uppercase
                                  focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           required>

                    @error('code')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TYPE --}}
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">
                        Tipe Gudang
                    </label>
                    <select name="warehouse_type_id"
                            class="w-full border-gray-300 rounded-lg px-4 py-2 bg-white 
                                   focus:ring-2 focus:ring-emerald-500">
                        @foreach ($types as $t)
                            <option value="{{ $t->id }}"
                                    @selected($warehouse->warehouse_type_id == $t->id)>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center gap-3 pt-4">
                    <button
                        class="px-5 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition font-medium">
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('branch.warehouse.index', $branchCode) }}"
                       class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm 
                              hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>

</x-app-layout>
