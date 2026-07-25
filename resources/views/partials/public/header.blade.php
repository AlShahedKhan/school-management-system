@php
    $navItems = [
        ['key' => 'home', 'label' => public_trans('public.nav.home'), 'href' => url('/'), 'active' => request()->routeIs('home') || request()->path() === '/'],
        ['key' => 'about', 'label' => public_trans('public.nav.about'), 'href' => route('public.about.index'), 'active' => request()->routeIs('public.about.*')],
        ['key' => 'features', 'label' => public_trans('public.nav.features'), 'href' => route('public.features.index'), 'active' => request()->routeIs('public.features.*')],
        ['key' => 'pricing', 'label' => public_trans('public.nav.pricing'), 'href' => route('public.pricing.index'), 'active' => request()->routeIs('public.pricing.*')],
        ['key' => 'blog', 'label' => public_trans('public.nav.blog'), 'href' => route('public.blogs.index'), 'active' => request()->routeIs('public.blogs.*')],
        ['key' => 'demo', 'label' => public_trans('public.nav.demo'), 'href' => route('public.demo'), 'active' => request()->routeIs('public.demo')],
        ['key' => 'teacher', 'label' => 'Teacher', 'href' => url('/teacher'), 'active' => request()->is('teacher')],
        ['key' => 'student', 'label' => 'Student', 'href' => url('/student'), 'active' => request()->is('student')],
    ];

    $activeLocale = app()->getLocale();
@endphp

