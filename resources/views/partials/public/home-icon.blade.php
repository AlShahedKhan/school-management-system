@php
    $icon = $icon ?? 'layout-dashboard';
@endphp

@switch($icon)
    @case('users')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" />
            <circle cx="9.5" cy="7" r="3" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-3-3.87" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 4.13a4 4 0 0 1 0 7.75" />
        </svg>
        @break
    @case('graduation-cap')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 8 9-5 9 5-9 5-9-5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10.5V14c0 1.6 2.2 3 5 3s5-1.4 5-3v-3.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 9v6" />
        </svg>
        @break
    @case('credit-card')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="2.5" y="5" width="19" height="14" rx="2.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 10.5h19" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 15h3" />
        </svg>
        @break
    @case('clipboard-list')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="6" y="4" width="12" height="16" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5h6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 10 1.5 1.5L13 9" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15h6" />
        </svg>
        @break
    @case('shield-check')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 5 6v5c0 5 3.5 8 7 10 3.5-2 7-5 7-10V6l-7-3Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.5 1.5 3.5-3.5" />
        </svg>
        @break
    @case('calendar-check')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M3 10h18" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 15 1.5 1.5 3.5-3.5" />
        </svg>
        @break
    @case('messages-square')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 9h8M8 13h5" />
        </svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="3" width="8" height="8" rx="2" />
            <rect x="13" y="3" width="8" height="5" rx="2" />
            <rect x="13" y="11" width="8" height="10" rx="2" />
            <rect x="3" y="14" width="8" height="7" rx="2" />
        </svg>
@endswitch
