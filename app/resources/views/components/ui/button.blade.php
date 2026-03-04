@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-bold tracking-tight rounded-xl transition-all duration-300 active:scale-[0.97] disabled:opacity-50 disabled:pointer-events-none ring-offset-2 dark:ring-offset-[#020617] focus:outline-none focus:ring-2 border';

    $variants = [
        'primary' => 'bg-gradient-to-b from-brand-500 to-brand-600 text-white shadow-[0_4px_12px_rgba(59,130,246,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)] hover:from-brand-400 hover:to-brand-500 hover:shadow-[0_6px_20px_rgba(59,130,246,0.4),inset_0_1px_1px_rgba(255,255,255,0.3)] ring-brand-500/40 border-transparent dark:border-brand-400/20',
        'secondary' => 'bg-white/40 dark:bg-white/5 text-slate-900 dark:text-slate-100 border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:bg-white/60 dark:hover:bg-white/10 ring-brand-500/20',
        'success' => 'bg-gradient-to-b from-emerald-500 to-emerald-600 text-white shadow-[0_4px_12px_rgba(16,185,129,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)] hover:from-emerald-400 hover:to-emerald-500 hover:shadow-[0_6px_20px_rgba(16,185,129,0.4)] ring-emerald-500/40 border-transparent dark:border-emerald-400/20',
        'frosted' => 'bg-white/10 dark:bg-white/5 text-slate-900 dark:text-white border-white/20 dark:border-white/5 shadow-[0_4px_12_px_rgba(0,0,0,0.05),inset_0_1px_1px_rgba(255,255,255,0.4)] backdrop-blur-2xl saturate-150 hover:bg-white/20 dark:hover:bg-white/10 ring-brand-500/20',
        'ghost' => 'text-secondary hover:text-main hover:bg-white/40 dark:hover:bg-white/5 ring-slate-200/20 backdrop-blur-sm border-transparent',
        'danger' => 'bg-gradient-to-b from-rose-500 to-rose-600 text-white shadow-[0_4px_12px_rgba(244,63,94,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)] hover:from-rose-400 hover:to-rose-500 hover:shadow-[0_6px_20px_rgba(244,63,94,0.4)] ring-rose-500/40 border-transparent dark:border-rose-400/20',
    ];

    $sizes = [
        'xs' => 'px-3 py-1.5 text-[10px] gap-1.5',
        'sm' => 'px-4 py-2 text-xs gap-2',
        'md' => 'px-6 py-2.5 text-sm gap-2',
        'lg' => 'px-8 py-3.5 text-base gap-2.5',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
        @elseif($icon)
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        @endif

        <span>{{ $slot }}</span>

        @if(!$loading && $iconRight)
            <span class="material-symbols-outlined text-[20px]">{{ $iconRight }}</span>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
        @if($loading)
            <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
        @elseif($icon)
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        @endif

        <span>{{ $slot }}</span>

        @if(!$loading && $iconRight)
            <span class="material-symbols-outlined text-[20px]">{{ $iconRight }}</span>
        @endif
    </button>
@endif
