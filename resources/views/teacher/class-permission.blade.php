@extends('layouts.teacher')

@section('title', 'Class Permissions')
@section('page-title', 'Class Permissions')

@section('content')
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 bg-white flex flex-row items-center justify-between">
        <h3 class="text-base font-semibold text-gray-800">
            Assigned Class & Subject Permissions
        </h3>
    </div>

    <!-- Table container -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="min-w-full text-xs text-left text-gray-500">
            <thead class="text-[10px] text-gray-400 uppercase bg-gray-50 tracking-wider font-semibold border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">SL</th>
                    <th scope="col" class="px-6 py-3">Class</th>
                    <th scope="col" class="px-6 py-3">Group</th>
                    <th scope="col" class="px-6 py-3">Section</th>
                    <th scope="col" class="px-6 py-3">Subject</th>
                </tr>
            </thead>
            <tbody id="permissionTableBody">
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading permissions...
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

    function fetchPermissions(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('permissionTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading permissions...
                </td>
            </tr>`;

        axios.get('{{ url('/api/teacher-permissions') }}', {
            params: { page }
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
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            No class permissions assigned to you.
                        </td>
                    </tr>`;
                renderPagination(meta);
                return;
            }

            data.forEach((item, index) => {
                const sl = ((meta.current_page - 1) * (meta.per_page || 10)) + index + 1;
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 border-b border-gray-100 transition-colors">
                        <td class="px-6 py-3.5 text-center font-medium text-gray-900">${sl}</td>
                        <td class="px-6 py-3.5 font-medium text-gray-900">${item.school_class?.class_name || 'N/A'}</td>
                        <td class="px-6 py-3.5">${item.school_group?.group_name || 'N/A'}</td>
                        <td class="px-6 py-3.5">${item.school_section?.section_name || 'N/A'}</td>
                        <td class="px-6 py-3.5 font-semibold text-blue-600">${item.school_subject?.subject_name || 'N/A'}</td>
                    </tr>`;
            });

            renderPagination(meta);
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-red-500">
                        Failed to load class permissions.
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
        prevBtn.onclick = () => fetchPermissions(meta.current_page - 1);
        controls.appendChild(prevBtn);

        for (let i = 1; i <= meta.last_page; i++) {
            const pgBtn = document.createElement('button');
            pgBtn.className = `w-8 h-8 rounded border text-xs font-semibold transition ${
                meta.current_page === i ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-600 hover:bg-gray-50'
            }`;
            pgBtn.innerText = i;
            pgBtn.onclick = () => fetchPermissions(i);
            controls.appendChild(pgBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'w-8 h-8 rounded border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-50';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right text-[10px]"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.onclick = () => fetchPermissions(meta.current_page + 1);
        controls.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchPermissions();
    });
</script>
@endpush
