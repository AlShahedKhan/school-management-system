@extends('layouts.teacher')

@section('title', 'Class Schedule')
@section('page-title', 'Class Schedule')

@section('content')
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 bg-white flex flex-row items-center justify-between">
        <h3 class="text-base font-semibold text-gray-800">
            My Routine & Class Schedule
        </h3>
    </div>

    <!-- Table container -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="min-w-full text-xs text-left text-gray-500">
            <thead class="text-[10px] text-gray-400 uppercase bg-gray-50 tracking-wider font-semibold border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">SL</th>
                    <th scope="col" class="px-6 py-3">Day</th>
                    <th scope="col" class="px-6 py-3">Time Slot</th>
                    <th scope="col" class="px-6 py-3">Class</th>
                    <th scope="col" class="px-6 py-3">Group</th>
                    <th scope="col" class="px-6 py-3">Section</th>
                    <th scope="col" class="px-6 py-3">Subject</th>
                </tr>
            </thead>
            <tbody id="routineTableBody">
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading schedule...
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

    function fetchRoutines(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('routineTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading schedule...
                </td>
            </tr>`;

        axios.get('{{ url('/api/school/routines') }}', {
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
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No routines or class schedules assigned to you.
                        </td>
                    </tr>`;
                renderPagination(meta);
                return;
            }

            data.forEach((item, index) => {
                const sl = ((meta.current_page - 1) * (meta.per_page || 10)) + index + 1;
                
                // Format time nicely
                const formatTime = (timeStr) => {
                    if (!timeStr) return 'N/A';
                    const parts = timeStr.split(':');
                    if (parts.length < 2) return timeStr;
                    let hours = parseInt(parts[0]);
                    const minutes = parts[1];
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    return `${hours}:${minutes} ${ampm}`;
                };

                const timeSlot = `${formatTime(item.start_time)} - ${formatTime(item.end_time)}`;

                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 border-b border-gray-100 transition-colors">
                        <td class="px-6 py-3.5 text-center font-medium text-gray-900">${sl}</td>
                        <td class="px-6 py-3.5 font-bold text-slate-700 capitalize">${item.day_name}</td>
                        <td class="px-6 py-3.5 font-semibold text-blue-600">${timeSlot}</td>
                        <td class="px-6 py-3.5 font-medium text-gray-900">${item.school_class?.class_name || 'N/A'}</td>
                        <td class="px-6 py-3.5">${item.school_group?.group_name || 'N/A'}</td>
                        <td class="px-6 py-3.5">${item.school_section?.section_name || 'N/A'}</td>
                        <td class="px-6 py-3.5 font-medium text-slate-800">${item.school_subject?.subject_name || 'N/A'}</td>
                    </tr>`;
            });

            renderPagination(meta);
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-red-500">
                        Failed to load class schedule.
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
        prevBtn.onclick = () => fetchRoutines(meta.current_page - 1);
        controls.appendChild(prevBtn);

        for (let i = 1; i <= meta.last_page; i++) {
            const pgBtn = document.createElement('button');
            pgBtn.className = `w-8 h-8 rounded border text-xs font-semibold transition ${
                meta.current_page === i ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-600 hover:bg-gray-50'
            }`;
            pgBtn.innerText = i;
            pgBtn.onclick = () => fetchRoutines(i);
            controls.appendChild(pgBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'w-8 h-8 rounded border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-50';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right text-[10px]"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.onclick = () => fetchRoutines(meta.current_page + 1);
        controls.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchRoutines();
    });
</script>
@endpush
