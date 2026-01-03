<x-app-layout>
<main class="max-w-3xl mx-auto px-6 py-10">
{{-- BREADCRUMB + BACK --}}
        <div class="mb-8 space-y-2">
            <div class="text-sm text-gray-500">
                {{ Breadcrumbs::render(
                    'company.warehouse.edit',
                    $companyCode,
                    $warehouse
                ) }}
            </div>

            <a href="{{ route('warehouse.show', [$companyCode, $warehouse->id]) }}"
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition">
                ← Kembali ke daftar warehouse
            </a>
        </div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900">Edit Warehouse</h1>

        
    </div>

    {{-- FORM --}}
    <form action="{{ route('warehouse.update', [$companyCode, $warehouse->id]) }}" 
          method="POST" 
          class="bg-white p-6 rounded-2xl border shadow-sm space-y-5">
        @csrf
        @method('PUT')

        {{-- CABANG --}}
        <div>
            <label class="block text-sm font-semibold mb-1">Cabang Restoran</label>

            <select name="cabang_resto_id"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    required>
                <option value="">Pilih Cabang</option>
                @foreach ($cabangs as $c)
                    <option value="{{ $c->id }}"
                        {{ $warehouse->cabang_resto_id == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- NAMA --}}
        <div>
            <label class="block text-sm font-semibold mb-1">Nama Warehouse</label>
            <input type="text" 
                   name="name" 
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   value="{{ old('name', $warehouse->name) }}" 
                   required>
        </div>

        {{-- KODE --}}
        <div>
            <label class="block text-sm font-semibold mb-1">Kode</label>
            <input type="text" 
                   name="code" 
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   value="{{ old('code', $warehouse->code) }}">
        </div>

        {{-- TIPE --}}
        <div>
            <label class="block text-sm font-semibold mb-1">Tipe Warehouse</label>

            <select name="warehouse_type_id"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Tipe</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}"
                        {{ $warehouse->warehouse_type_id == $t->id ? 'selected' : '' }}>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('warehouse.index', [$companyCode, 'tab' => 'types']) }}"
               class="text-xs text-blue-600 hover:underline mt-1 block">
               + Kelola Tipe Warehouse
            </a>
        </div>


        {{-- SUBMIT --}}
        <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
            Perbarui
        </button>

    </form>

</main>
</x-app-layout>
