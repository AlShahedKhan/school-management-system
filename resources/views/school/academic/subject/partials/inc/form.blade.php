<input type="hidden" name="record_id" id="record_id">

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div class="relative">
        <x-input.dropdown-select
            id="subjectFormClass"
            name="class_id"
            placeholder="Select Class"
            :value="old('class_id')"
            :options="[]"
            add-button-id="openClassFromSubjectForm"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="subjectFormGroup"
            name="group_id"
            placeholder="Select Group"
            :value="old('group_id')"
            :options="[]"
            add-button-id="openGroupFromSubjectForm"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="subjectFormSection"
            name="section_id"
            placeholder="Select Section"
            :value="old('section_id')"
            :options="[]"
            add-button-id="openSectionFromSubjectForm"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.control id="subject_code" class="peer placeholder:text-transparent" name="subject_code" placeholder=" " />
        <x-input.floating-label for="subject_code">Subject Code</x-input.floating-label>
        <div id="subject_code_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.control id="subject_name" class="peer placeholder:text-transparent" name="subject_name" placeholder=" " />
        <x-input.floating-label for="subject_name">Subject Name</x-input.floating-label>
        <div id="subject_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="subjectFormGrade"
            name="grade_id"
            placeholder="Select Grade Type"
            :value="old('grade_id')"
            :options="[]"
        />
        <div id="grade_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
</div>

<div class="mt-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
        <div class="relative">
            <x-input.control type="number" id="tutorial_mark" class="peer placeholder:text-transparent" name="tutorial_mark" placeholder=" " min="0" disabled />
            <x-input.floating-label for="tutorial_mark">Tutorial Mark</x-input.floating-label>
            <div id="tutorial_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control type="number" id="mcq_mark" class="peer placeholder:text-transparent" name="mcq_mark" placeholder=" " min="0" disabled />
            <x-input.floating-label for="mcq_mark">MCQ Mark</x-input.floating-label>
            <div id="mcq_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control type="number" id="writing_mark" class="peer placeholder:text-transparent" name="writing_mark" placeholder=" " min="0" disabled />
            <x-input.floating-label for="writing_mark">Writing Mark</x-input.floating-label>
            <div id="writing_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control type="number" id="practical_mark" class="peer placeholder:text-transparent" name="practical_mark" placeholder=" " min="0" disabled />
            <x-input.floating-label for="practical_mark">Practical Mark</x-input.floating-label>
            <div id="practical_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control type="number" id="total_mark" class="peer placeholder:text-transparent bg-gray-100/50" name="total_mark" placeholder=" " readonly />
            <x-input.floating-label for="total_mark">Total Mark</x-input.floating-label>
            <div id="total_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control type="number" id="fail_mark" class="peer placeholder:text-transparent" name="fail_mark" placeholder=" " min="0" disabled />
            <x-input.floating-label for="fail_mark">Fail Mark</x-input.floating-label>
            <div id="fail_mark_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
</div>

<input type="hidden" id="max_allowed_mark" value="100">
