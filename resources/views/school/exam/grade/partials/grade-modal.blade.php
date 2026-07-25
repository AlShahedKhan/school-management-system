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
        <div class="flex flex-row justify-end gap-2 px-6 py-3 border-t border-slate-200 bg-white">
            <x-button.secondary id="closeGradeModal" class="w-1/2 sm:w-auto sm:px-8">
                Cancel
            </x-button.secondary>
            <x-button.secondary id="backBtn" onclick="toggleStep(1)"
                class="hidden w-1/2 sm:w-auto sm:px-8">
                Back
            </x-button.secondary>
            <x-button.secondary id="nextBtn" onclick="validateStep1()"
                class="w-1/2 sm:w-auto sm:px-12">
                Next
            </x-button.secondary>
            <x-button.primary type="submit" id="saveBtn"
                class="hidden w-1/2 sm:w-auto sm:px-12">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
