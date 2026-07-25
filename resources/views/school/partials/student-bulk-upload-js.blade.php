<script>
const localApi=axios.create({baseURL:'/'});
localApi.defaults.headers.common['X-CSRF-TOKEN']=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
function setSelectedValue(id,value){
const input=document.getElementById(id);
if(!input)return;
input.value=value;
const button=document.getElementById(id+'Button');
const label=button?button.querySelector('[data-dropdown-select-label]'):null;
const menu=document.getElementById(id+'Menu');
if(menu){
let foundLabel='';
menu.querySelectorAll('[data-dropdown-select-option]').forEach(item=>{
const isSel=String(item.dataset.value)===String(value);
item.classList.toggle('bg-slate-100',isSel);
item.classList.toggle('text-slate-900',isSel);
item.classList.toggle('text-slate-800',!isSel);
item.setAttribute('aria-selected',String(isSel));
if(isSel){
foundLabel=item.textContent.trim();
}
});
if(label){
const placeholderEl=button.querySelector('[data-placeholder]');
const placeholderText=placeholderEl?placeholderEl.dataset.placeholder:'Select...';
label.textContent=foundLabel||placeholderText;
}
}
input.dispatchEvent(new Event('change',{bubbles:true}));
}
function bulkCanDownload(){
const c=document.getElementById('bulkClass').value;
const g=document.getElementById('bulkGroup').value;
const sec=document.getElementById('bulkSection').value;
const sess=document.getElementById('bulkSession').value;
return c&&g&&sec&&sess;
}
function updateBulkButtons(){
document.getElementById('btnDownloadTemplate').disabled=!bulkCanDownload();
}
async function loadBulkFees(){
const classId=document.getElementById('bulkClass').value;
const sessionId=document.getElementById('bulkSession').value;
const feeInput=document.getElementById('bulkFee');
if(!classId||!sessionId){
feeInput.value='';
return;
}
feeInput.value='Loading...';
try{
const res=await axios.get('/api/fee-templates',{params:{class_id:classId,session_id:sessionId,search:'Admission',all:1}});
const fees=res.data.data;
const admissionFee=fees&&fees.length>0?fees[0]:null;
feeInput.value=admissionFee?admissionFee.amount:'No fee defined';
}catch(e){
console.error("Fee Load Error:",e);
feeInput.value='Error';
}
}
function populateDropdownSelect(id,options,selectedValue,placeholder='Select...'){
const input=document.getElementById(id);
const button=document.getElementById(id+'Button');
const label=button?button.querySelector('[data-dropdown-select-label]'):null;
const menu=document.getElementById(id+'Menu');
if(!input||!menu)return;
let menuHtml='';
let selectedText=placeholder;
options.forEach(opt=>{
const isSelected=String(opt.value)===String(selectedValue);
if(isSelected){
selectedText=opt.label;
}
menuHtml+=`<button type="button" class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 ${isSelected?'bg-slate-100 text-slate-900':'text-slate-800'}" data-value="${opt.value}" role="option" aria-selected="${isSelected?'true':'false'}" data-dropdown-select-option>${opt.label}</button>`;
});
menu.innerHTML=menuHtml;
input.value=selectedValue|| '';
if(label){
label.textContent=selectedText;
}
menu.querySelectorAll('[data-dropdown-select-option]').forEach(option=>{
option.addEventListener('click',()=>{
input.value=option.dataset.value||'';
if(label){
label.textContent=option.textContent.trim();
}
menu.querySelectorAll('[data-dropdown-select-option]').forEach(item=>{
const isSel=item===option;
item.classList.toggle('bg-slate-100',isSel);
item.classList.toggle('text-slate-900',isSel);
item.classList.toggle('text-slate-800',!isSel);
item.setAttribute('aria-selected',String(isSel));
});
const root=input.closest('[data-dropdown-select]');
if(root){
root.classList.remove('is-open');
}
menu.classList.add('hidden');
if(button){
button.setAttribute('aria-expanded','false');
const icon=button.querySelector('i');
if(icon){
icon.classList.remove('rotate-180');
}
}
input.dispatchEvent(new Event('change',{bubbles:true}));
});
});
}
function populateDropdown(elemId,data,valKey,labelKey,defaultText,selectedValue=''){
const options=data.map(item=>({value:item[valKey],label:item[labelKey]}));
populateDropdownSelect(elemId,options,selectedValue,defaultText);
}
async function loadClasses(){
try{
const res=await axios.get('/api/get-school-classes');
populateDropdown('bulkClass',res.data.data,'id','class_name','Select Class');
}catch(e){
console.error('Bulk class load failed',e);
}
}
async function handleClassChange(){
const classId=document.getElementById('bulkClass').value;
populateDropdown('bulkGroup',[],'id','group_name','Select Group');
populateDropdown('bulkSection',[],'id','section_name','Select Section');
populateDropdown('bulkSession',[],'id','session_year','Select Session');
document.getElementById('bulkFee').value='';
updateBulkButtons();
if(!classId)return;
try{
const res=await axios.get('/api/get-school-groups',{params:{class_id:classId}});
populateDropdown('bulkGroup',res.data.data||[],'id','group_name','Select Group');
}catch(e){
console.error('Bulk group load failed',e);
}
}
async function handleGroupChange(){
const groupId=document.getElementById('bulkGroup').value;
populateDropdown('bulkSection',[],'id','section_name','Select Section');
populateDropdown('bulkSession',[],'id','session_year','Select Session');
document.getElementById('bulkFee').value='';
updateBulkButtons();
if(!groupId)return;
try{
const res=await axios.get('/api/get-school-sections',{params:{group_id:groupId}});
populateDropdown('bulkSection',res.data.data||[],'id','section_name','Select Section');
}catch(e){
console.error('Bulk section load failed',e);
}
}
async function handleSectionChange(){
const classId=document.getElementById('bulkClass').value;
const groupId=document.getElementById('bulkGroup').value;
const sectionId=document.getElementById('bulkSection').value;
populateDropdown('bulkSession',[],'id','session_year','Select Session');
document.getElementById('bulkFee').value='';
updateBulkButtons();
if(!classId||!sectionId)return;
try{
const params={class_id:classId};
if(groupId)params.group_id=groupId;
if(sectionId)params.section_id=sectionId;
const res=await axios.get('/api/get-school-sessions',{params});
populateDropdown('bulkSession',res.data.data||[],'id','session_year','Select Session');
}catch(e){
console.error('Bulk session load failed',e);
}
}
function handleSessionChange(){
updateBulkButtons();
loadBulkFees();
}
function showBulkResultSuccess(count){
document.getElementById('bulkState_processing').classList.add('hidden');
document.getElementById('bulkState_result').classList.remove('hidden');
const alertEl=document.getElementById('bulkResultAlert');
alertEl.className='p-3 mb-3 text-center bg-green-50 border border-green-100 text-green-700';
alertEl.style.borderRadius='0';
const iconEl=document.getElementById('bulkResultIcon');
iconEl.className='mdi mdi-checkbox-marked-circle-outline text-2xl block mb-1';
const textEl=document.getElementById('bulkResultText');
textEl.textContent=`Import complete. ${count} student(s) created successfully.`;
document.getElementById('bulkErrorSection').classList.add('hidden');
}
function showBulkResultErrors(message,errors){
document.getElementById('bulkState_processing').classList.add('hidden');
document.getElementById('bulkState_result').classList.remove('hidden');
const alertEl=document.getElementById('bulkResultAlert');
alertEl.className='p-3 mb-3 text-center bg-red-50 border border-red-100 text-red-700';
alertEl.style.borderRadius='0';
const iconEl=document.getElementById('bulkResultIcon');
iconEl.className='mdi mdi-alert-circle-outline text-2xl block mb-1';
const textEl=document.getElementById('bulkResultText');
textEl.textContent=message;
const errSection=document.getElementById('bulkErrorSection');
const errTbody=document.getElementById('bulkErrorTableBody');
errSection.classList.toggle('hidden',errors.length===0);
if(errors.length>0){
errTbody.innerHTML='';
errors.forEach(e=>{
const tr=document.createElement('tr');
tr.className='border-t border-gray-100';
tr.innerHTML=`<td class="px-2 py-1 border-r border-gray-200 text-center font-medium text-gray-700">${e.row}</td><td class="px-2 py-1 border-r border-gray-200 font-medium text-gray-700 whitespace-nowrap">${e.field}</td><td class="px-2 py-1 text-gray-600">${e.message}</td>`;
errTbody.appendChild(tr);
});
}
}
async function uploadBulkFile(){
const file=document.getElementById('bulkFileInput').files[0];
const errEl=document.getElementById('bulkUploadError');
errEl.classList.add('hidden');
if(!file){
errEl.textContent='Please select an Excel file (.xlsx or .xls).';
errEl.classList.remove('hidden');
return;
}
const fd=new FormData();
fd.append('file',file);
fd.append('class_id',document.getElementById('bulkClass').value);
fd.append('section_id',document.getElementById('bulkSection').value);
fd.append('session_id',document.getElementById('bulkSession').value);
fd.append('admission_date',document.getElementById('bulkAdmissionDate').value);
const groupId=document.getElementById('bulkGroup').value;
if(groupId)fd.append('group_id',groupId);
document.getElementById('bulkState_result').classList.add('hidden');
document.getElementById('bulkState_processing').classList.remove('hidden');
try{
const res=await axios.post('/api/school/student-import/upload',fd,{headers:{'Content-Type':'multipart/form-data'}});
showBulkResultSuccess(res.data.count);
}catch(err){
if(err.response?.status===422){
const data=err.response.data;
if(data.errors&&Array.isArray(data.errors)&&data.errors.length>0){
showBulkResultErrors(data.message,data.errors);
}else{
document.getElementById('bulkState_processing').classList.add('hidden');
errEl.textContent=data.message||'Validation failed.';
errEl.classList.remove('hidden');
}
}else{
const msg=err.response?.data?.message||'An unexpected error occurred. Please try again.';
showBulkResultErrors(msg,[]);
}
}
}
document.addEventListener('DOMContentLoaded',()=>{
window.lastActiveModalId='bulkUploadModal';
loadClasses();
document.getElementById('bulkUploadModal')?.classList.remove('hidden');
const bulkAdmissionDateInput=document.getElementById('bulkAdmissionDate');
if(bulkAdmissionDateInput&&!bulkAdmissionDateInput.value){
bulkAdmissionDateInput.value=new Date().toISOString().split('T')[0];
}
document.getElementById('bulkClass')?.addEventListener('change',handleClassChange);
document.getElementById('bulkGroup')?.addEventListener('change',handleGroupChange);
document.getElementById('bulkSection')?.addEventListener('change',handleSectionChange);
document.getElementById('bulkSession')?.addEventListener('change',handleSessionChange);
document.getElementById('btnDownloadTemplate')?.addEventListener('click',async()=>{
if(!bulkCanDownload())return;
try{
const params={class_id:document.getElementById('bulkClass').value,section_id:document.getElementById('bulkSection').value,session_id:document.getElementById('bulkSession').value};
const groupId=document.getElementById('bulkGroup').value;
if(groupId)params.group_id=groupId;
const res=await axios.get('/api/school/student-import/template',{params,responseType:'blob'});
const cd=res.headers['content-disposition']||'';
const match=cd.match(/filename="?([^";\n]+)"?/);
const filename=match?match[1]:'student-template.xlsx';
const url=URL.createObjectURL(res.data);
const a=document.createElement('a');
a.href=url;
a.download=filename;
document.body.appendChild(a);
a.click();
document.body.removeChild(a);
URL.revokeObjectURL(url);
}catch(err){
Swal.fire({icon:'error',title:'Error',text:'Failed to download template. Please try again.'});
}
});
document.getElementById('closeBulkUploadModal')?.addEventListener('click',()=>{
window.location.href='/school/students';
});
document.getElementById('bulkUploadForm')?.addEventListener('submit',function(e){
e.preventDefault();
const feeValue=document.getElementById('bulkFee').value.trim();
if(!feeValue||feeValue==='No fee defined'||feeValue==='Error'||feeValue==='Loading...'||parseFloat(feeValue)<=0){
Swal.fire({icon:'warning',title:'Admission Fee Required',text:'You cannot proceed because no admission fee template is defined for this class/session.',confirmButtonColor:'#2563eb'});
return;
}
uploadBulkFile();
});
document.getElementById('btnBulkReset')?.addEventListener('click',()=>{
document.getElementById('bulkUploadForm').reset();
const bulkAdmissionDateInput=document.getElementById('bulkAdmissionDate');
if(bulkAdmissionDateInput){
bulkAdmissionDateInput.value=new Date().toISOString().split('T')[0];
}
populateDropdown('bulkClass',[],'id','class_name','Select Class');
populateDropdown('bulkGroup',[],'id','group_name','Select Group');
populateDropdown('bulkSection',[],'id','section_name','Select Section');
populateDropdown('bulkSession',[],'id','session_year','Select Session');
loadClasses();
document.getElementById('bulkFee').value='';
document.getElementById('bulkState_result').classList.add('hidden');
document.getElementById('bulkState_processing').classList.add('hidden');
document.getElementById('bulkUploadError').classList.add('hidden');
updateBulkButtons();
});
document.getElementById('btnCreateFeeTemplateBulk')?.addEventListener('click',()=>{
const classId=document.getElementById('bulkClass').value;
const sessionId=document.getElementById('bulkSession').value;
if(!classId||!sessionId){
Swal.fire({icon:'warning',title:'Selection Required',text:'Please select both Class and Session first.',confirmButtonColor:'#2563eb'});
return;
}
document.getElementById('feeTemplateModal').classList.remove('hidden');
const payDateInput=document.getElementById('feePayDateInput');
if(payDateInput&&!payDateInput.value){
payDateInput.value=new Date().toISOString().split('T')[0];
}
});
document.getElementById('closeFeeTemplateModal')?.addEventListener('click',()=>{
document.getElementById('feeTemplateModal').classList.add('hidden');
});
document.getElementById('feeTemplateForm')?.addEventListener('submit',function(e){
e.preventDefault();
const classId=document.getElementById('bulkClass').value;
const sessionId=document.getElementById('bulkSession').value;
const groupId=document.getElementById('bulkGroup').value;
const sectionId=document.getElementById('bulkSection').value;
const feeName=document.getElementById('feeNameInput').value;
const amount=document.getElementById('feeAmountInput').value;
const payDate=document.getElementById('feePayDateInput').value;
Swal.fire({title:'Saving Fee Template...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
axios.post('/api/fee-templates',{class_id:classId,session_id:sessionId,group_id:groupId,section_id:sectionId,fee_type_name:'Admission',fee_name:feeName,amount:amount,pay_date:payDate})
.then(()=>{
Swal.close();
document.getElementById('feeTemplateModal').classList.add('hidden');
document.getElementById('feeTemplateForm').reset();
loadBulkFees();
})
.catch(err=>{
Swal.close();
let errorMsg='Failed to create fee template.';
if(err.response&&err.response.data.message){
errorMsg=err.response.data.message;
}
Swal.fire({icon:'error',title:'Error',text:errorMsg});
});
});
});
</script>
