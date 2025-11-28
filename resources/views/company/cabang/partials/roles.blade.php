<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

    <h2 class="text-xl font-bold text-gray-900 mb-5">Role di Cabang Ini</h2>

    @if ($roles->isEmpty())
        <p class="text-sm text-gray-500">Tidak ada role untuk cabang ini.</p>
    @else
        <ul class="space-y-3">
            @foreach ($roles as $r)
                <li class="border border-gray-200 rounded-xl px-4 py-3 flex justify-between items-center text-sm bg-gray-50">
                    <span class="font-medium text-gray-700">{{ $r->code }}</span>

                </li>
            @endforeach
        </ul>
    @endif

</div>
