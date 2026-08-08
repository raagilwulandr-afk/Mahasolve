@props(['status'])

@php
    $statusStr = is_object($status) ? $status->value : (string) $status;
    $config = match (strtolower($statusStr)) {
        'selesai', 'dikonfirmasi', 'disepakati' => [
            'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'label' => ucfirst(str_replace('_', ' ', $statusStr)),
        ],
        'dikerjakan', 'diproses', 'menunggu_pengerjaan' => [
            'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            'label' => ucfirst(str_replace('_', ' ', $statusStr)),
        ],
        'pending', 'ditawar_ulang' => [
            'bg' => 'bg-amber-50 text-amber-700 border-amber-100',
            'label' => ucfirst(str_replace('_', ' ', $statusStr)),
        ],
        'dibatalkan', 'ditolak', 'gagal' => [
            'bg' => 'bg-rose-50 text-rose-700 border-rose-100',
            'label' => ucfirst(str_replace('_', ' ', $statusStr)),
        ],
        default => [
            'bg' => 'bg-slate-50 text-slate-700 border-slate-100',
            'label' => ucfirst(str_replace('_', ' ', $statusStr)),
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-1 text-[11px] font-bold rounded-full border inline-flex items-center gap-1 {$config['bg']}"]) }}>
    {{ $config['label'] }}
</span>
