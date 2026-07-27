<x-modal.form
    id="studentModal"
    form-id="studentForm"
    title="Update Student Info"
    close-button-id="closeStudentModal"
    title-class="student-edit-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    panel-style="border-radius:4px; max-height:min(550px, calc(100dvh - 2.5rem));"
>
    <input type="hidden" name="student_id" id="student_id">
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Admission Details</span>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="edit_school" name="school" class="peer bg-slate-50" readonly placeholder=" " />
        <x-input.floating-label for="edit_school">School Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_class" name="class_id" placeholder="Select Class" add-button-id="add_edit_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
        <x-input.floating-label for="edit_class" :floating="false">Class</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_group" name="group_id" placeholder="Select Group" add-button-id="add_edit_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
        <x-input.floating-label for="edit_group" :floating="false">Group</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_section" name="section_id" placeholder="Select Section" add-button-id="add_edit_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
        <x-input.floating-label for="edit_section" :floating="false">Section</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_session" name="session_id" placeholder="Select Session" add-button-id="add_edit_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="edit_session" :floating="false">Session</x-input.floating-label>
    </div>
    <div class="flex items-start gap-2">
        <div class="relative flex-grow">
            <x-input.control id="edit_admission_fee" name="admission_fee" class="peer bg-slate-50" readonly placeholder=" " required />
            <x-input.floating-label for="edit_admission_fee">Admission Fee</x-input.floating-label>
        </div>
        <button id="btnCreateFeeTemplateEdit" type="button"
            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center border border-gray-200 bg-white text-blue-600 text-xs font-semibold transition-colors hover:bg-slate-50 hover:border-gray-300"
            style="border-radius: 0;"
        >+</button>
    </div>
    <div class="relative">
        <x-input.control id="edit_admission_date" name="admission_date" class="peer" type="date" required placeholder=" " />
        <x-input.floating-label for="edit_admission_date" :floating="false">Admission Date</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Student Personal Details</span>
    </div>
    <div class="relative">
        <x-input.control id="edit_student_name" name="student_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_student_name">Student Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="edit_father_name" name="father_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_father_name">Father's Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="edit_mother_name" name="mother_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_mother_name">Mother's Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="edit_mobile" name="mobile" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_mobile">Student Mobile</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Guardian Information</span>
    </div>
    <div class="relative">
        <x-input.control id="edit_g_name" name="g_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_g_name">Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="edit_g_relation" name="g_relation" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_g_relation">Relation</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="edit_g_mobile" name="g_mobile" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_g_mobile">Mobile</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Current Address</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_current_country" name="current_country" placeholder="Select Country" value="Bangladesh" :options="['Bangladesh' => 'Bangladesh', 'India' => 'India', 'Pakistan' => 'Pakistan', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada']" />
        <x-input.floating-label for="edit_current_country" :floating="false">Country *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_current_division" name="current_division" placeholder="Select Division" />
        <x-input.floating-label for="edit_current_division" :floating="false">Division *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_current_district" name="current_district" placeholder="Select District" />
        <x-input.floating-label for="edit_current_district" :floating="false">District *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_current_upazila" name="current_upazila" placeholder="Select Upazila" />
        <x-input.floating-label for="edit_current_upazila" :floating="false">Upazila *</x-input.floating-label>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="edit_current_village" name="current_village" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_current_village">Village</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 py-1">
        <input type="checkbox" id="sameAsCurrentAddressModal" onchange="toggleSameAddressModal()" class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
        <label for="sameAsCurrentAddressModal" class="text-xs font-medium text-slate-500 cursor-pointer select-none">Same as Current Address</label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Permanent Address</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_permanent_country" name="permanent_country" placeholder="Select Country" value="Bangladesh" :options="['Bangladesh' => 'Bangladesh', 'India' => 'India', 'Pakistan' => 'Pakistan', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada']" />
        <x-input.floating-label for="edit_permanent_country" :floating="false">Country *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_permanent_division" name="permanent_division" placeholder="Select Division" />
        <x-input.floating-label for="edit_permanent_division" :floating="false">Division *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_permanent_district" name="permanent_district" placeholder="Select District" />
        <x-input.floating-label for="edit_permanent_district" :floating="false">District *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="edit_permanent_upazila" name="permanent_upazila" placeholder="Select Upazila" />
        <x-input.floating-label for="edit_permanent_upazila" :floating="false">Upazila *</x-input.floating-label>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="edit_permanent_village" name="permanent_village" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="edit_permanent_village">Village</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Student Photo</span>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.photo id="photoInput" name="image" preview-id="imagePreview" />
        <x-input.floating-label for="photoInput" :floating="false" class="pointer-events-auto peer-focus:text-slate-500">
            Student Photo
        </x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="flex w-full gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="closeStudentModal" type="button" class="flex-1">
                Cancel
            </x-button.secondary>

            <x-button.primary type="submit" class="flex-1">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
