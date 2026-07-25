<script>
    document.getElementById('groupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearGroupErrors();
        const groupId = document.getElementById('group_id').value;
        const groupModalElement = document.getElementById('groupModal');
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
                'Accept': 'application/json',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => {
            Toastify({
                text: 'Group Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();

            const groupSavedEvent = new CustomEvent('school:group-saved', {
                cancelable: true,
                detail: {
                    groupItem: response.data.data,
                    returnModalId: groupModalElement?.dataset.returnModalId || null,
                    isNew: !groupId,
                },
            });

            document.dispatchEvent(groupSavedEvent);
            groupModalElement?.classList.add('hidden');
            this.reset();
            if (groupModalElement) {
                delete groupModalElement.dataset.returnModalId;
            }

            if (!groupSavedEvent.defaultPrevented) {
                setTimeout(() => { window.location.reload(); }, 500);
            }
        })
        .catch(error => {
            if (error.response?.status === 422 || error.response?.data?.errors) {
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
