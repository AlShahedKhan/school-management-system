<script>
    const localApi = axios.create({
        baseURL: '/'
    });
    localApi.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const geoApi = axios.create({
        baseURL: 'https://bdapis.com/api/v1.2'
    });

    function sanitizeEnglishDigits(value) {
        return value.replace(/[^0-9]/g, '');
    }

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

    async function syncPermanentFromCurrent() {
        if (!document.getElementById('sameAsCurrentAddress')?.checked) return;

        const currentCountry = document.getElementById('current_country')?.value || '';
        const currentDivision = document.getElementById('current_division')?.value || '';
        const currentDistrict = document.getElementById('current_district')?.value || '';
        const currentUpazila = document.getElementById('current_upazila')?.value || '';
        const currentVillage = document.getElementById('current_village')?.value || '';

        setSelectedValue('permanent_country', currentCountry);
        setSelectedValue('permanent_division', currentDivision);

        if (currentDivision) {
            await populateDistrict(currentDivision, 'permanent_district');
            setSelectedValue('permanent_district', currentDistrict);
        } else {
            populateDropdownSelect('permanent_district', [], '', 'Select District');
        }

        if (currentDistrict) {
            await populateUpazila(currentDistrict, 'permanent_upazila');
            setSelectedValue('permanent_upazila', currentUpazila);
        } else {
            populateDropdownSelect('permanent_upazila', [], '', 'Select Upazila');
        }

        const permVillage = document.getElementById('permanent_village');
        if (permVillage) {
            permVillage.value = currentVillage;
        }
    }

    async function toggleSameAddress() {
        const checked = document.getElementById('sameAsCurrentAddress').checked;
        ['country', 'division', 'district', 'upazila', 'village'].forEach(field => {
            if (field === 'village') {
                const el = document.getElementById(`permanent_${field}`);
                if (el) {
                    el.readOnly = checked;
                    el.classList.toggle('bg-slate-50', checked);
                    el.classList.toggle('text-slate-500', checked);
                    el.style.cursor = checked ? 'not-allowed' : '';
                }
            } else {
                const button = document.getElementById(`permanent_${field}Button`);
                if (button) {
                    button.disabled = checked;
                    button.classList.toggle('bg-slate-50', checked);
                    button.classList.toggle('text-slate-500', checked);
                    button.classList.toggle('cursor-not-allowed', checked);
                }
            }
        });
        if (checked) await syncPermanentFromCurrent();
    }
    // --- DYNAMIC DROPDOWN POPULATION HELPER ---
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

                // Close the dropdown menu
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

    async function loadLocations() {
        try {
            const res = await geoApi.get('/divisions');
            const divs = res.data.data || [];
            const options = divs.map(d => ({ value: d.division, label: d.division }));
            populateDropdownSelect('current_division', options, '', 'Select Division');
            populateDropdownSelect('permanent_division', options, '', 'Select Division');
        } catch (err) {
            console.error(err);
        }
    }

    async function populateDistrict(divisionValue, distId) {
        populateDropdownSelect(distId, [], '', 'Select District');
        if (!divisionValue) return;
        try {
            const res = await geoApi.get(`/division/${divisionValue}`);
            const options = (res.data.data || []).map(d => ({ value: d.district, label: d.district }));
            populateDropdownSelect(distId, options, '', 'Select District');
        } catch (err) {
            console.error(err);
        }
    }

    async function populateUpazila(districtValue, upaId) {
        populateDropdownSelect(upaId, [], '', 'Select Upazila');
        if (!districtValue) return;
        try {
            const res = await geoApi.get(`/district/${districtValue}`);
            const upazillas = (res.data.data && res.data.data[0]) ? (res.data.data[0].upazillas || []) : [];
            const options = upazillas.map(u => ({ value: u, label: u }));
            populateDropdownSelect(upaId, options, '', 'Select Upazila');
        } catch (err) {
            console.error(err);
        }
    }

    async function loadInitialData() {
        try {
            const res = await localApi.get('/api/get-school-classes');
            const classesOptions = res.data.data.map(item => ({ value: item.id, label: item.class_name }));
            populateDropdownSelect('a_class', classesOptions, '', 'Select Class');
        } catch (e) {
            console.error(e);
        }
    }
    async function loadGroups() {
        const classId = document.getElementById('a_class').value;
        populateDropdownSelect('a_group', [], '', 'Select Group');
        if (!classId) {
            loadSections();
            return;
        }
        try {
            const res = await localApi.get(`/api/get-school-groups?class_id=${classId}`);
            const groupsOptions = res.data.data.map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('a_group', groupsOptions, '', 'Select Group');
        } catch (e) {
            console.error(e);
        }
        loadSections();
    }
    async function loadSections() {
        const classId = document.getElementById('a_class').value;
        const groupId = document.getElementById('a_group').value;
        populateDropdownSelect('a_section', [], '', 'Select Section');
        if (!classId) {
            loadSessions();
            return;
        }
        try {
            const res = await localApi.get(`/api/get-school-sections?group_id=${groupId}`);
            let data = res.data.data;
            if (!groupId) data = data.filter(s => s.class_id == classId);
            const sectionsOptions = data.map(s => ({ value: s.id, label: s.section_name }));
            populateDropdownSelect('a_section', sectionsOptions, '', 'Select Section');
        } catch (e) {
            console.error(e);
        }
        loadSessions();
    }
    async function loadSessions() {
        const classId = document.getElementById('a_class').value;
        const groupId = document.getElementById('a_group').value;
        const sectionId = document.getElementById('a_section').value;
        populateDropdownSelect('a_session', [], '', 'Select Session');
        if (!classId) {
            loadFees();
            return;
        }
        try {
            const res = await localApi.get('/api/get-school-sessions', {
                params: {
                    class_id: classId,
                    group_id: groupId,
                    section_id: sectionId
                }
            });
            const sessionsOptions = res.data.data.map(s => ({ value: s.id, label: s.session_year }));
            populateDropdownSelect('a_session', sessionsOptions, '', 'Select Session');
        } catch (e) {
            console.error("Session load failed:", e);
        }
        loadFees();
    }
    async function loadFees() {
        const classId = document.getElementById('a_class').value;
        const sessionId = document.getElementById('a_session').value;
        const feeInput = document.getElementById('a_fee');
        if (!classId || !sessionId) {
            feeInput.value = '';
            return;
        }
        feeInput.value = 'Loading...';
        try {
            const res = await localApi.get('/api/fee-templates', {
                params: {
                    class_id: classId,
                    session_id: sessionId,
                    search: 'Admission',
                    all: 1
                }
            });
            const fees = res.data.data;
            const admissionFee = fees && fees.length > 0 ? fees[0] : null;
            feeInput.value = admissionFee ? admissionFee.amount : 'No fee defined';
        } catch (e) {
            console.error("Fee Load Error:", e);
            feeInput.value = 'Error';
        }
    }
    function registerAdmission() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('#admissionForm input, #admissionForm select').forEach(el => {
            el.style.borderColor = '#cbd5e1';
        });
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we secure your data.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        let formData = new FormData();
        formData.append('school_id', document.getElementById('school_id').value);
        document.querySelectorAll('#admissionForm input, #admissionForm select').forEach(el => {
            if (el.id && el.id !== 'student_image') {
                formData.append(el.id, el.value);
            }
        });
        formData.append('password', '00000000');
        formData.append('password_confirmation', '00000000');
        let img = document.getElementById('student_image').files[0];
        if (img) formData.append('image', img);
        localApi.post('/api/school-admission', formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'Congratulations! Your admission has been successful.',
                    confirmButtonColor: '#16a34a'
                }).then(() => {
                    window.location.href = res.data.redirect;
                });
            })
            .catch(err => {
                Swal.close();
                if (err.response && err.response.status === 422) {
                    const errors = err.response.data.errors;
                    Object.keys(errors).forEach((field) => {
                        let fieldId = field;
                        if (field === 'password') fieldId = 'studentPass';
                        if (field === 'image') fieldId = 'student_image';
                        const input = document.getElementById(fieldId);
                        if (input) {
                            input.style.borderColor = '#dc2626';
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-[10px] text-red-600 mt-1 font-medium';
                            errorDiv.innerText = errors[field][0];
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                        }
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the highlighted fields.',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    const errorTitle = err.response?.data?.message || 'System Error';
                    const errorDetail = err.response?.data?.error || err.message || 'An unknown error occurred.';
                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        html: `<div class="text-left bg-gray-100 p-3 rounded text-xs font-mono text-red-600 max-h-40 overflow-y-auto">${errorDetail}</div>`,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Understood'
                    });
                }
            });
    }
    document.addEventListener('DOMContentLoaded', () => {
        loadInitialData();
        loadLocations();
        document.getElementById('a_class')?.addEventListener('change', loadGroups);
        document.getElementById('a_group')?.addEventListener('change', loadSections);
        document.getElementById('a_section')?.addEventListener('change', loadSessions);
        document.getElementById('a_session')?.addEventListener('change', loadFees);
        document.getElementById('admissionModal')?.classList.remove('hidden');
        document.getElementById('closeAdmissionModal')?.addEventListener('click', () => {
            window.location.href = '/school/students';
        });
        ['student_mobile', 'g_mobile'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    this.value = sanitizeEnglishDigits(this.value);
                });
            }
        });
        ['student_name', 'father_name', 'mother_name', 'g_name'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s.-]/g, '');
                });
            }
        });

        document.getElementById('current_country')?.addEventListener('change', syncPermanentFromCurrent);
        document.getElementById('current_division')?.addEventListener('change', async (e) => {
            await populateDistrict(e.target.value, 'current_district');
            populateDropdownSelect('current_upazila', [], '', 'Select Upazila');
            syncPermanentFromCurrent();
        });
        document.getElementById('current_district')?.addEventListener('change', async (e) => {
            await populateUpazila(e.target.value, 'current_upazila');
            syncPermanentFromCurrent();
        });
        document.getElementById('current_upazila')?.addEventListener('change', syncPermanentFromCurrent);
        document.getElementById('current_village')?.addEventListener('input', syncPermanentFromCurrent);

        document.getElementById('permanent_division')?.addEventListener('change', async (e) => {
            await populateDistrict(e.target.value, 'permanent_district');
            populateDropdownSelect('permanent_upazila', [], '', 'Select Upazila');
        });
        document.getElementById('permanent_district')?.addEventListener('change', async (e) => {
            await populateUpazila(e.target.value, 'permanent_upazila');
        });
        const photoInput = document.getElementById('student_image');
        const imagePreview = document.getElementById('studentPreview');
        if (photoInput && imagePreview) {
            photoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" />`;
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
                }
            });
        }
        document.getElementById('btnCreateFeeTemplate')?.addEventListener('click', () => {
            const classId = document.getElementById('a_class').value;
            const sessionId = document.getElementById('a_session').value;
            if (!classId || !sessionId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select both Class and Session first.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            document.getElementById('feeTemplateModal').classList.remove('hidden');
            const payDateInput = document.getElementById('feePayDateInput');
            if (payDateInput && !payDateInput.value) {
                payDateInput.value = new Date().toISOString().split('T')[0];
            }
        });
        document.getElementById('closeFeeTemplateModal')?.addEventListener('click', () => {
            document.getElementById('feeTemplateModal').classList.add('hidden');
        });
        document.getElementById('feeTemplateForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const classId = document.getElementById('a_class').value;
            const sessionId = document.getElementById('a_session').value;
            const groupId = document.getElementById('a_group').value;
            const sectionId = document.getElementById('a_section').value;
            const feeName = document.getElementById('feeNameInput').value;
            const amount = document.getElementById('feeAmountInput').value;
            const payDate = document.getElementById('feePayDateInput').value;
            Swal.fire({
                title: 'Saving Fee Template...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            localApi.post('/api/fee-templates', {
                    class_id: classId,
                    session_id: sessionId,
                    group_id: groupId,
                    section_id: sectionId,
                    fee_type_name: 'Admission',
                    fee_name: feeName,
                    amount: amount,
                    pay_date: payDate
                })
                .then(() => {
                    Swal.close();
                    document.getElementById('feeTemplateModal').classList.add('hidden');
                    document.getElementById('feeTemplateForm').reset();
                    loadFees();
                })
                .catch(err => {
                    Swal.close();
                    let errorMsg = 'Failed to create fee template.';
                    if (err.response && err.response.data.message) {
                        errorMsg = err.response.data.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                });
        });
        document.getElementById('admissionForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const feeValue = document.getElementById('a_fee').value.trim();
            if (!feeValue || feeValue === 'No fee defined' || feeValue === 'Error' || feeValue === 'Loading...' || parseFloat(feeValue) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Admission Fee Required',
                    text: 'You cannot proceed because no admission fee template is defined for this class/session.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            registerAdmission();
        });
    });
</script>
