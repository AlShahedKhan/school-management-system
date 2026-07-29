<x-modal.form
    id="paymentModal"
    form-id="paymentForm"
    title="Due Payment"
    action="#"
    method="POST"
    class="due-pay-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    fields-class="grid grid-cols-1 gap-3"
>
    <input type="hidden" id="modalPaymentId">

    @include('school.fees.due-collection.partials.inc.form')

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" onclick="closeModal()" class="w-full">Cancel</x-button.secondary>
            <x-button.primary type="button" id="saveBtn" onclick="submitPayment()" class="w-full">
                <svg id="saveBtnSpinner" class="hidden animate-spin h-3 w-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="saveBtnText">Confirm</span>
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
