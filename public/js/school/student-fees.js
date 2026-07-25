const feesApi = axios.create({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

let currentPage = 1;
let classesList = [];

async function preloadData() {
    try {
        const res = await feesApi.get('/api/get-school-classes');
        classesList = res.data.data || [];
        populateDropdown('classFilter', classesList, 'id', 'class_name', 'All Classes');
    } catch (e) {
        console.error("Preload failed", e);
    }
    fetchFees(1);
}

function populateDropdown(elemId, data, valKey, textKey, defaultText) {
    const s = document.getElementById(elemId);
    if (!s) return;
    let html = `<option value="">${defaultText}</option>`;
    data.forEach(item => html += `<option value="${item[valKey]}">${item[textKey]}</option>`);
    s.innerHTML = html;
}

function fetchFees(page = 1) {
    currentPage = page;
    const params = {
        page: page,
        search: document.getElementById('feeSearch').value,
        class_id: document.getElementById('classFilter').value,
        session_id: document.getElementById('sessionFilter').value,
        fee_type_name: document.getElementById('feeTypeFilter').value,
        status: document.getElementById('statusFilter').value,
    };

    feesApi.get('/api/student-fees', { params })
        .then(res => {
            const meta = res.data;
            const items = res.data.data || [];
            const tbody = document.getElementById('feeTableBody');

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" class="text-center py-6 text-gray-400">No student fees found.</td></tr>';
                renderPagination(meta);
                return;
            }

            let html = '';
            items.forEach((item, index) => {
                const student = item.student || {};
                const studentName = student.student_name || 'N/A';
                const studentIdNumber = student.student_id_number || 'N/A';
                const className = student.school_class?.class_name || student.class_name || 'N/A';

                let totalPaid = item.total_paid || 0;
                let remainingDue = item.remaining_due || parseFloat(item.amount) - totalPaid;
                if (remainingDue < 0) remainingDue = 0;

                const statusClass = `status-${item.status || 'pending'}`;

                html += `
                <tr class="hover:bg-slate-50 transition-colors text-[11px]">
                    <td>${(meta.from || 0) + index}</td>
                    <td class="text-gray-700">${studentName}</td>
                    <td class="text-gray-500">${studentIdNumber}</td>
                    <td class="text-gray-600">${className}</td>
                    <td class="text-gray-600">${item.fee_type_name}</td>
                    <td class="text-gray-600">${item.fee_name || '---'}</td>
                    <td class="text-gray-700 font-medium">${parseFloat(item.amount).toFixed(2)}</td>
                    <td class="text-emerald-600">${totalPaid.toFixed(2)}</td>
                    <td class="${remainingDue > 0 ? 'text-red-500 font-medium' : 'text-gray-500'}">${remainingDue.toFixed(2)}</td>
                    <td><span class="status-badge ${statusClass}">${item.status}</span></td>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <button onclick="editFee(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteFee(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
            renderPagination(meta);
        }).catch(() => {
            document.getElementById('feeTableBody').innerHTML = '<tr><td colspan="11" class="text-center py-6 text-red-400">Error loading data.</td></tr>';
        });
}

async function editFee(id) {
    try {
        const res = await feesApi.get(`/api/student-fees/${id}`);
        const item = res.data;
        document.getElementById('feeForm').reset();
        document.getElementById('fee_id').value = item.id;
        document.getElementById('modalTitle').innerText = 'Edit Student Fee';
        document.getElementById('amount').value = item.amount || '';
        document.getElementById('pay_date').value = item.pay_date ? item.pay_date.split('T')[0] : '';
        document.getElementById('fee_name_input').value = item.fee_name || '';
        document.getElementById('status_input').value = item.status || 'pending';
        document.getElementById('feeModal').classList.remove('hidden');
    } catch (e) {
        Swal.fire('Error', 'Failed to fetch record.', 'error');
    }
}

function deleteFee(id) {
    Swal.fire({
        title: 'Delete this fee record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            feesApi.delete(`/api/student-fees/${id}`).then(() => {
                Toastify({
                    text: "Deleted Successfully",
                    style: { background: "#ef4444" }
                }).showToast();
                fetchFees(currentPage);
            });
        }
    });
}

document.getElementById('feeForm').onsubmit = function(e) {
    e.preventDefault();
    const rawId = document.getElementById('fee_id').value;
    const saveBtn = document.getElementById('saveBtn');

    const data = {
        amount: document.getElementById('amount').value,
        pay_date: document.getElementById('pay_date').value,
        fee_name: document.getElementById('fee_name_input').value || null,
        status: document.getElementById('status_input').value,
    };

    saveBtn.disabled = true;

    feesApi.put(`/api/student-fees/${rawId}`, data)
        .then((res) => {
            Toastify({
                text: res.data.message || "Fee Updated Successfully",
                style: { background: "#10b981" }
            }).showToast();
            closeFeeModal();
            fetchFees(currentPage);
        }).catch(err => {
            Swal.fire('Error', err.response?.data?.message || 'Update failed.', 'error');
        }).finally(() => saveBtn.disabled = false);
};

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const parts = dateString.split('T')[0];
    if (!parts) return dateString;
    const [year, month, day] = parts.split('-');
    return (year && month && day) ? `${day}/${month}/${year}` : dateString;
}

function renderPagination(meta) {
    const info = document.getElementById('paginationInfo');
    const controls = document.getElementById('paginationControls');
    info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
    controls.innerHTML = '';
    if (!meta.total || meta.total === 0) return;

    const createBtn = (content, page, active = false, disabled = false) => {
        const btn = document.createElement('button');
        btn.className = `pagination-btn ${active ? 'active' : ''}`;
        btn.innerHTML = content;
        if (disabled) btn.disabled = true;
        if (!disabled && !active) btn.onclick = () => fetchFees(page);
        return btn;
    };

    const btnGroup = document.createElement('div');
    btnGroup.className = 'flex items-center gap-1';
    btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-left text-lg"></i>', meta.current_page - 1, false, meta.current_page === 1));

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        btnGroup.appendChild(createBtn(i, i, i === meta.current_page));
    }

    btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-right text-lg"></i>', meta.current_page + 1, false, meta.current_page === meta.last_page));
    controls.appendChild(btnGroup);
}

function closeFeeModal() {
    document.getElementById('feeModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnFilter').addEventListener('click', () => document.getElementById('filterModal').classList.remove('hidden'));
    document.getElementById('applyFilter').addEventListener('click', () => {
        fetchFees(1);
        document.getElementById('filterModal').classList.add('hidden');
    });
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('classFilter').value = '';
        document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';
        document.getElementById('feeTypeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        fetchFees(1);
        document.getElementById('filterModal').classList.add('hidden');
    });

    document.getElementById('feeSearch').addEventListener('input', () => fetchFees(1));
    document.getElementById('feeSearchMobile').addEventListener('input', function() {
        document.getElementById('feeSearch').value = this.value;
        fetchFees(1);
    });

    const closeFeeModalBtn = document.getElementById('closeFeeModal');
    if (closeFeeModalBtn) {
        closeFeeModalBtn.addEventListener('click', () => closeFeeModal());
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
    };
});

preloadData();
