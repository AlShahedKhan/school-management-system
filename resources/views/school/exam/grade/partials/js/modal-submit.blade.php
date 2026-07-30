<script>
    document.getElementById('gradeForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearGradeErrors();

        const id = document.getElementById('edit_id').value;
        const full_mark = document.getElementById('full_mark').value;
        const submitBtn = e.target.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i> Saving...';

        const gradeRows = document.querySelectorAll('.grade-row');
        const grades = [];
        gradeRows.forEach(row => {
            grades.push({
                grade_name: row.querySelector('[name="grade_name"]').value,
                grade_point: row.querySelector('[name="grade_point"]').value,
                mark_from: row.querySelector('[name="mark_from"]').value,
                mark_to: row.querySelector('[name="mark_to"]').value,
            });
        });

        try {
            let response;
            if (id) {
                response = await axios.put(`/api/school-exam-grades/${id}`, { ...grades[0], full_mark });
            } else {
                response = await axios.post('/api/school-exam-grades', { full_mark, grades });
            }

            const gradeModal = document.getElementById('gradeModal');
            const returnModalId = gradeModal?.dataset.returnModalId || null;
            const gradeSavedEvent = new CustomEvent('school:grade-saved', {
                cancelable: true,
                detail: {
                    fullMark: full_mark,
                    response: response?.data || null,
                    returnModalId,
                    isNew: !id,
                },
            });
            document.dispatchEvent(gradeSavedEvent);
            closeGradeModal();
            Toastify({ text: 'Grade saved successfully!', style: { background: '#10b981' }, duration: 3000 }).showToast();
            if (!gradeSavedEvent.defaultPrevented && typeof fetchGrades === 'function') {
                fetchGrades(typeof currentPage === 'undefined' ? 1 : currentPage);
            }
        } catch (err) {
            if (err.response?.status === 422) {
                const errors = err.response.data.errors;
                let handled = false;
                Object.keys(errors).forEach(function(field) {
                    const element = document.getElementById(field + '_error');
                    if (element) {
                        element.textContent = errors[field][0];
                        element.classList.remove('hidden');
                        handled = true;
                    }
                });
                if (!handled) {
                    const flat = Object.values(errors).flat().filter(Boolean);
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: flat.join('\n') });
                }
                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: err.response?.data?.message || 'Something went wrong.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save';
        }
    });
</script>
