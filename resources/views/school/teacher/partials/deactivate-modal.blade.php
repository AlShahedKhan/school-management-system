<x-modal.form
    id="deactivateTeacherModal"
    form-id="deactivateTeacherForm"
    title="Deactivate Teacher & Add Replacement"
    close-button-id="closeDeactivateTeacherModal"
    title-class="teacher-deactivate-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    <!-- Hidden input for the old teacher being deactivated -->
    <input type="hidden" name="old_teacher_id" id="deactivate_old_teacher_id">

    <!-- Information about the deactivated teacher -->
    <div class="text-xs text-slate-600 bg-slate-50 p-3 border border-slate-100 space-y-1 w-full mb-4 md:col-span-2 col-span-1">
        <p><strong>Deactivating Teacher:</strong> <span id="deactivate_teacher_name" class="font-semibold text-slate-800"></span></p>
        <p><strong>Designation:</strong> <span id="deactivate_teacher_designation"></span></p>
        <p><strong>ID Number:</strong> <span id="deactivate_teacher_id_number" class="font-mono"></span></p>
    </div>

    <div class="relative">
        <x-input.control id="replacementName" class="peer placeholder:text-transparent" name="name" placeholder=" " required />
        <x-input.floating-label for="replacementName">
            Full name
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control id="replacementDesignation" class="peer placeholder:text-transparent" name="designation" placeholder=" " />
        <x-input.floating-label for="replacementDesignation">
            Designation
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control
            id="replacementMobile"
            class="peer placeholder:text-transparent"
            name="mobile"
            required
            placeholder=" "
            inputmode="numeric"
            pattern="[0-9]+"
            title="Must provide numbers only." />
        <x-input.floating-label for="replacementMobile">
            Mobile
        </x-input.floating-label>
        <p id="replacementMobileError" class="mt-1 hidden text-[10px] text-red-500">
            Must provide numbers only.
        </p>
    </div>

    <div class="relative">
        <x-input.control id="replacementEmail" class="peer placeholder:text-transparent" type="email" name="email" placeholder=" " required />
        <x-input.floating-label for="replacementEmail">
            Email address
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.photo id="replacementPhotoInput" name="photo" previewId="replacementImagePreview" />
        <x-input.floating-label for="replacementPhotoInput" :floating="false" class="pointer-events-auto peer-focus:text-slate-500">
            Profile photo
        </x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="closeDeactivateTeacherModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>

            <x-button.primary id="saveDeactivateTeacher" type="submit" class="w-full">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
