<x-modal.form id="feeTemplateModal" form-id="feeTemplateForm" title="Create Admission Fee" close-button-id="closeFeeTemplateModal" title-class="text-center font-semibold text-slate-800 text-base">
<div class="relative">
<x-input.control id="feeNameInput" class="peer placeholder:text-transparent" name="fee_name" value="Admission Fee" required placeholder=" " />
<x-input.floating-label for="feeNameInput">Fee Name</x-input.floating-label>
</div>
<div class="relative">
<x-input.control id="feeAmountInput" class="peer placeholder:text-transparent" type="number" name="amount" required placeholder=" " min="0" step="any" />
<x-input.floating-label for="feeAmountInput">Amount</x-input.floating-label>
</div>
<div class="relative">
<x-input.control id="feePayDateInput" class="peer" type="date" name="pay_date" required />
<x-input.floating-label for="feePayDateInput" :floating="false">Pay Date</x-input.floating-label>
</div>
</x-modal.form>
