@extends('layouts.admin')
@section('title', 'SMS Templates')
@section('page-title', 'SMS Templates')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.template-card{border-radius:0 !important;transition:all .3s;border:1px solid #e5e7eb;background:#ffffff;}
.template-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.05);}
button,select,input,textarea{border-radius:0 !important;}
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple{border:1.5px solid #cbd5e1 !important;border-radius:0 !important;min-height:34px !important;outline:none !important;background-color:#fff !important;transition:border-color 0.2s ease-in-out;}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple{border-color:#2563eb !important;}
.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:30px !important;font-size:12px !important;color:#1e293b !important;padding-left:10px !important;}
.select2-container--default .select2-selection--single .select2-selection__arrow{height:32px !important;right:10px !important;}
.select2-container--default .select2-selection--multiple{position:relative;padding-right:30px !important;}
.select2-container--default .select2-selection--multiple::after{content:"";position:absolute;right:12px;top:50%;transform:translateY(-50%);border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;width:0;pointer-events:none;}
.select2-container--default .select2-selection--multiple .select2-selection__rendered{font-size:12px !important;color:#1e293b !important;padding:0 5px !important;}
.select2-container--default .select2-selection--multiple .select2-selection__choice{background-color:#f1f5f9 !important;border:1px solid #cbd5e1 !important;border-radius:0 !important;margin-top:4px !important;font-size:11px !important;color:#1e293b !important;padding:2px 5px !important;}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{color:#ef4444 !important;margin-right:5px !important;border:none !important;background:transparent !important;}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{background-color:transparent !important;color:#b91c1c !important;}
.select2-dropdown{border:1.5px solid #1e293b !important;border-radius:0 !important;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -4px rgba(0,0,0,0.1) !important;}
.select2-container--default .select2-results__option{font-size:12px !important;padding:6px 10px !important;}
.select2-container--default .select2-results__option--highlighted[aria-selected]{background-color:#2563eb !important;color:#ffffff !important;}
.select2-container--default .select2-results__option[aria-selected=true]{background-color:#f1f5f9 !important;color:#1e293b !important;}
.toast-success{background:#10b981 !important;border-radius:10px !important;box-shadow:0 4px 12px rgba(16,185,129,0.3) !important;}
.toast-error{background:#ef4444 !important;border-radius:10px !important;box-shadow:0 4px 12px rgba(239,68,68,0.3) !important;}
</style>
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
<div class="bg-white shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h2 class="text-xl font-bold text-gray-800">SMS Templates</h2>
<p class="text-xs text-gray-500">Manage global default templates or assign custom templates to specific schools</p>
</div>
<div>
<button id="createCustomTemplateBtn" class="flex items-center justify-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-medium transition-all">
<i class="fa fa-plus-circle"></i> Create Custom Template
</button>
</div>
</div>
<div class="mb-8">
<h3 class="text-lg font-bold text-gray-700 mb-4 border-b border-gray-200 pb-2">Default Templates (সিস্টেমের ডিফল্ট টেমপ্লেট)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($defaultTemplates as $template)
<div class="template-card p-4 flex flex-col justify-between">
<div>
<div class="flex justify-between items-center mb-2">
<span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
{{ str_replace('_', ' ', $template->sms_type->value) }}
</span>
<span class="text-xs text-blue-600 font-semibold">Default</span>
</div>
<h4 class="font-bold text-sm text-gray-800 mb-2">{{ $template->title }}</h4>
<p class="text-xs text-gray-600 font-mono whitespace-pre-wrap border border-dashed border-gray-200 p-2 bg-gray-50" style="max-height: 120px; overflow-y: auto;">{{ $template->template_body }}</p>
</div>
<div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
<button onclick="editTemplate({{ json_encode($template) }})" class="px-3 py-1 text-xs border border-blue-600 text-blue-600 hover:bg-blue-50 font-medium transition-all">
<i class="fa fa-edit"></i> Edit Default
</button>
</div>
</div>
@endforeach
</div>
</div>
<div>
<h3 class="text-lg font-bold text-gray-700 mb-4 border-b border-gray-200 pb-2">Custom Templates (কাস্টম টেমপ্লেটসমূহ)</h3>
@if($customTemplates->isEmpty())
<div class="bg-white border border-gray-200 p-8 text-center text-gray-500 text-sm">
No custom templates configured yet. Click "Create Custom Template" to get started.
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($customTemplates as $template)
<div class="template-card p-4 flex flex-col justify-between">
<div>
<div class="flex justify-between items-center mb-2">
<span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600">
{{ str_replace('_', ' ', $template->sms_type->value) }}
</span>
<span class="text-xs text-green-600 font-semibold">{{ $template->schools->count() }} School(s)</span>
</div>
<h4 class="font-bold text-sm text-gray-800 mb-2">{{ $template->title }}</h4>
<p class="text-xs text-gray-600 font-mono whitespace-pre-wrap border border-dashed border-gray-200 p-2 bg-gray-50 mb-3" style="max-height: 120px; overflow-y: auto;">{{ $template->template_body }}</p>
<div class="text-[11px] text-gray-500">
<strong>Assigned Schools:</strong>
<div class="mt-1 flex flex-wrap gap-1 max-h-16 overflow-y-auto">
@foreach($template->schools as $sc)
<span class="bg-gray-100 border border-gray-200 text-gray-700 px-1 py-0.5 text-[10px]">{{ $sc->school_name }}</span>
@endforeach
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-gray-100 flex justify-end gap-2">
<button onclick="editTemplate({{ json_encode($template) }}, {{ json_encode($template->schools->pluck('id')) }})" class="px-3 py-1 text-xs border border-blue-600 text-blue-600 hover:bg-blue-50 font-medium transition-all">
<i class="fa fa-edit"></i> Edit
</button>
<button onclick="deleteTemplate({{ $template->id }})" class="px-3 py-1 text-xs border border-red-600 text-red-600 hover:bg-red-50 font-medium transition-all">
<i class="fa fa-trash"></i> Delete
</button>
</div>
</div>
@endforeach
</div>
@endif
</div>
</div>
<div class="modal fade premium-modal" id="templateModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered mx-auto" style="width: 95%; max-width: 520px;">
<div class="modal-content modal-content-sharp shadow-2xl" style="border-radius: 0; border: 1.5px solid #1e293b !important; background: #fff;">
<form id="templateForm">
<div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-white">
<div class="flex items-center gap-3">
<div class="w-7 h-7 bg-slate-900 text-white flex-shrink-0 flex items-center justify-center">
<i class="fas fa-sms text-md"></i>
</div>
<div class="min-w-0">
<h3 class="text-[11px] font-black text-gray-800 uppercase tracking-widest" id="modalTitle">SMS Template Config</h3>
</div>
</div>
<button type="button" class="text-gray-400 hover:text-gray-800 transition-colors shadow-none" data-bs-dismiss="modal">
<i class="mdi mdi-close text-lg"></i>
</button>
</div>
<div class="modal-body p-4 space-y-4">
<input type="hidden" id="templateId" name="id">
<input type="hidden" id="isDefault" name="is_default" value="0">
<div class="w-full">
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Template Title</label>
<input type="text" id="title" name="title" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required>
</div>
<div id="typeGroup" class="w-full">
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">SMS Type</label>
<select id="sms_type" name="sms_type" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required>
<option value="admission">Admission (ভর্তি)</option>
<option value="readmission">Re-admission (পুনরায় ভর্তি)</option>
<option value="promotion">Promotion (প্রমোশন)</option>
<option value="teacher_registration">Teacher Registration (শিক্ষক নিবন্ধন)</option>
<option value="income">Income / Fee Collection (ফি রসিদ)</option>
</select>
</div>
<div id="schoolsGroup" class="w-full">
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Assign to Schools</label>
<select id="school_ids" name="school_ids[]" class="w-full" multiple="multiple">
@foreach($schools as $school)
<option value="{{ $school->id }}">{{ $school->school_name }} ({{ $school->upazila }}, {{ $school->district }})</option>
@endforeach
</select>
</div>
<div class="w-full">
<div class="flex justify-between items-center mb-1">
<label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Template Body</label>
<span id="charCount" class="text-[10px] text-gray-500">0 characters</span>
</div>
<textarea id="template_body" name="template_body" rows="6" class="form-control font-mono" style="border: 1.5px solid #cbd5e1 !important; padding: 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required></textarea>
<div class="mt-2 p-2 bg-blue-50 border border-blue-100 text-[10px] text-blue-700">
<strong>সাপোর্টেড প্লেসহোল্ডারস:</strong>
<div class="mt-1 flex flex-wrap gap-1">
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{student_name}</span>
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{school_name}</span>
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{class_name}</span>
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{fee_amount}</span>
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{student_id}</span>
<span class="bg-blue-100 px-1 border border-blue-200 font-mono">{date}</span>
</div>
</div>
</div>
</div>
<div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-gray-100 p-3 bg-gray-50/50">
<button type="button" class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest transition-all" style="border: 1.5px solid #64748b !important; background: transparent; color: #64748b; border-radius: 0;" data-bs-dismiss="modal">Discard</button>
<button type="submit" class="w-full sm:w-auto px-6 py-2 text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-md" style="border: 1.5px solid #2563eb !important; background: #2563eb; color: #ffffff; border-radius: 0;">
<i class="mdi mdi-check-circle-outline text-xs"></i> Save Template
</button>
</div>
</form>
</div>
</div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
$('#school_ids').select2({placeholder:"Select schools...",width:'100%',dropdownParent:$('#templateModal')});
$('#template_body').on('input',function(){
let length=$(this).val().length;
$('#charCount').text(length+' characters');
});
$('#createCustomTemplateBtn').click(function(){
$('#templateForm')[0].reset();
$('#templateId').val('');
$('#isDefault').val('0');
$('#school_ids').val(null).trigger('change');
$('#modalTitle').text('Create Custom Template');
$('#typeGroup').show();
$('#schoolsGroup').show();
$('#charCount').text('0 characters');
new bootstrap.Modal(document.getElementById('templateModal')).show();
});
$('#templateForm').submit(function(e){
e.preventDefault();
let formData={
id:$('#templateId').val(),
title:$('#title').val(),
sms_type:$('#sms_type').val(),
template_body:$('#template_body').val(),
is_default:$('#isDefault').val(),
school_ids:$('#school_ids').val()
};
$.ajax({
url:"{{ route('admin.sms-templates.save') }}",
method:"POST",
data:formData,
success:function(res){
if(res.status==='success'){
Toastify({text:res.message,duration:3000,className:"toast-success"}).showToast();
bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
setTimeout(function(){location.reload();},1000);
}else{
Toastify({text:res.message,duration:3000,className:"toast-error"}).showToast();
}
},
error:function(xhr){
let msg=xhr.responseJSON?xhr.responseJSON.message:"Error saving template";
Toastify({text:msg,duration:3000,className:"toast-error"}).showToast();
}
});
});
});
function editTemplate(template,assignedSchoolIds=[]){
$('#templateForm')[0].reset();
$('#templateId').val(template.id);
$('#isDefault').val(template.is_default?'1':'0');
$('#title').val(template.title);
$('#sms_type').val(template.sms_type.value?template.sms_type.value:template.sms_type);
$('#template_body').val(template.template_body);
$('#school_ids').val(assignedSchoolIds).trigger('change');
$('#charCount').text(template.template_body.length+' characters');
if(template.is_default){
$('#modalTitle').text('Edit Default Template');
$('#typeGroup').hide();
$('#schoolsGroup').hide();
}else{
$('#modalTitle').text('Edit Custom Template');
$('#typeGroup').show();
$('#schoolsGroup').show();
}
new bootstrap.Modal(document.getElementById('templateModal')).show();
}
function deleteTemplate(id){
Swal.fire({
title:'Are you sure?',
text:"Do you want to delete this custom template?",
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
cancelButtonColor:'#3085d6',
confirmButtonText:'Yes, delete it!'
}).then((result)=>{
if(result.isConfirmed){
$.ajax({
url:"/admin/sms-templates/"+id,
method:"DELETE",
success:function(res){
if(res.status==='success'){
Toastify({text:res.message,duration:3000,className:"toast-success"}).showToast();
setTimeout(function(){location.reload();},1000);
}
}
});
}
});
}
</script>
@endpush
