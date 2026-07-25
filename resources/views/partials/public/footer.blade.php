@php
    $supportLinks = [
        ['label' => public_trans('public.footer.links.help_center'), 'href' => url('/login')],
        ['label' => public_trans('public.footer.links.system_status'), 'href' => route('public.features.index')],
        ['label' => public_trans('public.footer.links.about_us'), 'href' => url('/#about')],
    ];

    $legalLinks = [
        ['label' => public_trans('public.footer.links.privacy_policy'), 'href' => url('/#contact')],
        ['label' => public_trans('public.footer.links.terms'), 'href' => url('/#contact')],
        ['label' => public_trans('public.footer.links.cookie'), 'href' => url('/#contact')],
    ];

    $footerDescription = $brandAssets['footerDescription']
        ?? 'The complete school management platform trusted by institutions across Bangladesh. Simplify administration, empower teachers, and engage parents all in one place.';

    $trustBadges = $brandAssets['footerTrustBadges'] ?? [
        ['label' => 'SSL', 'style' => 'success'],
        ['label' => 'GDPR Compliant', 'style' => 'neutral'],
        ['label' => '99.9% Uptime', 'style' => 'info'],
    ];

    $badgeStyleClasses = [
        'success' => 'footer-badge--success',
        'neutral' => 'footer-badge--neutral',
        'info' => 'footer-badge--info',
    ];
@endphp

<footer class="public-footer mt-16 border-t border-slate-200/80 bg-slate-50/70">
    <div class="mx-auto w-full max-w-[1280px] px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
        <div class="grid grid-cols-2 gap-x-8 gap-y-10 lg:grid-cols-[minmax(320px,1fr)_180px_180px] lg:gap-x-20">
            <div class="col-span-2 border-b border-slate-200/80 pb-10 text-center lg:col-span-1 lg:border-b-0 lg:pb-0 lg:text-left">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center lg:justify-start">
                    <img
                        src="{{ $brandAssets['logoLightUrl'] ?? asset('images/logo.png') }}"
                        alt="{{ $brandAssets['logoAlt'] ?? 'Astha Academics' }}"
                        class="public-logo public-logo-light h-10 w-auto object-contain sm:h-12"
                    >
                    <img
                        src="{{ $brandAssets['logoDarkUrl'] ?? asset('images/logo.png') }}"
                        alt="{{ $brandAssets['logoAlt'] ?? 'Astha Academics' }}"
                        class="public-logo public-logo-dark h-10 w-auto object-contain sm:h-12"
                    >
                </a>

                <p class="mx-auto mt-5 max-w-[34ch] text-sm leading-7 text-slate-600 sm:max-w-[42ch] sm:text-base sm:leading-8 lg:mx-0">
                    {{ $footerDescription }}
                </p>

                <div class="mx-auto mt-6 flex max-w-[300px] flex-wrap justify-center gap-2.5 sm:max-w-none sm:gap-3 lg:mx-0 lg:justify-start">
                    @foreach ($trustBadges as $badge)
                        <span class="footer-badge {{ $badgeStyleClasses[$badge['style'] ?? 'success'] ?? 'footer-badge--success' }} inline-flex min-h-8 items-center gap-2 rounded-full border px-3 py-1.5 text-[11px] font-medium shadow-sm sm:text-xs">
                            <span class="footer-badge-icon inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full">
                                <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M6.7 11.3 3.9 8.5l1-1 1.8 1.8 4.4-4.4 1 1-5.4 5.4Z" />
                                </svg>
                            </span>
                            {{ $badge['label'] ?? '' }}
                        </span>
                    @endforeach
                </div>
            </div>

            <nav aria-label="{{ public_trans('public.footer.support_title') }}" class="min-w-0 pl-2 sm:pl-6 lg:pl-0">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 sm:text-xs">{{ public_trans('public.footer.support_title') }}</h2>
                <ul class="mt-5 space-y-4">
                    @foreach ($supportLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="public-footer-link block text-sm leading-6 text-slate-600 sm:text-base">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <nav aria-label="{{ public_trans('public.footer.legal_title') }}" class="min-w-0 pr-2 text-right sm:pr-6 lg:pr-0 lg:text-left">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 sm:text-xs">{{ public_trans('public.footer.legal_title') }}</h2>
                <ul class="mt-5 space-y-4">
                    @foreach ($legalLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="public-footer-link block text-sm leading-6 text-slate-600 sm:text-base">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>

    <div class="border-t border-slate-200/80">
        <div class="mx-auto flex min-h-14 w-full max-w-[1280px] items-center justify-center px-4 py-4 text-center sm:min-h-16 sm:px-6 sm:py-5 lg:px-8">
            <p class="text-xs leading-6 text-slate-400 sm:text-sm">
                &copy; {{ now()->year }} {{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}. {{ public_trans('public.footer.rights') }}
            </p>
        </div>
    </div>
</footer>

