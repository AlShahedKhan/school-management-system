@extends('layouts.school')

@section('title', 'Fail List')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    @media print {
        body * { visibility: hidden !important; }
        #failPrintArea, #failPrintArea * { visibility: visible !important; }
        #failPrintArea { position: absolute; inset: 0; width: 100%; margin: 0; padding: 12px; }
        #failPrintArea .school-data-table-frame { border: 0 !important; box-shadow: none !important; }
        #failPrintArea .school-data-table-scroll { overflow: visible !important; }
        #failPrintArea table { min-width: 0 !important; width: 100% !important; }
    }
</style>

<div class="main-view-container grid w-full grid-cols-1 p-3">
    <div class="mx-auto w-full max-w-full">
        <x-school.list-header title="Fail List" breadcrumb-current="Fail List" keep-title>
            <x-slot:search>
                <div class="flex items-center gap-2">
                    <x-input.search id="failSearch" placeholder="Search fail list..." class="w-72" />
                    <x-button.secondary type="button" onclick="restoreFailSearch()">Restore</x-button.secondary>
                </div>
            </x-slot:search>
            <x-slot:actions>
                <x-dropdown button-id="btnFailExport" menu-id="failExportDropdown" label="Export" align="full">
                    <x-dropdown.item onclick="exportFailList('pdf')">PDF</x-dropdown.item>
                    <x-dropdown.item onclick="exportFailList('excel')">Excel</x-dropdown.item>
                    <x-dropdown.item onclick="exportFailList('print')">Print</x-dropdown.item>
                </x-dropdown>
                <x-button.primary type="button" onclick="openFailModal()" class="w-full lg:w-auto">
                    Generate Fail List
                </x-button.primary>
            </x-slot:actions>
            <x-slot:mobile-search>
                <div class="col-span-3 grid grid-cols-3 gap-2">
                    <x-input.search id="failSearchMobile" placeholder="Search fail list..." class="col-span-2 min-w-0" />
                    <x-button.secondary type="button" onclick="restoreFailSearch()" class="w-full">Restore</x-button.secondary>
                </div>
            </x-slot:mobile-search>
        </x-school.list-header>

        <div id="failPrintArea">
        <div id="failResultSummary" class="mb-3 hidden border border-slate-200 bg-white px-4 py-3 text-[11px] text-slate-600 shadow-sm"></div>

        <x-school.data-table :empty="false" :empty-colspan="11" min-width="1320px" tbody-id="failListBody">
            <x-slot:columns>
                <colgroup>
                    <col style="width:48px"><col style="width:95px"><col style="width:95px"><col style="width:85px">
                    <col style="width:80px"><col style="width:125px"><col style="width:125px"><col style="width:180px">
                    <col style="width:105px"><col style="width:280px"><col style="width:220px">
                </colgroup>
            </x-slot:columns>
            <x-slot:head>
                @foreach (['SL','Class','Group','Section','Session','Exam','Student ID','Student Name','Failed Subjects','Subject Name','Required to Pass'] as $heading)
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">{{ $heading }}</x-table.th>
                @endforeach
            </x-slot:head>
            <tr><td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-gray-500">Generate a Fail List to view failed students.</td></tr>
        </x-school.data-table>
        </div>
    </div>
</div>

<x-modal.form
    id="failListModal"
    form-id="failListForm"
    title="Generate Fail List"
    close-button-id="closeFailListModal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[480px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    panel-style="border-radius:4px; max-height:min(620px, calc(100dvh - 2.5rem));"
    title-class="m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="grid grid-cols-1 gap-3 md:grid-cols-2"
