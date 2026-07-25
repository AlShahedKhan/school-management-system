<x-modal.form
    id="examModal"
    form-id="examForm"
    title="Add Exam"
    close-button-id="closeExamModal"
    title-class="exam-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    submit-label="Save"
>
    @include('school.exam.exam_name.partials.inc.form')
</x-modal.form>
