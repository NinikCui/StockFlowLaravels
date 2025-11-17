<x-app-layout>
<main class="max-w-3xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900">Tambah Warehouse</h1>

        <a href="{{ route('warehouse.index', $companyCode) }}"
            class="text-gray-500 text-sm hover:text-gray-700">← Kembali</a>
    </div>

    {{-- FORM --}}
    <form action="{{ route('warehouse.store', $companyCode) }}" 
          method="POST" 
          class="bg-white p-6 rounded-2xl border shadow-sm space-y-5">
        @csrf

        {{-- CABANG --}}
        <div>
            <label class="block text-sm text-gray-700 font-medium mb-1">
                Cabang Restoran
            </label>

            <select name="cabang_resto_id"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    required>
                <option value="">Pilih Cabang</option>

                @foreach ($cabangs as $c)
                    <option value="{{ $c->id }}">
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- NAMA --}}
        <div>
            <label class="block text-sm text-gray-700 font-medium mb-1">Nama Warehouse</label>
            <input type="text" 
                   name="name" 
                   class="w-full border rounded-lg px-3 py-2 text-sm" 
                   required>
        </div>

        {{-- KODE --}}
        <div>
            <label class="block text-sm text-gray-700 font-medium mb-1">Kode (opsional)</label>
            <input type="text" 
                   name="code" 
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   placeholder="Contoh: WH-UTAMA">
        </div>

        {{-- TIPE --}}
        <div>
            <label class="block text-sm text-gray-700 font-medium mb-1">Tipe Warehouse</label>

            <select name="warehouse_type_id" 
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Tipe</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>

            {{-- Link kelola tipe, langsung ke TAB 2 --}}
            <a href="{{ route('warehouse.index', [$companyCode, 'tab' => 'types']) }}"
                class="text-xs text-blue-600 hover:underline mt-1 block">
                + Kelola Tipe Warehouse
            </a>
        </div>

        {{-- SUBMIT --}}
        <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
            Simpan
        </button>

    </form>

</main>
</x-app-layout>
