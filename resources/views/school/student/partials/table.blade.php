<div class="table-card">
    <div class="table-responsive custom-scrollbar">
        <table>
            <thead>
                <tr>
                    <th class="text-left">Sl</th>
                    <th class="text-left">Photo</th>
                    <th class="text-left">Id Number</th>
                    <th class="text-left">Student Name</th>
                    <th class="text-left">Mobile Number</th>
                    <th class="text-left">Father Name</th>
                    <th class="text-left">Class</th>
                    <th class="text-left">Group</th>
                    <th class="text-left">Section</th>
                    <th class="text-left">Session</th>
                    <th class="text-left">Student Type</th>
                    <th class="text-left">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody id="studentTableBody"></tbody>
        </table>
    </div>
    <div class="pagination-container">
        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
            0 of 0
        </div>
        <div class="flex items-center gap-1" id="paginationControls"></div>
    </div>
</div>

@include('school.partials.student-details-modal')
@include('school.partials.student-view-modal')
@include('school.partials.fee-template-modal')
@include('school.partials.student-edit-modal')
@include('school.partials.quick-add-modals')
@include('school.partials.student-deactivate-modal')
@include('school.partials.student-activate-modal')
