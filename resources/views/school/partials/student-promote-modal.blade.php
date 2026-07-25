<x-modal.form
    id="promoteModal"
    form-id="promoteForm"
    title="Student Promotion"
    close-button-id="closePromoteModal"
    title-class="student-promote-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    panel-style="border-radius:4px; max-height:min(550px, calc(100dvh - 2.5rem));"
    submit-label="Confirm"
>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
        <span class="text-xs font-bold text-slate-700">Source Academic Details & Date</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_class" name="from_class" placeholder="Select Class" add-button-id="add_promote_from_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
        <x-input.floating-label for="from_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_group" name="from_group" placeholder="Select Group" add-button-id="add_promote_from_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
        <x-input.floating-label for="from_group" :floating="false">Group *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_section" name="from_section" placeholder="Select Section" add-button-id="add_promote_from_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
        <x-input.floating-label for="from_section" :floating="false">Section *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_session" name="from_session" placeholder="Select Session" add-button-id="add_promote_from_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="from_session" :floating="false">Session *</x-input.floating-label>
    </div>
    <div class="flex items-start gap-2">
        <div class="relative flex-grow">
            <x-input.control id="promote_fee" class="peer bg-slate-50" readonly placeholder=" " required />
            <x-input.floating-label for="promote_fee">Promote Fee *</x-input.floating-label>
        </div>
        <button id="btnCreateFeeTemplatePromote" type="button"
            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center border border-gray-200 bg-white text-blue-600 text-xs font-semibold transition-colors hover:bg-slate-50 hover:border-gray-300"
            style="border-radius: 0;"
        >+</button>
    </div>
    <div class="relative">
        <x-input.control id="promote_date" class="peer" type="date" required placeholder=" " />
        <x-input.floating-label for="promote_date" :floating="false">Promote Date *</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center justify-between mt-4 mb-2 pb-1 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
            <span class="text-xs font-bold text-slate-700">Select Students</span>
        </div>
        <div class="flex items-center gap-1.5">
            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer accent-blue-600">
            <label for="selectAllCheckbox" class="text-[11px] font-semibold text-slate-500 cursor-pointer select-none">Select All</label>
        </div>
    </div>
    <div class="col-span-1 md:col-span-2 border border-slate-200 overflow-x-auto custom-scrollbar mb-2 max-h-[160px] overflow-y-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="p-2 font-semibold text-slate-600">Sl</th>
                    <th class="p-2 font-semibold text-slate-600">ID Number</th>
                    <th class="p-2 font-semibold text-slate-600">Student Name</th>
                    <th class="p-2 font-semibold text-slate-600 text-center">Select</th>
                </tr>
            </thead>
            <tbody id="studentSelectionBody" class="divide-y divide-slate-100 bg-white text-slate-700">
                <tr>
                    <td colspan="4" class="p-4 text-center text-slate-400">Please select Source Academic Details first.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
        <span class="text-xs font-bold text-slate-700">Destination Academic Details</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="to_class" name="to_class" placeholder="Select Class" add-button-id="add_promote_to_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
        <x-input.floating-label for="to_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="to_group" name="to_group" placeholder="Select Group" add-button-id="add_promote_to_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
        <x-input.floating-label for="to_group" :floating="false">Group *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="to_section" name="to_section" placeholder="Select Section" add-button-id="add_promote_to_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
        <x-input.floating-label for="to_section" :floating="false">Section *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="to_session" name="to_session" placeholder="Select Session" add-button-id="add_promote_to_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="to_session" :floating="false">Session *</x-input.floating-label>
    </div>
</x-modal.form>
@include('school.partials.fee-template-modal')
