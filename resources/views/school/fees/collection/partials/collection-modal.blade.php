<x-modal.form
    id="paymentModal"
    form-id="paymentForm"
    title="Collect Fee"
    close-button-id="closePaymentModal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    panel-style="border-radius:4px; max-height:min(90vh, calc(100dvh - 2.5rem));"
    fields-class="w-full"
>
    <span id="modalTitle" class="hidden">Collect Fee</span>

    @include('school.fees.collection.partials.inc.form')

    <input type="hidden" id="admission_student_id">
    <input type="hidden" id="for_month" value="">
    <input type="hidden" id="is_advance" value="0">

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" onclick="closeModal()" class="w-full">Cancel</x-button.secondary>
            <x-button.primary type="submit" id="saveBtn" class="w-full">
                <svg id="saveBtnSpinner" class="hidden animate-spin h-3 w-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="saveBtnText">Save</span>
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
