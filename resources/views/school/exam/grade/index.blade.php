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
        action="#"
        method="GET"
        :enctype="null"
        class="exam-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <div class="relative">
            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total Mark (Full Mark)</label>
            <x-input.dropdown-select
                id="filter_full_mark"
                placeholder="All Marks"
                :options="[
                    ['value' => '', 'label' => 'All Marks'],
                    ['value' => '100', 'label' => '100 Mark Grade'],
                    ['value' => '50', 'label' => '50 Mark Grade'],
                ]"
            />
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
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
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${item.mark_from ?? '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${item.mark_to ?? '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.grade_name || '-'}">${item.grade_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${item.grade_point ?? '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${item.full_mark ?? '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                    <button type="button" title="Edit" onclick="editGrade(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                        <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete" onclick="deleteGrade(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                        <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
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
            document.getElementById('gradeSearch')?.addEventListener('input', () => fetchGrades(1));

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

        document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
            document.getElementById('gradeSearch').value = '';
            currentPage = 1;
            fetchGrades(1);
        });

        document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
            document.getElementById('gradeSearchMobile').value = '';
            currentPage = 1;
            fetchGrades(1);
        });

        fetchGrades();
    </script>
@endsection
