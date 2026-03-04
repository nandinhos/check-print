@props([
    'variant' => 'neutral',
    'pulse' => false,
])

@php
    $variants = [
        'neutral' => 'bg-slate-100/50 text-slate-600 border-slate-200/50',
        'primary' => 'bg-brand-500/10 text-brand-600 border-brand-500/20',
        'brand' => 'bg-brand-500/10 text-brand-600 border-brand-500/20',
        'secondary' => 'bg-slate-500/10 text-slate-600 border-slate-500/20',
        'success' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
        'warning' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
        'danger' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
    ];

    $pulseColors = [
        'neutral' => 'bg-slate-400',
        'primary' => 'bg-brand-500',
        'brand' => 'bg-brand-500',
        'secondary' => 'bg-slate-500',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
    ];

    $classes = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border ' . ($variants[$variant] ?? $variants['neutral']);
    $pulseColor = $pulseColors[$variant] ?? $pulseColors['neutral'];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($pulse)
        <span class="relative flex h-1.5 w-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $pulseColor }} opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $pulseColor }}"></span>
        </span>
    @endif
    {{ $slot }}
</span>
