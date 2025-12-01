<x-app-layout :branchCode="$branchCode">

<div class="max-w-3xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Tambah Supplier</h1>

    <form method="POST" action="{{ route('branch.supplier.store',  $branchCode) }}"
          class="bg-white p-6 rounded-xl shadow border space-y-6">
        @csrf

        {{-- NAME --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Nama Supplier *</label>
            <input type="text" name="name" required
                class="w-full px-4 py-3 border rounded-xl" value="{{ old('name') }}">
        </div>

        {{-- CONTACT PERSON --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Contact Person</label>
            <input type="text" name="contact_name"
                class="w-full px-4 py-3 border rounded-xl" value="{{ old('contact_name') }}">
        </div>

        {{-- PHONE --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">No. Telepon</label>
            <input type="text" name="phone"
                class="w-full px-4 py-3 border rounded-xl" value="{{ old('phone') }}">
        </div>

        {{-- EMAIL --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Email</label>
            <input type="email" name="email"
                class="w-full px-4 py-3 border rounded-xl" value="{{ old('email') }}">
        </div>

        {{-- ADDRESS --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Alamat</label>
            <textarea name="address" rows="3"
                class="w-full px-4 py-3 border rounded-xl">{{ old('address') }}</textarea>
        </div>

        {{-- CITY --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Kota</label>
            <input type="text" name="city"
                class="w-full px-4 py-3 border rounded-xl" value="{{ old('city') }}">
        </div>

        {{-- NOTES --}}
        <div>
            <label class="font-medium text-gray-700 block mb-1">Catatan</label>
            <textarea name="notes" rows="3"
                class="w-full px-4 py-3 border rounded-xl">{{ old('notes') }}</textarea>
        </div>

        {{-- SUBMIT --}}
        <div class="pt-4">
            <button type="submit"
                class="px-6 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700">
                Simpan Supplier
            </button>
        </div>

    </form>
</div>

</x-app-layout>
