<div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
            {{ $title }}
        </p>
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $iconBg }}">
            <svg class="w-4 h-4 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $svg !!}
            </svg>
        </span>
    </div>

    <p class="text-3xl font-bold text-gray-900">
        {{ $value }}
    </p>
</div>
