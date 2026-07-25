<x-modal.form
    id="sessionModal"
    form-id="sessionForm"
    title="Add Session"
    close-button-id="closeSessionModal"
    title-class="session-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    submit-label="Save"
>
    @include('school.academic.session.partials.inc.form')
</x-modal.form>
