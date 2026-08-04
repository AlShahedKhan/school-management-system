@extends('layouts.school')

@section('title', 'Merit List')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="main-view-container grid w-full grid-cols-1 p-3">
    <div class="mx-auto w-full max-w-full">
        <x-school.list-header title="Merit List" breadcrumb-current="Merit List" keep-title>
            <x-slot:search>
                <div class="flex items-center gap-2">
                    <x-input.search id="meritSearch" placeholder="Search merit list..." class="w-72" />
                    <x-button.secondary type="button" onclick="restoreMeritSearch()">Restore</x-button.secondary>
                </div>
            </x-slot:search>

            <x-slot:actions>
                <x-dropdown button-id="btnMeritExport" menu-id="meritExportDropdown" label="Export" align="full">
                    <x-dropdown.item onclick="exportMeritList('pdf')">PDF</x-dropdown.item>
                    <x-dropdown.item onclick="exportMeritList('excel')">Excel</x-dropdown.item>
                    <x-dropdown.item onclick="window.print()">Print</x-dropdown.item>
                </x-dropdown>
                <x-button.primary type="button" onclick="openMeritModal()" class="w-full lg:w-auto">
                    Generate Merit List
                </x-button.primary>
            </x-slot:actions>

            <x-slot:mobile-search>
                <div class="col-span-3 grid grid-cols-3 gap-2">
                    <x-input.search id="meritSearchMobile" placeholder="Search merit list..." class="col-span-2 min-w-0" />
                    <x-button.secondary type="button" onclick="restoreMeritSearch()" class="w-full">Restore</x-button.secondary>
                </div>
            </x-slot:mobile-search>
        </x-school.list-header>

        <div id="meritResultSummary" class="mb-3 hidden border border-slate-200 bg-white px-4 py-3 text-[11px] text-slate-600 shadow-sm"></div>

        <x-school.data-table
            :empty="false"
            :empty-colspan="11"
            empty-message="Generate a Merit List to view student rankings."
            min-width="1160px"
            tbody-id="meritListBody"
        >
            <x-slot:columns>
                <colgroup>
                    <col style="width: 48px;">
                    <col style="width: 100px;">
                    <col style="width: 95px;">
                    <col style="width: 85px;">
                    <col style="width: 80px;">
                    <col style="width: 130px;">
                    <col style="width: 125px;">
                    <col style="width: 180px;">
                    <col style="width: 105px;">
                    <col style="width: 115px;">
                    <col style="width: 105px;">
                </colgroup>
            </x-slot:columns>

            <x-slot:head>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Session</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Exam</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student ID</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Name</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Total Marks</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Obtained Marks</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Merit Serial</x-table.th>
            </x-slot:head>

            <tr>
                <td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-gray-500">
                    Generate a Merit List to view student rankings.
                </td>
            </tr>
        </x-school.data-table>
    </div>
</div>

<x-modal.form
    id="meritListModal"
    form-id="meritListForm"
    title="Generate Merit List"
    close-button-id="closeMeritListModal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[480px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    panel-style="border-radius:4px; max-height:min(620px, calc(100dvh - 2.5rem));"
    title-class="m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="grid grid-cols-1 gap-3 md:grid-cols-2"
>
    <div class="relative">
        <x-input.dropdown-select id="meritClass" placeholder="Select Class" :options="[]" />
        <x-input.floating-label for="meritClass" :floating="false">Class</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="meritGroup" placeholder="Select Group" :options="[]" />
        <x-input.floating-label for="meritGroup" :floating="false">Group</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="meritSection" placeholder="Select Section" :options="[]" />
        <x-input.floating-label for="meritSection" :floating="false">Section</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="meritSession" placeholder="Select Session" :options="[]" />
        <x-input.floating-label for="meritSession" :floating="false">Session</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select id="meritExam" placeholder="Select Exam" :options="[]" />
        <x-input.floating-label for="meritExam" :floating="false">Exam</x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.dropdown-select
            id="meritSortOrder"
            placeholder="Select Sort Order"
            value="first"
            :options="[
                ['value' => 'first', 'label' => 'First'],
                ['value' => 'last', 'label' => 'Last'],
            ]"
        />
        <x-input.floating-label for="meritSortOrder" :floating="false">Sort Order</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pb-4 pt-3">
            <x-button.secondary id="closeMeritListModal" type="button" class="w-full">Cancel</x-button.secondary>
            <x-button.primary id="generateMeritListButton" type="submit" class="w-full">Generate Merit List</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

