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
    <div class="relative">
        <x-input.dropdown-select
            id="discountStudent"
            name="student_id"
            placeholder="Select Student"
        />
        <div id="student_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="discountFeeType"
                name="fee_type_id"
                placeholder="Select Fee Type"
            />
            <div id="fee_type_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control id="discountFeeName" name="fee_name" readonly placeholder=" " class="peer placeholder:text-transparent" />
            <x-input.floating-label for="discountFeeName">Fee Name</x-input.floating-label>
            <div id="fee_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
    <div class="relative">
        <x-input.control id="beforeDiscount" name="before_discount" type="number" readonly placeholder=" " class="peer placeholder:text-transparent" />
        <x-input.floating-label for="beforeDiscount">Before Discount</x-input.floating-label>
        <div id="before_discount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-3 border border-slate-200">
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
        <div class="relative">
            <x-input.control id="afterDiscount" name="after_discount" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-green-50 text-green-700" />
            <x-input.floating-label for="afterDiscount">After Discount</x-input.floating-label>
            <div id="after_discount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.control id="discountStartDate" name="start_date" type="date" placeholder=" " class="peer" />
            <x-input.floating-label for="discountStartDate" :floating="false">Start Date</x-input.floating-label>
            <div id="start_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
        <div class="relative">
            <x-input.control id="discountEndDate" name="end_date" type="date" placeholder=" " class="peer" />
            <x-input.floating-label for="discountEndDate" :floating="false">End Date</x-input.floating-label>
            <div id="end_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
        </div>
    </div>
</div>
