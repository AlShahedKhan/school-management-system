@props(['showGradeAddButton' => false])

<x-modal.form
    id="subjectModal"
    form-id="subjectForm"
    title="Add Subject"
    close-button-id="closeSubjectModal"
    title-class="subject-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="grid grid-cols-1"
>
    @include('school.academic.subject.partials.inc.form', ['showGradeAddButton' => $showGradeAddButton])
    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="cancelSubjectBtn" class="w-full">Close</x-button.secondary>
            <x-button.primary id="saveSubjectBtn" type="submit" class="w-full">Save</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
