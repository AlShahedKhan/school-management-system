<script>
    document.getElementById('sessionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSessionErrors();
        const recordId = document.getElementById('record_id').value;
        const formData = new FormData(this);
        if (recordId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = recordId
            ? `{{ url('/api/school-sessions') }}/${recordId}`
            : `{{ url('/api/school-sessions') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Session Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            sessionModal.classList.add('hidden');
            setTimeout(() => { window.location.reload(); }, 500);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showSessionErrors(error.response.data.errors);
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
