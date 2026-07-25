<x-modal.form
    id="routineModal"
    form-id="routineForm"
    title="Add Routine"
    close-button-id="closeRoutineModal"
    title-class="routine-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="grid grid-cols-1"
>
    @include('school.academic.routine.partials.inc.form')
    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="cancelRoutineBtn" class="w-full">Close</x-button.secondary>
            <x-button.primary id="saveRoutineBtn" type="submit" class="w-full">Save</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
