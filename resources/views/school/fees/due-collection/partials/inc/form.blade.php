<input type="hidden" id="rawDueVal">
<input type="hidden" id="rawOverdueVal">

<div class="grid grid-cols-2 gap-3 col-span-full">
    <x-input.dropdown-select id="modalPayType" placeholder="Pay Type" />
    <div class="relative">
        <x-input.control id="modalPayDate" type="date" placeholder=" " required class="peer placeholder:text-transparent" value="{{ date('Y-m-d') }}" />
        <x-input.floating-label for="modalPayDate" :floating="false" class="text-[10px]">Pay Date</x-input.floating-label>
    </div>
</div>

<div class="grid grid-cols-2 gap-3 col-span-full">
    <div class="relative">
        <x-input.control id="modalTotal" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-gray-100 cursor-not-allowed" />
        <x-input.floating-label for="modalTotal" class="text-[10px]">Total Payable</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.control id="modalPayAmount" type="number" step="any" placeholder=" " required class="peer placeholder:text-transparent" oninput="calculateRemaining()" />
        <x-input.floating-label for="modalPayAmount" class="text-[10px] text-green-600">Paid Amount (৳)</x-input.floating-label>
    </div>
</div>

<div class="grid grid-cols-2 gap-3 col-span-full">
    <div class="relative">
        <x-input.control id="modalRemaining" type="number" readonly placeholder=" " class="peer placeholder:text-transparent bg-red-50/10 cursor-not-allowed" />
        <x-input.floating-label for="modalRemaining" class="text-[10px] text-red-600">Remaining Due</x-input.floating-label>
    </div>
    <x-input.dropdown-select id="modalPayMethod" placeholder="Payment Method" />
</div>
