@props([
    'label',
    'value',
    'trend' => null,
    'trendColor' => 'emerald',
])

@php
    $trendColors = [
        'emerald' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
        'rose' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
        'amber' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
        'brand' => 'bg-brand-500/10 text-brand-600 border-brand-500/20',
        'slate' => 'bg-slate-500/10 text-slate-600 border-slate-500/20',
    ];

    $tColor = $trendColors[$trendColor] ?? $trendColors['emerald'];
@endphp

<div {{ $attributes->merge(['class' => 'premium-card p-5 group flex items-center justify-between overflow-hidden relative']) }}>
    {{-- Subtle Glow Background based on trendColor --}}
    <div class="pointer-events-none absolute -inset-px opacity-10 transition-opacity duration-500"
         style="background: radial-gradient(120px circle at 90% 10%, {{ $trendColor === 'brand' ? 'rgba(59,130,246,0.2)' : ($trendColor === 'amber' ? 'rgba(245,158,11,0.2)' : 'rgba(16,185,129,0.2)') }}, transparent 80%);">
    </div>

    <div class="flex flex-col gap-1 relative z-10">
        <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{ $label }}</span>
        <span class="text-xl font-black font-display tracking-tight text-main">{{ $value }}</span>
    </div>

    @if($trend)
        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-tighter border {{ $tColor }}">
            {{ $trend }}
        </span>
    @endif
</div>
