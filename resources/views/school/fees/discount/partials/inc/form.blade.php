<div class="space-y-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="discountClass"
                name="class_id"
                placeholder="Select Class"
                add-button-id="openClassFromDiscount"
                add-button-label="Add class"
                add-button-target="classModal"
            />
            <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.dropdown-select
                id="discountGroup"
                name="group_id"
                placeholder="Select Group"
                add-button-id="openGroupFromDiscount"
                add-button-label="Add group"
                add-button-target="groupModal"
            />
            <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="discountSection"
                name="section_id"
                placeholder="Select Section"
                add-button-id="openSectionFromDiscount"
                add-button-label="Add section"
                add-button-target="sectionModal"
            />
            <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.dropdown-select
                id="discountSession"
                name="session_id"
                placeholder="Select Session"
                add-button-id="openSessionFromDiscount"
                add-button-label="Add session"
                add-button-target="sessionModal"
            />
            <div id="session_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="discountStudentScope"
                name="student_scope"
                placeholder="Select Student Option"
                :options="[
                    'all' => 'All Students',
                    'multiple' => 'Multiple Students',
                    'single' => 'Single Student',
                ]"
            />
            <div id="student_scope_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative"></div>
    </div>
    <div id="div_discount_students" class="relative" style="display:none;">
        <x-fee.student-selector
            id="discount_student_list"
            label="Select Students"
            count-id="discount_student_count"
        />
    </div>
    <div id="student_ids_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    <input type="hidden" id="discount_student_ids" name="student_ids" value="">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="discountScope"
                name="discount_scope"
                placeholder="Select Discount Scope"
                :options="[
                    'session' => 'Session',
                    'exam' => 'Exam',
                ]"
            />
            <div id="discount_scope_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div id="div_discount_fee_type" class="relative">
            <x-input.dropdown-select
                id="discountFeeType"
                name="fee_type_id"
                placeholder="Select Fee Type"
            />
            <div id="fee_type_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div id="div_discount_exam_grade" class="relative" style="display:none;">
            <x-input.dropdown-select
                id="discountMinGrade"
                name="minimum_grade"
                placeholder="Select Minimum Qualifying Grade"
            />
            <div class="mt-1 text-[10px] text-slate-400">Students achieving this grade or any higher grade will receive the discount.</div>
            <div id="minimum_grade_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3 border border-slate-200">
        <div id="div_before_discount" class="relative">
            <x-input.control id="beforeDiscount" name="before_discount" type="number" readonly placeholder=" " class="peer placeholder:text-transparent" />
            <x-input.floating-label for="beforeDiscount">Before Discount</x-input.floating-label>
            <div id="before_discount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.dropdown-select
                id="discountType"
                name="discount_type"
                placeholder="Discount Type"
            />
            <div id="discount_type_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control id="discountValue" name="discount_value" type="number" step="any" placeholder=" " class="peer placeholder:text-transparent" />
            <x-input.floating-label for="discountValue">Discount Value</x-input.floating-label>
            <div id="discount_value_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div id="div_after_discount" class="relative">
            <x-input.control id="afterDiscount" name="after_discount" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-green-50 text-green-700" />
            <x-input.floating-label for="afterDiscount">After Discount</x-input.floating-label>
            <div id="after_discount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
</div>