<header id="publicHeader" class="public-header sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/85">
    <div id="publicHeaderInner" class="public-header-inner relative mx-auto flex h-[72px] w-full max-w-[1280px] items-center px-4 sm:px-6 md:justify-between md:px-3 lg:px-8">
        <button
            id="mobileMenuButton"
            type="button"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="mobileMenu"
            class="public-surface flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 md:hidden"
        >
            <svg
                id="menuOpenIcon"
                class="h-4.5 w-4.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg
                id="menuCloseIcon"
                class="hidden h-4.5 w-4.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="absolute left-1/2 flex -translate-x-1/2 items-center justify-center gap-0 md:hidden">
            <button
                type="button"
                data-theme-toggle
                aria-label="Toggle dark mode"
                aria-pressed="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>
                </svg>
            </button>

            <button
                type="button"
                data-language-trigger
                aria-label="{{ public_trans('public.language.change') }}"
                aria-expanded="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                </svg>
            </button>

            <button
                type="button"
                data-support-trigger
                aria-label="{{ public_trans('public.support.open') }}"
                aria-expanded="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path d="M4 14v-2a8 8 0 0 1 16 0v2"/>
                    <path d="M4 14a2 2 0 0 1 2-2h1v7H6a2 2 0 0 1-2-2v-3ZM20 14a2 2 0 0 0-2-2h-1v7h1a2 2 0 0 0 2-2v-3Z"/>
                    <path d="M17 19c0 1.1-.9 2-2 2h-3"/>
                </svg>
            </button>

            <a
                href="{{ url('/login') }}"
                aria-label="{{ public_trans('public.nav.login') }}"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="m10 17 5-5-5-5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/>
                </svg>
            </a>
        </div>

        <a href="{{ url('/') }}" class="ml-auto flex shrink-0 items-center md:ml-0">
            <img
                src="{{ $brandAssets['logoLightUrl'] ?? asset('images/logo.png') }}"
                alt="{{ $brandAssets['logoAlt'] ?? 'Astha Academics' }}"
                class="public-logo public-logo-light h-8 w-auto max-w-[72px] object-contain sm:h-12 sm:max-w-none md:max-w-[78px] lg:max-w-none"
            >
            <img
                src="{{ $brandAssets['logoDarkUrl'] ?? asset('images/logo.png') }}"
                alt="{{ $brandAssets['logoAlt'] ?? 'Astha Academics' }}"
                class="public-logo public-logo-dark h-8 w-auto max-w-[72px] object-contain sm:h-12 sm:max-w-none md:max-w-[78px] lg:max-w-none"
            >
        </a>

        <nav class="hidden items-center gap-0 md:flex md:shrink-0 lg:absolute lg:left-1/2 lg:-translate-x-1/2">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    data-nav-key="{{ $item['key'] }}"
                    aria-current="{{ $item['active'] ? 'page' : 'false' }}"
                    class="public-nav-link {{ $item['active'] ? 'is-active' : '' }} flex h-10 items-center px-2.5 text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 lg:px-4 lg:text-sm"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-0 md:flex md:shrink-0 lg:gap-0">
            <button
                type="button"
                data-theme-toggle
                aria-label="Toggle dark mode"
                aria-pressed="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 lg:h-9 lg:w-9"
            >
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>
                </svg>
            </button>

            <button
                type="button"
                data-language-trigger
                aria-label="{{ public_trans('public.language.change') }}"
                aria-expanded="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 lg:h-9 lg:w-9"
            >
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                </svg>
            </button>

            <button
                type="button"
                data-support-trigger
                aria-label="{{ public_trans('public.support.open') }}"
                aria-expanded="false"
                class="quick-action-btn public-surface flex h-8 w-8 items-center justify-center rounded-full text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 lg:h-9 lg:w-9"
            >
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path d="M4 14v-2a8 8 0 0 1 16 0v2"/>
                    <path d="M4 14a2 2 0 0 1 2-2h1v7H6a2 2 0 0 1-2-2v-3ZM20 14a2 2 0 0 0-2-2h-1v7h1a2 2 0 0 0 2-2v-3Z"/>
                    <path d="M17 19c0 1.1-.9 2-2 2h-3"/>
                </svg>
            </button>

            <a
                href="{{ url('/login') }}"
                class="public-login-link ml-1 inline-flex h-9 items-center justify-center rounded-full border border-slate-300 px-3 text-sm font-medium text-slate-700 transition-all duration-200 ease-out hover:border-blue-600 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 lg:h-[38px] lg:px-4"
            >
                {{ public_trans('public.nav.login') }}
            </a>
        </div>
    </div>

    <div
        id="languagePanel"
        class="public-popover public-panel pointer-events-none fixed right-4 top-[84px] z-[60] w-[220px] translate-y-2 rounded-2xl border border-slate-200 p-3 opacity-0 shadow-xl md:right-6"
        aria-hidden="true"
    >
        <div class="mb-2">
            <p class="text-sm font-semibold text-slate-900">{{ public_trans('public.language.title') }}</p>
            <p class="text-xs text-slate-500">{{ public_trans('public.language.description') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <a
                href="{{ route('public.language.switch', 'en') }}"
                data-language-option="en"
                class="public-surface rounded-xl border border-slate-200 px-3 py-2 text-center text-sm font-medium text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 {{ $activeLocale === 'en' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}"
            >
                {{ public_trans('public.language.english') }}
            </a>
            <a
                href="{{ route('public.language.switch', 'bn') }}"
                data-language-option="bn"
                class="public-surface rounded-xl border border-slate-200 px-3 py-2 text-center text-sm font-medium text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 {{ $activeLocale === 'bn' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}"
            >
                {{ public_trans('public.language.bangla') }}
            </a>
        </div>
    </div>

    <div
        id="supportPanel"
        class="public-popover public-panel pointer-events-none fixed inset-x-4 top-[84px] z-[60] translate-y-2 rounded-2xl border border-slate-200 p-4 opacity-0 shadow-xl md:left-auto md:right-6 md:w-[280px]"
        aria-hidden="true"
    >
        <div class="mb-3">
            <p class="text-sm font-semibold text-slate-900">{{ public_trans('public.support.title') }}</p>
            <p class="mt-1 text-xs leading-5 text-slate-500">
                {{ public_trans('public.support.description') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a
                href="{{ url('/login') }}"
                class="inline-flex h-10 items-center justify-center rounded-full bg-blue-600 px-4 text-sm font-semibold text-white transition-all duration-200 ease-out hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                {{ public_trans('public.support.login') }}
            </a>
            <button
                type="button"
                id="supportPanelClose"
                class="public-surface inline-flex h-10 items-center justify-center rounded-full border border-slate-200 px-4 text-sm font-medium text-slate-700 transition-all duration-200 ease-out hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                {{ public_trans('public.support.close') }}
            </button>
        </div>
    </div>

    <div
        id="mobileMenuBackdrop"
        class="pointer-events-none fixed inset-x-0 bottom-0 top-[72px] z-30 bg-slate-900/40 opacity-0 transition-opacity duration-300 ease-out md:hidden"
    ></div>

    <aside
        id="mobileMenu"
        class="pointer-events-none fixed left-0 top-[72px] z-40 flex h-[calc(100vh-72px)] w-[54vw] min-w-[190px] max-w-[220px] -translate-x-full flex-col border-r border-t border-slate-200 bg-white shadow-2xl transition-transform duration-300 ease-out md:hidden"
        aria-hidden="true"
    >
        <div class="flex flex-1 flex-col overflow-y-auto px-3 py-3 sm:px-4">
            <nav class="flex flex-col gap-1">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        style="--item-index: {{ $loop->index }}"
                        data-nav-key="{{ $item['key'] }}"
                        aria-current="{{ $item['active'] ? 'page' : 'false' }}"
                        class="drawer-link-item public-drawer-link {{ $item['active'] ? 'is-active' : '' }} px-3 py-2.5 text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto border-t border-slate-200 pt-3">
                <div class="grid grid-cols-1 gap-2">
                    <a
                        href="{{ url('/login') }}"
                        class="public-login-link inline-flex h-10 items-center justify-center rounded-full border border-slate-300 text-sm font-medium text-slate-700 transition-all duration-200 ease-out hover:border-blue-600 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    >
                        {{ public_trans('public.nav.login') }}
                    </a>
                </div>
            </div>
        </div>
    </aside>
</header>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.documentElement;
        const header = document.getElementById('publicHeader');
        const menuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        const menuOpenIcon = document.getElementById('menuOpenIcon');
        const menuCloseIcon = document.getElementById('menuCloseIcon');
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        const navLinks = document.querySelectorAll('[data-nav-key]');
        const themeButtons = document.querySelectorAll('[data-theme-toggle]');
        const languageTriggers = document.querySelectorAll('[data-language-trigger]');
        const supportTriggers = document.querySelectorAll('[data-support-trigger]');
        const languagePanel = document.getElementById('languagePanel');
        const supportPanel = document.getElementById('supportPanel');
        const supportPanelClose = document.getElementById('supportPanelClose');
        const languageOptions = document.querySelectorAll('[data-language-option]');
        const serverActiveKey = document.querySelector('[data-nav-key].is-active')?.dataset.navKey || 'home';

        if (!header || !menuButton || !mobileMenu || !mobileMenuBackdrop || !languagePanel || !supportPanel) {
            return;
        }

        const setScrolledState = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 12);
        };

        const setThemeState = (isDark) => {
            root.classList.toggle('theme-dark', isDark);
            themeButtons.forEach((button) => {
                button.classList.toggle('is-active', isDark);
                button.setAttribute('aria-pressed', String(isDark));
            });
            localStorage.setItem('public-theme', isDark ? 'dark' : 'light');
        };

        const setActiveNavState = () => {
            const hash = window.location.hash.replace('#', '');
            const activeKey = hash || (window.location.pathname === '/' ? 'home' : serverActiveKey);

            navLinks.forEach((link) => {
                const isActive = link.dataset.navKey === activeKey;
                link.classList.toggle('is-active', isActive);
                link.setAttribute('aria-current', isActive ? 'page' : 'false');
            });
        };

        const setLanguageState = (language) => {
            root.lang = language;

            languageOptions.forEach((option) => {
                const isActive = option.dataset.languageOption === language;
                option.classList.toggle('bg-blue-50', isActive);
                option.classList.toggle('text-blue-600', isActive);
                option.classList.toggle('border-blue-200', isActive);
            });
        };

        const closePanel = (panel, triggers) => {
            panel.classList.add('pointer-events-none', 'opacity-0', 'translate-y-2');
            panel.classList.remove('opacity-100', 'translate-y-0');
            panel.setAttribute('aria-hidden', 'true');
            triggers.forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
        };

        const openPanel = (panel, triggers) => {
            panel.classList.remove('pointer-events-none', 'opacity-0', 'translate-y-2');
            panel.classList.add('opacity-100', 'translate-y-0');
            panel.setAttribute('aria-hidden', 'false');
            triggers.forEach((trigger) => trigger.setAttribute('aria-expanded', 'true'));
        };

        const closeAuxPanels = () => {
            closePanel(languagePanel, languageTriggers);
            closePanel(supportPanel, supportTriggers);
        };

        const closeMenu = () => {
            mobileMenu.classList.add('-translate-x-full', 'pointer-events-none');
            mobileMenu.classList.remove('is-open');
            mobileMenuBackdrop.classList.add('pointer-events-none', 'opacity-0');
            mobileMenuBackdrop.classList.remove('opacity-100');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
            menuButton.setAttribute('aria-expanded', 'false');
            mobileMenu.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        const openMenu = () => {
            mobileMenu.classList.remove('-translate-x-full', 'pointer-events-none');
            mobileMenu.classList.add('is-open');
            mobileMenuBackdrop.classList.remove('pointer-events-none', 'opacity-0');
            mobileMenuBackdrop.classList.add('opacity-100');
            menuOpenIcon.classList.add('hidden');
            menuCloseIcon.classList.remove('hidden');
            menuButton.setAttribute('aria-expanded', 'true');
            mobileMenu.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            closeAuxPanels();
        };

        const togglePanel = (panel, triggers) => {
            const isOpen = panel.getAttribute('aria-hidden') === 'false';
            closeAuxPanels();
            if (!isOpen) {
                openPanel(panel, triggers);
            }
        };

        const savedTheme = localStorage.getItem('public-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        setThemeState(savedTheme ? savedTheme === 'dark' : prefersDark);

        setLanguageState(root.lang || 'en');
        setActiveNavState();

        setScrolledState();
        window.addEventListener('scroll', setScrolledState, { passive: true });
        window.addEventListener('hashchange', setActiveNavState);

        themeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const isDark = !root.classList.contains('theme-dark');
                setThemeState(isDark);
            });
        });

        navLinks.forEach((link) => {
            link.addEventListener('click', () => {
                const nextKey = link.dataset.navKey;
                navLinks.forEach((navLink) => {
                    const isActive = navLink.dataset.navKey === nextKey;
                    navLink.classList.toggle('is-active', isActive);
                    navLink.setAttribute('aria-current', isActive ? 'page' : 'false');
                });
            });
        });

        languageTriggers.forEach((trigger) => {
            trigger.addEventListener('click', () => {
                togglePanel(languagePanel, languageTriggers);
            });
        });

        supportTriggers.forEach((trigger) => {
            trigger.addEventListener('click', () => {
                togglePanel(supportPanel, supportTriggers);
            });
        });

        languageOptions.forEach((option) => {
            option.addEventListener('click', () => {
                closePanel(languagePanel, languageTriggers);
            });
        });

        supportPanelClose?.addEventListener('click', () => {
            closePanel(supportPanel, supportTriggers);
        });

        menuButton.addEventListener('click', () => {
            const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeMenu();
                return;
            }
            openMenu();
        });

        mobileMenuBackdrop.addEventListener('click', closeMenu);
        mobileMenuLinks.forEach((link) => {
            link.addEventListener('click', closeMenu);
        });

        document.addEventListener('click', (event) => {
            const target = event.target;

            if (!languagePanel.contains(target) && !Array.from(languageTriggers).some((trigger) => trigger.contains(target))) {
                closePanel(languagePanel, languageTriggers);
            }

            if (!supportPanel.contains(target) && !Array.from(supportTriggers).some((trigger) => trigger.contains(target))) {
                closePanel(supportPanel, supportTriggers);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
                closeAuxPanels();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                closeMenu();
                closeAuxPanels();
            }
        });
    });
</script>
@endpush
