<input type="hidden" name="edit_id" id="edit_id">
<div class="relative">
    <x-input.dropdown-select
        id="examFormClass"
        name="class_id"
        placeholder="Select Class"
        :value="old('class_id')"
        :options="[]"
        add-button-id="openClassFromExamForm"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="examFormGroup"
        name="group_id"
        placeholder="Select Group"
        :value="old('group_id')"
        :options="[]"
        add-button-id="openGroupFromExamForm"
        add-button-label="Add group"
        add-button-target="groupModal"
    />
    <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="examFormSection"
        name="section_id"
        placeholder="Select Section"
        :value="old('section_id')"
        :options="[]"
        add-button-id="openSectionFromExamForm"
        add-button-label="Add section"
        add-button-target="sectionModal"
    />
    <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="examFormSession"
        name="session_id"
        placeholder="Select Session"
        :value="old('session_id')"
        :options="[]"
        add-button-id="openSessionFromExamForm"
        add-button-label="Add session"
        add-button-target="sessionModal"
    />
    <div id="session_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="exam_name" class="peer placeholder:text-transparent" name="exam_name" placeholder=" " />
    <x-input.floating-label for="exam_name">Exam Name</x-input.floating-label>
    <div id="exam_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control type="date" id="exam_start_date" class="peer placeholder:text-transparent" name="exam_start_date" placeholder=" " />
    <x-input.floating-label for="exam_start_date">Exam Start Date</x-input.floating-label>
    <div id="exam_start_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control type="date" id="exam_end_date" class="peer placeholder:text-transparent" name="exam_end_date" placeholder=" " />
    <x-input.floating-label for="exam_end_date">Exam End Date</x-input.floating-label>
    <div id="exam_end_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
