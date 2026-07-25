<script>
    const studentModal = document.getElementById('studentModal');
    const detailsModal = document.getElementById('detailsModal');
    const filterModal  = document.getElementById('filterModal');
    const exportModal  = document.getElementById('exportModal');
    const photoInput   = document.getElementById('photoInput');
    const imagePreview = document.getElementById('imagePreview');
    const detailsForm  = document.getElementById('detailsForm');
    let currentPage    = 1;

    function populateDropdownSelect(id, options, selectedValue, placeholder = 'Select...') {
        const input = document.getElementById(id);
        const button = document.getElementById(id + 'Button');
        const label = button ? button.querySelector('[data-dropdown-select-label]') : null;
        const menu = document.getElementById(id + 'Menu');

        if (!input || !menu) return;

        let menuHtml = '';
        let selectedText = placeholder;

        options.forEach(opt => {
            const isSelected = String(opt.value) === String(selectedValue);
            if (isSelected) {
                selectedText = opt.label;
            }
            menuHtml += `
                <button
                    type="button"
                    class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 ${isSelected ? 'bg-slate-100 text-slate-900' : 'text-slate-800'}"
                    data-value="${opt.value}"
                    role="option"
                    aria-selected="${isSelected ? 'true' : 'false'}"
                    data-dropdown-select-option
                >
                    ${opt.label}
                </button>
            `;
        });

        menu.innerHTML = menuHtml;
        input.value = selectedValue || '';
        if (label) {
            label.textContent = selectedText;
        }

        menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            option.addEventListener('click', () => {
                input.value = option.dataset.value || '';
                if (label) {
                    label.textContent = option.textContent.trim();
                }

                menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                    const isSel = item === option;
                    item.classList.toggle('bg-slate-100', isSel);
                    item.classList.toggle('text-slate-900', isSel);
                    item.classList.toggle('text-slate-800', !isSel);
                    item.setAttribute('aria-selected', String(isSel));
                });

                const root = input.closest('[data-dropdown-select]');
                if (root) {
                    root.classList.remove('is-open');
                }
                menu.classList.add('hidden');
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('rotate-180');
                    }
                }

                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    const geoApi = axios.create({
        baseURL: 'https://bdapis.com/api/v1.2'
    });

    function setSelectedValue(id, value) {
        const input = document.getElementById(id);
        if (!input) return;
        input.value = value;
        const button = document.getElementById(id + 'Button');
        const label = button ? button.querySelector('[data-dropdown-select-label]') : null;
        const menu = document.getElementById(id + 'Menu');
        if (menu) {
            let foundLabel = '';
            menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                const isSel = String(item.dataset.value) === String(value);
                item.classList.toggle('bg-slate-100', isSel);
                item.classList.toggle('text-slate-900', isSel);
                item.classList.toggle('text-slate-800', !isSel);
                item.setAttribute('aria-selected', String(isSel));
                if (isSel) {
                    foundLabel = item.textContent.trim();
                }
            });
            if (label) {
                const placeholderEl = button.querySelector('[data-placeholder]');
                const placeholderText = placeholderEl ? placeholderEl.dataset.placeholder : 'Select...';
                label.textContent = foundLabel || placeholderText;
            }
        }
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    if (photoInput && imagePreview) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML =
                    `<img src="${e.target.result}" class="w-full h-full object-cover" />`;
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            }
        });
    }

    function fetchStudents(page = 1) {
        currentPage = page;
        const search = document.getElementById('studentSearch')?.value || '';
        const classVal = document.getElementById('classFilter')?.value || '';
        const groupVal = document.getElementById('groupFilter')?.value || '';
        const sectionVal = document.getElementById('sectionFilter')?.value || '';
        const sessionVal = document.getElementById('sessionFilter')?.value || '';

        axios.get('{{ url('/api/school/students') }}', {
                params: {
                    search,
                    class: classVal,
                    group: groupVal,
                    section: sectionVal,
                    session: sessionVal,
                    page
                }
            })
            .then(res => {
                const students = res.data.data || [];
                const meta = res.data;
                const tbody = document.getElementById('studentTableBody');
                if (!tbody) return;
                tbody.innerHTML = '';

                students.forEach((s, index) => {
                    const sl = (meta.current_page - 1) * meta.per_page + (index + 1);
                    const photoUrl = s.image ? `/storage/${s.image}` :
                        `https://ui-avatars.com/api/?background=random&name=${s.student_name}`;
                    let statusText = s.status || 'Active';
                    if (statusText === 'Inactive') statusText = 'Unactive';
                    if (statusText === 'pending') statusText = 'Pending';
                    let badgeClass = 'badge-active';
                    if (statusText === 'Unactive') badgeClass = 'badge-inactive';
                    if (statusText === 'Pending') badgeClass = 'badge-pending';
                    tbody.innerHTML += `
            <tr>
                <td>${sl}</td>
                <td>
                    <img src="${photoUrl}" class="table-photo" style="border-radius: 50% !important; width: 24px !important; height: 24px !important; object-fit: cover !important;" />
                </td>
                <td><span class="student-id">${s.student_id_number}</span></td>
                <td><span class="student-name">${s.student_name}</span></td>
                <td><span class="cell-data">${s.mobile || '-'}</span></td>
                <td><span class="student-father-name">${s.father_name || '-'}</span></td>
                <td><span class="cell-data">${s.class_name || '-'}</span></td>
                <td><span class="cell-data">${s.group_name || '-'}</span></td>
                <td><span class="cell-data">${s.section_name || '-'}</span></td>
                <td><span class="cell-data">${s.session_year || '-'}</span></td>
                <td><span class="cell-data">${s.student_type || 'Admission'}</span></td>
                <td>
                    <span class="${badgeClass}">
                        ${statusText}
                    </span>
                </td>
                <td>
                    <div class="action-buttons flex justify-center gap-2">
                        <button onclick="viewAdmissionForm(${s.id}, ${sl})" title="View" class="btn-action view text-emerald-500">
                            <i class="far fa-eye" style="font-size: 15px;"></i>
                        </button>
                        <button onclick="editStudent(${s.id})" title="Edit" class="btn-action edit text-blue-500">
                            <i class="far fa-edit" style="font-size: 15px;"></i>
                        </button>
                        <button onclick="deleteStudent(${s.id}, \`${s.student_name.replace(/`/g, '\\`').replace(/"/g, '\\"')}\`)" title="Delete" class="btn-action delete text-red-400">
                            <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                        </button>
                        <button onclick="toggleStudentStatus(${s.id}, '${s.status}', '${s.student_name}', '${s.student_id_number}', '${s.inactive_date || ''}')"
                                title="${s.status === 'Inactive' ? 'Activate Student' : 'Deactivate Student'}"
                                class="btn-action status">
                            <i class="fas fa-circle" style="font-size: 15px; color: ${s.status === 'Inactive' ? '#ef4444' : '#10b981'} !important;"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
                });

                renderPagination(meta);
            }).catch(e => console.error("Load failed", e));
    }

    function renderPagination(meta) {
        const controls = document.getElementById('paginationControls');
        const info = document.getElementById('paginationInfo');
        if (!info || !controls) return;
        info.innerText = `${meta.to || 0} of ${meta.total}`;
        controls.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
        prevBtn.disabled = meta.current_page === 1;
        prevBtn.onclick = () => fetchStudents(meta.current_page - 1);
        controls.appendChild(prevBtn);

        for (let i = 1; i <= meta.last_page; i++) {
            if (i > 5 && i < meta.last_page) continue;
            const pgBtn = document.createElement('button');
            pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
            pgBtn.innerText = i;
            pgBtn.onclick = () => fetchStudents(i);
            controls.appendChild(pgBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.onclick = () => fetchStudents(meta.current_page + 1);
        controls.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('studentTableBody')) {
            fetchStudents();
        }

        document.getElementById('studentSearch')?.addEventListener('input', () => {
            const val = document.getElementById('studentSearch').value;
            const mobileSearch = document.getElementById('studentSearchMobile');
            if (mobileSearch) mobileSearch.value = val;
            fetchStudents(1);
        });

        document.getElementById('studentSearchMobile')?.addEventListener('input', () => {
            const val = document.getElementById('studentSearchMobile').value;
            const desktopSearch = document.getElementById('studentSearch');
            if (desktopSearch) desktopSearch.value = val;
            fetchStudents(1);
        });
    });
</script>
