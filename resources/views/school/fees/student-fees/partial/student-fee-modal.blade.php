<x-modal.form id="feeModal" form-id="feeForm" title="Edit Student Fee" close-button-id="closeFeeModal"
    title-class="m-0 text-center font-semibold leading-tight text-slate-800" method="PUT"
    class="px-0 sm:px-0"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-md overflow-hidden border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    body-class="bg-white px-6 pb-0 pt-2" fields-class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <input type="hidden" id="fee_id">

    <div class="relative">
        <x-input.control id="amount" type="number" name="amount" placeholder=" "
            class="peer placeholder:text-transparent" required />
        <x-input.floating-label for="amount">Amount</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control id="pay_date" type="date" name="pay_date" placeholder=" "
            class="peer" required />
        <x-input.floating-label for="pay_date" :floating="false">Pay Date</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control id="fee_name_input" name="fee_name" placeholder=" "
            class="peer placeholder:text-transparent" />
        <x-input.floating-label for="fee_name_input">Fee Name</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="status_input" name="status" required>
            @foreach (config('feestatus') as $key => $cfg)
                <option value="{{ $key }}">{{ $cfg['label'] }}</option>
            @endforeach
        </x-input.select>
        <x-input.floating-label for="status_input" :floating="false">Status</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" id="closeFeeModal" onclick="closeFeeModal()" class="w-full">
                Cancel
            </x-button.secondary>
            <x-button.primary type="submit" id="saveBtn" class="w-full">
                Update
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
