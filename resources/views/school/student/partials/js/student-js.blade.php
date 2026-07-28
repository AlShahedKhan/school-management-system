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
            <tr class="hover:bg-gray-50">
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                    <img src="${photoUrl}" class="h-6 w-6 rounded-full object-cover inline-block" />
                </td>
                <td class="h-8 border border-gray-300 px-3 font-mono">
                    <div class="school-data-table-cell-scroll" title="${s.student_id_number}">${s.student_id_number}</div>
                </td>
                <td class="h-8 border border-gray-300 px-3">
                    <div class="school-data-table-cell-scroll" title="${s.student_name}">${s.student_name}</div>
                </td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                    <div class="school-data-table-cell-scroll" title="${s.mobile || '-'}">${s.mobile || '-'}</div>
                </td>
                <td class="h-8 border border-gray-300 px-3">
                    <div class="school-data-table-cell-scroll" title="${s.father_name || '-'}">${s.father_name || '-'}</div>
                </td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${s.class_name || '-'}</td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${s.group_name || '-'}</td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${s.section_name || '-'}</td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${s.session_year || '-'}</td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${s.student_type || 'Admission'}</td>
                <td class="h-8 border border-gray-300 px-3 text-center">
                    <span class="${badgeClass}">
                        ${statusText}
                    </span>
                </td>
                <td class="h-8 whitespace-nowrap border border-gray-300 px-1 text-center min-w-[130px]">
                    <div class="flex h-7 w-full items-center justify-center space-x-1">
                        <button type="button" onclick="viewAdmissionForm(${s.id}, ${sl})" title="View" aria-label="View" class="flex h-7 w-6 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-emerald-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-emerald-500">
                            <i class="far fa-eye text-[15px]" aria-hidden="true"></i>
                        </button>
                        <button type="button" onclick="editStudent(${s.id})" title="Edit" aria-label="Edit" class="flex h-7 w-6 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-500">
                            <i class="far fa-edit text-[15px]" aria-hidden="true"></i>
                        </button>
                        <button type="button" onclick="deleteStudent(${s.id}, \`${s.student_name.replace(/`/g, '\\`').replace(/"/g, '\\"')}\`)" title="Delete" aria-label="Delete" class="flex h-7 w-6 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-red-500">
                            <i class="far fa-trash-alt text-[15px]" aria-hidden="true"></i>
                        </button>
                        <button type="button" onclick="toggleStudentStatus(${s.id}, '${s.status}', \`${s.student_name.replace(/`/g, '\\`').replace(/"/g, '\\"')}\`, '${s.student_id_number}', '${s.inactive_date || ''}')" title="${s.status === 'Inactive' ? 'Activate' : 'Deactivate'}" aria-label="Status" class="flex h-7 w-6 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-slate-500">
                            <i class="fas fa-toggle-on text-[15px] ${s.status === 'Inactive' ? 'text-red-500' : 'text-emerald-500'}" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
                });

                renderPagination(meta);
            }).catch(e => console.error("Load failed", e));
    }

    function viewAdmissionForm(id, sl) {
        axios.get(`{{ url('/api/school/students') }}/${id}`)
            .then(res => {
                const s = res.data.data || res.data;
                const modal = document.getElementById('admissionFormModal') || document.getElementById('detailsModal');
                if (!modal) return;

                const setText = (elemId, text) => {
                    const el = document.getElementById(elemId);
                    if (el) el.textContent = text || '';
                };

                setText('print_student_id_number', s.student_id_number);
                setText('print_admission_id', s.admission_id || sl);
                setText('print_date_box', s.created_at ? s.created_at.split('T')[0] : (s.admission_date || new Date().toISOString().split('T')[0]));
                setText('print_student_name', s.student_name);
                setText('print_father_name', s.father_name);
                setText('print_mother_name', s.mother_name);
                setText('print_mobile', s.mobile);

                setText('print_g_name', s.g_name || s.guardian_name || s.father_name);
                setText('print_g_relation', s.g_relation || s.relation || 'Father');
                setText('print_g_mobile', s.g_mobile || s.guardian_mobile || s.mobile);

                setText('print_class', s.class_name || s.school_class?.class_name || s.schoolClass?.class_name || s.class);
                setText('print_group', s.group_name || s.school_group?.group_name || s.schoolGroup?.group_name || s.group);
                setText('print_section', s.section_name || s.school_section?.section_name || s.schoolSection?.section_name || s.section);
                setText('print_session', s.session_year || s.school_session?.session_year || s.schoolSession?.session_year || s.session);
                setText('print_student_type', s.student_type || 'Admission');

                setText('print_curr_country', s.current_country || s.country || 'Bangladesh');
                setText('print_curr_division', s.current_division || s.division);
                setText('print_curr_district', s.current_district || s.district);
                setText('print_curr_upazila', s.current_upazila || s.upazila);
                setText('print_curr_village', s.current_village || s.village);

                setText('print_perm_country', s.permanent_country || s.country || 'Bangladesh');
                setText('print_perm_division', s.permanent_division || s.division);
                setText('print_perm_district', s.permanent_district || s.district);
                setText('print_perm_upazila', s.permanent_upazila || s.upazila);
                setText('print_perm_village', s.permanent_village || s.village);

                const imgEl = document.getElementById('print_student_photo');
                const placeholder = document.getElementById('print_photo_placeholder');
                const imagePath = s.student_image || s.image || s.photo;
                if (imgEl) {
                    if (imagePath) {
                        const srcUrl = imagePath.startsWith('http') || imagePath.startsWith('/') ? imagePath : `/storage/${imagePath}`;
                        imgEl.src = srcUrl;
                        imgEl.classList.remove('hidden');
                        if (placeholder) placeholder.classList.add('hidden');
                        imgEl.onerror = () => {
                            imgEl.classList.add('hidden');
                            if (placeholder) placeholder.classList.remove('hidden');
                        };
                    } else {
                        imgEl.classList.add('hidden');
                        if (placeholder) placeholder.classList.remove('hidden');
                    }
                }

                modal.classList.remove('hidden');
                setTimeout(fitAdmissionFormScale, 50);
            })
            .catch(err => {
                console.error('Fetch student error', err);
            });
    }

    function fitAdmissionFormScale() {
        const container = document.querySelector('.adm-form-container');
        if (!container) return;
        const availableWidth = Math.min(window.innerWidth - 24, 650);
        if (availableWidth < 650) {
            const scale = availableWidth / 650;
            container.style.zoom = scale;
        } else {
            container.style.zoom = '1';
        }
    }
    window.addEventListener('resize', fitAdmissionFormScale);

    function printAdmissionForm() {
        window.print();
    }

    function closeAdmissionFormModal() {
        document.getElementById('admissionFormModal')?.classList.add('hidden');
    }

    // Global caches for instant loading
    let cachedClasses = null;
    let cachedGroups = null;
    let cachedSections = null;
    let cachedSessions = null;
    const locationCache = new Map();

    async function getGeoData(endpoint) {
        if (locationCache.has(endpoint)) {
            return locationCache.get(endpoint);
        }
        try {
            const res = await geoApi.get(endpoint);
            const data = res.data.data || [];
            locationCache.set(endpoint, data);
            return data;
        } catch (err) {
            console.error('Geo API fetch error:', endpoint, err);
            return [];
        }
    }

    async function populateDivisions(selectId, selectedVal = '') {
        const divs = await getGeoData('/divisions');
        const options = divs.map(d => ({ value: d.division, label: d.division }));
        populateDropdownSelect(selectId, options, selectedVal, 'Select Division');
    }

    async function populateDistrict(divisionValue, distId, selectedVal = '') {
        populateDropdownSelect(distId, [], '', 'Select District');
        if (!divisionValue) return;
        const resData = await getGeoData(`/division/${divisionValue}`);
        const options = resData.map(d => ({ value: d.district, label: d.district }));
        populateDropdownSelect(distId, options, selectedVal, 'Select District');
    }

    async function populateUpazila(districtValue, upaId, selectedVal = '') {
        populateDropdownSelect(upaId, [], '', 'Select Upazila');
        if (!districtValue) return;
        const resData = await getGeoData(`/district/${districtValue}`);
        const upazillas = (resData && resData[0]) ? (resData[0].upazillas || []) : [];
        const options = upazillas.map(u => ({ value: u, label: u }));
        populateDropdownSelect(upaId, options, selectedVal, 'Select Upazila');
    }

    async function toggleSameAddressModal() {
        const checked = document.getElementById('sameAsCurrentAddressModal')?.checked;
        if (!checked) return;

        const currentCountry = document.getElementById('edit_current_country')?.value || '';
        const currentDivision = document.getElementById('edit_current_division')?.value || '';
        const currentDistrict = document.getElementById('edit_current_district')?.value || '';
        const currentUpazila = document.getElementById('edit_current_upazila')?.value || '';
        const currentVillage = document.getElementById('edit_current_village')?.value || '';

        setSelectedValue('edit_permanent_country', currentCountry);
        setSelectedValue('edit_permanent_division', currentDivision);

        if (currentDivision) {
            await populateDistrict(currentDivision, 'edit_permanent_district', currentDistrict);
        }
        if (currentDistrict) {
            await populateUpazila(currentDistrict, 'edit_permanent_upazila', currentUpazila);
        }

        const permVillage = document.getElementById('edit_permanent_village');
        if (permVillage) permVillage.value = currentVillage;
    }

    async function editStudent(id) {
        const modal = document.getElementById('studentModal');
        if (!modal) return;

        // 1. OPEN MODAL INSTANTLY FOR 0MS LATENCY RESPONSE
        modal.classList.remove('hidden');

        const idInp = document.getElementById('student_id');
        if (idInp) idInp.value = id;

        try {
            const fetchPromises = [
                axios.get(`{{ url('/api/school/students') }}/${id}`),
                cachedClasses ? Promise.resolve({ data: cachedClasses }) : axios.get('/api/get-school-classes'),
                cachedGroups ? Promise.resolve({ data: cachedGroups }) : axios.get('/api/get-school-groups'),
                cachedSections ? Promise.resolve({ data: cachedSections }) : axios.get('/api/get-school-sections'),
                cachedSessions ? Promise.resolve({ data: cachedSessions }) : axios.get('/api/get-school-sessions')
            ];

            const [studentRes, classRes, groupRes, sectionRes, sessionRes] = await Promise.all(fetchPromises);

            cachedClasses = classRes.data;
            cachedGroups = groupRes.data;
            cachedSections = sectionRes.data;
            cachedSessions = sessionRes.data;

            const s = studentRes.data.data || studentRes.data;

            // Input Fields
            const inputValues = {
                'edit_school': s.school_name || s.school?.school_name || s.school || '',
                'edit_admission_fee': s.admission_fee || '',
                'edit_admission_date': s.admission_date || (s.created_at ? s.created_at.split('T')[0] : ''),
                'edit_student_name': s.student_name || '',
                'edit_father_name': s.father_name || '',
                'edit_mother_name': s.mother_name || '',
                'edit_mobile': s.mobile || '',
                'edit_g_name': s.g_name || s.guardian_name || s.father_name || '',
                'edit_g_relation': s.g_relation || s.relation || 'Father',
                'edit_g_mobile': s.g_mobile || s.guardian_mobile || s.mobile || '',
                'edit_current_village': s.current_village || s.village || '',
                'edit_permanent_village': s.permanent_village || s.village || ''
            };

            for (const [fieldId, val] of Object.entries(inputValues)) {
                const el = document.getElementById(fieldId);
                if (el) el.value = val;
            }

            // Dropdowns (Class, Group, Section, Session)
            const classOpts = (classRes.data.data || classRes.data || []).map(c => ({ value: c.id, label: c.class_name }));
            const groupOpts = (groupRes.data.data || groupRes.data || []).map(g => ({ value: g.id, label: g.group_name }));
            const sectionOpts = (sectionRes.data.data || sectionRes.data || []).map(sec => ({ value: sec.id, label: sec.section_name }));
            const rawSessions = sessionRes.data.data || sessionRes.data || [];
            const sessionOpts = Array.from(new Map(rawSessions.map(sess => [sess.id, { value: sess.id, label: sess.session_year || sess.year || 'N/A' }])).values());

            populateDropdownSelect('edit_class', classOpts, s.class_id || s.class, 'Select Class');
            populateDropdownSelect('edit_group', groupOpts, s.group_id || s.group, 'Select Group');
            populateDropdownSelect('edit_section', sectionOpts, s.section_id || s.section, 'Select Section');
            populateDropdownSelect('edit_session', sessionOpts, s.session_id || s.session, 'Select Session');

            // Photo Preview
            const imgPreview = document.getElementById('imagePreview');
            const photoPath = s.student_image || s.image || s.photo;
            if (imgPreview) {
                if (photoPath) {
                    const srcUrl = photoPath.startsWith('http') || photoPath.startsWith('/') ? photoPath : `/storage/${photoPath}`;
                    imgPreview.innerHTML = `<img src="${srcUrl}" class="w-full h-full object-cover" />`;
                } else {
                    imgPreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
                }
            }

            // Location Dropdowns (Load asynchronously in parallel using cached Geo API)
            const currCountry = s.current_country || s.country || 'Bangladesh';
            const permCountry = s.permanent_country || s.country || 'Bangladesh';
            const currDiv = s.current_division || s.division || '';
            const currDist = s.current_district || s.district || '';
            const currUpa = s.current_upazila || s.upazila || '';
            const permDiv = s.permanent_division || s.division || '';
            const permDist = s.permanent_district || s.district || '';
            const permUpa = s.permanent_upazila || s.upazila || '';

            // Auto-check "Same as Current Address" checkbox if permanent address matches current address or was saved as same
            const sameAddressCb = document.getElementById('sameAsCurrentAddressModal');
            const isSameAddress = Boolean(
                s.same_as_current == 1 ||
                s.same_as_current === true ||
                s.same_as_current === '1' ||
                s.same_as_current === 'true' ||
                (!permDiv && !permDist && !permUpa) ||
                (currDiv && currDiv === permDiv && (!currDist || currDist === permDist) && (!currUpa || currUpa === permUpa))
            );
            if (sameAddressCb) {
                sameAddressCb.checked = isSameAddress;
            }

            const targetPermCountry = isSameAddress ? currCountry : permCountry;
            const targetPermDiv = isSameAddress ? currDiv : permDiv;
            const targetPermDist = isSameAddress ? currDist : permDist;
            const targetPermUpa = isSameAddress ? currUpa : permUpa;

            setSelectedValue('edit_current_country', currCountry);
            setSelectedValue('edit_permanent_country', targetPermCountry);

            if (isSameAddress) {
                const permVillage = document.getElementById('edit_permanent_village');
                if (permVillage) permVillage.value = s.current_village || s.village || '';
            }

            Promise.all([
                populateDivisions('edit_current_division', currDiv).then(() => {
                    if (currDiv) return populateDistrict(currDiv, 'edit_current_district', currDist);
                }).then(() => {
                    if (currDist) return populateUpazila(currDist, 'edit_current_upazila', currUpa);
                }),
                populateDivisions('edit_permanent_division', targetPermDiv).then(() => {
                    if (targetPermDiv) return populateDistrict(targetPermDiv, 'edit_permanent_district', targetPermDist);
                }).then(() => {
                    if (targetPermDist) return populateUpazila(targetPermDist, 'edit_permanent_upazila', targetPermUpa);
                })
            ]);

        } catch (err) {
            console.error('Edit student load error:', err);
        }
    }

    function deleteStudent(id, name) {
        const modal = document.getElementById('deleteStudentModal');
        if (modal) {
            const idInp = document.getElementById('delete_student_id');
            const nameEl = document.getElementById('delete_student_name');
            if (idInp) idInp.value = id;
            if (nameEl) nameEl.textContent = name;
            modal.classList.remove('hidden');
        } else {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                axios.delete(`{{ url('/api/school/students') }}/${id}`)
                    .then(() => {
                        fetchStudents(currentPage);
                    })
                    .catch(err => console.error(err));
            }
        }
    }

    function toggleStudentStatus(id, currentStatus, name, idNumber, inactiveDate) {
        const isInactive = currentStatus === 'Inactive' || currentStatus === 'Unactive';
        const targetModalId = isInactive ? 'activateStudentModal' : 'deactivateStudentModal';
        const modal = document.getElementById(targetModalId);

        if (modal) {
            const idKey = isInactive ? 'activate_student_id' : 'deactivate_student_id';
            const nameKey = isInactive ? 'activate_student_name' : 'deactivate_student_name';
            const numKey = isInactive ? 'activate_student_id_number' : 'deactivate_student_id_number';

            const idInp = document.getElementById(idKey);
            const nameEl = document.getElementById(nameKey);
            const numEl = document.getElementById(numKey);

            if (idInp) idInp.value = id;
            if (nameEl) nameEl.textContent = name;
            if (numEl) numEl.textContent = idNumber || '';

            if (!isInactive) {
                const dateInp = document.getElementById('deactivate_inactive_date');
                if (dateInp && !dateInp.value) {
                    dateInp.value = new Date().toISOString().split('T')[0];
                }
            }
            modal.classList.remove('hidden');
        } else {
            const newStatus = isInactive ? 'Active' : 'Inactive';
            axios.post(`{{ url('/api/school/students/status') }}/${id}`, { status: newStatus })
                .then(() => fetchStudents(currentPage))
                .catch(err => console.error(err));
        }
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

    async function loadFilterOptions() {
        try {
            const classRes = await axios.get('/api/get-school-classes');
            const classOpts = (classRes.data.data || classRes.data || []).map(c => ({ value: c.id, label: c.class_name }));

            populateDropdownSelect('classFilter', classOpts, '', 'Select Class');
            populateDropdownSelect('groupFilter', [], '', 'Select Group');
            populateDropdownSelect('sectionFilter', [], '', 'Select Section');
            populateDropdownSelect('sessionFilter', [], '', 'Select Session');
        } catch (e) {
            console.error('Filter options load error', e);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('studentTableBody')) {
            fetchStudents();
            loadFilterOptions();
        }

        document.getElementById('classFilter')?.addEventListener('change', async function() {
            const classId = this.value;
            populateDropdownSelect('groupFilter', [], '', 'Select Group');
            populateDropdownSelect('sectionFilter', [], '', 'Select Section');
            populateDropdownSelect('sessionFilter', [], '', 'Select Session');

            if (!classId) return;

            try {
                const [groupRes, sectionRes, sessionRes] = await Promise.all([
                    axios.get('/api/get-school-groups', { params: { class_id: classId } }),
                    axios.get('/api/get-school-sections', { params: { class_id: classId } }),
                    axios.get('/api/get-school-sessions', { params: { class_id: classId } })
                ]);

                const groupOpts = (groupRes.data.data || groupRes.data || []).map(g => ({ value: g.id, label: g.group_name }));
                const sectionOpts = (sectionRes.data.data || sectionRes.data || []).map(s => ({ value: s.id, label: s.section_name }));

                const rawSessions = sessionRes.data.data || sessionRes.data || [];
                const sessionMap = new Map();
                rawSessions.forEach(s => {
                    const label = s.session_year || s.year || 'N/A';
                    if (!sessionMap.has(s.id)) {
                        sessionMap.set(s.id, { value: s.id, label: label });
                    }
                });
                const sessionOpts = Array.from(sessionMap.values());

                populateDropdownSelect('groupFilter', groupOpts, '', 'Select Group');
                populateDropdownSelect('sectionFilter', sectionOpts, '', 'Select Section');
                populateDropdownSelect('sessionFilter', sessionOpts, '', 'Select Session');
            } catch (err) {
                console.error('Class filter change error', err);
            }
        });

        document.getElementById('groupFilter')?.addEventListener('change', async function() {
            const classId = document.getElementById('classFilter')?.value || '';
            const groupId = this.value;
            populateDropdownSelect('sectionFilter', [], '', 'Select Section');
            populateDropdownSelect('sessionFilter', [], '', 'Select Session');

            if (!classId) return;

            try {
                const [sectionRes, sessionRes] = await Promise.all([
                    axios.get('/api/get-school-sections', { params: { class_id: classId, group_id: groupId || '' } }),
                    axios.get('/api/get-school-sessions', { params: { class_id: classId, group_id: groupId || '' } })
                ]);

                const sectionOpts = (sectionRes.data.data || sectionRes.data || []).map(s => ({ value: s.id, label: s.section_name }));

                const rawSessions = sessionRes.data.data || sessionRes.data || [];
                const sessionMap = new Map();
                rawSessions.forEach(s => {
                    const label = s.session_year || s.year || 'N/A';
                    if (!sessionMap.has(s.id)) {
                        sessionMap.set(s.id, { value: s.id, label: label });
                    }
                });
                const sessionOpts = Array.from(sessionMap.values());

                populateDropdownSelect('sectionFilter', sectionOpts, '', 'Select Section');
                populateDropdownSelect('sessionFilter', sessionOpts, '', 'Select Session');
            } catch (err) {
                console.error('Group filter change error', err);
            }
        });

        document.getElementById('sectionFilter')?.addEventListener('change', async function() {
            const classId = document.getElementById('classFilter')?.value || '';
            const groupId = document.getElementById('groupFilter')?.value || '';
            const sectionId = this.value;
            populateDropdownSelect('sessionFilter', [], '', 'Select Session');

            if (!classId) return;

            try {
                const sessionRes = await axios.get('/api/get-school-sessions', {
                    params: { class_id: classId, group_id: groupId || '', section_id: sectionId || '' }
                });

                const rawSessions = sessionRes.data.data || sessionRes.data || [];
                const sessionMap = new Map();
                rawSessions.forEach(s => {
                    const label = s.session_year || s.year || 'N/A';
                    if (!sessionMap.has(s.id)) {
                        sessionMap.set(s.id, { value: s.id, label: label });
                    }
                });
                const sessionOpts = Array.from(sessionMap.values());

                populateDropdownSelect('sessionFilter', sessionOpts, '', 'Select Session');
            } catch (err) {
                console.error('Section filter change error', err);
            }
        });

        const btnFilter = document.getElementById('btnFilter');
        const filterModal = document.getElementById('filterModal');

        if (btnFilter && filterModal) {
            btnFilter.addEventListener('click', () => {
                filterModal.classList.remove('hidden');
            });
        }

        document.getElementById('applyFilter')?.addEventListener('click', () => {
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

        document.getElementById('resetFilter')?.addEventListener('click', () => {
            ['classFilter', 'groupFilter', 'sectionFilter', 'sessionFilter'].forEach(id => {
                const inp = document.getElementById(id);
                if (inp) inp.value = '';
                const btn = document.getElementById(id + 'Button');
                const label = btn ? btn.querySelector('[data-dropdown-select-label]') : null;
                if (label) label.textContent = 'Select...';
            });
            populateDropdownSelect('groupFilter', [], '', 'Select Group');
            populateDropdownSelect('sectionFilter', [], '', 'Select Section');
            populateDropdownSelect('sessionFilter', [], '', 'Select Session');
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

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

        document.getElementById('studentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('student_id')?.value;
            if (!id) return;

            const formData = new FormData(this);

            axios.post(`{{ url('/api/school/students') }}/${id}`, formData)
                .then(res => {
                    document.getElementById('studentModal')?.classList.add('hidden');
                    Toastify({ text: "Student updated successfully", duration: 3000, style: { background: "#10b981" } }).showToast();
                    fetchStudents(currentPage);
                })
                .catch(err => {
                    console.error('Update student error:', err);
                    alert(err.response?.data?.message || 'Failed to update student');
                });
        });

        document.getElementById('edit_current_division')?.addEventListener('change', async (e) => {
            await populateDistrict(e.target.value, 'edit_current_district');
            populateDropdownSelect('edit_current_upazila', [], '', 'Select Upazila');
            if (document.getElementById('sameAsCurrentAddressModal')?.checked) {
                toggleSameAddressModal();
            }
        });
        document.getElementById('edit_current_district')?.addEventListener('change', async (e) => {
            await populateUpazila(e.target.value, 'edit_current_upazila');
            if (document.getElementById('sameAsCurrentAddressModal')?.checked) {
                toggleSameAddressModal();
            }
        });
        document.getElementById('edit_current_upazila')?.addEventListener('change', () => {
            if (document.getElementById('sameAsCurrentAddressModal')?.checked) {
                toggleSameAddressModal();
            }
        });
        document.getElementById('edit_current_village')?.addEventListener('input', () => {
            if (document.getElementById('sameAsCurrentAddressModal')?.checked) {
                toggleSameAddressModal();
            }
        });
        document.getElementById('edit_permanent_division')?.addEventListener('change', async (e) => {
            await populateDistrict(e.target.value, 'edit_permanent_district');
            populateDropdownSelect('edit_permanent_upazila', [], '', 'Select Upazila');
        });
        document.getElementById('edit_permanent_district')?.addEventListener('change', async (e) => {
            await populateUpazila(e.target.value, 'edit_permanent_upazila');
        });

        // Form Submission Event Handlers
        document.getElementById('deleteStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('delete_student_id')?.value;
            if (!id) return;
            axios.delete(`{{ url('/api/school/students') }}/${id}`)
                .then(res => {
                    document.getElementById('deleteStudentModal')?.classList.add('hidden');
                    fetchStudents(currentPage);
                })
                .catch(err => console.error(err));
        });

        document.getElementById('deactivateStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('deactivate_student_id')?.value;
            const date = document.getElementById('deactivate_inactive_date')?.value;
            const reason = document.getElementById('deactivate_inactive_reason')?.value;
            if (!id) return;
            axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                status: 'Inactive',
                inactive_date: date,
                inactive_reason: reason
            }).then(res => {
                document.getElementById('deactivateStudentModal')?.classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => console.error(err));
        });

        document.getElementById('activateStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('activate_student_id')?.value;
            if (!id) return;
            axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                status: 'Active'
            }).then(res => {
                document.getElementById('activateStudentModal')?.classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => console.error(err));
        });

        // Close handlers for modals
        const modalCloseMap = {
            'closeStudentModal': 'studentModal',
            'closeDeleteStudentModal': 'deleteStudentModal',
            'closeDeactivateStudentModal': 'deactivateStudentModal',
            'closeActivateStudentModal': 'activateStudentModal',
            'closeDetailsModal': 'detailsModal',
            'closeAdmissionFormBtn': 'admissionFormModal'
        };

        Object.entries(modalCloseMap).forEach(([btnId, modalId]) => {
            document.getElementById(btnId)?.addEventListener('click', function() {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                } else {
                    this.closest('.premium-modal, [data-modal], .student-filter-modal')?.classList.add('hidden');
                }
            });
        });
    });
</script>
