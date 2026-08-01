<script>
    let gradingSystemsData = [];

    function openSubjectModal(title) {
        document.getElementById('subjectForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('subjectModalTitle').innerText = typeof title === 'string' ? title : 'Add Subject';
        document.getElementById('subjectModal').classList.remove('hidden');
        setDropdownValue('subjectFormClass', '', 'Select Class');
        setDropdownValue('subjectFormGroup', '', 'Select Group');
        setDropdownValue('subjectFormSection', '', 'Select Section');
        setDropdownValue('subjectFormGrade', '', 'Select Grade Type');
        populateDropdown('subjectFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('subjectFormSectionMenu', [], 'id', 'section_name');
        ['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark', 'total_mark', 'fail_mark'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = 0;
        });
        loadSubjectClassSelect();
        loadSubjectGradeSelect();
        document.getElementById('max_allowed_mark').value = 100;
        toggleMarkFields();
    }
    function closeSubjectModal() {
        document.getElementById('subjectModal').classList.add('hidden');
    }
    document.getElementById('openSubjectModalBtn')?.addEventListener('click', openSubjectModal);
    const closeSubjectModalBtn = document.getElementById('closeSubjectModal');
    if (closeSubjectModalBtn) closeSubjectModalBtn.addEventListener('click', closeSubjectModal);

    document.getElementById('cancelSubjectBtn')?.addEventListener('click', function() {
        closeSubjectModal();
    });

    document.addEventListener('school:dropdown-add-modal-opened', async function (event) {
        const { targetModalId, sourceDropdownId } = event.detail || {};
        if (targetModalId !== 'subjectModal') {
            return;
        }

        if (sourceDropdownId === 'm_subject' && typeof window.prepareMarkSubjectModal === 'function') {
            await window.prepareMarkSubjectModal();
            return;
        }

        const sourceMap = {
            subject_name: ['class_name', 'group_name', 'routine_section_name'],
            m_subject: ['m_class', 'm_group', 'm_section'],
            f_subject: ['f_class', 'f_group', 'f_section'],
        };

        const [classField, groupField, sectionField] = sourceMap[sourceDropdownId] || [];
        if (!classField) {
            return;
        }

        const classId = getSelectedDropdownId(classField);
        const groupId = getSelectedDropdownId(groupField);
        const sectionId = getSelectedDropdownId(sectionField);

        setDropdownValue('subjectFormClass', '', 'Select Class');
        setDropdownValue('subjectFormGroup', '', 'Select Group');
        setDropdownValue('subjectFormSection', '', 'Select Section');
        populateDropdown('subjectFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('subjectFormSectionMenu', [], 'id', 'section_name');

        if (!classId) {
            return;
        }

        await loadSubjectClassSelect(classId);
        if (groupId) {
            await loadSubjectGroupSelect(groupId);
        }
        if (sectionId) {
            await loadSubjectSectionSelect(sectionId);
        }
    });

    function getSelectedDropdownId(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return '';
        const menu = document.getElementById(`${inputId}Menu`);
        if (!menu) return input.value || '';
        const option = menu.querySelector(`[data-value="${CSS.escape(input.value)}"]`);
        return option?.dataset.optionId || option?.dataset.id || input.value || '';
    }

    function loadSubjectClassSelect(selectedId = null) {
        return axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('subjectFormClassMenu', data, 'id', 'class_name');
            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) setDropdownValue('subjectFormClass', item.id, item.class_name);
            }
        }).catch(err => console.error("Class dropdown error:", err));
    }

    function loadSubjectGroupSelect(selectedId = null) {
        const classId = document.querySelector('#subjectFormClass')?.value;
        const params = classId ? { class_id: classId } : {};
        return axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('subjectFormGroupMenu', data, 'id', 'group_name');
            if (selectedId) {
                const item = data.find(g => String(g.id) === String(selectedId));
                if (item) setDropdownValue('subjectFormGroup', item.id, item.group_name);
            }
        }).catch(err => console.error("Group dropdown error:", err));
    }

    function loadSubjectSectionSelect(selectedId = null) {
        const classId = document.querySelector('#subjectFormClass')?.value;
        const groupId = document.querySelector('#subjectFormGroup')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        return axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('subjectFormSectionMenu', data, 'id', 'section_name');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('subjectFormSection', item.id, item.section_name);
            }
        }).catch(err => console.error("Section dropdown error:", err));
    }

    function loadSubjectGradeSelect(selectedId = null, selectedFullMark = null) {
        return axios.get('/api/get-grading-systems').then(res => {
            const data = res.data.data || [];
            console.log('Grade API response:', data);
            gradingSystemsData = data;
            const items = data.map(g => ({ id: g.id, grade_name: parseInt(g.full_mark) + ' Mark Grade' }));
            populateDropdown('subjectFormGradeMenu', items, 'id', 'grade_name');
            if (selectedId || selectedFullMark) {
                const item = data.find(g => selectedId
                    ? String(g.id) === String(selectedId)
                    : String(g.full_mark) === String(selectedFullMark));
                if (item) {
                    setDropdownValue('subjectFormGrade', item.id, parseInt(item.full_mark) + ' Mark Grade');
                    toggleMarkFields();
                    updateMaxAllowedMark();
                }
            }
        }).catch(err => console.error("Grade dropdown error:", err));
    }

    document.addEventListener('school:grade-saved', async function (event) {
        const { fullMark, returnModalId, isNew } = event.detail || {};
        if (returnModalId !== 'subjectModal' || !isNew || !fullMark) return;

        event.preventDefault();
        await loadSubjectGradeSelect(null, fullMark);
        document.getElementById('subjectModal')?.classList.remove('hidden');
    });

    function updateMaxAllowedMark() {
        const gradeId = document.getElementById('subjectFormGrade').value;
        const grade = gradingSystemsData.find(g => String(g.id) === String(gradeId));
        document.getElementById('max_allowed_mark').value = grade ? grade.full_mark : 100;
    }

    function toggleMarkFields() {
        const gradeInput = document.getElementById('subjectFormGrade');
        if (!gradeInput) return;
        const disabled = !gradeInput.value;
        ['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark', 'fail_mark'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = disabled;
        });
    }

    document.getElementById('subjectFormGrade')?.addEventListener('change', function() {
        toggleMarkFields();
        updateMaxAllowedMark();
    });

    function calculateTotalMark() {
        const tutorial = parseFloat(document.getElementById('tutorial_mark').value) || 0;
        const mcq = parseFloat(document.getElementById('mcq_mark').value) || 0;
        const writing = parseFloat(document.getElementById('writing_mark').value) || 0;
        const practical = parseFloat(document.getElementById('practical_mark').value) || 0;
        const total = tutorial + mcq + writing + practical;
        document.getElementById('total_mark').value = total;
    }

    ['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', calculateTotalMark);
    });
</script>
