<x-app-layout :branchCode="$branchCode">

<div class="max-w-xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Gudang</h1>
        <p class="text-gray-600 mt-1">
            Tambahkan gudang baru untuk cabang <strong>{{ $branch->name }}</strong>.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 shadow-sm">

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                <ul class="list-disc ml-4 text-sm">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('branch.warehouse.store', $branchCode) }}" class="space-y-6">
            @csrf

            {{-- NAME --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Nama Gudang</label>
                <input type="text" name="name"
                       value="{{ old('name') }}"
                       class="w-full border-gray-300 rounded-lg px-4 py-2
                              focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            {{-- CODE --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kode Gudang</label>
                <input type="text" name="code"
                       value="{{ old('code') }}"
                       class="w-full border-gray-300 rounded-lg px-4 py-2 uppercase
                              focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: G01" required>
            </div>

            {{-- TYPE --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Tipe Gudang</label>
                <select name="warehouse_type_id"
                        class="w-full border-gray-300 rounded-lg px-4 py-2
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <option value="">-- Pilih Tipe Gudang --</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}" @selected(old('warehouse_type_id') == $t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTON --}}
            <div class="flex items-center gap-3">
                <button 
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg 
                           hover:bg-emerald-700 shadow-sm transition font-medium">
                    Simpan
                </button>

                <a href="{{ route('branch.warehouse.index', $branchCode) }}"
                   class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg transition hover:bg-gray-200 shadow-sm">
                    Batal
                </a>
            </div>

        </form>

    </div>

</div>

</x-app-layout>
