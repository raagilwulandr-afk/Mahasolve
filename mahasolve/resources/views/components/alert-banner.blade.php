@props(['type' => 'success', 'message' => ''])

@php
    $style = match ($type) {
        'error', 'danger' => 'bg-rose-50 border-rose-200 text-rose-800 text-rose-900 text-rose-700',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800 text-amber-900 text-amber-700',
        default => 'bg-emerald-50 border-emerald-200 text-emerald-800 text-emerald-900 text-emerald-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-2xl border flex items-start gap-3 shadow-sm {$style}"]) }}>
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1 -18 0 9 9 0 0 1 18 0z"/>
    </svg>
    <div>
        <p class="text-xs font-bold font-display uppercase tracking-wider">{{ $type === 'error' ? 'Terjadi Kesalahan' : 'Pemberitahuan' }}</p>
        <p class="text-xs mt-0.5 font-medium">{{ $message ?? $slot }}</p>
    </div>
</div>
