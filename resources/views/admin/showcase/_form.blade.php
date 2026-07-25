<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="border border-gray-100 bg-white p-5 shadow-sm">
        <div>
            <h3 class="text-sm font-bold text-gray-800">
                Showcase Details
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Add English and Bangla titles for the page showcase.
            </p>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <div>
                <label
                    for="title_en"
                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500"
                >
                    English Title
                </label>

                <input
                    type="text"
                    id="title_en"
                    name="title_en"
                    value="{{ old('title_en', $showcase->title_en ?? $showcase->title ?? '') }}"
                    placeholder="Enter English showcase title"
                    class="w-full border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                @error('title_en')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="title_bn"
                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500"
                >
                    Bangla Title
                </label>

                <input
                    type="text"
                    id="title_bn"
                    name="title_bn"
                    value="{{ old('title_bn', $showcase->title_bn ?? '') }}"
                    placeholder="Enter Bangla showcase title"
                    class="w-full border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                @error('title_bn')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="border border-gray-100 bg-white p-5 shadow-sm">
            <div>
                <h3 class="text-sm font-bold text-gray-800">
                    Showcase Image
                </h3>

                <p class="mt-1 text-xs text-gray-500">
                    Upload the page screenshot displayed on the website.
                </p>
            </div>

            <div class="mt-5">
                @if (isset($showcase) && $showcase->image)
                    <div class="mb-4 overflow-hidden border border-gray-200 bg-gray-50">
                        <img
                            src="{{ asset('storage/' . $showcase->image) }}"
                            alt="{{ $showcase->title_en ?? $showcase->title }}"
                            class="w-full object-cover"
                        >
                    </div>
                @endif

                <label
                    for="image"
                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500"
                >
                    Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    class="block w-full border border-gray-200 bg-white text-xs text-gray-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                >

                @error('image')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @if (isset($showcase) && $showcase->image)
                    <p class="mt-2 text-xs text-gray-400">
                        Leave the image empty to keep the current image.
                    </p>
                @endif
            </div>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center bg-blue-600 px-4 py-3 text-xs font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <i class="fas fa-floppy-disk mr-2"></i>

                {{ $submitLabel }}
            </button>
        </div>
    </div>
</div>
