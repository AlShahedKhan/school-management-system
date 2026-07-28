<x-modal.form
    id="gradeModal"
    form-id="gradeForm"
    title="Add Exam Grade"
    close-button-id="closeGradeModal"
    title-class="grade-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    body-class="bg-white px-6 pb-0 pt-2"
    fields-class="space-y-4"
>
    @include('school.exam.grade.partials.inc.form')

    <x-slot:footer>
        <div class="flex w-full min-w-0 flex-row gap-2 border-t border-slate-200 bg-white px-3 py-3 sm:justify-end sm:px-6">
            <x-button.secondary
                id="closeGradeModal"
                class="min-w-0 flex-1 !px-2 sm:flex-none sm:!px-8"
            >
                Cancel
            </x-button.secondary>
            <x-button.secondary
                id="backBtn"
                onclick="toggleStep(1)"
                class="hidden min-w-0 flex-1 !px-2 sm:flex-none sm:!px-8"
            >
                Back
            </x-button.secondary>
            <x-button.secondary
                id="nextBtn"
                onclick="validateStep1()"
                class="min-w-0 flex-1 !px-2 sm:flex-none sm:!px-12"
            >
                Next
            </x-button.secondary>
            <x-button.primary
                type="submit"
                id="saveBtn"
                class="hidden min-w-0 flex-1 !px-2 sm:flex-none sm:!px-12"
            >
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
