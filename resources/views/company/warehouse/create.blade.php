<x-app-layout>
    <main class="max-w-3xl mx-auto px-6 py-10">

        {{-- BREADCRUMB + BACK --}}
        <div class="mb-6 space-y-2">
            <div class="text-sm text-gray-500">
                {{ Breadcrumbs::render(
                    'company.warehouse.create',
                    $companyCode
                ) }}
            </div>

            <a href="{{ route('warehouse.index', $companyCode) }}"
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition">
                ← Kembali ke daftar warehouse
            </a>
        </div>

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Tambah Warehouse
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Tambahkan gudang baru untuk pengelolaan stok perusahaan.
            </p>
        </div>

        {{-- FORM CARD --}}
        <form action="{{ route('warehouse.store', $companyCode) }}"
              method="POST"
              class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
            @csrf

            {{-- CABANG --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cabang Restoran <span class="text-red-500">*</span>
                </label>

                <select name="cabang_resto_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm
                               focus:ring-emerald-200 focus:border-emerald-500"
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
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Warehouse <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm
                              focus:ring-emerald-200 focus:border-emerald-500"
                       placeholder="Contoh: Gudang Utama"
                       required>
            </div>

            {{-- KODE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Warehouse <span class="text-gray-400">(Opsional)</span>
                </label>
                <input type="text"
                       name="code"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm
                              focus:ring-emerald-200 focus:border-emerald-500"
                       placeholder="Contoh: WH-UTAMA">
            </div>

            {{-- TIPE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipe Warehouse
                </label>

                <select name="warehouse_type_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm
                               focus:ring-emerald-200 focus:border-emerald-500">
                    <option value="">Pilih Tipe</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <a href="{{ route('warehouse.index', [$companyCode, 'tab' => 'types']) }}"
                   class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:underline mt-2">
                    + Kelola Tipe Warehouse
                </a>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('warehouse.index', $companyCode) }}"
                   class="px-4 py-2 rounded-lg border border-gray-300 text-sm
                          text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-emerald-600 text-white text-sm
                               hover:bg-emerald-700 transition">
                    Simpan Warehouse
                </button>
            </div>

        </form>

    </main>
</x-app-layout>
