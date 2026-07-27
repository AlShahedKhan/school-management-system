<input type="hidden" name="record_id" id="record_id">
<div class="relative">
    <x-input.control id="amount" name="amount" type="number" placeholder=" " class="peer placeholder:text-transparent" />
    <x-input.floating-label id="amount_label" for="amount" :floating="false">Amount (Base Amount)</x-input.floating-label>
    <div id="amount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="pay_date" name="pay_date" type="date" placeholder=" " class="peer" />
    <x-input.floating-label id="pay_date_label" for="pay_date" :floating="false">Pay Date</x-input.floating-label>
    <div id="pay_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="fee_name_input" name="fee_name" placeholder=" " class="peer placeholder:text-transparent" />
    <x-input.floating-label for="fee_name_input">Fee Name</x-input.floating-label>
    <div id="fee_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="status_input"
        name="status"
        placeholder="Select Status"
        :value="old('status')"
        :options="[
            'paid' => 'Paid',
            'partial_paid' => 'Partial Paid',
            'due' => 'Due',
            'due_partial' => 'Due Partial',
            'over_due' => 'Over Due',
            'over_due_partial' => 'Over Due Partial',
            'advance' => 'Advance',
            'advance_partial' => 'Advance Partial',
            'pending' => 'Pending',
        ]"
    />
    <div id="status_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
