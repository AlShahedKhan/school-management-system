<x-modal.form id="filterModal" form-id="feeFilterForm" title="Student Fees Filter" close-button-id="resetFilter"
    title-class="m-0 text-center font-semibold leading-tight text-slate-800" method="GET"
    class="school-fee-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    body-class="bg-white px-6 pb-0 pt-2" fields-class="grid grid-cols-1 gap-3">

    <div class="relative">
        <x-input.select id="classFilter" name="class_id">
            <option value="">All Classes</option>
        </x-input.select>
        <x-input.floating-label for="classFilter" :floating="false">Class</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="sessionFilter" name="session_id">
            <option value="">All Sessions</option>
        </x-input.select>
        <x-input.floating-label for="sessionFilter" :floating="false">Session</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="feeTypeFilter" name="fee_type_name">
            <option value="">All Fee Types</option>
            <option value="Tuition">Tuition</option>
            <option value="Admission">Admission</option>
            <option value="Exams">Exams</option>
            <option value="Food">Food</option>
            <option value="Session">Session</option>
            <option value="Fine">Fine</option>
            <option value="Others">Others</option>
        </x-input.select>
        <x-input.floating-label for="feeTypeFilter" :floating="false">Fee Type</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.select id="statusFilter" name="status">
            <option value="">All Status</option>
            @foreach (config('feestatus') as $key => $cfg)
                <option value="{{ $key }}">{{ $cfg['label'] }}</option>
            @endforeach
        </x-input.select>
        <x-input.floating-label for="statusFilter" :floating="false">Status</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary type="button" id="resetFilter" class="w-full">
                Reset
            </x-button.secondary>
            <x-button.primary type="button" id="applyFilter" class="w-full">
                Apply
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
