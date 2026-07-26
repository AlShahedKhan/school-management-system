<header {{ $attributes->class(['topbar']) }}>
    <div class="flex items-center gap-3">
        {{ $leading ?? '' }}
    </div>

    <div class="flex flex-row-reverse items-center gap-2 relative z-50 topbar-actions">
        {{ $actions ?? $slot }}
    </div>
</header>
