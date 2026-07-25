@extends('layouts.teacher')

@section('title', 'Student List')
@section('page-title', 'Student List')

@section('content')
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <!-- Header with Filter & Search controls -->
    <div class="px-6 py-4 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-800">
            Student Directory
        </h3>
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" id="studentSearch" 
                class="border border-gray-200 text-xs px-3 py-1.5 h-[32px] w-[200px]" 
                placeholder="Search Student..." style="border-radius: 0;" />

            <select id="classFilter" class="border border-gray-200 text-xs px-2 py-1.5 h-[32px]" style="border-radius: 0;">
                <option value="">All Classes</option>
            </select>

            <select id="groupFilter" class="border border-gray-200 text-xs px-2 py-1.5 h-[32px]" style="border-radius: 0;">
                <option value="">All Groups</option>
            </select>

            <select id="sectionFilter" class="border border-gray-200 text-xs px-2 py-1.5 h-[32px]" style="border-radius: 0;">
                <option value="">All Sections</option>
            </select>
        </div>
    </div>

    <!-- Table container -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="min-w-full text-xs text-left text-gray-500">
            <thead class="text-[10px] text-gray-400 uppercase bg-gray-50 tracking-wider font-semibold border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">SL</th>
                    <th scope="col" class="px-6 py-3 text-center">Photo</th>
                    <th scope="col" class="px-6 py-3">Student Name</th>
                    <th scope="col" class="px-6 py-3">ID Number</th>
                    <th scope="col" class="px-6 py-3">Class</th>
                    <th scope="col" class="px-6 py-3">Group</th>
                    <th scope="col" class="px-6 py-3">Section</th>
                    <th scope="col" class="px-6 py-3">Mobile Number</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading students...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination/Info footer -->
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
        <div class="text-[11px] text-gray-500" id="paginationInfo">
            Showing 0 of 0 entries
        </div>
        <div class="flex items-center gap-1" id="paginationControls">
            <!-- Buttons dynamically generated -->
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let currentPage = 1;

    // Load filter options
    function loadFilters() {
        // Load classes
        axios.get('{{ url('/api/get-classes') }}')
        .then(res => {
            const select = document.getElementById('classFilter');
            const data = res.data.data || res.data;
            data.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.class_name}</option>`;
            });
        });

        // Load groups
        axios.get('{{ url('/api/get-groups') }}')
        .then(res => {
            const select = document.getElementById('groupFilter');
            const data = res.data.data || res.data;
            data.forEach(g => {
                select.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
            });
        });

        // Load sections
        axios.get('{{ url('/api/get-sections') }}')
        .then(res => {
            const select = document.getElementById('sectionFilter');
            const data = res.data.data || res.data;
            data.forEach(s => {
                select.innerHTML += `<option value="${s.id}">${s.section_name}</option>`;
            });
        });
    }

    function fetchStudents(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('studentTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading students...
                </td>
            </tr>`;

        const search = document.getElementById('studentSearch').value;
        const classVal = document.getElementById('classFilter').value;
        const groupVal = document.getElementById('groupFilter').value;
        const sectionVal = document.getElementById('sectionFilter').value;

        axios.get('{{ url('/api/school/students') }}', {
            params: {
                search,
                class: classVal,
                group: groupVal,
                section: sectionVal,
                page
            }
        })
        .then(res => {
            const data = res.data.data || res.data;
            const meta = res.data.meta || {
                current_page: 1,
                last_page: 1,
                total: data.length,
                from: 1,
                to: data.length
            };

            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                            No students found.
                        </td>
                    </tr>`;
                renderPagination(meta);
                return;
            }

            data.forEach((student, index) => {
                const sl = ((meta.current_page - 1) * (meta.per_page || 10)) + index + 1;
                const photoUrl = student.image ? `/storage/${student.image}` : 
                    'https://ui-avatars.com/api/?background=random&name=' + student.student_name;
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 border-b border-gray-100 transition-colors">
                        <td class="px-6 py-3 text-center font-medium text-gray-900">${sl}</td>
                        <td class="px-6 py-3 text-center">
                            <img src="${photoUrl}" alt="${student.student_name}" class="w-8 h-8 rounded-full mx-auto object-cover border border-gray-100" />
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-900">${student.student_name}</td>
                        <td class="px-6 py-3 font-mono text-gray-600">${student.student_id_number || 'PENDING'}</td>
                        <td class="px-6 py-3">${student.class_name || 'N/A'}</td>
                        <td class="px-6 py-3">${student.group_name || 'N/A'}</td>
                        <td class="px-6 py-3">${student.section_name || 'N/A'}</td>
                        <td class="px-6 py-3">${student.mobile}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold rounded border ${
                                student.status === 'Active' || student.status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100'
                            }">
                                ${student.status || 'Pending'}
                            </span>
                        </td>
                    </tr>`;
            });

            renderPagination(meta);
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-red-500">
                        Failed to load students.
                    </td>
                </tr>`;
            console.error(err);
        });
    }

    function renderPagination(meta) {
        const controls = document.getElementById('paginationControls');
        const info = document.getElementById('paginationInfo');
        info.innerText = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0} entries`;
        controls.innerHTML = '';

        if (!meta.total) return;

        const prevBtn = document.createElement('button');
        prevBtn.className = 'w-8 h-8 rounded border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-50';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left text-[10px]"></i>';
        prevBtn.disabled = meta.current_page === 1;
        prevBtn.onclick = () => fetchStudents(meta.current_page - 1);
        controls.appendChild(prevBtn);

        for (let i = 1; i <= meta.last_page; i++) {
            const pgBtn = document.createElement('button');
            pgBtn.className = `w-8 h-8 rounded border text-xs font-semibold transition ${
                meta.current_page === i ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-600 hover:bg-gray-50'
            }`;
            pgBtn.innerText = i;
            pgBtn.onclick = () => fetchStudents(i);
            controls.appendChild(pgBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'w-8 h-8 rounded border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-50';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right text-[10px]"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.onclick = () => fetchStudents(meta.current_page + 1);
        controls.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadFilters();
        fetchStudents();

        document.getElementById('studentSearch').addEventListener('input', () => fetchStudents(1));
        document.getElementById('classFilter').addEventListener('change', () => fetchStudents(1));
        document.getElementById('groupFilter').addEventListener('change', () => fetchStudents(1));
        document.getElementById('sectionFilter').addEventListener('change', () => fetchStudents(1));
    });
</script>
@endpush
