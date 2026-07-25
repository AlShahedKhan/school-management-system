<script>
    document.getElementById('subjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSubjectErrors();
        const recordId = document.getElementById('record_id').value;
        const formData = new FormData(this);
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
        .then(() => {
            Toastify({
                text: 'Subject Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            subjectModal.classList.add('hidden');
            fetchSubjects(currentPage);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showSubjectErrors(error.response.data.errors);
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
