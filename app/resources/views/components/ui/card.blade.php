@props([
    'title' => null,
    'subtitle' => null,
    'glow' => true,
    'glowColor' => 'rgba(59,130,246,0.15)',
])

<div {{ $attributes->merge(['class' => 'relative premium-card p-5 group flex flex-col h-full overflow-hidden']) }}
     @if($glow)
     x-data="{
         handleMouseMove(e) {
             let rect = $el.getBoundingClientRect();
             $el.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
             $el.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
         }
     }"
     @mousemove="handleMouseMove"
     style="--glow-color: {{ $glowColor }};"
     @endif
>
    @if($glow)
        <!-- Glow Layer -->
        <div class="pointer-events-none absolute -inset-px opacity-0 transition-opacity duration-500 group-hover:opacity-100"
             style="background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), var(--glow-color), transparent 40%); z-index: 0; border-radius: inherit;">
        </div>
    @endif

    @if($title || $subtitle)
        <div class="mb-4 shrink-0 relative z-10">
            @if($title)
                <h3 class="text-sm font-bold text-main font-display tracking-tight">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="relative z-10 flex-1 flex flex-col">
        {{ $slot }}
    </div>
</div>
