@unless ($breadcrumbs->isEmpty())
<nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        @foreach ($breadcrumbs as $breadcrumb)
            <li class="inline-flex items-center">
                @if (! $loop->first)
                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clip-rule="evenodd" />
                    </svg>
                @endif

                @if ($breadcrumb->url && ! $loop->last)
                    <a href="{{ $breadcrumb->url }}"
                       class="font-medium hover:text-emerald-600 transition">
                        {{ $breadcrumb->title }}
                    </a>
                @else
                    <span class="font-semibold text-gray-900">
                        {{ $breadcrumb->title }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endunless
