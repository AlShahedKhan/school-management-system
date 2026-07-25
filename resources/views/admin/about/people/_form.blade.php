@php
    $aboutPerson ??= null;
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">English Content</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="name_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Name</label>
                        <input id="name_en" name="name_en" type="text" value="{{ old('name_en', $aboutPerson?->name_en) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('name_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="designation_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Designation</label>
                        <input id="designation_en" name="designation_en" type="text" value="{{ old('designation_en', $aboutPerson?->designation_en) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('designation_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="summary_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Summary</label>
                        <textarea id="summary_en" name="summary_en" rows="5" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('summary_en', $aboutPerson?->summary_en) }}</textarea>
                        @error('summary_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="details_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Details</label>
                        <textarea id="details_en" name="details_en" rows="10" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('details_en', $aboutPerson?->details_en) }}</textarea>
                        @error('details_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Bangla Content</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="name_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Name</label>
                        <input id="name_bn" name="name_bn" type="text" value="{{ old('name_bn', $aboutPerson?->name_bn) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('name_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="designation_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Designation</label>
                        <input id="designation_bn" name="designation_bn" type="text" value="{{ old('designation_bn', $aboutPerson?->designation_bn) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('designation_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="summary_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Summary</label>
                        <textarea id="summary_bn" name="summary_bn" rows="5" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('summary_bn', $aboutPerson?->summary_bn) }}</textarea>
                        @error('summary_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="details_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Details</label>
                        <textarea id="details_bn" name="details_bn" rows="10" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('details_bn', $aboutPerson?->details_bn) }}</textarea>
                        @error('details_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800">Profile Settings</h3>
            <div class="mt-4 grid gap-4">
                <div>
                    <label for="slug" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $aboutPerson?->slug) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="sort_order" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Sort Order</label>
                    <input id="sort_order" name="sort_order" type="number" min="1" value="{{ old('sort_order', $aboutPerson?->sort_order ?? 1) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-3 border border-gray-200 px-3 py-3 text-sm font-medium text-gray-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $aboutPerson?->is_active ?? true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                    Active on public site
                </label>
            </div>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800">Profile Image</h3>
            <div class="mt-4">
                @if ($aboutPerson?->image)
                    <div class="mb-4 flex justify-center">
                        <img src="{{ asset('storage/' . $aboutPerson->image) }}" alt="{{ $aboutPerson->name_en }}" class="h-28 w-28 rounded-full object-cover ring-4 ring-slate-100">
                    </div>
                @endif

                <input type="file" id="image" name="image" accept="image/*" class="block w-full border border-gray-200 bg-white text-xs text-gray-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                @error('image') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                @if ($aboutPerson?->image)
                    <p class="mt-2 text-xs text-gray-400">Leave empty to keep the current image.</p>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-3 border border-gray-100 bg-white p-5 shadow-sm">
            <button type="submit" class="inline-flex h-11 items-center justify-center bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.about.people.index') }}" class="inline-flex h-11 items-center justify-center border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                Cancel
            </a>
        </div>
    </div>
</div>
