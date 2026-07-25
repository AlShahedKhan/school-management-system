<input type="hidden" name="record_id" id="record_id">

<div class="grid grid-cols-2 gap-3">
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormDay"
            name="day_name"
            placeholder="Select Day"
            :value="old('day_name')"
            :options="[]"
        />
        <div id="day_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormTeacher"
            name="teacher_id"
            placeholder="Select Teacher"
            :value="old('teacher_id')"
            :options="[]"
        />
        <div id="teacher_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.control type="time" id="start_time" class="peer placeholder:text-transparent" name="start_time" placeholder=" " />
        <x-input.floating-label for="start_time">Start Time</x-input.floating-label>
        <div id="start_time_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.control type="time" id="end_time" class="peer placeholder:text-transparent" name="end_time" placeholder=" " />
        <x-input.floating-label for="end_time">End Time</x-input.floating-label>
        <div id="end_time_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormClass"
            name="class_id"
            placeholder="Select Class"
            :value="old('class_id')"
            :options="[]"
            add-button-id="openClassFromRoutineForm"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormGroup"
            name="group_id"
            placeholder="Select Group"
            :value="old('group_id')"
            :options="[]"
            add-button-id="openGroupFromRoutineForm"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormSection"
            name="section_id"
            placeholder="Select Section"
            :value="old('section_id')"
            :options="[]"
            add-button-id="openSectionFromRoutineForm"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="routineFormSubject"
            name="subject_id"
            placeholder="Select Subject"
            :value="old('subject_id')"
            :options="[]"
            add-button-id="openSubjectFromRoutineForm"
            add-button-label="Add subject"
            add-button-target="subjectModal"
        />
        <div id="subject_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
</div>
