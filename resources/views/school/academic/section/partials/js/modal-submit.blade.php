<script>
    document.getElementById('sectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSectionErrors();
        const sectionId = document.getElementById('section_record_id').value;
        const formData = new FormData(this);
        if (sectionId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = sectionId
            ? `{{ url('/api/sections') }}/${sectionId}`
            : `{{ url('/api/sections') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Section Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            sectionModal.classList.add('hidden');
            setTimeout(() => { window.location.reload(); }, 500);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showSectionErrors(error.response.data.errors);
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
