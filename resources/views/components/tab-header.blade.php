@props(['tabs', 'active'])

<div class="flex border-b border-gray-200 mb-8">
    @foreach ($tabs as $key => $label)
        <a href="?tab={{ $key }}"
           class="
               px-4 py-2 font-semibold text-sm border-b-2 transition mr-6
               {{ $active === $key ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent' }}
           ">
            {{ $label }}
        </a>
    @endforeach
</div>