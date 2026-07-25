<x-modal.form
    id="deleteStudentModal"
    form-id="deleteStudentForm"
    title="Delete Student"
    close-button-id="closeDeleteStudentModal"
    title-class="text-center font-semibold text-slate-800 text-base"
    panel-style="border-radius:4px; max-height:min(300px, calc(100dvh - 2.5rem));"
    fields-class="flex flex-col space-y-3 w-full items-center justify-center"
>
    <input type="hidden" id="delete_student_id" name="student_id">
    
    <p class="text-xs text-slate-600 text-center w-full block">Are you sure you want to delete <span id="delete_student_name" class="font-bold"></span>?</p>
    <p class="text-[11px] text-red-500 font-bold text-center w-full block">This action cannot be undone.</p>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="closeDeleteStudentModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>

            <button type="submit" class="flex items-center justify-center whitespace-nowrap rounded-none border-2 border-black bg-black text-white transition-colors hover:bg-slate-800 h-8 px-4 text-[10px] w-full font-bold">
                CONFIRM DELETE
            </button>
        </div>
    </x-slot:footer>
</x-modal.form>
