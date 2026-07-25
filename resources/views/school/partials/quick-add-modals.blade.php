{{-- Quick Add Class Modal --}}
<x-modal.form
    id="quickClassModal"
    form-id="quickClassForm"
    title="Add Class"
    close-button-id="closeQuickClassModal"
    submit-label="Save"
    class="z-[200]"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px;"
>
    <div class="relative">
        <x-input.control id="quick_class_name" name="class_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="quick_class_name">Class Name</x-input.floating-label>
    </div>
</x-modal.form>

{{-- Quick Add Group Modal --}}
<x-modal.form
    id="quickGroupModal"
    form-id="quickGroupForm"
    title="Add Group"
    close-button-id="closeQuickGroupModal"
    submit-label="Save"
    class="z-[200]"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px;"
>
    <div class="relative">
        <x-input.dropdown-select id="quick_group_class" name="class_id" placeholder="Select Class" />
        <x-input.floating-label for="quick_group_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_group_name" name="group_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="quick_group_name">Group Name</x-input.floating-label>
    </div>
</x-modal.form>

{{-- Quick Add Section Modal --}}
<x-modal.form
    id="quickSectionModal"
    form-id="quickSectionForm"
    title="Add Section"
    close-button-id="closeQuickSectionModal"
    submit-label="Save"
    class="z-[200]"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px;"
>
    <div class="relative">
        <x-input.dropdown-select id="quick_section_class" name="class_id" placeholder="Select Class" />
        <x-input.floating-label for="quick_section_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="quick_section_group" name="group_id" placeholder="Select Group" />
        <x-input.floating-label for="quick_section_group" :floating="false">Group *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_section_name" name="section_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="quick_section_name">Section Name</x-input.floating-label>
    </div>
</x-modal.form>

{{-- Quick Add Session Modal --}}
<x-modal.form
    id="quickSessionModal"
    form-id="quickSessionForm"
    title="Add Session"
    close-button-id="closeQuickSessionModal"
    submit-label="Save"
    class="z-[200]"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px;"
>
    <div class="relative">
        <x-input.dropdown-select id="quick_session_class" name="class_id" placeholder="Select Class" />
        <x-input.floating-label for="quick_session_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="quick_session_group" name="group_id" placeholder="Select Group" />
        <x-input.floating-label for="quick_session_group" :floating="false">Group</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="quick_session_section" name="section_id" placeholder="Select Section" />
        <x-input.floating-label for="quick_session_section" :floating="false">Section</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_session_start_date" type="date" required placeholder=" " onchange="calculateQuickSession()" />
        <x-input.floating-label for="quick_session_start_date" :floating="false">Start Date *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_session_end_date" type="date" required placeholder=" " onchange="calculateQuickSession()" />
        <x-input.floating-label for="quick_session_end_date" :floating="false">End Date *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_session_year" readonly placeholder=" " required />
        <x-input.floating-label for="quick_session_year">Session Year *</x-input.floating-label>
    </div>
    <input type="hidden" id="quick_session_total_days" name="total_days">
</x-modal.form>

{{-- Quick Add Subject Modal --}}
<x-modal.form
    id="quickSubjectModal"
    form-id="quickSubjectForm"
    title="Add Subject"
    close-button-id="closeQuickSubjectModal"
    submit-label="Save"
    class="z-[200]"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px;"
>
    <div class="relative">
        <x-input.dropdown-select id="quick_subject_class" name="class_id" placeholder="Select Class" />
        <x-input.floating-label for="quick_subject_class" :floating="false">Class *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="quick_subject_group" name="group_id" placeholder="Select Group" />
        <x-input.floating-label for="quick_subject_group" :floating="false">Group</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="quick_subject_section" name="section_id" placeholder="Select Section" />
        <x-input.floating-label for="quick_subject_section" :floating="false">Section *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_subject_name" name="subject_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="quick_subject_name">Subject Name *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_subject_code" name="subject_code" class="peer placeholder:text-transparent" placeholder=" " />
        <x-input.floating-label for="quick_subject_code">Subject Code</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_subject_theory_marks" name="marks[theory_marks]" type="number" class="peer placeholder:text-transparent" placeholder=" " />
        <x-input.floating-label for="quick_subject_theory_marks">Theory Marks</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="quick_subject_practical_marks" name="marks[practical_marks]" type="number" class="peer placeholder:text-transparent" placeholder=" " />
        <x-input.floating-label for="quick_subject_practical_marks">Practical Marks</x-input.floating-label>
    </div>
</x-modal.form>
