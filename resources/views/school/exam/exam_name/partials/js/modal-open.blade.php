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
        const examModal = document.getElementById('examModal');
        const returnModalId = examModal?.dataset.returnModalId;

        examModal?.classList.add('hidden');

        if (returnModalId) {
            document.getElementById(returnModalId)?.classList.remove('hidden');
            delete examModal.dataset.returnModalId;
        }
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

        document.addEventListener('click', function (event) {
            const sessionAddButton = event.target.closest('[data-dropdown-add-target="sessionModal"]');

            if (sessionAddButton) {
                const sourceDropdown = sessionAddButton.closest('[data-dropdown-select]')
                    ?.querySelector('[data-dropdown-select-input]');

                if (sourceDropdown && ['examFormSession', 'examSessionFilter'].includes(sourceDropdown.id)) {
                    const parentMap = {
                        examFormSession: ['examFormClass', 'examFormGroup', 'examFormSection'],
                        examSessionFilter: ['examClassFilter', 'examGroupFilter', 'examSectionFilter'],
                    };
                    const [classInputId, groupInputId, sectionInputId] = parentMap[sourceDropdown.id];
                    const classId = document.getElementById(classInputId)?.value || '';
                    const groupId = document.getElementById(groupInputId)?.value || '';
                    const sectionId = document.getElementById(sectionInputId)?.value || '';

                    if (!classId || !groupId || !sectionId) {
                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();

                        Toastify({
                            text: 'Please select class, group and section first.',
                            gravity: 'top',
                            position: 'right',
                            style: { background: '#f59e0b' },
                        }).showToast();

                        return;
                    }
                }
            }

            const groupAddButton = event.target.closest('[data-dropdown-add-target="groupModal"]');

            if (groupAddButton) {
                const sourceDropdown = groupAddButton.closest('[data-dropdown-select]')
                    ?.querySelector('[data-dropdown-select-input]');

                if (sourceDropdown && ['examFormGroup', 'examGroupFilter'].includes(sourceDropdown.id)) {
                    const parentMap = {
                        examFormGroup: 'examFormClass',
                        examGroupFilter: 'examClassFilter',
                    };
                    const classId = document.getElementById(parentMap[sourceDropdown.id])?.value || '';

                    if (!classId) {
                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();

                        Toastify({
                            text: 'Please select class first.',
                            gravity: 'top',
                            position: 'right',
                            style: { background: '#f59e0b' },
                        }).showToast();

                        return;
                    }
                }
            }

            const sectionAddButton = event.target.closest('[data-dropdown-add-target="sectionModal"]');

            if (!sectionAddButton) {
                return;
            }

            const sourceDropdown = sectionAddButton.closest('[data-dropdown-select]')
                ?.querySelector('[data-dropdown-select-input]');

            if (!sourceDropdown || !['examFormSection', 'examSectionFilter'].includes(sourceDropdown.id)) {
                return;
            }

            const parentMap = {
                examFormSection: ['examFormClass', 'examFormGroup'],
                examSectionFilter: ['examClassFilter', 'examGroupFilter'],
            };
            const [classInputId, groupInputId] = parentMap[sourceDropdown.id];
            const classId = document.getElementById(classInputId)?.value || '';
            const groupId = document.getElementById(groupInputId)?.value || '';

            if (classId && groupId) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            Toastify({
                text: 'Please select class and group first.',
                gravity: 'top',
                position: 'right',
                style: { background: '#f59e0b' },
            }).showToast();
        }, true);

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

        document.addEventListener('school:dropdown-add-modal-opened', async function (event) {
            const { targetModalId, sourceDropdownId } = event.detail || {};
            if (targetModalId !== 'examModal') {
                return;
            }

            const sourceMap = {
                exam_name: ['class_name', 'group_name', 'section_name', 'session_name'],
            };

            const parentIds = sourceMap[sourceDropdownId];
            if (!parentIds) {
                return;
            }

            const [classField, groupField, sectionField, sessionField] = parentIds;
            const className = getDropdownSelectSelectedLabel(classField);
            const groupName = getDropdownSelectSelectedLabel(groupField);
            const sectionName = getDropdownSelectSelectedLabel(sectionField);
            const sessionName = getDropdownSelectSelectedLabel(sessionField);

            setDropdownValue('examFormClass', '', 'Select Class');
            setDropdownValue('examFormGroup', '', 'Select Group');
            setDropdownValue('examFormSection', '', 'Select Section');
            setDropdownValue('examFormSession', '', 'Select Session');
            populateDropdown('examFormGroupMenu', [], 'id', 'group_name');
            populateDropdown('examFormSectionMenu', [], 'id', 'section_name');
            populateDropdown('examFormSessionMenu', [], 'id', 'session_year');

            if (!className) {
                return;
            }

            await loadExamFormClassSelect(className);
            const classId = document.getElementById('examFormClass')?.value || '';
            if (!classId) {
                return;
            }

            if (groupName) {
                await loadExamFormGroupSelect(classId, groupName);
            }

            const groupId = document.getElementById('examFormGroup')?.value || '';
            if (sectionName && groupId) {
                await loadExamFormSectionSelect(classId, groupId, sectionName);
            }

            const sectionId = document.getElementById('examFormSection')?.value || '';
            if (sessionName && sectionId) {
                await loadExamFormSessionSelect(classId, groupId, sectionId, sessionName);
            }
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

        document.addEventListener('school:section-saved', async function (event) {
            const { sectionItem, returnModalId, isNew } = event.detail || {};

            if (
                returnModalId !== 'examModal'
                || !isNew
                || !sectionItem?.id
                || !sectionItem?.class_id
                || !sectionItem?.group_id
            ) {
                return;
            }

            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateDropdown('examFormClassMenu', classes, 'id', 'class_name');

                const selectedClass = classes.find(item => String(item.id) === String(sectionItem.class_id));
                if (selectedClass) {
                    setDropdownValue('examFormClass', selectedClass.id, selectedClass.class_name);
                }

                const groupResponse = await axios.get('/api/get-school-groups', {
                    params: { class_id: sectionItem.class_id },
                });
                const groups = groupResponse.data.data || [];
                populateDropdown('examFormGroupMenu', groups, 'id', 'group_name');

                const selectedGroup = groups.find(item => String(item.id) === String(sectionItem.group_id));
                if (selectedGroup) {
                    setDropdownValue('examFormGroup', selectedGroup.id, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get('/api/get-school-sections', {
                    params: {
                        class_id: sectionItem.class_id,
                        group_id: sectionItem.group_id,
                    },
                });
                const sections = sectionResponse.data.data || [];
                populateDropdown('examFormSectionMenu', sections, 'id', 'section_name');

                const selectedSection = sections.find(item => String(item.id) === String(sectionItem.id));
                if (selectedSection) {
                    setDropdownValue('examFormSection', selectedSection.id, selectedSection.section_name);
                }

                setDropdownValue('examFormSession', '', 'Select Session');
                populateDropdown('examFormSessionMenu', [], 'id', 'session_year');
                loadExamFormSessionBySection(sectionItem.class_id, sectionItem.group_id, sectionItem.id);
            } finally {
                document.getElementById('examModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:session-saved', async function (event) {
            const { sessionItem, returnModalId, isNew } = event.detail || {};

            if (
                returnModalId !== 'examModal'
                || !isNew
                || !sessionItem?.id
                || !sessionItem?.class_id
                || !sessionItem?.group_id
                || !sessionItem?.section_id
            ) {
                return;
            }

            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateDropdown('examFormClassMenu', classes, 'id', 'class_name');

                const selectedClass = classes.find(item => String(item.id) === String(sessionItem.class_id));
                if (selectedClass) {
                    setDropdownValue('examFormClass', selectedClass.id, selectedClass.class_name);
                }

                const groupResponse = await axios.get('/api/get-school-groups', {
                    params: { class_id: sessionItem.class_id },
                });
                const groups = groupResponse.data.data || [];
                populateDropdown('examFormGroupMenu', groups, 'id', 'group_name');

                const selectedGroup = groups.find(item => String(item.id) === String(sessionItem.group_id));
                if (selectedGroup) {
                    setDropdownValue('examFormGroup', selectedGroup.id, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get('/api/get-school-sections', {
                    params: {
                        class_id: sessionItem.class_id,
                        group_id: sessionItem.group_id,
                    },
                });
                const sections = sectionResponse.data.data || [];
                populateDropdown('examFormSectionMenu', sections, 'id', 'section_name');

                const selectedSection = sections.find(item => String(item.id) === String(sessionItem.section_id));
                if (selectedSection) {
                    setDropdownValue('examFormSection', selectedSection.id, selectedSection.section_name);
                }

                const sessionResponse = await axios.get('/api/get-school-sessions', {
                    params: {
                        class_id: sessionItem.class_id,
                        group_id: sessionItem.group_id,
                        section_id: sessionItem.section_id,
                    },
                });
                const sessions = (sessionResponse.data.data || [])
                    .map(item => ({ id: item.id, session_year: item.session_year }));
                populateDropdown('examFormSessionMenu', sessions, 'id', 'session_year');

                const selectedSession = sessions.find(item => String(item.id) === String(sessionItem.id));
                if (selectedSession) {
                    setDropdownValue('examFormSession', selectedSession.id, selectedSession.session_year);
                }
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
        return axios.get('/api/get-school-classes').then(res => {
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
        return axios.get('/api/get-school-groups', { params }).then(res => {
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
        return axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('examFormSectionMenu', data, 'id', 'section_name');
            if (selectedName) {
                const item = data.find(s => s.section_name === selectedName);
                if (item) setDropdownValue('examFormSection', item.id, item.section_name);
            }
        }).catch(() => {});
    }

    function loadExamFormSessionSelect(classId, groupId, sectionId, selectedName = null) {
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        if (sectionId) params.section_id = sectionId;
        return axios.get('/api/get-school-sessions', { params }).then(res => {
            const data = res.data.data || [];
            const items = data.map(s => ({ id: s.id, session_year: s.session_year }));
            populateDropdown('examFormSessionMenu', items, 'id', 'session_year');
            if (selectedName) {
                const item = data.find(s => s.session_year === selectedName);
                if (item) setDropdownValue('examFormSession', item.id, item.session_year);
            }
        }).catch(() => {});
    }

    function getDropdownSelectSelectedId(inputId) {
        const input = document.getElementById(inputId);
        if (!input) {
            return '';
        }

        const menu = document.getElementById(`${inputId}Menu`);
        if (!menu) {
            return input.value || '';
        }

        const option = menu.querySelector(`[data-value="${CSS.escape(input.value)}"]`);
        return option?.dataset.optionId || option?.dataset.id || input.value || '';
    }

    function getDropdownSelectSelectedLabel(inputId) {
        const input = document.getElementById(inputId);
        if (!input) {
            return '';
        }

        const menu = document.getElementById(`${inputId}Menu`);
        const option = menu?.querySelector(`[data-value="${CSS.escape(input.value)}"]`);
        return option?.textContent?.trim() || input.value || '';
    }

    function normalizeDateInputValue(value) {
        return value ? String(value).slice(0, 10) : '';
    }

    async function editExam(id) {
        try {
            const res = await axios.get('/api/school-exam-names/' + id);
            const item = res.data;

            document.getElementById('edit_id').value = item.id;
            document.getElementById('examModalTitle').textContent = 'Edit Exam';
            document.getElementById('examFormName').value = item.exam_name || '';
            document.getElementById('exam_start_date').value = normalizeDateInputValue(item.exam_start_date);
            document.getElementById('exam_end_date').value = normalizeDateInputValue(item.exam_end_date);

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
