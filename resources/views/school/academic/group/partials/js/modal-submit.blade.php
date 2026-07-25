<script>
    document.getElementById('groupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearGroupErrors();
        const groupId = document.getElementById('group_id').value;
        const formData = new FormData(this);
        if (groupId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = groupId
            ? `{{ url('/api/groups') }}/${groupId}`
            : `{{ url('/api/groups') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Group Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            groupModal.classList.add('hidden');
            setTimeout(() => { window.location.reload(); }, 500);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showGroupErrors(error.response.data.errors);
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
