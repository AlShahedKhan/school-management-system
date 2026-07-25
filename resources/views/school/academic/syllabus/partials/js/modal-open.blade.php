<script>
    function openSyllabusModal(title) {
        document.getElementById('syllabusForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('syllabusModalTitle').innerText = typeof title === 'string' ? title : 'Add Syllabus';
        document.getElementById('syllabusModal').classList.remove('hidden');
        setDropdownValue('syllabusFormClass', '', 'Select Class');
        setDropdownValue('syllabusFormGroup', '', 'Select Group');
        setDropdownValue('syllabusFormSection', '', 'Select Section');
        setDropdownValue('syllabusFormSession', '', 'Select Session');
        setDropdownValue('syllabusFormSubject', '', 'Select Subject');
        setDropdownValue('syllabusFormExam', '', 'Select Exam');
        populateDropdown('syllabusFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('syllabusFormSectionMenu', [], 'id', 'section_name');
        populateDropdown('syllabusFormSessionMenu', [], 'id', 'session_year');
        populateDropdown('syllabusFormSubjectMenu', [], 'id', 'subject_name');
        populateDropdown('syllabusFormExamMenu', [], 'id', 'exam_name');
        ['start_page', 'end_page'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        loadSyllabusClassSelect();
    }
    function closeSyllabusModal() {
        document.getElementById('syllabusModal').classList.add('hidden');
    }
    document.getElementById('openSyllabusModalBtn')?.addEventListener('click', openSyllabusModal);
    const closeSyllabusModalBtn = document.getElementById('closeSyllabusModal');
    if (closeSyllabusModalBtn) closeSyllabusModalBtn.addEventListener('click', closeSyllabusModal);

    document.getElementById('cancelSyllabusBtn')?.addEventListener('click', function() {
        closeSyllabusModal();
    });

    function loadSyllabusClassSelect(selectedId = null) {
        axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormClassMenu', data, 'id', 'class_name');
            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormClass', item.id, item.class_name);
            }
        }).catch(err => console.error("Class dropdown error:", err));
    }

    function loadSyllabusGroupSelect(selectedId = null) {
        const classId = document.querySelector('#syllabusFormClass')?.value;
        const params = classId ? { class_id: classId } : {};
        axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormGroupMenu', data, 'id', 'group_name');
            if (selectedId) {
                const item = data.find(g => String(g.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormGroup', item.id, item.group_name);
            }
        }).catch(err => console.error("Group dropdown error:", err));
    }

    function loadSyllabusSectionSelect(selectedId = null) {
        const classId = document.querySelector('#syllabusFormClass')?.value;
        const groupId = document.querySelector('#syllabusFormGroup')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormSectionMenu', data, 'id', 'section_name');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormSection', item.id, item.section_name);
            }
        }).catch(err => console.error("Section dropdown error:", err));
    }

    function loadSyllabusSessionSelect(selectedId = null) {
        const classId = document.querySelector('#syllabusFormClass')?.value;
        const groupId = document.querySelector('#syllabusFormGroup')?.value;
        const sectionId = document.querySelector('#syllabusFormSection')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        if (sectionId) params.section_id = sectionId;
        axios.get('/api/get-school-sessions', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormSessionMenu', data, 'id', 'session_year');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormSession', item.id, item.session_year);
            }
        }).catch(err => console.error("Session dropdown error:", err));
    }

    function loadSyllabusSubjectSelect(selectedId = null) {
        const classId = document.querySelector('#syllabusFormClass')?.value;
        const params = classId ? { class_id: classId } : {};
        axios.get('/api/get-school-subjects', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormSubjectMenu', data, 'id', 'subject_name');
            if (selectedId) {
                const item = data.find(s => String(s.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormSubject', item.id, item.subject_name);
            }
        }).catch(err => console.error("Subject dropdown error:", err));
    }

    function loadSyllabusExamSelect(selectedId = null) {
        const classId = document.querySelector('#syllabusFormClass')?.value;
        const groupId = document.querySelector('#syllabusFormGroup')?.value;
        const sectionId = document.querySelector('#syllabusFormSection')?.value;
        const sessionId = document.querySelector('#syllabusFormSession')?.value;
        const params = {};
        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;
        if (sectionId) params.section_id = sectionId;
        if (sessionId) params.session_id = sessionId;
        axios.get('/api/get-school-exams', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('syllabusFormExamMenu', data, 'id', 'exam_name');
            if (selectedId) {
                const item = data.find(e => String(e.id) === String(selectedId));
                if (item) setDropdownValue('syllabusFormExam', item.id, item.exam_name);
            }
        }).catch(err => console.error("Exam dropdown error:", err));
    }
</script>
