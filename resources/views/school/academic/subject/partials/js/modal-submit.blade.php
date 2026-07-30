<script>
    document.getElementById('subjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSubjectErrors();
        const recordId = document.getElementById('record_id').value;
        const subjectModalElement = document.getElementById('subjectModal');
        const formData = new FormData(this);
        ['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark', 'total_mark', 'fail_mark'].forEach(id => {
            if (!formData.has(id) || formData.get(id) === null || formData.get(id) === '') {
                const el = document.getElementById(id);
                formData.set(id, el ? (el.value || 0) : 0);
            }
        });
        if (recordId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = recordId
            ? `{{ url('/api/school-subjects') }}/${recordId}`
            : `{{ url('/api/school-subjects') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => {
            Toastify({
                text: 'Subject Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();

            const subjectSavedEvent = new CustomEvent('school:subject-saved', {
                cancelable: true,
                detail: {
                    subjectItem: response.data.data,
                    returnModalId: subjectModalElement?.dataset.returnModalId || null,
                    isNew: !recordId,
                },
            });
            document.dispatchEvent(subjectSavedEvent);

            subjectModalElement?.classList.add('hidden');
            this.reset();
            if (subjectModalElement) {
                delete subjectModalElement.dataset.returnModalId;
            }

            if (!subjectSavedEvent.defaultPrevented) {
                if (typeof fetchSubjects === 'function') {
                    fetchSubjects(currentPage);
                }
            }
        })
        .catch(error => {
            if (error.response?.status === 422) {
                const messages = showSubjectErrors(error.response.data.errors || {});
                Swal.fire({
                    icon: 'error',
                    title: 'Please fix the subject form',
                    text: messages.join('\n') || 'Please complete all required fields.',
                });
                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: error.response?.data?.message || 'Something went wrong.'
            });
        });
    });
</script>
