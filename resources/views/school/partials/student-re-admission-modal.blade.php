<x-modal.form
    id="readmitModal"
    form-id="readmitForm"
    title="Student Re-Admission"
    close-button-id="closeReadmitModal"
    title-class="student-readmission-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    panel-style="border-radius:4px; max-height:min(550px, calc(100dvh - 2.5rem));"
    submit-label="Confirm"
>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
        <span class="text-xs font-bold text-slate-700">Source Academic Details</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_class" name="from_class" placeholder="Select Class" add-button-id="add_readmit_from_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
        <x-input.floating-label for="from_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_group" name="from_group" placeholder="Select Group" add-button-id="add_readmit_from_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
        <x-input.floating-label for="from_group" :floating="false">Group *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_section" name="from_section" placeholder="Select Section" add-button-id="add_readmit_from_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
        <x-input.floating-label for="from_section" :floating="false">Section *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="from_session" name="from_session" placeholder="Select Session" add-button-id="add_readmit_from_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="from_session" :floating="false">Session *</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
        <span class="text-xs font-bold text-slate-700">Re-Admission Action</span>
    </div>
    <div class="relative md:col-span-2">
        <x-input.dropdown-select id="readmit_student" name="readmit_student" placeholder="Select Student" />
        <x-input.floating-label for="readmit_student" :floating="false">Student *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="readmit_date" class="peer" type="date" required placeholder=" " />
        <x-input.floating-label for="readmit_date" :floating="false">Re-Admission Date *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="to_session" name="to_session" placeholder="Select Target Session" add-button-id="add_readmit_to_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="to_session" :floating="false">Target Session *</x-input.floating-label>
    </div>
    <div class="flex items-start gap-2 md:col-span-2">
        <div class="relative flex-grow">
            <x-input.control id="readmit_fee" class="peer bg-slate-50" readonly placeholder=" " required />
            <x-input.floating-label for="readmit_fee">Re-Admission Fee *</x-input.floating-label>
        </div>
        <button id="btnCreateFeeTemplateReadmit" type="button"
            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center border border-gray-200 bg-white text-blue-600 text-xs font-semibold transition-colors hover:bg-slate-50 hover:border-gray-300"
            style="border-radius: 0;"
        >+</button>
    </div>
</x-modal.form>
@include('school.partials.fee-template-modal')
