<x-modal.form
    id="slipModal"
    form-id="slipForm"
    title="Payment Slip"
    close-button-id="closeSlipFooter"
    action="#"
    method="GET"
    :enctype="null"
    class="slip-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[400px]"
>
    <x-input.dropdown-select id="slipClassFilter" placeholder="Select Class"
        add-button-id="openClassFromSlip"
        add-button-label="Add class"
        add-button-target="classModal" />
    <x-input.dropdown-select id="slipGroupFilter" placeholder="Select Group"
        add-button-id="openGroupFromSlip"
        add-button-label="Add group"
        add-button-target="groupModal" />
    <x-input.dropdown-select id="slipSectionFilter" placeholder="Select Section"
        add-button-id="openSectionFromSlip"
        add-button-label="Add section"
        add-button-target="sectionModal" />
    <x-input.dropdown-select id="slipSessionFilter" placeholder="Select Session"
        add-button-id="openSessionFromSlip"
        add-button-label="Add session"
        add-button-target="sessionModal" />

    <div class="relative">
        <x-input.control id="slipFromDate" type="date" placeholder=" " class="peer placeholder:text-transparent" />
        <x-input.floating-label for="slipFromDate" :floating="false" class="text-[10px]">From Date</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="slipToDate" type="date" placeholder=" " class="peer placeholder:text-transparent" />
        <x-input.floating-label for="slipToDate" :floating="false" class="text-[10px]">To Date</x-input.floating-label>
    </div>

    <x-input.dropdown-select id="slipStudentFilter" placeholder="— Select Student —" required />
    <p id="slipStudentError" class="hidden text-[9px] text-red-500">Please select a student.</p>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="closeSlipFooter" type="button" class="w-full">Cancel</x-button.secondary>
            <x-button.primary id="slipShowPage" type="button" class="w-full">Show Slip</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
