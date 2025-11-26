<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

    <h2 class="text-xl font-bold text-gray-900 mb-5">Pegawai di Cabang Ini</h2>

    @if ($pegawai->isEmpty())
        <p class="text-sm text-gray-500">Belum ada pegawai di cabang ini.</p>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($pegawai as $p)
                <div class="border border-gray-200 bg-white shadow-sm rounded-xl p-4 relative">

                    {{-- Manager Badge --}}
                    @if ($cabang->manager_user_id == $p['id'])
                        <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-lg absolute top-3 right-3">
                            Manager
                        </span>
                    @endif

                    <p class="font-semibold text-gray-800">{{ $p['username'] }}</p>
                    <p class="text-sm text-gray-500">{{ $p['email'] }}</p>

                    <p class="text-xs text-gray-400 mt-1">
                        Role: {{ $p['role_name'] }}
                    </p>

                    <a href="/{{ $companyCode }}/pegawai/edit/{{ $p['id'] }}"
                       class="text-emerald-600 text-sm mt-3 inline-block font-medium hover:text-emerald-700">
                        Edit →
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>
