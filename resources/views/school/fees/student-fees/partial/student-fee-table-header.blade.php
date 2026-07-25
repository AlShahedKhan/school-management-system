<div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

        <div class="w-full lg:w-auto">
            <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
            <div class="flex items-center text-slate-400 text-[12px] mt-1">
                <span>School</span>
                <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                <span id="pageTitle" class="text-slate-500"></span>
            </div>

            <x-input.search
                id="feeSearch"
                placeholder="Search Student Fees..."
                class="w-full sm:w-64 mt-3 hidden lg:block"
                style="border-radius: 0;"
            />
        </div>

        <div class="flex flex-row items-center gap-2 w-full lg:w-auto">
            <x-button.secondary id="btnFilter" class="flex-1 lg:flex-none">
                Filter
            </x-button.secondary>

            <x-button.secondary id="btnExport" class="flex-1 lg:flex-none">
                Export
            </x-button.secondary>
        </div>
    </div>

    <x-input.search
        id="feeSearchMobile"
        placeholder="Search Student Fees..."
        class="w-full mt-3 lg:hidden"
        style="border-radius: 0;"
    />
</div>
