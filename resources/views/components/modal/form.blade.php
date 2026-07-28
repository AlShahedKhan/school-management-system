@props([
    'id',
    'formId',
    'title',
    'closeButtonId',
    'action' => null,
    'method' => 'POST',
    'enctype' => 'multipart/form-data',
    'cancelLabel' => 'Cancel',
    'submitLabel' => 'Save',
    'panelClass' => 'custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]',
    'panelStyle' => 'border-radius:4px; max-height:min(360px, calc(100dvh - 2.5rem));',
    'titleClass' => 'm-0 text-center font-semibold leading-tight text-slate-800',
    'headerClass' => 'flex shrink-0 items-center justify-center bg-white px-4 pb-2 pt-8 sm:px-6 md:pt-8 lg:pt-8',
    'formClass' => 'm-0',
    'bodyClass' => 'bg-white px-6 pb-0 pt-2',
    'fieldsClass' => 'grid grid-cols-1 gap-3 md:grid-cols-2',
])

@php
    $normalizedMethod = strtoupper($method);
    $htmlMethod = $normalizedMethod === 'GET' ? 'GET' : 'POST';
    $titleId = $id . 'Title';
@endphp

<x-modal
    :id="$id"
    :panel-class="$panelClass"
    :panel-style="$panelStyle"
    {{ $attributes->class([
        'fixed inset-0 z-[100] hidden flex items-center justify-center overflow-y-auto bg-slate-950/30 px-4 py-5 sm:px-6',
    ])->merge([
        'role' => 'dialog',
        'aria-modal' => 'true',
        'aria-labelledby' => $titleId,
    ]) }}
>
    @if(!empty($title))
    <div class="{{ $headerClass }}">
        <h4 id="{{ $titleId }}" class="{{ $titleClass }}">
            {{ $title }}
        </h4>
    </div>
    @endif

    <form
        id="{{ $formId }}"
        class="{{ $formClass }}"
        method="{{ strtolower($htmlMethod) }}"
        @if ($action) action="{{ $action }}" @endif
        @if ($enctype) enctype="{{ $enctype }}" @endif
    >
        @if ($normalizedMethod !== 'GET')
            @csrf
        @endif

        @if (! in_array($normalizedMethod, ['GET', 'POST'], true))
            @method($normalizedMethod)
        @endif

        <div class="{{ $bodyClass }}">
            <div class="{{ $fieldsClass }}">
                {{ $slot }}
            </div>
        </div>

        @isset($footer)
            {{ $footer }}
        @else
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4">
                <x-button.secondary :id="$closeButtonId" class="w-full">
                    {{ $cancelLabel }}
                </x-button.secondary>

                <x-button.primary type="submit" class="w-full">
                    {{ $submitLabel }}
                </x-button.primary>
            </div>
        @endisset
    </form>
</x-modal>
