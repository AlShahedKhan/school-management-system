<input type="hidden" name="record_id" id="record_id">
<div class="relative">
    <x-input.dropdown-select
        id="sessionFormClass"
        name="class_id"
        placeholder="Select Class"
        :value="old('class_id')"
        :options="[]"
        add-button-id="openClassFromSessionForm"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="sessionFormGroup"
        name="group_id"
        placeholder="Select Group"
        :value="old('group_id')"
        :options="[]"
        add-button-id="openGroupFromSessionForm"
        add-button-label="Add group"
        add-button-target="groupModal"
    />
    <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="sessionFormSection"
        name="section_id"
        placeholder="Select Section"
        :value="old('section_id')"
        :options="[]"
        add-button-id="openSectionFromSessionForm"
        add-button-label="Add section"
        add-button-target="sectionModal"
    />
    <div id="section_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control type="date" id="start_date" class="peer placeholder:text-transparent" name="start_date" placeholder=" " onchange="calculateSession()" />
    <x-input.floating-label for="start_date">Start Date</x-input.floating-label>
    <div id="start_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control type="date" id="end_date" class="peer placeholder:text-transparent" name="end_date" placeholder=" " onchange="calculateSession()" />
    <x-input.floating-label for="end_date">End Date</x-input.floating-label>
    <div id="end_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="session_year" class="peer placeholder:text-transparent bg-gray-100/50" name="session_year" placeholder=" " readonly />
    <x-input.floating-label for="session_year">Session Year</x-input.floating-label>
    <div id="session_year_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="total_days" class="peer placeholder:text-transparent bg-gray-100/50" name="total_days" placeholder=" " readonly />
    <x-input.floating-label for="total_days">Total Days</x-input.floating-label>
    <div id="total_days_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="remaining_days" class="peer placeholder:text-transparent bg-gray-100/50" name="remaining_days" placeholder=" " readonly />
    <x-input.floating-label for="remaining_days">Remaining Days</x-input.floating-label>
    <div id="remaining_days_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
