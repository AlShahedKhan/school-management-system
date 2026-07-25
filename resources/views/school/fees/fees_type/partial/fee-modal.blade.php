<x-modal.form id="feeModal" form-id="feeForm" title="Fee template" close-button-id="closeFeeModal"
    title-class="fee-modal-title m-0 text-center font-semibold leading-tight text-slate-800" method="POST"
    class="px-0 sm:px-0"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-4xl overflow-hidden border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    body-class="bg-white px-6 pb-0 pt-2" fields-class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <input type="hidden" id="fee_id" name="fee_id">

    <div class="relative">
        <x-input.select id="class_id" name="class_id" onchange="loadGroups()" required>
            <option value="">Select Class</option>
        </x-input.select>
        <x-input.floating-label for="class_id" :floating="false">Class</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="group_id" name="group_id" onchange="loadSections()">
            <option value="">No Group</option>
        </x-input.select>
        <x-input.floating-label for="group_id" :floating="false">Group</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="section_id" name="section_id">
            <option value="">No Section</option>
        </x-input.select>
        <x-input.floating-label for="section_id" :floating="false">Section</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="session_id" name="session_id" required>
            <option value="">Select Session</option>
        </x-input.select>
        <x-input.floating-label for="session_id" :floating="false">Session</x-input.floating-label>
    </div>

    <div class="relative sm:col-span-2">
        <x-input.select id="fee_type_name" name="fee_type_name" required onchange="handleFeeTypeChange()">
            <option value="">Select Fee Type</option>
            <option value="Admission">Admission</option>
            <option value="Promote">Promote</option>
            <option value="Tuition">Tuition</option>
            <option value="Food">Food</option>
            <option value="Fine">Fine</option>
            <option value="Session">Session</option>
            <option value="Exams">Exam</option>
        </x-input.select>
        <x-input.floating-label for="fee_type_name" :floating="false">Fee Type</x-input.floating-label>
    </div>

    <div id="div_fee_name" class="relative hidden-field">
        <div id="fee_name_wrapper" class="relative">
            <x-input.control id="fee_name_input" name="fee_name" placeholder=" "
                class="peer placeholder:text-transparent" />
            <x-input.floating-label for="fee_name_input">Fee Name</x-input.floating-label>
        </div>

        <div id="fine_fee_wrapper" class="relative hidden" style="margin-top: 15px">
            <x-input.select id="fine_fee_name_select" name="fine_fee_name" class="hidden" placeholder=" ">
                <option value="">Select Fine Type</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
                <option value="payment-due">Payment Due</option>
                <option value="payment-overdue">Payment Overdue</option>
                <option value="exam-fee-due">Exam Fee Due</option>
            </x-input.select>
            <x-input.floating-label for="fine_fee_name_select" :floating="false">Fine Type</x-input.floating-label>
        </div>
    </div>

    <div id="div_exam_name" class="relative hidden-field">
        <x-input.select id="exam_id" name="exam_id">
            <option value="">Select Exam</option>
        </x-input.select>
        <x-input.floating-label for="exam_id" :floating="false">Exam Name</x-input.floating-label>
    </div>

    <div id="div_amount" class="relative hidden-field">
        <x-input.control id="amount" name="amount" type="number" placeholder=" "
            class="peer placeholder:text-transparent" />
        <x-input.floating-label id="amount_label" for="amount" :floating="false">Amount</x-input.floating-label>
    </div>

    <div id="div_pay_date" class="relative hidden-field">
        <x-input.control id="pay_date" name="pay_date" type="date" placeholder=" " class="peer" />
        <x-input.floating-label id="pay_date_label" for="pay_date" :floating="false">Pay Date</x-input.floating-label>
    </div>

    <div id="div_frequency" class="relative hidden">
        <x-input.select id="frequency" name="frequency">
            <option value="one_time">One Time</option>
            <option value="monthly">Monthly</option>
            <option value="per_exam">Per Exam</option>
            <option value="event_triggered">Event Triggered</option>
        </x-input.select>
        <x-input.floating-label for="frequency" :floating="false">Frequency</x-input.floating-label>
    </div>

    <div id="div_due_day" class="relative hidden-field">
        <x-input.select id="due_day" name="due_day">
            <option value="">Select Day</option>
        </x-input.select>
        <x-input.floating-label id="due_day_label" for="due_day" :floating="false">Due Day</x-input.floating-label>
    </div>

    <div id="div_food_type" class="relative hidden-field">
        <x-input.select id="food_type" name="food_type" onchange="handleFoodTypeChange()">
            <option value="">Select Type</option>
            <option value="single">Single</option>
            <option value="multiple">Multiple</option>
            <option value="all">All</option>
        </x-input.select>
        <x-input.floating-label for="food_type" :floating="false">Student Type</x-input.floating-label>
    </div>

    <div id="div_food_students" class="relative hidden-field sm:col-span-2">
        <x-input.select id="food_student_ids" name="student_ids[]" multiple class="h-[160px]" />
        <x-input.floating-label for="food_student_ids" :floating="false">Select Students</x-input.floating-label>
        <div id="food_student_count" class="text-[10px] text-gray-400 mt-1 hidden"></div>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" id="closeFeeModal" onclick="closeFeeModal()" class="w-full">
                Cancel
            </x-button.secondary>
            <x-button.primary type="submit" id="saveBtn" class="w-full">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>