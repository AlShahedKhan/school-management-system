<script>
    document.getElementById('classForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearClassErrors();
        const classId = document.getElementById('class_id').value;
        const classModalElement = document.getElementById('classModal');
        const formData = new FormData(this);
        if (classId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = classId
            ? `{{ url('/api/classes') }}/${classId}`
            : `{{ url('/api/classes') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => {
            Toastify({
                text: 'Class Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();

            const classSavedEvent = new CustomEvent('school:class-saved', {
                cancelable: true,
                detail: {
                    classItem: response.data.data,
                    returnModalId: classModalElement?.dataset.returnModalId || null,
                    isNew: !classId,
                },
            });

            document.dispatchEvent(classSavedEvent);
            classModalElement?.classList.add('hidden');
            this.reset();
            if (classModalElement) {
                delete classModalElement.dataset.returnModalId;
            }

            if (!classSavedEvent.defaultPrevented) {
                setTimeout(() => { window.location.reload(); }, 500);
            }
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showClassErrors(error.response.data.errors);
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
