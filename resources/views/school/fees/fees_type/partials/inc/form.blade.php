<input type="hidden" name="record_id" id="record_id">
<div class="relative">
    <x-input.dropdown-select
        id="feeFormClass"
        name="class_id"
        placeholder="Select Class"
        :value="old('class_id')"
        :options="[]"
        add-button-id="openClassFromFeeForm"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="feeFormGroup"
        name="group_id"
        placeholder="Select Group"
        :value="old('group_id')"
        :options="[]"
        add-button-id="openGroupFromFeeForm"
        add-button-label="Add group"
        add-button-target="groupModal"
    />
    <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="feeFormSection"
        name="section_id"
        placeholder="Select Section"
        :value="old('section_id')"
        :options="[]"
        add-button-id="openSectionFromFeeForm"
        add-button-label="Add section"
        add-button-target="sectionModal"
    />
    <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="feeFormSession"
        name="session_id"
        placeholder="Select Session"
        :value="old('session_id')"
        :options="[]"
        add-button-id="openSessionFromFeeForm"
        add-button-label="Add session"
        add-button-target="sessionModal"
    />
    <div id="session_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative" id="fee_type_wrapper">
    <x-input.dropdown-select
        id="fee_type_name"
        name="fee_type_name"
        placeholder="Select Fee Type"
        :value="old('fee_type_name')"
        :options="[
            'Admission' => 'Admission',
            'Promote' => 'Promote',
            'Tuition' => 'Tuition',
            'Food' => 'Food',
            'Session' => 'Session',
            'Exams' => 'Exam',
        ]"
    />
    <div id="fee_type_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div id="div_fee_name" class="relative hidden-field">
    <div class="grid grid-cols-2 gap-3">
        <div id="fee_name_wrapper" class="relative col-span-2">
            <x-input.control id="fee_name_input" name="fee_name" placeholder=" " class="peer placeholder:text-transparent" />
            <x-input.floating-label for="fee_name_input">Fee Name</x-input.floating-label>
            <div id="fee_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
</div>

<div id="div_exam_name" class="relative hidden-field">
    <x-input.dropdown-select
        id="exam_id"
        name="exam_id"
        placeholder="Select Exam"
        :value="old('exam_id')"
        :options="[]"
    />
</div>
<div id="div_amount" class="relative hidden-field">
    <x-input.control id="amount" name="amount" type="number" placeholder=" " class="peer placeholder:text-transparent" />
    <x-input.floating-label id="amount_label" for="amount" :floating="false">Amount</x-input.floating-label>
    <div id="amount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div id="div_pay_date" class="relative hidden-field">
    <x-input.control id="pay_date" name="pay_date" type="date" placeholder=" " class="peer" />
    <x-input.floating-label id="pay_date_label" for="pay_date" :floating="false">Due Date</x-input.floating-label>
    <div id="pay_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div id="div_frequency" class="relative hidden">
    <x-input.dropdown-select
        id="frequency"
        name="frequency"
        placeholder="Select Frequency"
        :value="old('frequency')"
        :options="[
            'one_time' => 'One Time',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'per_exam' => 'Per Exam',
            'event_triggered' => 'Event Triggered',
        ]"
    />
</div>
<div id="div_due_day" class="relative hidden-field">
    <x-input.dropdown-select
        id="due_day"
        name="due_day"
        placeholder="Select Day"
        :value="old('due_day')"
        :options="[]"
    />
</div>
<div id="div_food_type" class="relative hidden-field">
    <x-input.dropdown-select
        id="food_type"
        name="food_type"
        placeholder="Select Student"
        :value="old('food_type')"
        :options="[
            'all' => 'All Students',
            'multiple' => 'Multiple Students',
            'single' => 'Single Student',
        ]"
    />
</div>
<div id="div_food_students" class="relative hidden-field sm:col-span-2" style="display:none;">
    <x-fee.student-selector
        id="food_student_list"
        label="Select Students"
        count-id="food_student_count"
    />
</div>
