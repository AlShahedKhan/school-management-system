<input type="hidden" name="section_record_id" id="section_record_id">
<div class="relative">
    <x-input.dropdown-select
        id="sectionClassSelect"
        name="class_id"
        placeholder="Select Class"
        :value="old('class_id')"
        :options="[]"
        add-button-id="openClassFromSectionForm"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <div id="class_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.dropdown-select
        id="sectionGroupSelect"
        name="group_id"
        placeholder="Select Group"
        :value="old('group_id')"
        :options="[]"
        add-button-id="openGroupFromSectionForm"
        add-button-label="Add group"
        add-button-target="groupModal"
    />
    <div id="group_id_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control id="section_name" class="peer placeholder:text-transparent" name="section_name" placeholder=" " />
    <x-input.floating-label for="section_name">Section Name</x-input.floating-label>
    <div id="section_name_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
