<x-modal.form id="bulkUploadModal" form-id="bulkUploadForm" title="Bulk Student Upload" close-button-id="closeBulkUploadModal" title-class="student-bulk-upload-modal-title m-0 text-center font-semibold leading-tight text-slate-800" panel-style="border-radius:4px; max-height:min(550px, calc(100dvh - 2.5rem));" submit-label="Upload">
<div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2 pb-1 border-b border-slate-100">
<div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
<span class="text-xs font-bold text-slate-700">Academic Combination</span>
</div>
<div class="relative">
<x-input.dropdown-select id="bulkClass" name="class_id" placeholder="Select Class" add-button-id="add_bulk_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
<x-input.floating-label for="bulkClass" :floating="false">Class *</x-input.floating-label>
</div>
<div class="relative">
<x-input.dropdown-select id="bulkGroup" name="group_id" placeholder="Select Group" add-button-id="add_bulk_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
<x-input.floating-label for="bulkGroup" :floating="false">Group *</x-input.floating-label>
</div>
<div class="relative">
<x-input.dropdown-select id="bulkSection" name="section_id" placeholder="Select Section" add-button-id="add_bulk_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
<x-input.floating-label for="bulkSection" :floating="false">Section *</x-input.floating-label>
</div>
<div class="relative">
<x-input.dropdown-select id="bulkSession" name="session_id" placeholder="Select Session" add-button-id="add_bulk_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
<x-input.floating-label for="bulkSession" :floating="false">Session *</x-input.floating-label>
</div>
<div class="flex items-start gap-2">
<div class="relative flex-grow">
<x-input.control id="bulkFee" class="peer bg-slate-50" readonly placeholder=" " required />
<x-input.floating-label for="bulkFee">Admission Fee *</x-input.floating-label>
</div>
<button id="btnCreateFeeTemplateBulk" type="button" class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center border border-gray-200 bg-white text-blue-600 text-xs font-semibold transition-colors hover:bg-slate-50 hover:border-gray-300" style="border-radius:0;">+</button>
</div>
<div class="relative">
<x-input.control id="bulkAdmissionDate" name="admission_date" class="peer" type="date" required placeholder=" " />
<x-input.floating-label for="bulkAdmissionDate" :floating="false">Admission Date *</x-input.floating-label>
</div>
<div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-4 mb-2 pb-1 border-b border-slate-100">
<div class="h-3.5 w-1 bg-blue-600 rounded-full"></div>
<span class="text-xs font-bold text-slate-700">Template & Upload</span>
</div>
<div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
<div>
<button id="btnDownloadTemplate" type="button" class="w-full h-[30px] border border-slate-300 bg-white hover:bg-slate-50 text-[10px] font-semibold text-slate-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" style="border-radius:0;" disabled>
<i class="fa-solid fa-download text-blue-600"></i> Download Excel Template
</button>
</div>
<div>
<input type="file" id="bulkFileInput" accept=".xlsx,.xls" required class="form-input-fixed flex h-[30px] w-full items-center border border-slate-300 bg-white text-[10px] text-slate-600 outline-none transition-colors file:mr-4 file:h-full file:border-0 file:border-r file:border-slate-200 file:bg-slate-50 file:px-3 file:text-[10px] file:text-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer" style="border-radius:0;" />
<p id="bulkUploadError" class="text-xs text-red-500 mt-2 hidden"></p>
</div>
</div>
<div id="bulkState_processing" class="hidden col-span-1 md:col-span-2 text-center py-6">
<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
<p class="text-xs text-slate-600 font-medium">Processing student import, please wait...</p>
</div>
<div id="bulkState_result" class="hidden col-span-1 md:col-span-2">
<div class="p-3 mb-3 text-center border" id="bulkResultAlert" style="border-radius:0;">
<i id="bulkResultIcon" class="text-2xl block mb-1"></i>
<p id="bulkResultText" class="text-xs font-medium"></p>
</div>
<div id="bulkErrorSection" class="hidden">
<p class="text-xs font-semibold text-red-600 mb-1">Validation Errors Found:</p>
<div class="overflow-auto max-h-[150px] border border-gray-200" style="border-radius:0;">
<table class="w-full text-xs">
<thead>
<tr class="bg-gray-50 border-b border-gray-200">
<th class="px-2 py-1 text-left border-r border-gray-200 font-semibold whitespace-nowrap">Row</th>
<th class="px-2 py-1 text-left border-r border-gray-200 font-semibold whitespace-nowrap">Field</th>
<th class="px-2 py-1 text-left font-semibold">Error Message</th>
</tr>
</thead>
<tbody id="bulkErrorTableBody"></tbody>
</table>
</div>
</div>
<div class="mt-4 flex gap-3 justify-end">
<button id="btnBulkReset" type="button" class="px-4 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors">Import Another</button>
<a href="{{ route('school.students') }}" class="bg-blue-600 text-white py-1.5 px-4 text-xs font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center">Student List</a>
</div>
</div>
</x-modal.form>
@include('school.partials.fee-template-modal')