>
    @foreach ([['failClass','Class'],['failGroup','Group'],['failSection','Section'],['failSession','Session'],['failExam','Exam']] as [$id, $label])
        <div class="relative">
            <x-input.dropdown-select :id="$id" :placeholder="'Select '.$label" :options="[]" />
            <x-input.floating-label :for="$id" :floating="false">{{ $label }}</x-input.floating-label>
        </div>
    @endforeach
    <div class="relative">
        <x-input.dropdown-select id="failSortOrder" placeholder="Select Sort Order" value="first" :options="[
            ['value' => 'first', 'label' => 'First'],
            ['value' => 'last', 'label' => 'Last'],
        ]" />
        <x-input.floating-label for="failSortOrder" :floating="false">Sort Order</x-input.floating-label>
    </div>
    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pb-4 pt-3">
            <x-button.secondary id="closeFailListModal" type="button" class="w-full">Cancel</x-button.secondary>
            <x-button.primary id="generateFailListButton" type="submit" class="w-full">Generate Fail List</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

<script>
    let failPayload = null;
    const failToken = document.querySelector('meta[name="csrf-token"]')?.content;
    axios.defaults.headers.common['X-CSRF-TOKEN'] = failToken;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.withCredentials = true;

    const failParts = id => {
        const input = document.getElementById(id), root = input?.closest('[data-dropdown-select]');
        return { input, root, label: root?.querySelector('[data-dropdown-select-label]'), menu: root?.querySelector('[data-dropdown-select-menu]') };
    };
    const failValue = id => failParts(id).input?.value || '';
    const setFailValue = (id, value = '', label = null) => {
        const parts = failParts(id); if (!parts.input) return;
        const selected = [...(parts.menu?.querySelectorAll('[data-dropdown-select-option]') || [])].find(o => String(o.dataset.value) === String(value));
        parts.input.value = value;
        if (parts.label) parts.label.textContent = label ?? selected?.textContent.trim() ?? parts.label.dataset.placeholder ?? 'Select...';
        parts.menu?.querySelectorAll('[data-dropdown-select-option]').forEach(o => {
            const active = o === selected; o.classList.toggle('bg-slate-100', active); o.classList.toggle('text-slate-900', active); o.classList.toggle('text-slate-800', !active); o.setAttribute('aria-selected', String(active));
        });
    };
    const fillFailOptions = (id, data, valueField, labelField = valueField) => {
        const parts = failParts(id); if (!parts.menu) return;
        parts.menu.innerHTML = '';
        (data || []).forEach(item => {
            const option = document.createElement('button'); option.type = 'button'; option.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 hover:bg-slate-100';
            option.dataset.value = String(item[valueField] ?? ''); option.setAttribute('data-dropdown-select-option',''); option.setAttribute('role','option'); option.setAttribute('aria-selected','false'); option.textContent = item[labelField] ?? item[valueField] ?? '';
            option.addEventListener('click', () => { setFailValue(id, option.dataset.value, option.textContent.trim()); parts.menu.classList.add('hidden'); parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded','false'); parts.input.dispatchEvent(new Event('change',{bubbles:true})); });
            parts.menu.appendChild(option);
        });
        setFailValue(id);
    };
    const failGet = (url, params = {}) => axios.get(url, { params }).then(r => r.data.data || []);
    const clearFail = ids => ids.forEach(id => fillFailOptions(id, [], 'id'));
    function fetchFailClasses() { return failGet('/api/get-school-classes').then(data => fillFailOptions('failClass', data, 'id', 'class_name')); }
    function fetchFailGroups() { const classId = failValue('failClass'); clearFail(['failGroup','failSection','failSession','failExam']); return classId ? failGet('/api/get-school-groups',{class_id:classId}).then(data => fillFailOptions('failGroup',data,'id','group_name')) : Promise.resolve(); }
    function fetchFailSections() { const classId=failValue('failClass'), groupId=failValue('failGroup'); clearFail(['failSection','failSession','failExam']); return classId && groupId ? failGet('/api/get-school-sections',{class_id:classId,group_id:groupId}).then(data=>fillFailOptions('failSection',data,'id','section_name')) : Promise.resolve(); }
    function fetchFailSessions() { const classId=failValue('failClass'), groupId=failValue('failGroup'), sectionId=failValue('failSection'); clearFail(['failSession','failExam']); return classId && groupId && sectionId ? failGet('/api/get-school-sessions',{class_id:classId,group_id:groupId,section_id:sectionId}).then(data=>fillFailOptions('failSession',data,'id','session_year')) : Promise.resolve(); }
    function fetchFailExams() { const ids={class_id:failValue('failClass'),group_id:failValue('failGroup'),section_id:failValue('failSection'),session_id:failValue('failSession')}; clearFail(['failExam']); return Object.values(ids).every(Boolean) ? failGet('/api/get-school-exams',ids).then(data=>fillFailOptions('failExam',data,'id','exam_name')) : Promise.resolve(); }
    function openFailModal() { document.getElementById('failListModal')?.classList.remove('hidden'); fetchFailClasses().catch(showFailError); }
    function closeFailModal() { document.getElementById('failListModal')?.classList.add('hidden'); }
    function showFailError(error) { Swal.fire({icon:'error',title:'Unable to load filters',text:error.response?.data?.message || 'Please try again.'}); }
    function failNumber(value) { const n=Number(value); return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.?0+$/,''); }
    function failEscape(value) { return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[c]); }
    function currentFailRows() { const query=((document.getElementById('failSearch')?.value || document.getElementById('failSearchMobile')?.value || '').trim().toLowerCase()); const rows=failPayload?.rows || []; return query ? rows.filter(row => Object.values(row).flatMap(v => Array.isArray(v) ? v.map(x => Object.values(x)) : [v]).flat().join(' ').toLowerCase().includes(query)) : rows; }
    function renderFailRows() {
        if (!failPayload) return; const rows=currentFailRows(), body=document.getElementById('failListBody'), summary=document.getElementById('failResultSummary');
        summary.textContent=`${failPayload.filters.class} / ${failPayload.filters.group} / ${failPayload.filters.section} / ${failPayload.filters.session} / ${failPayload.filters.exam} · ${rows.length} failed students`; summary.classList.remove('hidden');
        if (!rows.length) { body.innerHTML='<tr><td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No failed students found.</td></tr>'; return; }
        body.innerHTML=rows.map(row => {
            const names=row.subject_details.map(item => `${failEscape(item.name)} (${failNumber(item.mark)})`).join('<br>');
            const required=row.subject_details.map(item => failNumber(item.required_to_pass)).join('<br>');
            return `<tr class="hover:bg-slate-50"><td class="border border-gray-300 px-3 py-2 text-center">${row.sl}</td><td class="border border-gray-300 px-3 py-2">${failEscape(row.class)}</td><td class="border border-gray-300 px-3 py-2">${failEscape(row.group)}</td><td class="border border-gray-300 px-3 py-2">${failEscape(row.section)}</td><td class="border border-gray-300 px-3 py-2 text-center">${failEscape(row.session)}</td><td class="border border-gray-300 px-3 py-2">${failEscape(row.exam)}</td><td class="border border-gray-300 px-3 py-2 font-mono">${failEscape(row.student_id)}</td><td class="border border-gray-300 px-3 py-2 font-semibold">${failEscape(row.student_name)}</td><td class="border border-gray-300 px-3 py-2 text-center">${row.failed_subjects}</td><td class="border border-gray-300 px-3 py-2 leading-5">${names}</td><td class="border border-gray-300 px-3 py-2 text-center leading-5">${required}</td></tr>`;
        }).join('');
    }
    function restoreFailSearch() { ['failSearch','failSearchMobile'].forEach(id=>{const input=document.getElementById(id); if(input) input.value='';}); renderFailRows(); }
    function exportFailList(type) {
        const rows = currentFailRows();
        if (!rows.length) {
            Swal.fire({ icon: 'warning', title: 'Nothing to export', text: 'Generate a fail list first.' });
            return;
        }

        if (type === 'pdf' || type === 'print') {
            axios.post('/api/school-fail-list/export-pdf', {
                class_id: failValue('failClass'), group_id: failValue('failGroup'), section_id: failValue('failSection'),
                session_id: failValue('failSession'), exam_id: failValue('failExam'), sort_order: failValue('failSortOrder'),
            }, { responseType: 'blob' }).then(response => {
                const pdfUrl = URL.createObjectURL(response.data);
                if (type === 'print') {
                    window.open(pdfUrl, '_blank');
                } else {
                    const link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `fail-list-${new Date().toISOString().slice(0, 10)}.pdf`;
                    link.click();
                }
                setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
            }).catch(() => Swal.fire({ icon: 'error', title: 'PDF Export Failed', text: 'Unable to generate the fail-list PDF.' }));
            return;
        }

        const headings = ['SL', 'Class', 'Group', 'Section', 'Session', 'Exam', 'Student ID', 'Student Name', 'Failed Subjects', 'Subject Name', 'Required to Pass'];
        const exportRows = rows.map(row => [
            row.sl, row.class, row.group, row.section, row.session, row.exam, row.student_id, row.student_name,
            row.failed_subjects,
            row.subject_details.map(item => `${item.name} (${failNumber(item.mark)})`).join('; '),
            row.subject_details.map(item => failNumber(item.required_to_pass)).join('; '),
        ]);

        if (type === 'excel') {
            const csv = [headings, ...exportRows]
                .map(row => row.map(value => `"${String(value ?? '').replace(/"/g, '""')}"`).join(','))
                .join('\n');
            const link = document.createElement('a');
            link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            link.download = `fail-list-${new Date().toISOString().slice(0, 10)}.csv`;
            link.click();
            URL.revokeObjectURL(link.href);
            return;
        }

        window.print();
    }
    document.addEventListener('click', event => {
        const trigger = event.target.closest('#btnFailExport');
        const menu = document.getElementById('failExportDropdown');
        if (trigger && menu) {
            event.stopPropagation();
            menu.classList.toggle('hidden');
            trigger.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
            return;
        }
        if (!event.target.closest('#failExportDropdown') && menu) {
            menu.classList.add('hidden');
            document.getElementById('btnFailExport')?.setAttribute('aria-expanded', 'false');
        }
    });
    function validateFailFilters() { const fields=[['failClass','Class'],['failGroup','Group'],['failSection','Section'],['failSession','Session'],['failExam','Exam'],['failSortOrder','Sort Order']]; const missing=fields.find(([id])=>!failValue(id)); if(missing){Swal.fire({icon:'warning',title:'Incomplete filters',text:`${missing[1]} is required.`}); return false;} return true; }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('failClass')?.addEventListener('change',fetchFailGroups); document.getElementById('failGroup')?.addEventListener('change',fetchFailSections); document.getElementById('failSection')?.addEventListener('change',fetchFailSessions); document.getElementById('failSession')?.addEventListener('change',fetchFailExams); document.getElementById('closeFailListModal')?.addEventListener('click',closeFailModal);
        ['failSearch','failSearchMobile'].forEach(id=>document.getElementById(id)?.addEventListener('input',e=>{const other=document.getElementById(id==='failSearch'?'failSearchMobile':'failSearch'); if(other) other.value=e.target.value; renderFailRows();}));
        document.getElementById('failListForm')?.addEventListener('submit', event => { event.preventDefault(); if(!validateFailFilters()) return; const button=document.getElementById('generateFailListButton'); button.disabled=true; button.textContent='Generating...'; axios.post('/api/school-fail-list',{class_id:failValue('failClass'),group_id:failValue('failGroup'),section_id:failValue('failSection'),session_id:failValue('failSession'),exam_id:failValue('failExam'),sort_order:failValue('failSortOrder')}).then(response=>{failPayload=response.data; renderFailRows(); closeFailModal();}).catch(error=>Swal.fire({icon:'error',title:'Fail List Error',text:error.response?.data?.message || 'Unable to generate the fail list.'})).finally(()=>{button.disabled=false; button.textContent='Generate Fail List';}); });
    });
</script>
@endsection
