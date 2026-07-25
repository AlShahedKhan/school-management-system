<script>
    const teacherModal = document.getElementById('teacherModal');
    const photoInput = document.getElementById('photoInput');
    const imagePreview = document.getElementById('imagePreview');
    const teacherMobileInput = document.querySelector('#teacherForm input[name="mobile"]');
    const teacherMobileError = document.getElementById('teacherMobileError');
    let currentPage = {{ isset($teachers) ? $teachers->currentPage() : 1 }};

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

    const fetchTeachers = (page = 1) => {
        currentPage = page;
        const tbody = document.getElementById('teacherTableBody');

        if (!tbody) {
            return;
        }

        const search = getTeacherSearchValue();
        const teacherId = document.getElementById('teacherFilter')?.value || '';
        axios.get('{{ url('/api/teachers') }}', {
                params: {
                    search,
                    teacher_id: teacherId,
                    page
                }
            })
            .then(res => {
                const teachers = res.data.data || res.data;
                const meta = res.data.meta || {
                    current_page: 1,
                    last_page: 1,
                    total: teachers.length,
                    from: 1,
                    to: teachers.length
                };
                tbody.innerHTML = '';
                teachers.forEach((t, index) => {
                    const sl = ((meta.current_page - 1) * (meta.per_page || teachers.length)) + index + 1;
                    const photoUrl = t.photo ? `/storage/${t.photo}` :
                        'https://ui-avatars.com/api/?background=random&name=' + t.name;
                    const statusVal = t.status || 'Active';
                    const statusHtml = statusVal === 'Hold' 
                        ? `<span class="px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Hold</span>`
                        : `<span class="px-2 py-0.5 text-[10px] font-semibold bg-green-100 text-green-700 rounded-full">Active</span>`;

                    let startDate = t.salary_start_date || '-';
                    if (startDate.includes('T')) startDate = startDate.split('T')[0];
                    const salaryFormatted = parseFloat(t.salary_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    tbody.innerHTML += `
                   <tr class="hover:bg-gray-50">
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center"><img src="${photoUrl}" alt="${t.name}" class="h-6 w-6 rounded-full object-cover inline-block" /></td>
                       <td class="h-8 border border-gray-300 px-3 font-medium">${t.name}</td>
                       <td class="h-8 border border-gray-300 px-3 font-mono">${t.id_number || 'PENDING'}</td>
                       <td class="h-8 border border-gray-300 px-3">${t.designation || 'N/A'}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><a href="tel:${t.mobile}" class="inline-block text-blue-500">${t.mobile}</a></td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">৳${salaryFormatted}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${startDate}</td>
                       <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${t.pay_date || '-'}</td>
                       <td class="h-8 border border-gray-300 px-3 text-center">${statusHtml}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center min-w-[110px]">
                            <div class="flex h-6 w-full items-center justify-center space-x-1">
                                <button type="button" onclick="editTeacher(${t.id})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit ${t.name}">
                                    <i class="far fa-edit text-xs"></i>
                                </button>
                                <button type="button" onclick="toggleTeacherStatus(${t.id}, '${statusVal}', '${t.name.replace(/'/g, "\\'")}', '${(t.designation || '').replace(/'/g, "\\'")}', '${t.id_number}')" class="text-gray-600 hover:text-slate-800 p-1" title="Toggle Status">
                                    <i class="fas fa-toggle-on text-xs"></i>
                                </button>
                                <button type="button" onclick="deleteTeacher(${t.id})" class="text-red-600 hover:text-red-800 p-1" title="Delete ${t.name}">
                                    <i class="far fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                   </tr>`;
                });
            }).catch(e => console.error("Load failed", e));
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

    function toggleTeacherStatus(id, currentStatus, name, designation, idNumber) {
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

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('closeDeactivateTeacherModal')?.addEventListener('click', () => {
            document.getElementById('deactivateTeacherModal').classList.add('hidden');
        });
    });
</script>
