@php
    $feature ??= null;
    $iconOptions = collect(\App\Support\HomePageDefaults::ICONS)
        ->mapWithKeys(fn (string $icon) => [$icon => str($icon)->replace('-', ' ')->title()])
        ->all();
@endphp

<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">English Content</h3>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label for="title_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                            <input id="title_en" name="title_en" type="text" value="{{ old('title_en', $feature?->title_en) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('title_en')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</label>
                            <textarea id="description_en" name="description_en" rows="6" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('description_en', $feature?->description_en) }}</textarea>
                            @error('description_en')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">Bangla Content</h3>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label for="title_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                            <input id="title_bn" name="title_bn" type="text" value="{{ old('title_bn', $feature?->title_bn) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('title_bn')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</label>
                            <textarea id="description_bn" name="description_bn" rows="6" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('description_bn', $feature?->description_bn) }}</textarea>
                            @error('description_bn')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Feature Settings</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="icon" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Icon</label>
                        <select id="icon" name="icon" class="h-11 w-full border border-gray-200 bg-white px-3 text-sm outline-none transition focus:border-blue-500">
                            @foreach ($iconOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('icon', $feature?->icon ?? 'layout-dashboard') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('icon')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Sort Order</label>
                        <input id="sort_order" name="sort_order" type="number" min="1" value="{{ old('sort_order', $feature?->sort_order ?? 1) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('sort_order')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 border border-gray-200 px-3 py-3 text-sm font-medium text-gray-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $feature?->is_active ?? true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                        Active on public site
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-3 border border-gray-100 bg-white p-5 shadow-sm">
                <button type="submit" class="inline-flex h-11 items-center justify-center bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('admin.features.index') }}" class="inline-flex h-11 items-center justify-center border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
