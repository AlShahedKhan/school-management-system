<x-layout.header>
    <x-slot:leading>
        <button id="hamburger" class="text-blue-600 text-xl" aria-label="Toggle sidebar"
            aria-controls="sidebar" aria-expanded="false">
            <i id="hamburgerIcon" class="fas fa-bars"></i>
        </button>
    </x-slot:leading>

    <x-slot:actions>
        {{-- Profile Toggle --}}
        <div class="relative">
            <button id="profileBtn"
                class="topbar-action topbar-action-profile flex items-center justify-center"
                title="{{ auth()->user()->school_name }}">
                <i class="fas fa-user" aria-hidden="true"></i>
            </button>

            <div id="profileMenu"
                class="hidden absolute right-0 top-full z-50 overflow-hidden mt-3 bg-gray-50"
                style="border-radius: 0;">
                <a href="#" class="profile-dropdown-link profile-menu-item" data-tab="profile">
                    <i class="far fa-user" aria-hidden="true"></i>
                    <span>Profile</span>
                </a>
                <a href="#" class="profile-dropdown-link profile-menu-item" data-tab="principle">
                    <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    <span>Principal</span>
                </a>
                <a href="#" id="logoutBtnDropdown"
                    class="profile-dropdown-link profile-dropdown-logout">
                    <i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        {{-- Language Toggle --}}
        <div class="relative">
            <button type="button" id="topbarLanguageBtn"
                class="topbar-action topbar-action-language flex items-center justify-center"
                title="Language" aria-label="Language" aria-expanded="false"
                aria-controls="schoolLanguageMenu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                    aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
                </svg>
            </button>

            <div id="schoolLanguageMenu" class="school-language-menu hidden mt-3" role="menu">
                <button type="button" class="school-language-option is-active" data-language-option="en"
                    role="menuitem">
                    <span class="school-language-dot" aria-hidden="true"></span>
                    <span class="school-language-option-label">English</span>
                </button>

                <button type="button" class="school-language-option" data-language-option="bn"
                    role="menuitem">
                    <span class="school-language-dot" aria-hidden="true"></span>
                    <span class="school-language-option-label">বাংলা</span>
                </button>
            </div>
        </div>

        {{-- Notification Toggle --}}
        <div class="relative">
            <button id="topbarNotificationBtn"
                class="topbar-action topbar-action-notification relative flex items-center justify-center"
                title="Notifications">
                <i class="fas fa-bell"></i>
            </button>

            {{-- Notification Box Container --}}
            <div id="notificationBox" class="school-notification-menu mt-3 hidden">

                {{-- Main Box --}}
                <div class="school-notification-panel">

                    <div class="school-notification-header">
                        Notifications
                    </div>

                    <div class="school-notification-tabs" role="tablist"
                        aria-label="Notification filters">
                        <button type="button" class="school-notification-tab is-active"
                            data-notification-tab="all">
                            All <span>0</span>
                        </button>
                        <button type="button" class="school-notification-tab"
                            data-notification-tab="unread">
                            Unread <span>0</span>
                        </button>
                        <button type="button" class="school-notification-tab"
                            data-notification-tab="read">
                            Read <span>0</span>
                        </button>
                    </div>

                    <div class="school-notification-list custom-scrollbar">
                        <div class="school-notification-empty" data-notification-empty>
                            <i class="far fa-bell" aria-hidden="true"></i>
                            <span>No notifications available</span>
                        </div>
                    </div>

                    <div class="school-notification-footer">
                        <button type="button" data-mark-all-read>Mark all as read</button>
                        <a href="#">View all notifications</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Support Toggle --}}
        <div class="relative">
            <button type="button" id="topbarSupportBtn"
                class="topbar-action topbar-action-support flex items-center justify-center"
                title="Support" aria-label="Support" aria-expanded="false" aria-controls="supportMenu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                    aria-hidden="true">
                    <path d="M4 14v-2a8 8 0 0 1 16 0v2" />
                    <path d="M18 19c0 1.1-.9 2-2 2h-3" />
                    <path
                        d="M4 14a2 2 0 0 1 2-2h1v6H6a2 2 0 0 1-2-2v-2ZM20 14a2 2 0 0 0-2-2h-1v6h1a2 2 0 0 0 2-2v-2Z" />
                </svg>
            </button>

            <div id="supportMenu" class="school-support-menu hidden mt-2" role="dialog"
                aria-modal="false" aria-labelledby="supportMenuTitle">
                <div id="supportMenuContent" class="school-support-card" tabindex="-1">
                    <div class="school-support-heading">
                        <h2 id="supportMenuTitle" class="school-support-title">Support</h2>
                        <p class="school-support-text">Contact Astha Academics directly</p>
                    </div>

                    <div class="school-support-list">
                        <a href="tel:01337225555" class="school-support-item">
                            <span class="school-support-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .95.68l1.5 4.5a1 1 0 0 1-.5 1.21l-2.27 1.14a11.04 11.04 0 0 0 5.5 5.5l1.14-2.27a1 1 0 0 1 1.21-.5l4.5 1.5A1 1 0 0 1 21 15.72V19a2 2 0 0 1-2 2h-1C9.72 21 3 14.28 3 6V5Z" />
                                </svg>
                            </span>
                            <span>Mobile: 01337225555</span>
                        </a>

                        <a href="https://wa.me/8801337225555" target="_blank" rel="noopener noreferrer"
                            class="school-support-item">
                            <span class="school-support-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 19.5 4 20l.5-3.5A8 8 0 1 1 7.5 19.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.5 9.5c.3 2.7 2.3 4.7 5 5l1.1-1.1a.7.7 0 0 1 .7-.17l2 .67M9.2 7.2l-.6 1.4a.7.7 0 0 0 .08.7l.7.9" />
                                </svg>
                            </span>
                            <span>WhatsApp: 01337225555</span>
                        </a>

                        <a href="mailto:support@asthaacademics.com" class="school-support-item">
                            <span class="school-support-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <rect width="18" height="14" x="3" y="5" rx="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m3 7 9 6 9-6" />
                                </svg>
                            </span>
                            <span>support@asthaacademics.com</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dark Mode Toggle --}}
        <button type="button" id="topbarThemeBtn"
            class="topbar-action topbar-action-theme flex items-center justify-center" title="Dark mode"
            aria-label="Toggle dark mode" aria-pressed="false">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" />
            </svg>
        </button>
    </x-slot:actions>
</x-layout.header>

