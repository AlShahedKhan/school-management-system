@props([
    'id' => 'sidebar',
    'logo' => null,
    'logoAlt' => 'Dashboard logo',
    'logoId' => 'sidebarLogo',
    'navId' => 'menuNav',
    'desktopToggleId' => 'sidebarDesktopToggle',
    'mobileCloseId' => 'mobileSidebarClose',
])

<aside id="{{ $id }}" {{ $attributes->class(['sidebar']) }}>
    <div class="sidebar-brand p-6 text-center">
        <div
            class="sidebar-brand-logo flex h-20 w-full items-center justify-center overflow-hidden border border-gray-300 bg-white"
            style="border-radius: 0;"
        >
            <img
                id="{{ $logoId }}"
                src="{{ $logo }}"
                alt="{{ $logoAlt }}"
                class="h-full w-full object-contain"
                style="display: block;"
            >
        </div>

        <button
            type="button"
            id="{{ $desktopToggleId }}"
            class="sidebar-desktop-toggle"
            aria-label="Collapse sidebar"
            aria-controls="{{ $id }}"
            aria-expanded="true"
        >
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>

        <button
            type="button"
            id="{{ $mobileCloseId }}"
            class="sidebar-mobile-close"
            aria-label="Close sidebar"
        >
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <div class="sidebar-header-divider"></div>

    <nav class="custom-scrollbar flex-1 overflow-y-auto p-3" id="{{ $navId }}">
        {{ $slot }}
    </nav>

    {{ $footer ?? '' }}
</aside>
