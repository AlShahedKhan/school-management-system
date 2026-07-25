<x-modal.form
    id="activateStudentModal"
    form-id="activateStudentForm"
    title="Activate Student"
    close-button-id="closeActivateStudentModal"
    title-class="text-center font-semibold text-slate-800 text-base"
    panel-style="border-radius:4px; max-height:min(350px, calc(100dvh - 2.5rem));"
    fields-class="flex flex-col space-y-4 w-full"
>
    <input type="hidden" id="activate_student_id" name="student_id">
    
    <div class="text-xs text-slate-600 bg-slate-50 p-3 border border-slate-100 space-y-1 w-full">
        <p><strong>Student Name:</strong> <span id="activate_student_name"></span></p>
        <p><strong>Student ID:</strong> <span id="activate_student_id_number"></span></p>
        <p><strong>Current Status:</strong> <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700">Inactive</span></p>
        <p><strong>Inactive Since:</strong> <span id="activate_inactive_since"></span></p>
    </div>
    
    <p class="text-xs text-slate-500 text-center w-full block">Are you sure you want to activate this student?</p>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="closeActivateStudentModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>

            <button type="submit" class="flex items-center justify-center whitespace-nowrap rounded-none border-2 border-black bg-black text-white transition-colors hover:bg-slate-800 h-8 px-4 text-[10px] w-full font-bold">
                Confirm
            </button>
        </div>
    </x-slot:footer>
</x-modal.form>
