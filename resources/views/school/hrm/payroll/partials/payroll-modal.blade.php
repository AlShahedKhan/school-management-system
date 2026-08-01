<x-modal.form
    id="payrollModal"
    form-id="payrollForm"
    title="Pay Salary / Payroll"
    close-button-id="closePayrollModal"
    action="{{ url('/api/payrolls') }}"
    method="POST"
    class="payroll-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius: 4px; max-height: min(550px, calc(100dvh - 2.5rem));"
    title-class="payroll-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    @include('school.hrm.payroll.partials.inc.form')

    <x-slot:footer>
        <div id="payrollModalFooter" class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4">
            <x-button.secondary id="closePayrollModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>
            <x-button.primary id="submitPayrollBtn" type="submit" class="w-full">
                Pay Salary
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>