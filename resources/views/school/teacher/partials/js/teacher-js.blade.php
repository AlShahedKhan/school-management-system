<script>
    const teacherModal = document.getElementById('teacherModal');
    const photoInput = document.getElementById('photoInput');
    const imagePreview = document.getElementById('imagePreview');
    const teacherMobileInput = document.querySelector('#teacherForm input[name="mobile"]');
    const teacherMobileError = document.getElementById('teacherMobileError');
    let currentPage = {{ isset($teachers) ? $teachers->currentPage() : 1 }};
    let currentTeachersList = [];

    function sanitizeEnglishDigits(value) {
        return value.replace(/[^0-9]/g, '');
    }

    function setDropdownValue(id, value, placeholder = 'Select...') {
        const input = document.getElementById(id);
        const label = document.querySelector(`#${id}Button [data-dropdown-select-label]`);
        const menu = document.getElementById(`${id}Menu`);
        if (input) input.value = value || '';

        let selectedText = placeholder;
        if (menu) {
            menu.querySelectorAll('[data-dropdown-select-option]').forEach(opt => {
                const isSelected = (opt.dataset.value || '') === String(value || '');
                opt.classList.toggle('bg-slate-100', isSelected);
                opt.classList.toggle('text-slate-900', isSelected);
                opt.classList.toggle('text-slate-800', !isSelected);
                opt.setAttribute('aria-selected', String(isSelected));
                if (isSelected) {
                    selectedText = opt.textContent.trim();
                }
            });
        }
        if (label) label.textContent = selectedText;
    }

    function setTeacherMobileError(show, message = 'Must provide numbers only.') {
        if (!teacherMobileError || !teacherMobileInput) return;
        teacherMobileError.textContent = message;
        teacherMobileError.classList.toggle('hidden', !show);
        teacherMobileInput.classList.toggle('border-red-500', show);
        teacherMobileInput.classList.toggle('focus:border-red-500', show);
        teacherMobileInput.setCustomValidity(show ? message : '');
    }

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" />`;
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            }
        });
    }

    if (teacherMobileInput) {
        teacherMobileInput.addEventListener('input', function() {
            const sanitizedValue = sanitizeEnglishDigits(this.value);
            const hasInvalidChars = this.value !== sanitizedValue;
            this.value = sanitizedValue;
            setTeacherMobileError(hasInvalidChars);
        });

        teacherMobileInput.addEventListener('paste', function(event) {
            event.preventDefault();
            const pastedText = (event.clipboardData || window.clipboardData).getData('text');
            const sanitizedValue = sanitizeEnglishDigits(pastedText);
            this.value = sanitizedValue;
            setTeacherMobileError(pastedText !== sanitizedValue);
        });
    }

    document.getElementById('openTeacherModal')?.addEventListener('click', () => {
        document.getElementById('teacherForm').reset();
        document.getElementById('teacher_id').value = '';
        setDropdownValue('teacherPayDate', '', 'Select Pay Date...');
        if (imagePreview) imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
        setTeacherMobileError(false);
        teacherModal.classList.remove('hidden');
    });

    document.getElementById('closeTeacherModal')?.addEventListener('click', () => {
        teacherModal.classList.add('hidden');
    });

    const getTeacherSearchValue = () => {
        const desktopSearch = document.getElementById('teacherSearch');
        const mobileSearch = document.getElementById('teacherSearchMobile');
        const activeElement = document.activeElement;

        if (activeElement === mobileSearch) {
            return mobileSearch.value;
        }

        if (activeElement === desktopSearch) {
            return desktopSearch.value;
        }

        return desktopSearch?.value || mobileSearch?.value || '';
    };

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

    const fetchTeachers = (page = 1) => {
        currentPage = page;
        const tbody = document.getElementById('teacherTableBody');

        if (!tbody) {
            return;
        }

        const search = getTeacherSearchValue();
        const teacherId = document.getElementById('teacherFilter')?.value || '';
        const designation = document.getElementById('teacherFilterDesignation')?.value || '';
        const status = document.getElementById('teacherFilterStatus')?.value || '';

        axios.get('{{ route('school.teachers.data') }}', {
                params: {
                    search,
                    teacher_id: teacherId,
                    designation,
                    status,
                    per_page: 30,
                    page
                }
            })
            .then(res => {
                const teachers = res.data.data || (Array.isArray(res.data) ? res.data : []);
                currentTeachersList = teachers;
                const meta = {
                    current_page: res.data.current_page || 1,
                    last_page: res.data.last_page || 1,
                    total: res.data.total !== undefined ? res.data.total : teachers.length,
                    per_page: res.data.per_page || 10,
                    from: res.data.from || (teachers.length ? 1 : 0),
                    to: res.data.to || teachers.length
                };
                tbody.innerHTML = '';
                teachers.forEach((t, index) => {
                    const sl = ((meta.current_page - 1) * (meta.per_page || teachers.length)) + index + 1;
                    const photoUrl = t.photo ? `/storage/${t.photo}` :
                        'https://ui-avatars.com/api/?background=random&name=' + encodeURIComponent(t.name);
                    const statusVal = t.status || 'Active';
                    const statusHtml = statusVal === 'Hold' 
                        ? `<span class="px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Hold</span>`
                        : `<span class="px-2 py-0.5 text-[10px] font-semibold bg-green-100 text-green-700 rounded-full">Active</span>`;

                    let startDate = t.salary_start_date || '-';
                    if (startDate.includes('T')) startDate = startDate.split('T')[0];
                    const salaryFormatted = parseFloat(t.salary_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const safeName = (t.name || '').replace(/"/g, '&quot;');

                    tbody.innerHTML += `
                   <tr class="hover:bg-gray-50">
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center"><img src="${photoUrl}" alt="${safeName}" class="h-6 w-6 rounded-full object-cover inline-block" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(t.name)}';" /></td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 font-mono">${t.id_number || 'PENDING'}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 font-medium text-slate-800">${t.name}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${t.designation || 'N/A'}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><a href="tel:${t.mobile}" class="inline-block text-blue-500">${t.mobile}</a></td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">৳${salaryFormatted}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${startDate}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${t.pay_date || '-'}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${statusHtml}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                            <div class="flex h-6 w-full items-center justify-center space-x-1">
                                <button type="button" onclick="editTeacher(${t.id})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit ${safeName}">
                                    <i class="far fa-edit text-xs"></i>
                                </button>
                                <button type="button" onclick="toggleTeacherStatus(${t.id})" class="text-gray-600 hover:text-slate-800 p-1" title="Toggle Status">
                                    <i class="fas fa-toggle-on text-xs"></i>
                                </button>
                                <button type="button" onclick="deleteTeacher(${t.id})" class="text-red-600 hover:text-red-800 p-1" title="Delete ${safeName}">
                                    <i class="far fa-trash-alt text-xs"></i>
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
        info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
        controls.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
        prevBtn.disabled = meta.current_page === 1;
        prevBtn.onclick = () => fetchTeachers(meta.current_page - 1);
        controls.appendChild(prevBtn);

        for (let i = 1; i <= meta.last_page; i++) {
            if (i > 5 && i < meta.last_page) continue;
            const pgBtn = document.createElement('button');
            pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
            pgBtn.innerText = i;
            pgBtn.onclick = () => fetchTeachers(i);
            controls.appendChild(pgBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.onclick = () => fetchTeachers(meta.current_page + 1);
        controls.appendChild(nextBtn);
    }

    let teacherSearchTimer = null;
    const bindTeacherSearch = (input, peer) => {
        if (!input) return;
        input.addEventListener('input', () => {
            if (peer) peer.value = input.value;
            clearTimeout(teacherSearchTimer);
            teacherSearchTimer = setTimeout(() => fetchTeachers(1), 450);
        });
    };

    bindTeacherSearch(document.getElementById('teacherSearch'), document.getElementById('teacherSearchMobile'));
    bindTeacherSearch(document.getElementById('teacherSearchMobile'), document.getElementById('teacherSearch'));

    document.getElementById('teacherForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const tid = document.getElementById('teacher_id').value;
        const formData = new FormData(this);
        const sanitizedMobile = sanitizeEnglishDigits(formData.get('mobile') || '');
        formData.set('mobile', sanitizedMobile);

        if (!sanitizedMobile) {
            setTeacherMobileError(true);
            Swal.fire({
                icon: 'error',
                title: 'Invalid mobile number',
                text: 'Must provide numbers only.'
            });
            teacherMobileInput.focus();
            return;
        }

        setTeacherMobileError(false);

        if (tid) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = tid ? `{{ url('/api/teachers') }}/${tid}` : '{{ url('/api/teachers') }}';
        axios.post(apiUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((res) => {
                Toastify({
                    text: tid ? "Teacher Updated Successfully!" : "Teacher Registered Successfully!",
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                teacherModal.classList.add('hidden');
                fetchTeachers(currentPage);
            }).catch(err => {
                let errorMsg = 'Action failed';
                if (err.response && err.response.data.errors) {
                    errorMsg = Object.values(err.response.data.errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: errorMsg
                });
            });
    });

    function editTeacher(id) {
        axios.get('{{ url('/api/teachers') }}/' + id).then(res => {
            const t = res.data;
            document.getElementById('teacher_id').value = t.id;
            document.querySelector('#teacherForm input[name="name"]').value = t.name || '';
            document.querySelector('#teacherForm input[name="designation"]').value = t.designation || '';
            document.querySelector('#teacherForm input[name="mobile"]').value = t.mobile || '';
            document.querySelector('#teacherForm input[name="email"]').value = t.email || '';
            document.querySelector('#teacherForm input[name="salary_amount"]').value = t.salary_amount || '';
            
            let startDate = t.salary_start_date || '';
            if (startDate.includes('T')) startDate = startDate.split('T')[0];
            document.querySelector('#teacherForm input[name="salary_start_date"]').value = startDate;

            setDropdownValue('teacherPayDate', t.pay_date || '', 'Select Pay Date...');

            const photoUrl = t.photo ? `/storage/${t.photo}` :
                'https://ui-avatars.com/api/?background=random&name=' + t.name;
            if (imagePreview) imagePreview.innerHTML = `<img src="${photoUrl}" />`;
            teacherModal.classList.remove('hidden');
        });
    }

    function deleteTeacher(id) {
        Swal.fire({
            title: 'Delete Faculty?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'CONFIRM DELETE'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('{{ url('/api/teachers') }}/' + id, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    Toastify({
                        text: "Record Deleted",
                        style: {
                            background: "#ef4444"
                        }
                    }).showToast();
                    fetchTeachers(currentPage);
                });
            }
        });
    }

    function toggleTeacherStatus(id) {
        const teacher = currentTeachersList.find(t => String(t.id) === String(id));
        if (!teacher) return;

        const currentStatus = teacher.status || 'Active';
        const name = teacher.name;
        const designation = teacher.designation || '';
        const idNumber = teacher.id_number || '';

        if (currentStatus === 'Hold') {
            Swal.fire({
                title: 'Reactivate Faculty?',
                text: `Are you sure you want to activate ${name}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ACTIVATE'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`{{ url('/api/teachers') }}/${id}/activate`, {}, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then((res) => {
                        Toastify({
                            text: "Teacher activated successfully!",
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "#10b981"
                            }
                        }).showToast();
                        fetchTeachers(currentPage);
                    }).catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Action Failed',
                            text: err.response?.data?.message || 'Failed to activate teacher'
                        });
                    });
                }
            });
        } else {
            document.getElementById('deactivate_old_teacher_id').value = id;
            document.getElementById('deactivate_teacher_name').textContent = name;
            document.getElementById('deactivate_teacher_designation').textContent = designation || 'N/A';
            document.getElementById('deactivate_teacher_id_number').textContent = idNumber || 'PENDING';
            document.getElementById('deactivateTeacherModal').classList.remove('hidden');
        }
    }

    let allTeachersData = [];

    function updateDependentFilterOptions(source) {
        const teacherInput = document.getElementById('teacherFilter');
        const designationInput = document.getElementById('teacherFilterDesignation');
        const statusInput = document.getElementById('teacherFilterStatus');

        const selectedTeacherId = teacherInput?.value || '';
        const selectedDesignation = designationInput?.value || '';
        const selectedStatus = statusInput?.value || '';

        if (source === 'teacher' && selectedTeacherId) {
            const targetTeacher = allTeachersData.find(t => String(t.id) === String(selectedTeacherId));
            if (targetTeacher) {
                if (targetTeacher.designation) {
                    const desigOpts = [...new Set(allTeachersData.map(t => t.designation).filter(Boolean))].map(d => ({ value: d, label: d }));
                    populateDropdownSelect('teacherFilterDesignation', desigOpts, targetTeacher.designation, 'Select Designation');
                }
                const statusVal = targetTeacher.status || 'Active';
                const statusOpts = [
                    { value: 'Active', label: 'Active' },
                    { value: 'Hold', label: 'Hold' }
                ];
                populateDropdownSelect('teacherFilterStatus', statusOpts, statusVal, 'Select Status');
            }
            return;
        }

        let filteredTeachers = allTeachersData;
        if (selectedDesignation) {
            filteredTeachers = filteredTeachers.filter(t => t.designation === selectedDesignation);
        }
        if (selectedStatus) {
            filteredTeachers = filteredTeachers.filter(t => (t.status || 'Active') === selectedStatus);
        }

        const teacherOpts = filteredTeachers.map(t => ({ value: String(t.id), label: `${t.name} (${t.id_number || ''})` }));
        const currentValid = filteredTeachers.some(t => String(t.id) === String(selectedTeacherId));
        populateDropdownSelect('teacherFilter', teacherOpts, currentValid ? selectedTeacherId : '', 'Select Teacher');
    }

    function initFilterOptions() {
        axios.get('{{ route('school.teachers.data') }}', { params: { all: true } })
            .then(res => {
                allTeachersData = res.data.data || res.data || [];

                const teacherOpts = allTeachersData.map(t => ({ value: t.id, label: `${t.name} (${t.id_number || ''})` }));
                populateDropdownSelect('teacherFilter', teacherOpts, '', 'Select Teacher');

                const designations = [...new Set(allTeachersData.map(t => t.designation).filter(Boolean))];
                const desigOpts = designations.map(d => ({ value: d, label: d }));
                populateDropdownSelect('teacherFilterDesignation', desigOpts, '', 'Select Designation');
            })
            .catch(err => console.error("Filter options load error", err));

        const statusOpts = [
            { value: 'Active', label: 'Active' },
            { value: 'Hold', label: 'Hold' }
        ];
        populateDropdownSelect('teacherFilterStatus', statusOpts, '', 'Select Status');
    }

    function initTeacherPage() {
        if (document.getElementById('teacherTableBody')) {
            fetchTeachers();
        }
        initFilterOptions();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTeacherPage);
    } else {
        initTeacherPage();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('closeDeactivateTeacherModal')?.addEventListener('click', () => {
            document.getElementById('deactivateTeacherModal').classList.add('hidden');
        });

        document.getElementById('teacherFilterDesignation')?.addEventListener('change', () => {
            updateDependentFilterOptions('designation');
        });

        document.getElementById('teacherFilter')?.addEventListener('change', () => {
            updateDependentFilterOptions('teacher');
        });

        document.getElementById('teacherFilterStatus')?.addEventListener('change', () => {
            updateDependentFilterOptions('status');
        });

        document.getElementById('btnFilter')?.addEventListener('click', () => {
            document.getElementById('filterModal')?.classList.remove('hidden');
        });

        document.getElementById('closeFilterModal')?.addEventListener('click', () => {
            document.getElementById('filterModal')?.classList.add('hidden');
        });

        document.getElementById('resetFilter')?.addEventListener('click', () => {
            populateDropdownSelect('teacherFilter', [], '', 'Select Teacher');
            populateDropdownSelect('teacherFilterDesignation', [], '', 'Select Designation');
            populateDropdownSelect('teacherFilterStatus', [], '', 'Select Status');
            initFilterOptions();
            document.getElementById('filterModal')?.classList.add('hidden');
            fetchTeachers(1);
        });

        document.getElementById('applyFilter')?.addEventListener('click', () => {
            document.getElementById('filterModal')?.classList.add('hidden');
            fetchTeachers(1);
        });
    });
</script>