<script>
    let meritPayload = null;
    const meritToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = meritToken;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.withCredentials = true;

    function meritDropdownParts(id) {
        const input = document.getElementById(id);
        const root = input?.closest('[data-dropdown-select]');

        return {
            input,
            root,
            label: root?.querySelector('[data-dropdown-select-label]'),
            menu: root?.querySelector('[data-dropdown-select-menu]'),
        };
    }

    function selectedMeritId(id) {
        return meritDropdownParts(id).input?.value || '';
    }

    function setMeritDropdownValue(id, value = '', label = null) {
        const parts = meritDropdownParts(id);
        if (!parts.input) return;

        const selected = Array.from(parts.menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(value || ''));
        parts.input.value = value || '';
        if (parts.label) {
            parts.label.textContent = label ?? selected?.textContent.trim() ?? parts.label.dataset.placeholder ?? 'Select...';
        }

        parts.menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            const isSelected = option === selected;
            option.classList.toggle('bg-slate-100', isSelected);
            option.classList.toggle('text-slate-900', isSelected);
            option.classList.toggle('text-slate-800', !isSelected);
            option.setAttribute('aria-selected', String(isSelected));
        });
    }

    function fillMeritOptions(id, data, valueField, labelField = valueField) {
        const parts = meritDropdownParts(id);
        if (!parts.menu) return;

        parts.menu.innerHTML = '';
        (data || []).forEach(item => {
            const option = document.createElement('button');
            const value = item[valueField] ?? '';
            const label = item[labelField] ?? value;
            option.type = 'button';
            option.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
            option.dataset.value = String(value);
            option.dataset.optionId = String(item.id ?? '');
            option.setAttribute('data-dropdown-select-option', '');
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');
            option.textContent = label;
            option.addEventListener('click', () => {
                setMeritDropdownValue(id, option.dataset.value, option.textContent.trim());
                parts.menu.classList.add('hidden');
                parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                parts.input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            parts.menu.appendChild(option);
        });

        setMeritDropdownValue(id);
    }

    function clearMeritDropdowns(ids) {
        ids.forEach(id => fillMeritOptions(id, [], ''));
    }

    function fetchMeritClasses() {
        return axios.get('/api/get-school-classes').then(response => {
            fillMeritOptions('meritClass', response.data.data || [], 'id', 'class_name');
        });
    }

    function handleMeritClassChange() {
        const classId = selectedMeritId('meritClass');
        clearMeritDropdowns(['meritGroup', 'meritSection', 'meritSession', 'meritExam']);
        if (!classId) return;

        axios.get('/api/get-school-groups', { params: { class_id: classId } })
            .then(response => fillMeritOptions('meritGroup', response.data.data || [], 'id', 'group_name'))
            .catch(showMeritError);
    }

    function handleMeritGroupChange() {
        const classId = selectedMeritId('meritClass');
        const groupId = selectedMeritId('meritGroup');
        clearMeritDropdowns(['meritSection', 'meritSession', 'meritExam']);
        if (!classId || !groupId) return;

        axios.get('/api/get-school-sections', { params: { class_id: classId, group_id: groupId } })
            .then(response => fillMeritOptions('meritSection', response.data.data || [], 'id', 'section_name'))
            .catch(showMeritError);
    }

    function handleMeritSectionChange() {
        const classId = selectedMeritId('meritClass');
        const groupId = selectedMeritId('meritGroup');
        const sectionId = selectedMeritId('meritSection');
        clearMeritDropdowns(['meritSession', 'meritExam']);
        if (!classId || !groupId || !sectionId) return;

        axios.get('/api/get-school-sessions', { params: { class_id: classId, group_id: groupId, section_id: sectionId } })
            .then(response => fillMeritOptions('meritSession', response.data.data || [], 'id', 'session_year'))
            .catch(showMeritError);
    }

    function handleMeritSessionChange() {
        const classId = selectedMeritId('meritClass');
        const groupId = selectedMeritId('meritGroup');
        const sectionId = selectedMeritId('meritSection');
        const sessionId = selectedMeritId('meritSession');
        clearMeritDropdowns(['meritExam']);
        if (!classId || !groupId || !sectionId || !sessionId) return;

        axios.get('/api/get-school-exams', { params: { class_id: classId, group_id: groupId, section_id: sectionId, session_id: sessionId } })
            .then(response => fillMeritOptions('meritExam', response.data.data || [], 'id', 'exam_name'))
            .catch(showMeritError);
    }

    function openMeritModal() {
        document.getElementById('meritListModal')?.classList.remove('hidden');
        fetchMeritClasses().catch(showMeritError);
    }

    function closeMeritModal() {
        document.getElementById('meritListModal')?.classList.add('hidden');
    }

    function showMeritError(error) {
        Swal.fire({
            icon: 'error',
            title: 'Unable to load filters',
            text: error.response?.data?.message || 'Please try again.',
        });
    }

    function formatMeritNumber(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '0';
        return Number.isInteger(number) ? String(number) : number.toFixed(2).replace(/\.?(0+)$/, '');
    }

    function escapeMeritHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    function renderMeritList(payload) {
        meritPayload = payload;
        renderMeritRows();
    }

    function currentMeritRows() {
        const desktopQuery = document.getElementById('meritSearch')?.value || '';
        const mobileQuery = document.getElementById('meritSearchMobile')?.value || '';
        const query = (desktopQuery || mobileQuery).trim().replace(/\s+/g, ' ').toLowerCase();
        const rows = meritPayload?.rows || [];

        if (!query) return rows;

        return rows.filter(row => [
            row.class, row.group, row.section, row.session, row.exam,
            row.student_id, row.student_name, row.total_marks,
            row.obtained_marks, row.merit_serial,
        ].some(value => String(value ?? '').toLowerCase().includes(query)));
    }

    function renderMeritRows() {
        if (!meritPayload) return;

        const payload = meritPayload;
        const body = document.getElementById('meritListBody');
        const rows = currentMeritRows();
        const summary = document.getElementById('meritResultSummary');

        summary.textContent = `${payload.filters.class} / ${payload.filters.group} / ${payload.filters.section} / ${payload.filters.session} / ${payload.filters.exam} · Sort: ${payload.filters.sort_order === 'last' ? 'Last first' : 'First first'} · ${rows.length} students`;
        summary.classList.remove('hidden');

        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No merit records found.</td></tr>';
            return;
        }

        body.innerHTML = rows.map(row => `
            <tr class="hover:bg-slate-50">
                <td class="border border-gray-300 px-3 py-2 text-center">${formatMeritNumber(row.sl)}</td>
                <td class="border border-gray-300 px-3 py-2">${escapeMeritHtml(row.class)}</td>
                <td class="border border-gray-300 px-3 py-2">${escapeMeritHtml(row.group)}</td>
                <td class="border border-gray-300 px-3 py-2">${escapeMeritHtml(row.section)}</td>
                <td class="border border-gray-300 px-3 py-2 text-center">${escapeMeritHtml(row.session)}</td>
                <td class="border border-gray-300 px-3 py-2">${escapeMeritHtml(row.exam)}</td>
                <td class="border border-gray-300 px-3 py-2 font-mono">${escapeMeritHtml(row.student_id)}</td>
                <td class="border border-gray-300 px-3 py-2 font-semibold">${escapeMeritHtml(row.student_name)}</td>
                <td class="border border-gray-300 px-3 py-2 text-center">${formatMeritNumber(row.total_marks)}</td>
                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">${formatMeritNumber(row.obtained_marks)}</td>
                <td class="border border-gray-300 px-3 py-2 text-center font-bold text-blue-700">${formatMeritNumber(row.merit_serial)}</td>
            </tr>`).join('');
    }

    function restoreMeritSearch() {
        ['meritSearch', 'meritSearchMobile'].forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = '';
        });
        renderMeritRows();
    }

    let meritSearchTimer;

    function handleMeritSearchInput(event) {
        const value = event.target.value;
        const otherId = event.target.id === 'meritSearch' ? 'meritSearchMobile' : 'meritSearch';
        const otherInput = document.getElementById(otherId);

        if (otherInput) otherInput.value = value;

        window.clearTimeout(meritSearchTimer);
        meritSearchTimer = window.setTimeout(renderMeritRows, 150);
    }

    function exportMeritList(format) {
        if (format === 'pdf') {
            exportMeritPdf();
            return;
        }

        if (format === 'print') {
            window.print();
            return;
        }

        if (format !== 'excel') return;

        const rows = currentMeritRows();
        const headings = ['SL', 'Class', 'Group', 'Section', 'Session', 'Exam', 'Student ID', 'Student Name', 'Total Marks', 'Obtained Marks', 'Merit Serial'];
        const csv = [headings, ...rows.map(row => [
            row.sl, row.class, row.group, row.section, row.session, row.exam,
            row.student_id, row.student_name, row.total_marks,
            row.obtained_marks, row.merit_serial,
        ])].map(row => row.map(value => `"${String(value ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');

        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
        link.download = 'merit-list.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function exportMeritPdf() {
        if (!validateMeritFilters()) return;

        axios.post('/api/school-merit-list/export-pdf', {
            class_id: selectedMeritId('meritClass'),
            group_id: selectedMeritId('meritGroup'),
            section_id: selectedMeritId('meritSection'),
            session_id: selectedMeritId('meritSession'),
            exam_id: selectedMeritId('meritExam'),
            sort_order: selectedMeritId('meritSortOrder'),
        }, { responseType: 'blob' }).then(response => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(response.data);
            link.download = 'merit-list.pdf';
            link.click();
            URL.revokeObjectURL(link.href);
        }).catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'PDF export failed',
                text: 'Unable to generate the merit list PDF. Please try again.',
            });
        });
    }

    function validateMeritFilters() {
        const fields = [
            ['meritClass', 'Class'],
            ['meritGroup', 'Group'],
            ['meritSection', 'Section'],
            ['meritSession', 'Session'],
            ['meritExam', 'Exam'],
            ['meritSortOrder', 'Sort Order'],
        ];
        const missing = fields.find(([id]) => !selectedMeritId(id));
        if (missing) {
            Swal.fire({ icon: 'warning', title: 'Incomplete filters', text: `${missing[1]} is required.` });
            return false;
        }
        return true;
    }

    function initMeritExportDropdown() {
        const button = document.getElementById('btnMeritExport');
        const menu = document.getElementById('meritExportDropdown');
        if (!button || !menu) return;

        const closeMenu = () => {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        };

        button.addEventListener('click', event => {
            event.stopPropagation();
            const open = menu.classList.contains('hidden');

            menu.classList.toggle('hidden', !open);
            button.setAttribute('aria-expanded', String(open));
        });

        menu.addEventListener('click', closeMenu);
        document.addEventListener('click', event => {
            if (!menu.contains(event.target) && event.target !== button) closeMenu();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenu();
        });
    }

    const initMeritListPage = () => {
        if (document.body.dataset.meritListReady === 'true') return;
        document.body.dataset.meritListReady = 'true';

        initMeritExportDropdown();
        document.getElementById('meritSearch')?.addEventListener('input', handleMeritSearchInput);
        document.getElementById('meritSearchMobile')?.addEventListener('input', handleMeritSearchInput);
        document.getElementById('meritClass')?.addEventListener('change', handleMeritClassChange);
        document.getElementById('meritGroup')?.addEventListener('change', handleMeritGroupChange);
        document.getElementById('meritSection')?.addEventListener('change', handleMeritSectionChange);
        document.getElementById('meritSession')?.addEventListener('change', handleMeritSessionChange);
        document.getElementById('closeMeritListModal')?.addEventListener('click', closeMeritModal);
        fetchMeritClasses().catch(showMeritError);

        document.getElementById('meritListForm')?.addEventListener('submit', event => {
            event.preventDefault();
            if (!validateMeritFilters()) return;

            const button = document.getElementById('generateMeritListButton');
            button.disabled = true;
            button.textContent = 'Generating...';

            axios.post('/api/school-merit-list', {
                class_id: selectedMeritId('meritClass'),
                group_id: selectedMeritId('meritGroup'),
                section_id: selectedMeritId('meritSection'),
                session_id: selectedMeritId('meritSession'),
                exam_id: selectedMeritId('meritExam'),
                sort_order: selectedMeritId('meritSortOrder'),
            }).then(response => {
                renderMeritList(response.data);
                closeMeritModal();
            }).catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Merit List Error',
                    text: error.response?.data?.message || 'Unable to generate the merit list.',
                });
            }).finally(() => {
                button.disabled = false;
                button.textContent = 'Generate Merit List';
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMeritListPage, { once: true });
    } else {
        initMeritListPage();
    }
</script>
@endsection
