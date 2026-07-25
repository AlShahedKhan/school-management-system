<script>
    function loadExams() {
        fetchExams(currentPage || 1);
    }

    function openExamModal() {
        resetExamForm();
        document.getElementById('examModalTitle').textContent = 'Add Exam';
        document.getElementById('examModal').classList.remove('hidden');
        loadExamFormClassSelect();
    }

    function closeExamModal() {
        document.getElementById('examModal').classList.add('hidden');
    }

    function loadExamFormGroupByClass(classId) {
        const params = classId ? { class_id: classId } : {};
        axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormGroupMenu', data, 'id', 'group_name');
            setDropdownValue('examFormGroup', '', 'Select Group');
            setDropdownValue('examFormSection', '', 'Select Section');
            populateDropdown('examFormSectionMenu', [], 'id', 'section_name');
            setDropdownValue('examFormSession', '', 'Select Session');
            populateDropdown('examFormSessionMenu', [], 'id', 'session_year');

            loadExamFormSectionByGroup(classId, '');
        }).catch(() => {});
    }

    function loadExamFormSectionByGroup(classId, groupId) {
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormSectionMenu', data, 'id', 'section_name');
            setDropdownValue('examFormSession', '', 'Select Session');
            populateDropdown('examFormSessionMenu', [], 'id', 'session_year');
        }).catch(() => {});
    }

    function loadExamFormSessionBySection(classId, groupId, sectionId) {
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        if (sectionId) params.section_id = sectionId;
        axios.get('/api/get-school-sessions', { params }).then(res => {
            const data = res.data.data || [];
            const items = data.map(s => ({ id: s.id, session_year: s.session_year }));
            populateDropdown('examFormSessionMenu', items, 'id', 'session_year');
        }).catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', function () {
        const closeBtn = document.getElementById('closeExamModal');
        if (closeBtn) closeBtn.addEventListener('click', closeExamModal);

        document.getElementById('examFormClass')?.addEventListener('change', function () {
            loadExamFormGroupByClass(this.value);
        });

        document.getElementById('examFormGroup')?.addEventListener('change', function () {
            const classInput = document.getElementById('examFormClass');
            const classId = classInput ? classInput.value : '';
            loadExamFormSectionByGroup(classId, this.value);
        });

        document.getElementById('examFormSection')?.addEventListener('change', function () {
            const classInput = document.getElementById('examFormClass');
            const classId = classInput ? classInput.value : '';
            const groupInput = document.getElementById('examFormGroup');
            const groupId = groupInput ? groupInput.value : '';
            loadExamFormSessionBySection(classId, groupId, this.value);
        });

        document.addEventListener('school:class-saved', async function (event) {
            const { classItem, returnModalId, isNew } = event.detail || {};

            if (returnModalId !== 'examModal' || !isNew || !classItem?.id) {
                return;
            }

            event.preventDefault();

            try {
                const response = await axios.get('/api/get-school-classes');
                populateDropdown('examFormClassMenu', response.data.data || [], 'id', 'class_name');
                setDropdownValue('examFormClass', classItem.id, classItem.class_name);
                loadExamFormGroupByClass(classItem.id);
            } finally {
                document.getElementById('examModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:group-saved', async function (event) {
            const { groupItem, returnModalId, isNew } = event.detail || {};

            if (returnModalId !== 'examModal' || !isNew || !groupItem?.id || !groupItem?.class_id) {
                return;
            }

            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateDropdown('examFormClassMenu', classes, 'id', 'class_name');

                const selectedClass = classes.find(item => String(item.id) === String(groupItem.class_id));
                if (selectedClass) {
                    setDropdownValue('examFormClass', selectedClass.id, selectedClass.class_name);
                }

                const groupResponse = await axios.get('/api/get-school-groups', {
                    params: { class_id: groupItem.class_id },
                });
                const groups = groupResponse.data.data || [];
                populateDropdown('examFormGroupMenu', groups, 'id', 'group_name');

                const selectedGroup = groups.find(item => String(item.id) === String(groupItem.id));
                if (selectedGroup) {
                    setDropdownValue('examFormGroup', selectedGroup.id, selectedGroup.group_name);
                }

                setDropdownValue('examFormSection', '', 'Select Section');
                populateDropdown('examFormSectionMenu', [], 'id', 'section_name');
                setDropdownValue('examFormSession', '', 'Select Session');
                populateDropdown('examFormSessionMenu', [], 'id', 'session_year');
                loadExamFormSectionByGroup(groupItem.class_id, groupItem.id);
            } finally {
                document.getElementById('examModal')?.classList.remove('hidden');
            }
        });
    });

    function resetExamForm() {
        document.getElementById('examForm').reset();
        document.getElementById('edit_id').value = '';
        setDropdownValue('examFormClass', '', 'Select Class');
        setDropdownValue('examFormGroup', '', 'Select Group');
        setDropdownValue('examFormSection', '', 'Select Section');
        setDropdownValue('examFormSession', '', 'Select Session');
        document.querySelectorAll('#examForm .text-red-500').forEach(e => e.classList.add('hidden'));
    }

    function loadExamFormClassSelect(selectedName = null) {
        axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormClassMenu', data, 'id', 'class_name');
            if (selectedName) {
                const item = data.find(c => c.class_name === selectedName);
                if (item) setDropdownValue('examFormClass', item.id, item.class_name);
            }
        }).catch(() => {});
    }

    function loadExamFormGroupSelect(classId, selectedName = null) {
        const params = classId ? { class_id: classId } : {};
        axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormGroupMenu', data, 'id', 'group_name');
            if (selectedName) {
                const item = data.find(g => g.group_name === selectedName);
                if (item) setDropdownValue('examFormGroup', item.id, item.group_name);
            }
        }).catch(() => {});
    }

    function loadExamFormSectionSelect(classId, groupId, selectedName = null) {
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormSectionMenu', data, 'id', 'section_name');
            if (selectedName) {
                const item = data.find(s => s.section_name === selectedName);
                if (item) setDropdownValue('examFormSection', item.id, item.section_name);
            }
        }).catch(() => {});
    }

    function loadExamFormSessionSelect(classId, sectionId, selectedName = null) {
        const params = {};
        if (classId) params.class_id = classId;
        if (sectionId) params.section_id = sectionId;
        axios.get('/api/get-school-sessions', { params }).then(res => {
            const data = res.data.data || [];
            const items = data.map(s => ({ id: s.id, session_year: s.session_year }));
            populateDropdown('examFormSessionMenu', items, 'id', 'session_year');
            if (selectedName) {
                const item = data.find(s => s.session_year === selectedName);
                if (item) setDropdownValue('examFormSession', item.id, item.session_year);
            }
        }).catch(() => {});
    }

    async function editExam(id) {
        try {
            const res = await axios.get('/api/school-exam-names/' + id);
            const item = res.data;

            document.getElementById('edit_id').value = item.id;
            document.getElementById('examModalTitle').textContent = 'Edit Exam';
            document.getElementById('exam_name').value = item.exam_name || '';

            const classRes = await axios.get('/api/get-school-classes');
            const classes = classRes.data.data || [];
            populateDropdown('examFormClassMenu', classes, 'id', 'class_name');
            const matchedClass = classes.find(c => String(c.id) === String(item.class_id));
            if (matchedClass) {
                setDropdownValue('examFormClass', matchedClass.id, matchedClass.class_name);

                const groupRes = await axios.get('/api/get-school-groups?class_id=' + matchedClass.id);
                const groups = groupRes.data.data || [];
                populateDropdown('examFormGroupMenu', groups, 'id', 'group_name');
                const matchedGroup = groups.find(g => String(g.id) === String(item.group_id));
                const groupId = matchedGroup ? matchedGroup.id : '';
                const groupQuery = groupId ? '&group_id=' + groupId : '';

                if (matchedGroup) {
                    setDropdownValue('examFormGroup', matchedGroup.id, matchedGroup.group_name);
                }

                const sectionRes = await axios.get('/api/get-school-sections?class_id=' + matchedClass.id + groupQuery);
                const sections = sectionRes.data.data || [];
                populateDropdown('examFormSectionMenu', sections, 'id', 'section_name');
                const matchedSection = sections.find(s => String(s.id) === String(item.section_id));
                if (matchedSection) {
                    setDropdownValue('examFormSection', matchedSection.id, matchedSection.section_name);

                    const sessionRes = await axios.get('/api/get-school-sessions?class_id=' + matchedClass.id + groupQuery + '&section_id=' + matchedSection.id);
                    const sessions = sessionRes.data.data || [];
                    const sessionItems = sessions.map(s => ({ id: s.id, session_year: s.session_year }));
                    populateDropdown('examFormSessionMenu', sessionItems, 'id', 'session_year');
                    const matchedSession = sessions.find(s => String(s.id) === String(item.session_id));
                    if (matchedSession) {
                        setDropdownValue('examFormSession', matchedSession.id, matchedSession.session_year);
                    }
                }
            }

            document.getElementById('examModal').classList.remove('hidden');
        } catch (err) {
            Swal.fire('Error', 'Failed to load exam data.', 'error');
        }
    }
</script>
