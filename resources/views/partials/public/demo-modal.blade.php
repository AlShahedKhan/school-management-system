<div data-demo-modal class="demo-modal fixed inset-0 z-[90] hidden items-center justify-center px-4 py-6"
    aria-hidden="true">
    <div class="demo-modal-backdrop absolute inset-0 bg-slate-950/55" data-demo-close></div>
    <div class="demo-modal-panel relative max-h-[calc(100vh-48px)] w-full max-w-[540px] overflow-y-auto border border-slate-200 bg-white p-0 shadow-[0_30px_80px_rgba(15,23,42,0.22)]"
        role="dialog" aria-modal="true" aria-labelledby="demoModalTitle">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-600">
                    {{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}</p>
                <h2 id="demoModalTitle" class="mt-1 text-xl font-bold text-slate-950">
                    {{ public_trans('public.demo.title') }}</h2>
            </div>
            <button type="button" data-demo-close
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                aria-label="{{ public_trans('public.demo.close') }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        @include('partials.public.demo-form', [
            'formMode' => 'modal',
            'formIdPrefix' => 'demo_modal',
            'formWrapperClass' => 'space-y-4 px-5 py-5 sm:px-6',
        ])
    </div>
</div>
