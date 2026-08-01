    {{-- Cascade Select Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
        <div class="relative">
            <x-input.dropdown-select id="feeClassFilter" placeholder="Select Class"
                add-button-id="openClassFromCollect"
                add-button-label="Add class"
                add-button-target="classModal" />
        </div>
        <div class="relative">
            <x-input.dropdown-select id="feeGroupFilter" placeholder="Select Group"
                add-button-id="openGroupFromCollect"
                add-button-label="Add group"
                add-button-target="groupModal" />
        </div>
        <div class="relative">
            <x-input.dropdown-select id="feeSectionFilter" placeholder="Select Section"
                add-button-id="openSectionFromCollect"
                add-button-label="Add section"
                add-button-target="sectionModal" />
        </div>
        <div class="relative">
            <x-input.dropdown-select id="feeSessionFilter" placeholder="Select Session"
                add-button-id="openSessionFromCollect"
                add-button-label="Add session"
                add-button-target="sessionModal" />
        </div>
        <div class="relative sm:col-span-2 lg:col-span-1">
            <x-input.dropdown-select id="feeStudentFilter" placeholder="Select Student" required />
            <p id="feeStudentError" class="hidden text-[9px] text-red-500 mt-0.5">Please select a student.</p>
        </div>
    </div>

    {{-- Student Details --}}
    <div id="feeStudentInfo" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 p-3 bg-white border border-gray-200">
        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Name</span><p id="feeDispName" class="text-xs font-medium text-blue-600 truncate">-</p></div>
        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Class</span><p id="feeDispClass" class="text-xs">-</p></div>
        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Group</span><p id="feeDispGroup" class="text-xs">-</p></div>
        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Section</span><p id="feeDispSection" class="text-xs">-</p></div>
    </div>

    {{-- Month Grid --}}
    <div id="feeMonthGridWrap" class="hidden mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[9px] text-gray-400 uppercase tracking-widest">Session Months</span>
            <span id="feeClearanceStatus" class="text-[9px] font-medium hidden"></span>
        </div>
        <div id="feeMonthGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-1.5"></div>
    </div>

    {{-- Payment Information --}}
    <div id="feePaymentSection" class="hidden">
        <div class="border-b border-gray-200/60 pb-1 mb-3">
            <span class="text-gray-400 text-[9px] tracking-widest">Payment Information</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
            <div class="col-span-1">
                <x-input.dropdown-select id="fees_type" placeholder="Select Fees Type" required />
            </div>
            <div class="col-span-1">
                <x-input.dropdown-select id="fee_name" placeholder="Select Fee Name" required />
            </div>
            <div class="col-span-1">
                <div class="relative">
                    <x-input.control id="total_payable" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-gray-100 cursor-not-allowed" />
                    <x-input.floating-label for="total_payable" class="text-[10px]">Total Payable</x-input.floating-label>
                </div>
            </div>
            <div class="col-span-1 sm:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white p-4 border border-gray-100">
                    <div class="relative">
                        <x-input.control id="type_amount" type="number" step="any" placeholder=" " required class="peer placeholder:text-transparent" />
                        <x-input.floating-label for="type_amount" class="text-[10px] text-green-600">Paid Amount (৳)</x-input.floating-label>
                    </div>
                    <div class="relative">
                        <x-input.control id="payable_due" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-red-50/10 cursor-not-allowed" />
                        <x-input.floating-label for="payable_due" class="text-[10px] text-red-600">Remaining Due</x-input.floating-label>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <x-input.dropdown-select id="pay_method" placeholder="Payment Method" />
            </div>
            <div class="col-span-1">
                <div class="relative">
                    <x-input.control id="pay_date" type="date" placeholder=" " required class="peer placeholder:text-transparent" value="{{ date('Y-m-d') }}" />
                    <x-input.floating-label for="pay_date" :floating="false" class="text-[10px]">Payment Date</x-input.floating-label>
                </div>
            </div>
        </div>
    </div>
