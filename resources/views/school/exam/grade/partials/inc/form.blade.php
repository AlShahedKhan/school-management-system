<input type="hidden" name="edit_id" id="edit_id">

<div id="step1" class="space-y-4">
    <div class="relative">
        <div class="flex gap-2">
            <div class="relative min-w-0 flex-1">
                <x-input.dropdown-select
                    id="full_mark"
                    name="full_mark"
                    value="100"
                    placeholder="Select Total Mark"
                    :options="[
                        ['value' => '100', 'label' => '100 Mark Grade'],
                        ['value' => '50', 'label' => '50 Mark Grade'],
                    ]"
                />
                <x-input.floating-label for="full_mark" :floating="false">
                    Total Mark (Full Mark)
                </x-input.floating-label>
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
        <div id="customFullMarkInput" class="mt-3 hidden gap-2">
            <div class="relative min-w-0 flex-1">
                <x-input.control
                    id="custom_full_mark"
                    type="number"
                    min="1"
                    step="0.01"
                    class="peer placeholder:text-transparent"
                    placeholder=" "
                />
                <x-input.floating-label for="custom_full_mark">
                    Custom full mark
                </x-input.floating-label>
            </div>
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

    <div class="hidden grid-cols-1 gap-2 px-3 mb-2 sm:grid sm:grid-cols-[2fr_2fr_2fr_2fr_1fr]">
        <div class="min-w-0 text-[9px] capitalize text-gray-400 font-normal">Minimum mark</div>
        <div class="min-w-0 text-[9px] capitalize text-gray-400 font-normal">Maximum mark</div>
        <div class="min-w-0 text-[9px] capitalize text-gray-400 font-normal">Letter name</div>
        <div class="min-w-0 text-[9px] capitalize text-gray-400 font-normal">Point no</div>
        <div></div>
    </div>

    <div id="gradeRowsContainer" class="space-y-3"></div>

    <div class="flex justify-end pr-5 sm:pr-3">
        <x-button.secondary
            type="button"
            onclick="addGradeRow()"
            class="h-8 w-8 px-0 text-blue-600"
            aria-label="Add grade distribution row"
            title="Add grade distribution row"
        >
            <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
        </x-button.secondary>
    </div>
</div>
