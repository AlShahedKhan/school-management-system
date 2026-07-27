<script>
    document.getElementById('examForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearExamErrors();
        const submitBtn = e.target.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i> Saving...';

        const classInput = document.querySelector('#examFormClass');
        const groupInput = document.querySelector('#examFormGroup');
        const sectionInput = document.querySelector('#examFormSection');
        const sessionInput = document.querySelector('#examFormSession');
        const examNameInput = document.getElementById('examFormName');

        const payload = {
            class_id: classInput ? classInput.value : '',
            group_id: groupInput ? groupInput.value : '',
            section_id: sectionInput ? sectionInput.value : '',
            session_id: sessionInput ? sessionInput.value : '',
            exam_name: examNameInput ? examNameInput.value.trim() : '',
            exam_start_date: document.getElementById('exam_start_date')?.value || '',
            exam_end_date: document.getElementById('exam_end_date')?.value || '',
        };

        const editId = document.getElementById('edit_id').value;
        const urlPath = editId ? `/api/school-exam-names/${editId}` : '/api/school-exam-names';

        let response = null;
        try {
            const requestConfig = {
                headers: {
                    Accept: 'application/json',
                },
            };

            response = editId
                ? await axios.post(urlPath, { ...payload, _method: 'PUT' }, requestConfig)
                : await axios.post(urlPath, payload, requestConfig);
        } catch (err) {
            const errors = err.response?.data?.errors;

            if (err.response?.status === 422 || errors) {
                showExamErrors(errors ?? {});
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save';
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: err.response?.data?.message || err.response?.data?.error || err.message || 'Something went wrong.'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save';
            return;
        }

        try {
            const examModal = document.getElementById('examModal');
            const returnModalId = examModal?.dataset.returnModalId || null;
            const examItem = response?.data?.data || null;

            const examSavedEvent = new CustomEvent('school:exam-saved', {
                detail: {
                    examItem,
                    returnModalId,
                    isNew: !editId,
                },
            });

            document.dispatchEvent(examSavedEvent);
            closeExamModal();
            Toastify({ text: 'Exam name saved successfully!', style: { background: '#10b981' }, duration: 3000 }).showToast();

            if (typeof loadExams === 'function') {
                try {
                    await loadExams();
                } catch (reloadError) {
                    console.error('Exam list refresh failed after save:', reloadError);
                }
            }
        } catch (uiError) {
            console.error('Exam save succeeded but UI update failed:', uiError);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save';
        }
    });
</script>
