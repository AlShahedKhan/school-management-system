<input type="hidden" name="group_id" id="group_id">
<div class="relative">
    <x-input.dropdown-select
        id="groupClassSelect"
        name="class_id"
        placeholder="Select Class"
        :value="old('class_id')"
        :options="[]"
        add-button-id="openClassFromGroupForm"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="group_name" class="peer placeholder:text-transparent" name="group_name" placeholder=" " />
    <x-input.floating-label for="group_name">Group Name</x-input.floating-label>
    <div id="group_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
