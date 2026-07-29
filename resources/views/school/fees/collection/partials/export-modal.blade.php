<x-modal.form
    id="exportModal"
    form-id="exportForm"
    title="Export Payments"
    close-button-id="closeExportFooter"
    action="#"
    method="GET"
    :enctype="null"
    class="export-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[400px]"
>
    <div class="px-5 pt-4 pb-2 space-y-3 col-span-full">
        <p class="text-[9px] text-gray-400 uppercase tracking-widest">Filter by (optional)</p>

        <div>
            <label class="block text-[10px] text-blue-600 font-medium mb-1">Student ID (Quick Search)</label>
            <div class="relative">
                <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="exportStudentIdSearch"
                    placeholder="Type student ID..."
                    class="w-full border border-blue-200 bg-blue-50/20 pl-7 pr-3 py-1.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                    style="border-radius:0;" />
            </div>
            <p id="exportIdNotFound" class="hidden text-[9px] text-red-500 mt-1">No student found with this ID.</p>
        </div>

        <div class="h-px bg-gray-100"></div>

        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Class</label>
            <select id="exportClassFilter"
                class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                style="border-radius:0;">
                <option value="">All Classes</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Group</label>
            <select id="exportGroupFilter"
                class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                style="border-radius:0;">
                <option value="">All Groups</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Section</label>
            <select id="exportSectionFilter"
                class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                style="border-radius:0;">
                <option value="">All Sections</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Session</label>
            <select id="exportSessionFilter"
                class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                style="border-radius:0;">
                <option value="">All Sessions</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] mb-1 font-medium" style="color:#2563eb;">
                Student <span class="text-red-500">*</span>
            </label>
            <select id="exportStudentFilter" required
                class="w-full border py-1.5 pl-2.5 pr-7 text-xs outline-none appearance-none h-[32px] border-blue-300 bg-blue-50/30 focus:border-blue-500"
                style="border-radius:0;">
                <option value="">— Select Student —</option>
            </select>
            <p id="exportStudentError" class="hidden text-[9px] text-red-500 mt-1">Please select a student.</p>
        </div>
    </div>

    <div class="h-px bg-gray-100 col-span-full mx-5 my-3"></div>

    <div class="px-5 space-y-2 col-span-full">
        <button id="exportPdf"
            class="w-full flex items-center gap-3 px-4 py-2.5 border border-gray-200 hover:border-blue-500 hover:bg-blue-50/40 text-gray-600 hover:text-blue-600 transition-all group"
            style="border-radius:0;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 group-hover:text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <div class="text-left">
                <div class="text-[11px] font-semibold tracking-wide uppercase">Download Report</div>
                <div class="text-[9px] text-gray-400 mt-0.5">Save as A4 PDF file</div>
            </div>
        </button>
    </div>

    <x-slot:footer>
        <div class="px-5 py-4">
            <x-button.secondary id="closeExportFooter" type="button" class="w-full">Cancel</x-button.secondary>
        </div>
    </x-slot:footer>
</x-modal.form>
