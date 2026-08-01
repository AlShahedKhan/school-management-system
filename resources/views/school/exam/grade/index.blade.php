@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.exam.grade.partials.header')
            @include('school.exam.grade.partials.table')
        </div>
    </div>

    {{-- Filter Modal --}}
    <x-modal.form
        id="filterModal"
        form-id="gradeFilterForm"
        title="Grade Filter"
        close-button-id="resetFilter"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    >
        <div class="relative">
            <x-input.dropdown-select
                id="filter_full_mark"
                placeholder="All Marks"
                :options="[
                    ['value' => '', 'label' => 'All Marks'],
                    ['value' => '100', 'label' => '100 Mark Grade'],
                    ['value' => '50', 'label' => '50 Mark Grade'],
                ]"
            />
            <x-input.floating-label for="filter_full_mark" :floating="false">
                Total Mark (Full Mark)
            </x-input.floating-label>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.exam.grade.partials.grade-modal')
    @include('school.partials.export-dropdown')

    @include('school.exam.grade.partials.js.modal-open')
    @include('school.exam.grade.partials.js.modal-submit')
    @include('school.exam.grade.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;

        function escapeGradeHtml(value) {
            return String(value ?? '-').replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);
        }

        function gradeTableCell(value, alignment = 'text-center') {
            const content = escapeGradeHtml(value);

            return `
                <td class="h-8 border border-gray-300 px-3 ${alignment}">
                    <div class="school-data-table-cell-scroll" title="${content}">${content}</div>
                </td>`;
        }

        function syncGradeSearch(sourceId) {
            const source = document.getElementById(sourceId);
            const targetId = sourceId === 'gradeSearch' ? 'gradeSearchMobile' : 'gradeSearch';
            const target = document.getElementById(targetId);

            if (source && target) {
                target.value = source.value;
            }
        }

        function restoreGradeSearch() {
            const desktopSearch = document.getElementById('gradeSearch');
            const mobileSearch = document.getElementById('gradeSearchMobile');

            if (desktopSearch) desktopSearch.value = '';
            if (mobileSearch) mobileSearch.value = '';

            currentPage = 1;
            fetchGrades(1);
        }

        function fetchGrades(page = 1) {
            currentPage = page;

            const search = document.getElementById('gradeSearch')?.value || '';
            const full_mark = document.getElementById('filter_full_mark')?.value || '';

            const params = {
                page,
                search,
                full_mark,
            };

            axios.get('/api/school-exam-grades', { params }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('gradeTableBody');
                tbody.innerHTML = '';

                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="7" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No grades found.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                meta.data.forEach((item, i) => {
                    const sl = meta.from ? meta.from + i : i + 1;
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            ${gradeTableCell(sl)}
                            ${gradeTableCell(item.mark_from)}
                            ${gradeTableCell(item.mark_to)}
                            ${gradeTableCell(item.grade_name, 'text-left')}
                            ${gradeTableCell(item.grade_point)}
                            ${gradeTableCell(item.full_mark)}
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="mx-auto flex h-8 items-center justify-center space-x-1">
                                    <button type="button" title="Edit grade" aria-label="Edit grade" onclick="editGrade(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1">
                                        <i class="far fa-edit text-sm" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete grade" aria-label="Delete grade" onclick="deleteGrade(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1">
                                        <i class="far fa-trash-alt text-sm" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });

                renderPagination(meta);
            }).catch(err => {
                console.error('Load Error:', err);
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchGrades(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchGrades(i);
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchGrades(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function deleteGrade(id) {
            Swal.fire({
                title: 'Delete Grade?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-exam-grades/' + id).then(() => {
                        Toastify({ text: 'Grade deleted', style: { background: '#ef4444' }, duration: 3000 }).showToast();
                        fetchGrades(currentPage);
                    }).catch(() => {
                        Swal.fire('Error', 'Could not delete grade.', 'error');
                    });
                }
            });
        }

        function exportData(type) {
            const search = document.getElementById('gradeSearch')?.value || '';
            const full_mark = document.getElementById('filter_full_mark')?.value || '';
            const params = new URLSearchParams({ type, search, full_mark });
            window.location.href = `/api/school-exam-grades-export?${params.toString()}`;
        }

        function resetGradeFilterFullMark() {
            const input = document.getElementById('filter_full_mark');
            const label = document.querySelector('#filter_full_markButton [data-dropdown-select-label]');
            const menu = document.getElementById('filter_full_markMenu');

            if (input) input.value = '';
            if (label) label.textContent = 'All Marks';
            menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                const selected = option.dataset.value === '';
                option.setAttribute('aria-selected', String(selected));
                option.classList.toggle('bg-slate-100', selected);
                option.classList.toggle('text-slate-900', selected);
                option.classList.toggle('text-slate-800', !selected);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('gradeSearch')?.addEventListener('input', event => {
                syncGradeSearch(event.currentTarget.id);
                fetchGrades(1);
            });
            document.getElementById('gradeSearchMobile')?.addEventListener('input', event => {
                syncGradeSearch(event.currentTarget.id);
                fetchGrades(1);
            });

            document.getElementById('exportPdf')?.addEventListener('click', () => exportData('pdf'));
            document.getElementById('exportExcel')?.addEventListener('click', () => exportData('excel'));
            document.getElementById('exportPrint')?.addEventListener('click', () => window.print());

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal').classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                resetGradeFilterFullMark();
                currentPage = 1;
                fetchGrades(1);
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                currentPage = 1;
                fetchGrades(1);
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function (e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });
        });

        document.getElementById('btnRestoreDesktop')?.addEventListener('click', restoreGradeSearch);
        document.getElementById('btnRestoreMobile')?.addEventListener('click', restoreGradeSearch);

        fetchGrades();
    </script>
@endsection
