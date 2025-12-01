<x-app-layout :branchCode="$branchCode">

<main class="max-w-4xl mx-auto px-6 py-10">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Edit Supplier</h1>

        <a href="{{ route('branch.supplier.show', [$branchCode, $supplier->id]) }}"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 text-sm font-medium">
            ← Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
        <form action="{{ route('branch.supplier.update', [$branchCode, $supplier->id]) }}"
              method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- NAME --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier</label>
                <input type="text" name="name" required
                    value="{{ old('name', $supplier->name) }}"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-emerald-300 focus:border-emerald-500">
            </div>

            {{-- CONTACT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kontak (PIC)</label>
                    <input type="text" name="contact_name"
                        value="{{ old('contact_name', $supplier->contact_name) }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $supplier->phone) }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            {{-- EMAIL & CITY --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', $supplier->email) }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kota</label>
                    <input type="text" name="city"
                        value="{{ old('city', $supplier->city) }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            {{-- ADDRESS --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2"
                    class="w-full border rounded-lg px-4 py-2">{{ old('address', $supplier->address) }}</textarea>
            </div>

            {{-- NOTES --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3"
                    class="w-full border rounded-lg px-4 py-2">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            {{-- STATUS --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="is_active"
                    class="w-full border rounded-lg px-4 py-2">
                    <option value="1" {{ $supplier->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$supplier->is_active ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- ACTIONS --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('branch.supplier.show', [$branchCode, $supplier->id]) }}"
                    class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 
                         hover:bg-gray-100 text-sm shadow-sm transition">
                        Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 
                                text-sm shadow-sm transition">
                    Simpan
                </button>
            </div>

        </form>
    </div>

</main>

</x-app-layout>
