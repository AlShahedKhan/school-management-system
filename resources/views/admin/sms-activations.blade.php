@extends('layouts.admin')
@section('title', 'SMS Activation List')
@section('page-title', 'SMS Activation List')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.activation-table th{background-color:#f8fafc;color:#475569;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;}
.switch{position:relative;display:inline-block;width:34px;height:20px;}
.switch input{opacity:0;width:0;height:0;}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:.4s;border-radius:20px;}
.slider:before{position:absolute;content:"";height:12px;width:12px;left:4px;bottom:4px;background-color:white;transition:.4s;border-radius:50%;}
input:checked + .slider{background-color:#2563eb;}
input:focus + .slider{box-shadow:0 0 1px #2563eb;}
input:checked + .slider:before{transform:translateX(14px);}
.toast-success{background:#10b981 !important;border-radius:10px !important;box-shadow:0 4px 12px rgba(16,185,129,0.3) !important;}
.toast-error{background:#ef4444 !important;border-radius:10px !important;box-shadow:0 4px 12px rgba(239,68,68,0.3) !important;}
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
</style>
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
<div class="bg-white shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h2 class="text-xl font-bold text-gray-800">SMS Activations</h2>
<p class="text-xs text-gray-500">Configure which events trigger automated notifications for specific schools</p>
</div>
<div>
<button id="createActivationBtn" class="flex items-center justify-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-medium transition-all">
<i class="fa fa-plus-circle"></i> New SMS Activation
</button>
</div>
</div>
<div class="bg-white shadow-sm border border-gray-100 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse activation-table">
<thead>
<tr class="border-b border-gray-100">
<th class="p-3">School Name</th>
<th class="p-3">Location</th>
<th class="p-3">Sms Type</th>
<th class="p-3">Template</th>
<th class="p-3">Allowed Hours</th>
<th class="p-3">Schedule</th>
<th class="p-3">Channel</th>
<th class="p-3 text-center">Status</th>
<th class="p-3 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200">
@if($activations->isEmpty())
<tr>
<td colspan="9" class="p-8 text-center text-gray-500">No active SMS activation configurations found.</td>
</tr>
@else
@foreach($activations as $act)
<tr class="hover:bg-slate-50 transition-colors">
<td class="p-3 font-semibold text-gray-800">{{ $act->school->school_name }}</td>
<td class="p-3 text-xs text-gray-600">{{ $act->school->country }} / {{ $act->school->division }} / {{ $act->school->district }} / {{ $act->school->upazila }}</td>
<td class="p-3">
<span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
{{ str_replace('_', ' ', $act->sms_type->value) }}
</span>
</td>
<td class="p-3 text-xs text-gray-600">
@if($act->template)
<span class="text-blue-600 font-medium">{{ $act->template->title }}</span>
@else
<span class="text-gray-500 italic">System Default</span>
@endif
</td>
<td class="p-3 text-xs text-gray-600 font-mono">{{ \Carbon\Carbon::parse($act->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($act->end_time)->format('h:i A') }}</td>
<td class="p-3 text-xs text-gray-600">
<div class="capitalize font-semibold">{{ $act->schedule_type }}</div>
@if($act->schedule_type !== 'daily' && is_array($act->schedule_dates))
<div class="text-[10px] text-gray-500 mt-0.5">{{ implode(', ', $act->schedule_dates) }}</div>
@endif
</td>
<td class="p-3 text-xs uppercase font-medium">{{ $act->send_channel->value }}</td>
<td class="p-3 text-center">
<label class="switch">
<input type="checkbox" onchange="toggleStatus({{ $act->id }}, this.checked)" {{ $act->is_active ? 'checked' : '' }}>
<span class="slider"></span>
</label>
</td>
<td class="p-3 text-right">
<div class="inline-flex gap-2">
<button onclick="editActivation({{ json_encode($act) }})" class="p-1 px-2 text-xs border border-blue-600 text-blue-600 hover:bg-blue-50">
<i class="fa fa-edit"></i>
</button>
<button onclick="deleteActivation({{ $act->id }})" class="p-1 px-2 text-xs border border-red-600 text-red-600 hover:bg-red-50">
<i class="fa fa-trash"></i>
</button>
</div>
</td>
</tr>
@endforeach
@endif
</tbody>
</table>
</div>
</div>
</div>
<div class="modal fade premium-modal" id="activationModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered mx-auto" style="width: 95%; max-width: 580px;">
<div class="modal-content modal-content-sharp shadow-2xl" style="border-radius: 0; border: 1.5px solid #1e293b !important; background: #fff;">
<form id="activationForm">
<div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-white">
<div class="flex items-center gap-3">
<div class="w-7 h-7 bg-slate-900 text-white flex-shrink-0 flex items-center justify-center">
<i class="fas fa-toggle-on text-md"></i>
</div>
<div class="min-w-0">
<h3 class="text-[11px] font-black text-gray-800 uppercase tracking-widest" id="modalTitle">New SMS Activation</h3>
</div>
</div>
<button type="button" class="text-gray-400 hover:text-gray-800 transition-colors shadow-none" data-bs-dismiss="modal">
<i class="mdi mdi-close text-lg"></i>
</button>
</div>
<div class="modal-body p-4 space-y-4">
<input type="hidden" id="activationId" name="id">
<div id="geoFields" class="w-full border-b border-dashed border-gray-200 pb-3 mb-2">
<h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Target School Locator</h4>
<div class="grid grid-cols-2 gap-3">
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Country</label>
<select id="country" class="form-control text-xs" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;">
<option value="">Select Country</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Division</label>
<select id="division" class="form-control text-xs" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" disabled>
<option value="">Select Division</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">District</label>
<select id="district" class="form-control text-xs" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" disabled>
<option value="">Select District</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Upazila</label>
<select id="upazila" class="form-control text-xs" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" disabled>
<option value="">Select Upazila</option>
</select>
</div>
<div class="col-span-2">
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">School</label>
<select id="school_id" name="school_id" class="w-full text-xs" disabled required>
<option value="">Select School</option>
</select>
</div>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">SMS Type</label>
<select id="sms_type" name="sms_type" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required>
<option value="">Select Type</option>
<option value="admission">Admission (ভর্তি)</option>
<option value="readmission">Re-admission (পুনরায় ভর্তি)</option>
<option value="promotion">Promotion (প্রমোশন)</option>
<option value="teacher_registration">Teacher Registration (শিক্ষক নিবন্ধন)</option>
<option value="income">Income / Fee Collection (ফি রসিদ)</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Select Template</label>
<select id="admin_sms_template_id" name="admin_sms_template_id" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;">
<option value="">Default Template (সিস্টেমের ডিফল্ট টেমপ্লেট)</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Delivery Time Frame (Start)</label>
<input type="time" id="start_time" name="start_time" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" value="09:00" required>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Delivery Time Frame (End)</label>
<input type="time" id="end_time" name="end_time" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" value="20:00" required>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Schedule Type</label>
<select id="schedule_type" name="schedule_type" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required>
<option value="daily">Daily (প্রতিদিন)</option>
<option value="single">Single Date (নির্দিষ্ট একদিন)</option>
<option value="multiple">Multiple Dates (নির্দিষ্ট কয়েক দিন)</option>
</select>
</div>
<div>
<label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Send Channel</label>
<select id="send_channel" name="send_channel" class="form-control" style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;" required>
<option value="message">Message (SMS)</option>
<option value="call">Voice Call</option>
<option value="both">Both (Message + Call)</option>
</select>
</div>
<div id="datesContainer" class="col-span-2 hidden bg-slate-50 p-3 border border-slate-200">
<div class="flex justify-between items-center mb-2">
<label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Scheduled Dates</label>
<button type="button" onclick="addDateField()" id="addDateBtn" class="text-[11px] text-blue-600 hover:underline"><i class="fa fa-plus"></i> Add Date</button>
</div>
<div id="datesWrapper" class="flex flex-col gap-2">
</div>
</div>
<div class="col-span-2">
<label class="flex items-center gap-2 text-sm font-semibold">
<input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4">
Active (সক্রিয়)
</label>
</div>
</div>
</div>
<div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-gray-100 p-3 bg-gray-50/50">
<button type="button" class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest transition-all" style="border: 1.5px solid #64748b !important; background: transparent; color: #64748b; border-radius: 0;" data-bs-dismiss="modal">Discard</button>
<button type="submit" class="w-full sm:w-auto px-6 py-2 text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-md" style="border: 1.5px solid #2563eb !important; background: #2563eb; color: #ffffff; border-radius: 0;">
<i class="mdi mdi-check-circle-outline text-xs"></i> Save Activation
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
const allTemplates = @json($templates);
$(document).ready(function(){
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
$('#school_id').select2({placeholder:"Select target school...",width:'100%',dropdownParent:$('#activationModal')});
$('#schedule_type').change(function(){
let type=$(this).val();
$('#datesWrapper').empty();
if(type==='single'){
$('#datesContainer').removeClass('hidden');
$('#addDateBtn').hide();
addDateField();
}else if(type==='multiple'){
$('#datesContainer').removeClass('hidden');
$('#addDateBtn').show();
addDateField();
}else{
$('#datesContainer').addClass('hidden');
}
});
loadCountries();
$('#country').change(function(){
resetDropdowns(['division','district','upazila','school_id']);
if($(this).val()){
loadDivisions($(this).val());
}
});
$('#division').change(function(){
resetDropdowns(['district','upazila','school_id']);
if($(this).val()){
loadDistricts($('#country').val(),$(this).val());
}
});
$('#district').change(function(){
resetDropdowns(['upazila','school_id']);
if($(this).val()){
loadUpazilas($('#country').val(),$('#division').val(),$(this).val());
}
});
$('#upazila').change(function(){
resetDropdowns(['school_id']);
if($(this).val()){
loadSchools($('#country').val(),$('#division').val(),$('#district').val(),$(this).val());
}
});
$('#sms_type').change(function(){
let type=$(this).val();
let schoolId=$('#school_id').val();
updateTemplateDropdown(type,schoolId);
});
$('#school_id').change(function(){
let type=$('#sms_type').val();
let schoolId=$(this).val();
if(type && schoolId){
updateTemplateDropdown(type,schoolId);
}
});
$('#createActivationBtn').click(function(){
$('#activationForm')[0].reset();
$('#activationId').val('');
$('#datesWrapper').empty();
$('#datesContainer').addClass('hidden');
$('#is_active').prop('checked',true);
resetDropdowns(['division','district','upazila','school_id']);
$('#country').val('').trigger('change');
$('#geoFields').show();
$('#modalTitle').text('New SMS Activation');
new bootstrap.Modal(document.getElementById('activationModal')).show();
});
$('#activationForm').submit(function(e){
e.preventDefault();
let dates=[];
$('input[name="schedule_dates[]"]').each(function(){
if($(this).val()) dates.push($(this).val());
});
let formData={
id:$('#activationId').val(),
school_id:$('#school_id').val(),
sms_type:$('#sms_type').val(),
admin_sms_template_id:$('#admin_sms_template_id').val(),
start_time:$('#start_time').val(),
end_time:$('#end_time').val(),
schedule_type:$('#schedule_type').val(),
schedule_dates:dates,
send_channel:$('#send_channel').val(),
is_active:$('#is_active').is(':checked')?1:0
};
$.ajax({
url:"{{ route('admin.sms-activations.save') }}",
method:"POST",
data:formData,
success:function(res){
if(res.status==='success'){
Toastify({text:res.message,duration:3000,className:"toast-success"}).showToast();
bootstrap.Modal.getInstance(document.getElementById('activationModal')).hide();
setTimeout(function(){location.reload();},1000);
}
},
error:function(xhr){
let msg=xhr.responseJSON?xhr.responseJSON.message:"Error saving activation settings";
Toastify({text:msg,duration:3000,className:"toast-error"}).showToast();
}
});
});
});
function loadCountries(){
$.get("{{ route('admin.locations.countries') }}",function(data){
let options='<option value="">Select Country</option>';
data.forEach(function(c){
options+=`<option value="${c}">${c}</option>`;
});
$('#country').html(options);
});
}
function loadDivisions(country){
$.get("{{ route('admin.locations.divisions') }}",{country:country},function(data){
let options='<option value="">Select Division</option>';
data.forEach(function(d){
options+=`<option value="${d}">${d}</option>`;
});
$('#division').html(options).prop('disabled',false);
});
}
function loadDistricts(country,division){
$.get("{{ route('admin.locations.districts') }}",{country:country,division:division},function(data){
let options='<option value="">Select District</option>';
data.forEach(function(d){
options+=`<option value="${d}">${d}</option>`;
});
$('#district').html(options).prop('disabled',false);
});
}
function loadUpazilas(country,division,district){
$.get("{{ route('admin.locations.upazilas') }}",{country:country,division:division,district:district},function(data){
let options='<option value="">Select Upazila</option>';
data.forEach(function(u){
options+=`<option value="${u}">${u}</option>`;
});
$('#upazila').html(options).prop('disabled',false);
});
}
function loadSchools(country,division,district,upazila,selectedSchoolId=null){
$.get("{{ route('admin.locations.schools') }}",{country:country,division:division,district:district,upazila:upazila},function(data){
let options='<option value="">Select School</option>';
data.forEach(function(s){
options+=`<option value="${s.id}">${s.school_name}</option>`;
});
$('#school_id').html(options).prop('disabled',false);
if(selectedSchoolId){
$('#school_id').val(selectedSchoolId).trigger('change');
}
});
}
function resetDropdowns(ids){
ids.forEach(function(id){
let placeholder=$('#'+id+' option:first').text();
$('#'+id).html(`<option value="">${placeholder}</option>`).prop('disabled',true);
if(id==='school_id'){
$('#school_id').trigger('change');
}
});
}
function updateTemplateDropdown(type,schoolId){
let options='<option value="">Default Template (সিস্টেমের ডিফল্ট টেমপ্লেট)</option>';
let filtered=allTemplates.filter(function(t){
if(t.is_default||t.sms_type!==type&&t.sms_type.value!==type)return false;
if(t.schools&&t.schools.length>0){
return t.schools.some(s=>s.id==schoolId);
}
return true;
});
filtered.forEach(function(t){
options+=`<option value="${t.id}">${t.title}</option>`;
});
$('#admin_sms_template_id').html(options);
}
function addDateField(dateVal=''){
let wrapper=$('#datesWrapper');
let index=wrapper.children().length;
let row=$(`
<div class="flex items-center gap-2 date-row">
<input type="date" name="schedule_dates[]" class="border border-gray-300 px-3 py-1.5 text-xs flex-1" value="${dateVal}" required>
<button type="button" onclick="removeDateField(this)" class="h-8 w-8 flex items-center justify-center border border-red-200 text-red-500 hover:bg-red-50"><i class="fa fa-trash"></i></button>
</div>
`);
wrapper.append(row);
}
function removeDateField(btn){
let wrapper=$('#datesWrapper');
if($('#schedule_type').val()==='single'||wrapper.children().length>1){
$(btn).closest('.date-row').remove();
}else{
Toastify({text:"At least one scheduled date is required.",duration:3000,className:"toast-error"}).showToast();
}
}
function editActivation(act){
$('#activationForm')[0].reset();
$('#activationId').val(act.id);
$('#sms_type').val(act.sms_type.value?act.sms_type.value:act.sms_type);
$('#start_time').val(act.start_time.substring(0,5));
$('#end_time').val(act.end_time.substring(0,5));
$('#schedule_type').val(act.schedule_type).trigger('change');
$('#send_channel').val(act.send_channel.value?act.send_channel.value:act.send_channel);
$('#is_active').prop('checked',act.is_active);
if(act.schedule_type!=='daily'&&Array.isArray(act.schedule_dates)){
$('#datesWrapper').empty();
act.schedule_dates.forEach(function(d){
addDateField(d);
});
}
$('#geoFields').hide();
let opt=`<option value="${act.school_id}" selected>${act.school.school_name}</option>`;
$('#school_id').html(opt).trigger('change');
new bootstrap.Modal(document.getElementById('activationModal')).show();
}
function deleteActivation(id){
Swal.fire({
title:'Are you sure?',
text:"Do you want to cancel this school SMS activation?",
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
cancelButtonColor:'#3085d6',
confirmButtonText:'Yes, cancel it!'
}).then((result)=>{
if(result.isConfirmed){
$.ajax({
url:"/admin/sms-activations/"+id,
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
@endsection
