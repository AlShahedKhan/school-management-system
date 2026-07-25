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

        const payload = {
            class_id: classInput ? classInput.value : '',
            group_id: groupInput ? groupInput.value : '',
            section_id: sectionInput ? sectionInput.value : '',
            session_id: sessionInput ? sessionInput.value : '',
            exam_name: document.getElementById('exam_name').value.trim(),
        };

        const editId = document.getElementById('edit_id').value;
        const urlPath = editId ? `/api/school-exam-names/${editId}` : '/api/school-exam-names';

        try {
            const requestConfig = {
                headers: {
                    Accept: 'application/json',
                },
            };

            if (editId) {
                await axios.post(urlPath, { ...payload, _method: 'PUT' }, requestConfig);
            } else {
                await axios.post(urlPath, payload, requestConfig);
            }

            closeExamModal();
            Toastify({ text: 'Exam name saved successfully!', style: { background: '#10b981' }, duration: 3000 }).showToast();
            await loadExams();
        } catch (err) {
            const errors = err.response?.data?.errors;

            if (err.response?.status === 422 || errors) {
                showExamErrors(errors ?? {});
                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: err.response?.data?.message || err.response?.data?.error || 'Something went wrong.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save';
        }
    });
</script>
