<input type="hidden" id="fee_id" name="fee_id">
<div class="relative">
    <x-input.control id="amount" type="number" name="amount" placeholder=" " class="peer placeholder:text-transparent" required />
    <x-input.floating-label for="amount">Amount</x-input.floating-label>
    <div id="amount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="pay_date" type="date" name="pay_date" placeholder=" " class="peer" required />
    <x-input.floating-label for="pay_date" :floating="false">Pay Date</x-input.floating-label>
    <div id="pay_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="fee_name_input" name="fee_name" placeholder=" " class="peer placeholder:text-transparent" />
    <x-input.floating-label for="fee_name_input">Fee Name</x-input.floating-label>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="status_input"
        name="status"
        placeholder="Select Status"
        :value="old('status')"
        :options="collect(config('feestatus'))->mapWithKeys(fn($cfg, $key) => [$key => $cfg['label']])->toArray()"
    />
    <div id="status_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
