@props([
    'label' => null,
    'icon' => null,
    'error' => null,
    'type' => 'text',
])

@php
    $inputClasses = 'w-full bg-white/40 dark:bg-zinc-900/40 border border-glass backdrop-blur-md rounded-2xl text-sm font-medium text-main placeholder:text-muted focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 focus:bg-white dark:focus:bg-zinc-900 transition-all shadow-sm py-3';
    $inputClasses .= $icon ? ' pl-12 pr-4' : ' px-4';
    if ($error) {
        $inputClasses .= ' border-rose-500/50 ring-rose-500/10';
    }
@endphp

<div class="flex flex-col gap-1.5 w-full group">
    @if($label)
        <label class="block text-xs font-bold text-muted uppercase tracking-wider ml-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px] transition-colors group-focus-within:text-brand-500">
                {{ $icon }}
            </span>
        @endif

        <input type="{{ $type }}" {{ $attributes->merge(['class' => $inputClasses]) }}>

        @if($error)
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-rose-500 text-[18px]">
                error
            </span>
        @endif
    </div>

    @if($error)
        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-tight ml-1">
            {{ $error }}
        </p>
    @endif
</div>
