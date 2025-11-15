<x-app-layout>

@php
    $companyId = session('role.company.id');
@endphp

<main class="min-h-screen px-6 py-10 bg-gray-50 max-w-4xl mx-auto">

    <h1 class="text-3xl font-black text-gray-900 mb-6">
        Pengaturan Perusahaan
    </h1>

    @if(session('success'))
        <div class="p-3 mb-5 bg-emerald-100 text-emerald-700 rounded-lg border border-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update', $companyCode) }}" method="POST" class="space-y-10">
        @csrf

        {{-- GLOBAL INFO --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Informasi Perusahaan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-600">Nama Perusahaan</label>
                    <input type="text" name="company_name"
                           value="{{ $settings['global.company_name'] ?? '' }}"
                           class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Footer Struk</label>
                    <input type="text" name="receipt_footer"
                           value="{{ $settings['global.receipt_footer'] ?? '' }}"
                           class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>
            </div>
        </div>

        {{-- BRAND --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Branding</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-600">Primary Color</label>
                    <input type="color" name="primary_color"
                           value="{{ $settings['global.primary_color'] ?? '#059669' }}"
                           class="w-20 mt-1 h-10 rounded">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Secondary Color</label>
                    <input type="color" name="secondary_color"
                           value="{{ $settings['global.secondary_color'] ?? '#2563EB' }}"
                           class="w-20 mt-1 h-10 rounded">
                </div>
            </div>
        </div>

        {{-- FINANCE --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="font-semibold text-xl mb-4">Finance Settings</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-600">PPN (%)</label>
                    <input type="number" step="0.1" name="ppn_rate"
                           value="{{ $settings['global.ppn_rate'] ?? 11 }}"
                           class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Service Charge (%)</label>
                    <input type="number" step="0.1" name="service_charge"
                           value="{{ $settings['global.service_charge'] ?? 5 }}"
                           class="w-full mt-1 border rounded-xl px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                Simpan Pengaturan
            </button>
        </div>

    </form>
</main>

</x-app-layout>
