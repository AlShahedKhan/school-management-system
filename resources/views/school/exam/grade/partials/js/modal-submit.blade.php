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
            if (id) {
                await axios.put(`/api/school-exam-grades/${id}`, { ...grades[0], full_mark });
            } else {
                await axios.post('/api/school-exam-grades', { full_mark, grades });
            }

            closeGradeModal();
            Toastify({ text: 'Grade saved successfully!', style: { background: '#10b981' }, duration: 3000 }).showToast();
            fetchGrades(currentPage);
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
