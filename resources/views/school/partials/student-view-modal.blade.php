    @php
        $school = null;
        $user = Auth::user();
        if ($user) {
            if ($user->role === 'teacher') {
                $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
                if ($teacher) {
                    $school = \App\Models\School::find($teacher->school_id);
                }
            } else {
                $school = \App\Models\School::where('user_id', $user->id)->first();
            }
        }
    @endphp
    <x-modal.form
        id="admissionFormModal"
        formId="admissionViewForm"
        title=""
        closeButtonId="closeAdmissionFormBtn"
        :action="null"
        panelClass="custom-scrollbar mx-auto my-auto overflow-y-auto overflow-x-hidden border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] max-w-full"
        panelStyle="border-radius: 4px; max-height: min(95vh, calc(100dvh - 1rem)); max-width: 650px; width: calc(100vw - 1rem);"
        bodyClass="bg-white p-0 overflow-x-hidden"
        fieldsClass=""
    >
            {{-- Printable Form Area --}}
            @include('school.partials.admission-form-view')
            <x-slot:footer>
                <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3 no-print">
                    <x-button.secondary id="closeAdmissionFormBtn" onclick="closeAdmissionFormModal()" class="w-full">
                        Cancel
                    </x-button.secondary>
                    <x-button.primary onclick="printAdmissionForm()" class="w-full">
                        <i class="fa-solid fa-print"></i> Print
                    </x-button.primary>
                </div>
            </x-slot:footer>
    </x-modal.form>
