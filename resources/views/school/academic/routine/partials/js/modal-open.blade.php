<script>
    function openRoutineModal(title) {
        document.getElementById('routineForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('routineModalTitle').innerText = typeof title === 'string' ? title : 'Add Routine';
        document.getElementById('routineModal').classList.remove('hidden');
        setDropdownValue('routineFormDay', '', 'Select Day');
        setDropdownValue('routineFormTeacher', '', 'Select Teacher');
        setDropdownValue('routineFormClass', '', 'Select Class');
        setDropdownValue('routineFormGroup', '', 'Select Group');
        setDropdownValue('routineFormSection', '', 'Select Section');
        setDropdownValue('routineFormSubject', '', 'Select Subject');
        populateDropdown('routineFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('routineFormSectionMenu', [], 'id', 'section_name');
        populateDropdown('routineFormSubjectMenu', [], 'id', 'subject_name');
        loadRoutineDaySelect();
        loadRoutineTeacherSelect();
        loadRoutineClassSelect();
    }
    function closeRoutineModal() {
        document.getElementById('routineModal').classList.add('hidden');
    }
    document.getElementById('openRoutineModalBtn')?.addEventListener('click', openRoutineModal);
    const closeRoutineModalBtn = document.getElementById('closeRoutineModal');
    if (closeRoutineModalBtn) closeRoutineModalBtn.addEventListener('click', closeRoutineModal);

    document.getElementById('cancelRoutineBtn')?.addEventListener('click', function() {
        closeRoutineModal();
    });

    function loadRoutineDaySelect(selectedDay = null) {
        const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        const items = days.map(d => ({ id: d, day_name: d }));
        populateDropdown('routineFormDayMenu', items, 'id', 'day_name');
        if (selectedDay) {
            setDropdownValue('routineFormDay', selectedDay, selectedDay);
        }
    }

    function loadRoutineTeacherSelect(selectedId = null) {
        axios.get('/api/teachers', { params: { all: true, status: 'Active' } }).then(res => {
            const data = res.data.data || res.data || [];
            populateDropdown('routineFormTeacherMenu', data, 'id', 'name');
            if (selectedId) {
                const item = data.find(t => String(t.id) === String(selectedId));
                if (item) setDropdownValue('routineFormTeacher', item.id, item.name);
            }
        }).catch(err => console.error("Teacher dropdown error:", err));
    }

    function loadRoutineClassSelect(selectedId = null) {
        axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('routineFormClassMenu', data, 'id', 'class_name');
            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) setDropdownValue('routineFormClass', item.id, item.class_name);
            }
        }).catch(err => console.error("Class dropdown error:", err));
    }

    function loadRoutineGroupSelect(selectedId = null) {
        const classId = document.querySelector('#routineFormClass')?.value;
        const params = classId ? { class_id: classId } : {};
        axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('routineFormGroupMenu', data, 'id', 'group_name');
            if (selectedId) {
                const item = data.find(g => String(g.id) === String(selectedId));
                if (item) setDropdownValue('routineFormGroup', item.id, item.group_name);
            }
        }).catch(err => console.error("Group dropdown error:", err));
    }

    function loadRoutineSectionSelect(selectedId = null) {
        const classId = document.querySelector('#routineFormClass')?.value;
        const groupId = document.querySelector('#routineFormGroup')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('routineFormSectionMenu', data, 'id', 'section_name');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('routineFormSection', item.id, item.section_name);
            }
        }).catch(err => console.error("Section dropdown error:", err));
    }

    function loadRoutineSubjectSelect(selectedId = null) {
        const classId = document.querySelector('#routineFormClass')?.value;
        const groupId = document.querySelector('#routineFormGroup')?.value;
        const sectionId = document.querySelector('#routineFormSection')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        if (sectionId) params.section_id = sectionId;
        axios.get('/api/get-school-subjects', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('routineFormSubjectMenu', data, 'id', 'subject_name');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('routineFormSubject', item.id, item.subject_name);
            }
        }).catch(err => console.error("Subject dropdown error:", err));
    }
</script>
