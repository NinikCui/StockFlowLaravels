@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
        <div class="font-semibold text-red-800 mb-2">Terjadi kesalahan:</div>

        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
