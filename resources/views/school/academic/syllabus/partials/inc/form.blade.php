<input type="hidden" name="record_id" id="record_id">

<div class="grid grid-cols-2 gap-3">
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormClass"
            name="class_id"
            placeholder="Select Class"
            :value="old('class_id')"
            :options="[]"
            add-button-id="openClassFromSyllabusForm"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormGroup"
            name="group_id"
            placeholder="Select Group"
            :value="old('group_id')"
            :options="[]"
            add-button-id="openGroupFromSyllabusForm"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormSection"
            name="section_id"
            placeholder="Select Section"
            :value="old('section_id')"
            :options="[]"
            add-button-id="openSectionFromSyllabusForm"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormSession"
            name="session_id"
            placeholder="Select Session"
            :value="old('session_id')"
            :options="[]"
            add-button-id="openSessionFromSyllabusForm"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />
        <div id="session_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormSubject"
            name="subject_id"
            placeholder="Select Subject"
            :value="old('subject_id')"
            :options="[]"
            add-button-id="openSubjectFromSyllabusForm"
            add-button-label="Add subject"
            add-button-target="subjectModal"
        />
        <div id="subject_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="syllabusFormExam"
            name="exam_id"
            placeholder="Select Exam"
            :value="old('exam_id')"
            :options="[]"
            add-button-id="openExamFromSyllabusForm"
            add-button-label="Add exam"
            add-button-target="examModal"
        />
        <div id="exam_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
</div>

<div class="mt-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
        <div class="relative">
            <x-input.control id="start_page" class="peer placeholder:text-transparent" name="start_page" placeholder=" " />
            <x-input.floating-label for="start_page">Start Page</x-input.floating-label>
            <div id="start_page_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control id="end_page" class="peer placeholder:text-transparent" name="end_page" placeholder=" " />
            <x-input.floating-label for="end_page">End Page</x-input.floating-label>
            <div id="end_page_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
</div>
