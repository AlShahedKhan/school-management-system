@props([
    'id' => 'photoInput',
    'name' => 'photo',
    'previewId' => 'imagePreview',
    'accept' => 'image/*',
])

<div class="flex gap-3">
    <div class="flex-grow">
        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            accept="{{ $accept }}"
            data-photo-input
            data-preview-id="{{ $previewId }}"
            {{ $attributes->class([
                'form-input-fixed flex h-8 w-full items-center border border-slate-300 bg-white text-[10px] text-slate-600 outline-none transition-colors',
                'file:mr-4 file:h-full file:border-0 file:border-r file:border-slate-200 file:bg-slate-50 file:px-3 file:text-[10px] file:text-slate-600',
                'focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
            ])->merge([
                'style' => 'border-radius: 0;',
            ]) }}
        >
    </div>

    <div
        id="{{ $previewId }}"
        class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden border border-slate-300 bg-white text-slate-400"
        style="border-radius: 0;"
    >
        <i class="mdi mdi-camera text-sm" aria-hidden="true"></i>
    </div>
</div>

@once
    <script>
        (() => {
            const initializePhotoInputs = () => {
                document.querySelectorAll('[data-photo-input]').forEach((input) => {
                    if (input.dataset.photoReady === 'true') return;

                    input.dataset.photoReady = 'true';
                    input.addEventListener('change', () => {
                        const file = input.files?.[0];
                        const preview = document.getElementById(input.dataset.previewId);
                        if (!file || !preview) return;

                        const reader = new FileReader();
                        reader.addEventListener('load', () => {
                            const image = document.createElement('img');
                            image.src = reader.result;
                            image.alt = '';
                            image.className = 'h-full w-full object-contain';
                            preview.replaceChildren(image);
                        });
                        reader.readAsDataURL(file);
                    });
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializePhotoInputs);
            } else {
                initializePhotoInputs();
            }
        })();
    </script>
@endonce
