@props([
    'packages',
    'showEmptyState' => false,
    'sectionClass' => 'home-section-anchor mx-auto w-full max-w-[1280px] px-4 pb-20 sm:px-6 lg:px-8',
])

@if ($packages->isNotEmpty() || $showEmptyState)
    <section id="pricing" {{ $attributes->merge(['class' => $sectionClass]) }}>
        <div class="home-reveal mx-auto max-w-[760px] text-center">
            <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                {{ public_trans('public.packages.eyebrow') }}
            </p>
            <h2 class="home-title-font mt-4 text-[clamp(2rem,4vw,3rem)] font-black leading-tight text-slate-950">
                {{ public_trans('public.packages.title') }}
            </h2>
            <p class="mx-auto mt-4 max-w-[62ch] text-base leading-8 text-slate-600">
                {{ public_trans('public.packages.description') }}
            </p>
        </div>

        @if ($packages->isNotEmpty())
            <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($packages as $package)
                    @php
                        $hasDiscount = (float) $package->annual_discount_percent > 0;
                        $discountPercent = rtrim(rtrim(number_format((float) $package->annual_discount_percent, 2), '0'), '.');
                    @endphp

                    <article
                        class="home-reveal home-feature-card home-package-card flex h-full min-h-[360px] flex-col border border-slate-200 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-500">
                                    {{ public_trans('public.packages.plan_label') }}
                                </p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">
                                    {{ public_trans('public.packages.types.' . strtolower($package->package_type)) }}
                                </h3>
                            </div>
                            <span
                                class="home-package-trial-badge inline-flex shrink-0 border border-blue-100 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                {{ public_trans('public.packages.trial_days', ['count' => bn_number($package->free_trial_days)]) }}
                            </span>
                        </div>

                        <div class="mt-6 border-y border-slate-100 py-5">
                            <p class="text-sm font-semibold text-slate-500">
                                {{ public_trans('public.packages.price_label') }}
                            </p>
                            <div class="mt-2 flex items-end gap-2">
                                <p class="text-3xl font-black leading-none text-slate-950">
                                    {{ public_trans('public.packages.currency') }}{{ bn_number(number_format((float) $package->per_student_price, 2)) }}
                                </p>
                                <p class="pb-1 text-sm font-semibold text-slate-500">
                                    {{ public_trans('public.packages.per_student') }}
                                </p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">{{ public_trans('public.packages.students') }}</dt>
                                <dd class="font-bold text-slate-800">{{ bn_number(number_format($package->student_limit)) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">{{ public_trans('public.packages.teachers') }}</dt>
                                <dd class="font-bold text-slate-800">{{ bn_number(number_format($package->teacher_limit)) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">{{ public_trans('public.packages.sms_limit') }}</dt>
                                <dd class="font-bold text-slate-800">{{ bn_number(number_format($package->sms_limit)) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">{{ public_trans('public.packages.discount') }}</dt>
                                <dd class="font-bold text-slate-800">
                                    {{ $hasDiscount ? public_trans('public.packages.discount_value', ['percent' => bn_number($discountPercent)]) : public_trans('public.packages.no_discount') }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-auto pt-6">
                            @if ($hasDiscount)
                                <p class="text-xs font-semibold text-slate-400 line-through">
                                    {{ public_trans('public.packages.currency') }}{{ bn_number(number_format((float) $package->total_payable, 2)) }}
                                </p>
                            @endif
                            <p class="mt-1 text-xl font-black text-green-600">
                                {{ public_trans('public.packages.currency') }}{{ bn_number(number_format((float) $package->after_discount, 2)) }}
                            </p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">
                                {{ public_trans('public.packages.discounted_total') }}
                            </p>
                            <a href="{{ url('/login') }}"
                                class="mt-5 inline-flex h-11 w-full items-center justify-center border border-blue-600 bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                                {{ public_trans('public.packages.choose_plan') }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="mx-auto mt-12 max-w-[640px] border border-slate-200 bg-white p-8 text-center shadow-[0_18px_40px_rgba(15,23,42,0.045)]">
                <h3 class="text-xl font-bold text-slate-950">
                    {{ public_trans('public.packages.empty_title') }}
                </h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    {{ public_trans('public.packages.empty_description') }}
                </p>
            </div>
        @endif
    </section>
@endif
