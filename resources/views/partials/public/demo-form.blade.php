@php
    $formMode = $formMode ?? 'modal';
    $formIdPrefix = $formIdPrefix ?? 'demo';
    $submitButtonClass = $submitButtonClass ?? 'home-cta-primary inline-flex h-11 items-center justify-center px-5 text-sm font-semibold text-white transition-all duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2';
    $cancelButtonClass = $cancelButtonClass ?? 'inline-flex h-11 items-center justify-center border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';
    $formWrapperClass = $formWrapperClass ?? 'space-y-4';
@endphp

<form
    data-demo-form
    data-demo-form-mode="{{ $formMode }}"
    data-submitting-text="{{ public_trans('public.demo.submitting') }}"
    data-validation-error-text="{{ public_trans('public.demo.validation_error') }}"
    data-generic-error-text="{{ public_trans('public.demo.generic_error') }}"
    data-network-error-text="{{ public_trans('public.demo.network_error') }}"
    data-success-text="{{ public_trans('public.demo.success') }}"
    class="{{ $formWrapperClass }}"
    novalidate
>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_name" class="demo-label">{{ public_trans('public.demo.name') }}</label>
            <input id="{{ $formIdPrefix }}_name" name="name" type="text" class="demo-input" autocomplete="name" required>
            <p class="demo-field-error" data-demo-error="name"></p>
        </div>
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_school_name" class="demo-label">{{ public_trans('public.demo.school_name') }}</label>
            <input id="{{ $formIdPrefix }}_school_name" name="school_name" type="text" class="demo-input" autocomplete="organization" required>
            <p class="demo-field-error" data-demo-error="school_name"></p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_phone" class="demo-label">{{ public_trans('public.demo.phone') }}</label>
            <input id="{{ $formIdPrefix }}_phone" name="phone" type="tel" class="demo-input" autocomplete="tel" required>
            <p class="demo-field-error" data-demo-error="phone"></p>
        </div>
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_student_qty" class="demo-label">{{ public_trans('public.demo.student_qty') }}</label>
            <input id="{{ $formIdPrefix }}_student_qty" name="student_qty" type="number" min="1" class="demo-input" inputmode="numeric" placeholder="{{ public_trans('public.demo.student_qty_placeholder') }}">
            <p class="demo-field-error" data-demo-error="student_qty"></p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_booking_date" class="demo-label">{{ public_trans('public.demo.booking_date') }}</label>
            <input id="{{ $formIdPrefix }}_booking_date" name="booking_date" type="date" class="demo-input">
            <p class="demo-field-error" data-demo-error="booking_date"></p>
        </div>
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_booking_time" class="demo-label">{{ public_trans('public.demo.booking_time') }}</label>
            <input id="{{ $formIdPrefix }}_booking_time" name="booking_time" type="time" class="demo-input">
            <p class="demo-field-error" data-demo-error="booking_time"></p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_email" class="demo-label">{{ public_trans('public.demo.email') }}</label>
            <input id="{{ $formIdPrefix }}_email" name="email" type="email" class="demo-input" autocomplete="email">
            <p class="demo-field-error" data-demo-error="email"></p>
        </div>
        <div class="demo-field">
            <label for="{{ $formIdPrefix }}_message" class="demo-label">{{ public_trans('public.demo.message') }}</label>
            <textarea id="{{ $formIdPrefix }}_message" name="message" rows="4" class="demo-input resize-none" placeholder="{{ public_trans('public.demo.message_placeholder') }}"></textarea>
            <p class="demo-field-error" data-demo-error="message"></p>
        </div>
    </div>

    <div data-demo-status class="demo-status" role="status" aria-live="polite"></div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        @if ($formMode === 'modal')
            <button type="button" data-demo-close class="{{ $cancelButtonClass }}">
                {{ public_trans('public.demo.cancel') }}
            </button>
        @endif

        <button type="submit" data-demo-submit class="{{ $submitButtonClass }}">
            {{ public_trans('public.demo.submit') }}
        </button>
    </div>
</form>
