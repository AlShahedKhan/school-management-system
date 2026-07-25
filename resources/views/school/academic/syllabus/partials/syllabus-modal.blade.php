<x-modal.form
    id="syllabusModal"
    form-id="syllabusForm"
    title="Add Syllabus"
    close-button-id="closeSyllabusModal"
    title-class="syllabus-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="grid grid-cols-1"
>
    @include('school.academic.syllabus.partials.inc.form')
    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="cancelSyllabusBtn" class="w-full">Close</x-button.secondary>
            <x-button.primary id="saveSyllabusBtn" type="submit" class="w-full">Save</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
