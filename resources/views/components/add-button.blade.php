@props([])

@php
    $variants = [
        'primary' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'outline' => 'border border-gray-400 text-gray-700 hover:bg-gray-100',
    ];

    // SIZE STYLES
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' =>
           "{$variants[$variant]} {$sizes[$size]} rounded-xl shadow transition inline-flex items-center gap-2"
   ]) }}
>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" class="w-4 h-4" />
    @endif

    {{ $text }}
</a>