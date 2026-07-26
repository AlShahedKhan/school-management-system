<x-modal.form
    id="feeModal"
    form-id="feeForm"
    title="Fee Template"
    close-button-id="closeFeeModal"
    title-class="fee-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    submit-label="Save"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius:4px; max-height:min(360px, calc(100dvh - 2.5rem));"
>
    @include('school.fees.fees_type.partials.inc.form')

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" id="closeFeeModal" class="w-full">Cancel</x-button.secondary>
            <x-button.primary type="submit" class="w-full">Save</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
