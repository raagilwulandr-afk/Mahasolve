@props(['active' => false, 'href' => '#'])

@php
    $classes = $active
        ? 'px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 bg-indigo-50 text-indigo-700 font-bold border border-indigo-100/80 shadow-sm'
        : 'px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-50';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
