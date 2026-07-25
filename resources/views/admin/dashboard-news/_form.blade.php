<div class="border border-gray-100 bg-white p-4 shadow-sm">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="label" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                Label
            </label>
            <input
                id="label"
                name="label"
                value="{{ old('label', $dashboardNews->label ?? 'News') }}"
                class="w-full border border-gray-200 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-blue-500"
                maxlength="20"
                required
            >
            @error('label')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sort_order" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                Order
            </label>
            <input
                id="sort_order"
                name="sort_order"
                type="number"
                min="1"
                value="{{ old('sort_order', $dashboardNews->sort_order ?? 1) }}"
                class="w-full border border-gray-200 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-blue-500"
                required
            >
            @error('sort_order')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="message" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                Message
            </label>
            <input
                id="message"
                name="message"
                value="{{ old('message', $dashboardNews->message ?? '') }}"
                class="w-full border border-gray-200 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-blue-500"
                maxlength="180"
                required
            >
            @error('message')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="starts_at" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                Starts At
            </label>
            <input
                id="starts_at"
                name="starts_at"
                type="datetime-local"
                value="{{ old('starts_at', optional($dashboardNews->starts_at ?? null)->format('Y-m-d\TH:i')) }}"
                class="w-full border border-gray-200 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-blue-500"
            >
            @error('starts_at')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ends_at" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                Ends At
            </label>
            <input
                id="ends_at"
                name="ends_at"
                type="datetime-local"
                value="{{ old('ends_at', optional($dashboardNews->ends_at ?? null)->format('Y-m-d\TH:i')) }}"
                class="w-full border border-gray-200 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-blue-500"
            >
            @error('ends_at')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                @checked(old('is_active', $dashboardNews->is_active ?? true))
            >
            Active
        </label>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.dashboard-news.index') }}" class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
            {{ $submitLabel }}
        </button>
    </div>
</div>
