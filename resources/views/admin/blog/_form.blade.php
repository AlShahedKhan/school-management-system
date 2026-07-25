@php
    $blog ??= null;
    $english = $blog?->translations->firstWhere('locale', 'en');
    $bangla = $blog?->translations->firstWhere('locale', 'bn');
    $status = old('status', $blog?->status ?? 'draft');
    $publishedAt = old(
        'published_at',
        $blog?->published_at?->format('Y-m-d\TH:i')
    );
@endphp

<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="space-y-6">
            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">English Content</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="en_title" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                        <input id="en_title" name="en[title]" type="text" value="{{ old('en.title', $english?->title) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('en.title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="en_description" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</label>
                        <textarea id="en_description" name="en[description]" rows="8" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('en.description', $english?->description) }}</textarea>
                        @error('en.description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Bangla Content</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="bn_title" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                        <input id="bn_title" name="bn[title]" type="text" value="{{ old('bn.title', $bangla?->title) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('bn.title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bn_description" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</label>
                        <textarea id="bn_description" name="bn[description]" rows="8" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('bn.description', $bangla?->description) }}</textarea>
                        @error('bn.description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">English SEO</h3>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label for="en_seo_tags" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">SEO Tags</label>
                            <textarea id="en_seo_tags" name="en[seo_tags]" rows="3" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('en.seo_tags', $english?->seo_tags) }}</textarea>
                        </div>
                        <div>
                            <label for="en_meta_title" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Title</label>
                            <input id="en_meta_title" name="en[meta_title]" type="text" value="{{ old('en.meta_title', $english?->meta_title) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        </div>
                        <div>
                            <label for="en_meta_keywords" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Keywords</label>
                            <textarea id="en_meta_keywords" name="en[meta_keywords]" rows="3" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('en.meta_keywords', $english?->meta_keywords) }}</textarea>
                        </div>
                        <div>
                            <label for="en_meta_description" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Description</label>
                            <textarea id="en_meta_description" name="en[meta_description]" rows="4" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('en.meta_description', $english?->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">Bangla SEO</h3>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label for="bn_seo_tags" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">SEO Tags</label>
                            <textarea id="bn_seo_tags" name="bn[seo_tags]" rows="3" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('bn.seo_tags', $bangla?->seo_tags) }}</textarea>
                        </div>
                        <div>
                            <label for="bn_meta_title" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Title</label>
                            <input id="bn_meta_title" name="bn[meta_title]" type="text" value="{{ old('bn.meta_title', $bangla?->meta_title) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        </div>
                        <div>
                            <label for="bn_meta_keywords" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Keywords</label>
                            <textarea id="bn_meta_keywords" name="bn[meta_keywords]" rows="3" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('bn.meta_keywords', $bangla?->meta_keywords) }}</textarea>
                        </div>
                        <div>
                            <label for="bn_meta_description" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Description</label>
                            <textarea id="bn_meta_description" name="bn[meta_description]" rows="4" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('bn.meta_description', $bangla?->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Publish Settings</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="status" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</label>
                        <select id="status" name="status" class="h-11 w-full border border-gray-200 bg-white px-3 text-sm outline-none transition focus:border-blue-500">
                            <option value="draft" @selected($status === 'draft')>Draft</option>
                            <option value="published" @selected($status === 'published')>Published</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="published_at" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Published At</label>
                        <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAt }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                        @error('published_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 border border-gray-200 px-3 py-3 text-sm font-medium text-gray-700">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $blog?->is_featured)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                        Featured blog
                    </label>
                </div>
            </div>

            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Cover Image</h3>
                <div class="mt-4 space-y-4">
                    @if ($blog?->image)
                        <img
                            src="{{ asset('storage/' . $blog->image) }}"
                            alt="Blog image preview"
                            class="h-48 w-full border border-gray-200 object-cover"
                        >
                    @endif

                    <div>
                        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-600 file:mr-4 file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700">
                        @error('image')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border border-gray-100 bg-white p-5 shadow-sm">
                <button type="submit" class="inline-flex h-11 items-center justify-center bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('admin.blogs.index') }}" class="inline-flex h-11 items-center justify-center border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
