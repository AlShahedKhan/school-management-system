<x-modal.form
    id="admissionModal"
    form-id="admissionForm"
    title="Student Admission"
    close-button-id="closeAdmissionModal"
    title-class="student-admission-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    panel-style="border-radius:4px; max-height:min(550px, calc(100dvh - 2.5rem));"
>
    <input type="hidden" id="school_id" value="{{ Auth::user()->school_id }}">
    <input type="hidden" id="g_type" value="Other">
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Admission Details</span>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="a_school" value="{{ Auth::user()->school_name }}" class="peer bg-slate-50" readonly placeholder=" " />
        <x-input.floating-label for="a_school">School Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="a_class" name="class_id" placeholder="Select Class" add-button-id="add_a_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
        <x-input.floating-label for="a_class" :floating="false">Class</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="a_group" name="group_id" placeholder="Select Group" add-button-id="add_a_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
        <x-input.floating-label for="a_group" :floating="false">Group</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="a_section" name="section_id" placeholder="Select Section" add-button-id="add_a_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
        <x-input.floating-label for="a_section" :floating="false">Section</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="a_session" name="session_id" placeholder="Select Session" add-button-id="add_a_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
        <x-input.floating-label for="a_session" :floating="false">Session</x-input.floating-label>
    </div>
    <div class="flex items-start gap-2">
        <div class="relative flex-grow">
            <x-input.control id="a_fee" class="peer bg-slate-50" readonly placeholder=" " required />
            <x-input.floating-label for="a_fee">Admission Fee</x-input.floating-label>
        </div>
        <button id="btnCreateFeeTemplate" type="button"
            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center border border-gray-200 bg-white text-blue-600 text-xs font-semibold transition-colors hover:bg-slate-50 hover:border-gray-300"
            style="border-radius: 0;"
        >+</button>
    </div>
    <div class="relative">
        <x-input.control id="a_date" class="peer" type="date" required placeholder=" " />
        <x-input.floating-label for="a_date" :floating="false">Admission Date</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Student Personal Details</span>
    </div>
    <div class="relative">
        <x-input.control id="student_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="student_name">Student Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="father_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="father_name">Father's Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="mother_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="mother_name">Mother's Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="student_mobile" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="student_mobile">Student Mobile</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="dob" name="dob" class="peer" type="date" placeholder=" " />
        <x-input.floating-label for="dob" :floating="false">Date of Birth</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="nid_birth_certificate" name="nid_birth_certificate" class="peer placeholder:text-transparent" placeholder=" " />
        <x-input.floating-label for="nid_birth_certificate">NID / Birth Certificate</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="blood_group" name="blood_group" placeholder="Select Blood Group" :options="['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'O+' => 'O+', 'O-' => 'O-', 'AB+' => 'AB+', 'AB-' => 'AB-']" />
        <x-input.floating-label for="blood_group" :floating="false">Blood Group</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Guardian Information</span>
    </div>
    <div class="relative">
        <x-input.control id="g_name" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="g_name">Name</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="g_relation" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="g_relation">Relation</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="g_mobile" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="g_mobile">Mobile</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Current Address</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="current_country" name="current_country" placeholder="Select Country" value="Bangladesh" :options="['Bangladesh' => 'Bangladesh', 'India' => 'India', 'Pakistan' => 'Pakistan', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada']" />
        <x-input.floating-label for="current_country" :floating="false">Country *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="current_division" name="current_division" placeholder="Select Division" />
        <x-input.floating-label for="current_division" :floating="false">Division *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="current_district" name="current_district" placeholder="Select District" />
        <x-input.floating-label for="current_district" :floating="false">District *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="current_upazila" name="current_upazila" placeholder="Select Upazila" />
        <x-input.floating-label for="current_upazila" :floating="false">Upazila *</x-input.floating-label>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="current_village" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="current_village">Village</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 py-1">
        <input type="checkbox" id="sameAsCurrentAddress" onchange="toggleSameAddress()" class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
        <label for="sameAsCurrentAddress" class="text-xs font-medium text-slate-500 cursor-pointer select-none">Same as Current Address</label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Permanent Address</span>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="permanent_country" name="permanent_country" placeholder="Select Country" value="Bangladesh" :options="['Bangladesh' => 'Bangladesh', 'India' => 'India', 'Pakistan' => 'Pakistan', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada']" />
        <x-input.floating-label for="permanent_country" :floating="false">Country *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="permanent_division" name="permanent_division" placeholder="Select Division" />
        <x-input.floating-label for="permanent_division" :floating="false">Division *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="permanent_district" name="permanent_district" placeholder="Select District" />
        <x-input.floating-label for="permanent_district" :floating="false">District *</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="permanent_upazila" name="permanent_upazila" placeholder="Select Upazila" />
        <x-input.floating-label for="permanent_upazila" :floating="false">Upazila *</x-input.floating-label>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.control id="permanent_village" class="peer placeholder:text-transparent" placeholder=" " required />
        <x-input.floating-label for="permanent_village">Village</x-input.floating-label>
    </div>
    <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Student Photo</span>
    </div>
    <div class="relative col-span-1 md:col-span-2">
        <x-input.photo id="student_image" name="image" preview-id="studentPreview" />
        <x-input.floating-label for="student_image" :floating="false" class="pointer-events-auto peer-focus:text-slate-500">
            Student Photo
        </x-input.floating-label>
    </div>
</x-modal.form>
@include('school.partials.fee-template-modal')
