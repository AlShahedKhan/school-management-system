@props([
    'id',
    'name' => null,
    'options' => [],
    'placeholder' => 'Select...',
    'value' => '',
    'addButtonId' => null,
    'addButtonLabel' => 'Add new',
    'addButtonTarget' => null,
])

@php
    $selectedLabel = $placeholder;

    $normalizedOptions = collect($options)->map(function ($option, $key) {
        if (is_array($option)) {
            $normalized = [
                'value' => (string) ($option['value'] ?? $key),
                'label' => $option['label'] ?? $option['name'] ?? $key,
            ];

            foreach ($option as $attribute => $attributeValue) {
                if (! in_array($attribute, ['value', 'label', 'name'], true) && is_scalar($attributeValue)) {
                    $normalized[$attribute] = (string) $attributeValue;
                }
            }

            return $normalized;
        }

        return [
            'value' => (string) $key,
            'label' => $option,
        ];
    });

    foreach ($normalizedOptions as $option) {
        if ((string) $option['value'] === (string) $value) {
            $selectedLabel = $option['label'];
            break;
        }
    }
@endphp

<div {{ $attributes->class('relative w-full') }} data-dropdown-select>
    <input
        type="hidden"
        id="{{ $id }}"
        @if ($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        data-dropdown-select-input
    >

    <div class="flex w-full gap-2">
        <button
            type="button"
            id="{{ $id }}Button"
            class="m-0 flex h-8 min-w-0 flex-1 appearance-none items-center justify-between border border-slate-300 bg-white px-3 py-0 text-left text-[11px] font-normal text-slate-800 outline-none transition-colors hover:border-slate-400 focus:border-slate-500"
            style="border-radius: 0;"
            aria-expanded="false"
            aria-haspopup="listbox"
            data-dropdown-select-button
        >
            <span class="truncate" style="font-size: 0.8rem" data-dropdown-select-label data-placeholder="{{ $placeholder }}">{{ $selectedLabel }}</span>
            <i class="fas fa-caret-down text-[8px] text-slate-700 transition-transform duration-150"></i>
        </button>

        @if ($addButtonTarget)
            <button
                type="button"
                @if ($addButtonId) id="{{ $addButtonId }}" @endif
                class="m-0 flex h-8 w-8 shrink-0 items-center justify-center border border-slate-300 bg-white text-blue-600 outline-none transition-colors hover:border-blue-500 hover:bg-blue-50 focus:border-blue-600"
                style="border-radius: 0;"
                aria-label="{{ $addButtonLabel }}"
                data-dropdown-add-target="{{ $addButtonTarget }}"
            >
                <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
            </button>
        @endif
    </div>

    <div
        id="{{ $id }}Menu"
        class="absolute left-0 top-full z-50 hidden max-h-[120px] w-full overflow-y-auto border border-slate-300 bg-white text-[11px] text-slate-800 shadow-sm"
        style="top: calc(100% + 5px); @if ($addButtonTarget) width: calc(100% - 2.5rem); @endif"
        role="listbox"
        data-dropdown-select-menu
    >
        @foreach ($normalizedOptions as $option)
            <button
                type="button"
                class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 {{ (string) $option['value'] === (string) $value ? 'bg-slate-100 text-slate-900' : 'text-slate-800' }}"
                data-value="{{ $option['value'] }}"
                @foreach ($option as $attribute => $attributeValue)
                    @if (! in_array($attribute, ['value', 'label'], true))
                        data-option-{{ \Illuminate\Support\Str::kebab($attribute) }}="{{ $attributeValue }}"
                    @endif
                @endforeach
                role="option"
                aria-selected="{{ (string) $option['value'] === (string) $value ? 'true' : 'false' }}"
                data-dropdown-select-option
            >
                {{ $option['label'] }}
            </button>
        @endforeach
    </div>
</div>

@once
    <script>
        (() => {
            const initDropdownSelects = () => {
                document.querySelectorAll('[data-dropdown-select]').forEach((root) => {
                    if (root.dataset.dropdownSelectReady === 'true') {
                        return;
                    }

                    root.dataset.dropdownSelectReady = 'true';

                    const input = root.querySelector('[data-dropdown-select-input]');
                    const button = root.querySelector('[data-dropdown-select-button]');
                    const label = root.querySelector('[data-dropdown-select-label]');
                    const menu = root.querySelector('[data-dropdown-select-menu]');
                    const icon = button?.querySelector('i');

                    if (!input || !button || !label || !menu) {
                        return;
                    }

                    const setOpen = (open) => {
                        root.classList.toggle('is-open', open);
                        menu.classList.toggle('hidden', !open);
                        button.setAttribute('aria-expanded', String(open));
                        icon?.classList.toggle('rotate-180', open);
                    };

                    button.addEventListener('click', (event) => {
                        event.stopPropagation();
                        setOpen(menu.classList.contains('hidden'));
                    });

                    root.querySelector('[data-dropdown-add-target]')?.addEventListener('click', (event) => {
                        event.stopPropagation();
                        setOpen(false);

                        const target = event.currentTarget.dataset.dropdownAddTarget;
                        if (target.startsWith('/') || target.startsWith('http') || target.includes('/')) {
                            window.location.href = target;
                            return;
                        }

                        const targetModal = document.getElementById(target);
                        const currentModal = root.closest('[role="dialog"]');

                        if (targetModal && currentModal) {
                            targetModal.dataset.returnModalId = currentModal.id;
                        }

                        currentModal?.classList.add('hidden');
                        targetModal?.classList.remove('hidden');
                    });

                    menu.querySelectorAll('[data-dropdown-select-option]').forEach((option) => {
                        option.addEventListener('click', () => {
                            input.value = option.dataset.value || '';
                            label.textContent = option.textContent.trim();

                            menu.querySelectorAll('[data-dropdown-select-option]').forEach((item) => {
                                const isSelected = item === option;

                                item.classList.toggle('bg-slate-100', isSelected);
                                item.classList.toggle('text-slate-900', isSelected);
                                item.classList.toggle('text-slate-800', !isSelected);
                                item.setAttribute('aria-selected', String(isSelected));
                            });

                            setOpen(false);
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    });

                    document.addEventListener('click', (event) => {
                        if (!root.contains(event.target)) {
                            setOpen(false);
                        }
                    });

                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDropdownSelects);
            } else {
                initDropdownSelects();
            }
        })();
    </script>
@endonce
