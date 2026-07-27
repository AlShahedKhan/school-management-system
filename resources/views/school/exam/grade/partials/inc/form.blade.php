<input type="hidden" name="edit_id" id="edit_id">

<div id="step1" class="space-y-4">
    <div class="relative">
        <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total Mark (Full Mark)</label>
        <div class="flex gap-2">
            <div class="relative min-w-0 flex-1">
                <select id="full_mark" name="full_mark"
                    class="w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] focus:border-blue-600 outline-none transition-colors appearance-none bg-white"
                    style="border-radius: 0;">
                    <option value="100">100 Mark Grade</option>
                    <option value="50">50 Mark Grade</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <i class="fas fa-chevron-down text-[9px]"></i>
                </div>
            </div>
            <x-button.secondary
                type="button"
                onclick="toggleCustomFullMarkInput()"
                class="h-8 w-8 shrink-0 px-0"
                aria-label="Add custom full mark"
                title="Add custom full mark"
            >
                <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
            </x-button.secondary>
        </div>
        <div id="customFullMarkInput" class="mt-2 hidden flex gap-2">
            <input
                id="custom_full_mark"
                type="number"
                min="1"
                step="0.01"
                placeholder="Enter full mark"
                class="min-w-0 flex-1 border border-gray-200 px-3 text-xs h-[32px] outline-none focus:border-blue-600"
                style="border-radius: 0;"
            >
            <x-button.secondary type="button" onclick="addCustomFullMark()" class="h-8 px-3">
                Add
            </x-button.secondary>
        </div>
        <div id="full_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
</div>

<div id="step2" class="hidden space-y-4">
    <div class="flex justify-between items-center mb-2">
        <h4 class="text-[11px] font-medium text-blue-600 capitalize">Grade Distribution</h4>
    </div>

    <div class="grid grid-cols-9 gap-2 mb-2 px-1 hidden sm:grid">
        <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Minimum mark</div>
        <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Maximum mark</div>
        <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Letter name</div>
        <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Point no</div>
        <div class="col-span-1"></div>
    </div>

    <div id="gradeRowsContainer" class="space-y-3"></div>

    <div class="flex justify-end pr-5 sm:pr-3">
        <button type="button" onclick="addGradeRow()"
            class="text-blue-600 hover:text-blue-800 transition-all p-1">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
