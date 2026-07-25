<div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-2" style="border-radius:0;">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h3 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Teacher Registration</h3>
            <div class="flex items-center text-slate-400 text-[12px] mt-1">
                <span>School</span>
                <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                <span id="pageTitle" class="text-slate-500">Teacher Registration</span>
            </div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        @include('school.teacher.partials.search-desktop')
        @include('school.teacher.partials.export')
    </div>
    @include('school.teacher.partials.search-mobile')
</div>
@include('school.partials.export-dropdown')
