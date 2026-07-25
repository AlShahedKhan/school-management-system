<script>
    // --- QUICK ADD MODALS INTERACTION & SUBMISSION LOGIC ---
    window.lastActiveModalId = null;

    document.addEventListener('click', async (event) => {
        const btn = event.target.closest('[data-dropdown-add-target]');
        if (btn) {
            event.stopImmediatePropagation();
            event.preventDefault();

            const targetId = btn.dataset.dropdownAddTarget;
            const currentModal = btn.closest('[role="dialog"]');
            if (currentModal) {
                window.lastActiveModalId = currentModal.id;
            }

            const targetModal = document.getElementById(targetId);
            if (targetModal) {
                targetModal.classList.remove('hidden');
            }
            
            if (targetId === 'quickGroupModal') {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('quick_group_class', classesOptions, '', 'Select Class');
            } else if (targetId === 'quickSectionModal') {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('quick_section_class', classesOptions, '', 'Select Class');
                populateDropdownSelect('quick_section_group', [], '', 'Select Group');
            } else if (targetId === 'quickSessionModal') {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('quick_session_class', classesOptions, '', 'Select Class');
                populateDropdownSelect('quick_session_group', [], '', 'Select Group');
                populateDropdownSelect('quick_session_section', [], '', 'Select Section');
            } else if (targetId === 'quickSubjectModal') {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('quick_subject_class', classesOptions, '', 'Select Class');
                populateDropdownSelect('quick_subject_group', [], '', 'Select Group');
                populateDropdownSelect('quick_subject_section', [], '', 'Select Section');
            }
        }
    }, true);

    function closeQuickModal(modalId) {
        document.getElementById(modalId)?.classList.add('hidden');
        if (window.lastActiveModalId) {
            document.getElementById(window.lastActiveModalId)?.classList.remove('hidden');
            window.lastActiveModalId = null;
        }
    }

    ['closeQuickClassModal', 'closeQuickGroupModal', 'closeQuickSectionModal', 'closeQuickSessionModal', 'closeQuickSubjectModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', () => {
            const modal = document.getElementById(id).closest('[role="dialog"]');
            if (modal) {
                closeQuickModal(modal.id);
            }
        });
    });

    document.getElementById('quick_section_class')?.addEventListener('change', async function() {
        const classId = this.value;
        populateDropdownSelect('quick_section_group', [], '', 'Select Group');
        if (!classId) return;
        try {
            const res = await axios.get(`/api/get-school-groups?class_id=${classId}`);
            const groupsOptions = res.data.data.map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('quick_section_group', groupsOptions, '', 'Select Group');
        } catch (err) {
            console.error(err);
        }
    });

    document.getElementById('quick_subject_class')?.addEventListener('change', async function() {
        const classId = this.value;
        populateDropdownSelect('quick_subject_group', [], '', 'Select Group');
        populateDropdownSelect('quick_subject_section', [], '', 'Select Section');
        if (!classId) return;
        try {
            const res = await axios.get(`/api/get-school-groups?class_id=${classId}`);
            const groupsOptions = res.data.data.map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('quick_subject_group', groupsOptions, '', 'Select Group');

            const secRes = await axios.get(`/api/get-school-sections?class_id=${classId}`);
            const sectionsOptions = secRes.data.data.map(s => ({ value: s.id, label: s.section_name }));
            populateDropdownSelect('quick_subject_section', sectionsOptions, '', 'Select Section');
        } catch (err) {
            console.error(err);
        }
    });

    document.getElementById('quick_subject_group')?.addEventListener('change', async function() {
        const classId = document.getElementById('quick_subject_class').value;
        const groupId = this.value;
        populateDropdownSelect('quick_subject_section', [], '', 'Select Section');
        if (!classId) return;
        try {
            const res = await axios.get(`/api/get-school-sections?class_id=${classId}&group_id=${groupId || ''}`);
            const sectionsOptions = res.data.data.map(s => ({ value: s.id, label: s.section_name }));
            populateDropdownSelect('quick_subject_section', sectionsOptions, '', 'Select Section');
        } catch (err) {
            console.error(err);
        }
    });

    document.getElementById('quickClassForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const className = document.getElementById('quick_class_name').value;
        try {
            const res = await axios.post('/api/classes', { class_name: className });
            const newClass = res.data.data;
            
            // Refresh class selectors
            if (typeof loadInitialData === 'function') {
                await loadInitialData();
            }
            try {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('edit_class', classesOptions, '', 'Select Class');
            } catch (err) {}
            if (typeof loadFilterOptions === 'function') {
                await loadFilterOptions();
            }

            if (window.lastActiveModalId === 'admissionModal') {
                setSelectedValue('a_class', newClass.id);
            } else if (window.lastActiveModalId === 'studentModal') {
                setSelectedValue('edit_class', newClass.id);
            } else if (window.lastActiveModalId === 'bulkUploadModal') {
                if (typeof loadClasses === 'function') {
                    await loadClasses();
                }
                setSelectedValue('bulkClass', newClass.id);
            } else if (window.lastActiveModalId === 'permModal') {
                if (typeof initData === 'function') {
                    await initData();
                }
                setSelectedValue('class_id', newClass.id);
            } else if (window.lastActiveModalId === 'quickSubjectModal') {
                const classRes = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('quick_subject_class', classesOptions, '', 'Select Class');
                setSelectedValue('quick_subject_class', newClass.id);
            }

            closeQuickModal('quickClassModal');
            
            if (typeof Toastify === 'function') {
                Toastify({
                    text: "Class added successfully!",
                    style: { background: "#10b981" }
                }).showToast();
            } else {
                Swal.fire({ icon: 'success', title: 'Success', text: 'Class added successfully!' });
            }
        } catch (err) {
            console.error(err);
            let errorMsg = "Failed to add class.";
            if (err.response && err.response.data && err.response.data.errors) {
                errorMsg = Object.values(err.response.data.errors).flat().join('\n');
            }
            Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
        }
    });

    document.getElementById('quickGroupForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const classId = document.getElementById('quick_group_class').value;
        const groupName = document.getElementById('quick_group_name').value;
        if (!classId) {
            alert("Please select a class first.");
            return;
        }
        try {
            const res = await axios.post('/api/groups', { class_id: classId, group_name: groupName });
            const newGroup = res.data.data;
            
            if (window.lastActiveModalId === 'admissionModal') {
                const currentClassId = document.getElementById('a_class').value;
                if (currentClassId == classId) {
                    if (typeof loadGroups === 'function') {
                        await loadGroups();
                    }
                    setSelectedValue('a_group', newGroup.id);
                }
            } else if (window.lastActiveModalId === 'studentModal') {
                const currentClassId = document.getElementById('edit_class').value;
                if (currentClassId == classId) {
                    const r = await axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } });
                    const groupsOptions = r.data.data.map(g => ({ value: g.id, label: g.group_name }));
                    populateDropdownSelect('edit_group', groupsOptions, '', 'Select Group');
                    setSelectedValue('edit_group', newGroup.id);
                }
            } else if (window.lastActiveModalId === 'bulkUploadModal') {
                const currentClassId = document.getElementById('bulkClass').value;
                if (currentClassId == classId) {
                    if (typeof handleClassChange === 'function') {
                        await handleClassChange();
                    }
                    setSelectedValue('bulkGroup', newGroup.id);
                }
            } else if (window.lastActiveModalId === 'permModal') {
                const currentClassId = document.getElementById('class_id').value;
                if (currentClassId == classId) {
                    if (typeof initData === 'function') {
                        await initData();
                    }
                    filterDependents();
                    setSelectedValue('group_id', newGroup.id);
                }
            } else if (window.lastActiveModalId === 'quickSubjectModal') {
                const currentClassId = document.getElementById('quick_subject_class').value;
                if (currentClassId == classId) {
                    const r = await axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } });
                    const groupsOptions = r.data.data.map(g => ({ value: g.id, label: g.group_name }));
                    populateDropdownSelect('quick_subject_group', groupsOptions, '', 'Select Group');
                    setSelectedValue('quick_subject_group', newGroup.id);
                }
            }

            closeQuickModal('quickGroupModal');
            
            if (typeof Toastify === 'function') {
                Toastify({
                    text: "Group added successfully!",
                    style: { background: "#10b981" }
                }).showToast();
            } else {
                Swal.fire({ icon: 'success', title: 'Success', text: 'Group added successfully!' });
            }
        } catch (err) {
            console.error(err);
            let errorMsg = "Failed to add group.";
            if (err.response && err.response.data && err.response.data.errors) {
                errorMsg = Object.values(err.response.data.errors).flat().join('\n');
            }
            Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
        }
    });

    document.getElementById('quickSectionForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const classId = document.getElementById('quick_section_class').value;
        const groupId = document.getElementById('quick_section_group').value;
        const sectionName = document.getElementById('quick_section_name').value;
        if (!classId) {
            alert("Please select a class first.");
            return;
        }
        try {
            const res = await axios.post('/api/sections', { class_id: classId, group_id: groupId || null, section_name: sectionName });
            const newSec = res.data.data;
            
            if (window.lastActiveModalId === 'admissionModal') {
                const currentClassId = document.getElementById('a_class').value;
                const currentGroupId = document.getElementById('a_group').value;
                if (currentClassId == classId && currentGroupId == groupId) {
                    if (typeof loadSections === 'function') {
                        await loadSections();
                    }
                    setSelectedValue('a_section', newSec.id);
                }
            } else if (window.lastActiveModalId === 'studentModal') {
                const currentClassId = document.getElementById('edit_class').value;
                const currentGroupId = document.getElementById('edit_group').value;
                if (currentClassId == classId && currentGroupId == groupId) {
                    const r = await axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: groupId } });
                    const sectionsOptions = r.data.data.map(sec => ({ value: sec.id, label: sec.section_name }));
                    populateDropdownSelect('edit_section', sectionsOptions, '', 'Select Section');
                    setSelectedValue('edit_section', newSec.id);
                }
            } else if (window.lastActiveModalId === 'bulkUploadModal') {
                const currentClassId = document.getElementById('bulkClass').value;
                const currentGroupId = document.getElementById('bulkGroup').value;
                if (currentClassId == classId && currentGroupId == groupId) {
                    if (typeof handleGroupChange === 'function') {
                        await handleGroupChange();
                    }
                    setSelectedValue('bulkSection', newSec.id);
                }
            } else if (window.lastActiveModalId === 'permModal') {
                const currentClassId = document.getElementById('class_id').value;
                const currentGroupId = document.getElementById('group_id').value;
                if (currentClassId == classId && currentGroupId == groupId) {
                    if (typeof initData === 'function') {
                        await initData();
                    }
                    filterDependents();
                    setSelectedValue('section_id', newSec.id);
                }
            } else if (window.lastActiveModalId === 'quickSubjectModal') {
                const currentClassId = document.getElementById('quick_subject_class').value;
                const currentGroupId = document.getElementById('quick_subject_group').value;
                if (currentClassId == classId && currentGroupId == groupId) {
                    const r = await axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: groupId } });
                    const sectionsOptions = r.data.data.map(sec => ({ value: sec.id, label: sec.section_name }));
                    populateDropdownSelect('quick_subject_section', sectionsOptions, '', 'Select Section');
                    setSelectedValue('quick_subject_section', newSec.id);
                }
            }

            closeQuickModal('quickSectionModal');
            
            if (typeof Toastify === 'function') {
                Toastify({
                    text: "Section added successfully!",
                    style: { background: "#10b981" }
                }).showToast();
            } else {
                Swal.fire({ icon: 'success', title: 'Success', text: 'Section added successfully!' });
            }
        } catch (err) {
            console.error(err);
            let errorMsg = "Failed to add section.";
            if (err.response && err.response.data && err.response.data.errors) {
                errorMsg = Object.values(err.response.data.errors).flat().join('\n');
            }
            Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
        }
    });

    document.getElementById('quick_session_class')?.addEventListener('change', async function() {
        const classId = this.value;
        populateDropdownSelect('quick_session_group', [], '', 'Select Group');
        populateDropdownSelect('quick_session_section', [], '', 'Select Section');
        if (!classId) return;
        try {
            const res = await axios.get(`/api/get-school-groups?class_id=${classId}`);
            const groupsOptions = res.data.data.map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('quick_session_group', groupsOptions, '', 'Select Group');
        } catch (err) {
            console.error(err);
        }
    });

    document.getElementById('quick_session_group')?.addEventListener('change', async function() {
        const groupId = this.value;
        populateDropdownSelect('quick_session_section', [], '', 'Select Section');
        if (!groupId) return;
        try {
            const res = await axios.get('/api/get-school-sections', { params: { group_id: groupId } });
            const sectionsOptions = res.data.data.map(sec => ({ value: sec.id, label: sec.section_name }));
            populateDropdownSelect('quick_session_section', sectionsOptions, '', 'Select Section');
        } catch (err) {
            console.error(err);
        }
    });

    window.calculateQuickSession = function() {
        const startVal = document.getElementById('quick_session_start_date').value;
        const endVal = document.getElementById('quick_session_end_date').value;
        if (startVal && endVal) {
            const start = new Date(startVal);
            const end = new Date(endVal);
            const sYear = start.getFullYear();
            const eYearShort = String(end.getFullYear()).slice(-2);
            document.getElementById('quick_session_year').value = `${sYear}-${eYearShort}`;
            const diff = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('quick_session_total_days').value = diff > 0 ? diff : 0;
        }
    };

    document.getElementById('quickSessionForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const classId = document.getElementById('quick_session_class').value;
        const groupId = document.getElementById('quick_session_group').value;
        const sectionId = document.getElementById('quick_session_section').value;
        const startDate = document.getElementById('quick_session_start_date').value;
        const endDate = document.getElementById('quick_session_end_date').value;
        const sessionYear = document.getElementById('quick_session_year').value;
        const totalDays = document.getElementById('quick_session_total_days').value;

        if (!classId) {
            alert("Please select a class first.");
            return;
        }
        try {
            const res = await axios.post('/api/sessions', {
                class_id: classId,
                group_id: groupId || null,
                section_id: sectionId || null,
                start_date: startDate,
                end_date: endDate,
                session_year: sessionYear,
                total_days: totalDays
            });
            const newSession = res.data.data;

            // Refresh session selectors
            if (window.lastActiveModalId === 'admissionModal') {
                if (typeof loadSessions === 'function') {
                    await loadSessions();
                }
                setSelectedValue('a_session', newSession.id);
            } else if (window.lastActiveModalId === 'studentModal') {
                const editClassId = document.getElementById('edit_class').value;
                const r = await axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: editClassId } });
                const sessionsOptions = r.data.data.map(s => ({ value: s.id, label: s.session_year }));
                populateDropdownSelect('edit_session', sessionsOptions, '', 'Select Session');
                setSelectedValue('edit_session', newSession.id);
            } else if (window.lastActiveModalId === 'bulkUploadModal') {
                const currentClassId = document.getElementById('bulkClass').value;
                const currentGroupId = document.getElementById('bulkGroup').value;
                const currentSectionId = document.getElementById('bulkSection').value;
                if (currentClassId == classId && currentGroupId == groupId && currentSectionId == sectionId) {
                    if (typeof handleSectionChange === 'function') {
                        await handleSectionChange();
                    }
                    setSelectedValue('bulkSession', newSession.id);
                }
            }

            closeQuickModal('quickSessionModal');

            if (typeof Toastify === 'function') {
                Toastify({
                    text: "Session added successfully!",
                    style: { background: "#10b981" }
                }).showToast();
            } else {
                Swal.fire({ icon: 'success', title: 'Success', text: 'Session added successfully!' });
            }
        } catch (err) {
            console.error(err);
            alert("Failed to add session.");
        }
    });

    document.getElementById('quickSubjectForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const classId = document.getElementById('quick_subject_class').value;
        const groupId = document.getElementById('quick_subject_group').value;
        const sectionId = document.getElementById('quick_subject_section').value;
        const subjectName = document.getElementById('quick_subject_name').value;
        const subjectCode = document.getElementById('quick_subject_code').value;
        const theoryMarks = document.getElementById('quick_subject_theory_marks').value;
        const practicalMarks = document.getElementById('quick_subject_practical_marks').value;

        if (!classId || !sectionId || !subjectName) {
            alert("Please fill in all required fields.");
            return;
        }

        try {
            const res = await axios.post('/api/school-subjects', {
                class_id: classId,
                group_id: groupId || null,
                section_id: sectionId,
                subject_name: subjectName,
                subject_code: subjectCode || null,
                marks: {
                    theory_marks: theoryMarks || null,
                    practical_marks: practicalMarks || null
                }
            });
            const newSub = res.data;

            if (window.lastActiveModalId === 'permModal') {
                const currentClassId = document.getElementById('class_id').value;
                if (currentClassId == classId) {
                    if (typeof initData === 'function') {
                        await initData();
                    }
                    filterDependents();
                    setSelectedValue('subject_id', newSub.id);
                }
            }

            closeQuickModal('quickSubjectModal');

            if (typeof Toastify === 'function') {
                Toastify({
                    text: "Subject added successfully!",
                    style: { background: "#10b981" }
                }).showToast();
            } else {
                Swal.fire({ icon: 'success', title: 'Success', text: 'Subject added successfully!' });
            }
        } catch (err) {
            console.error(err);
            let errorMsg = "Failed to add subject.";
            if (err.response && err.response.data && err.response.data.errors) {
                errorMsg = Object.values(err.response.data.errors).flat().join('\n');
            }
            Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
        }
    });
</script>
