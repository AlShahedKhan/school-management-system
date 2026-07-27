<script>
    document.getElementById('sectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSectionErrors();
        const sectionId = document.getElementById('section_record_id').value;
        const sectionModalElement = document.getElementById('sectionModal');
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
                'Accept': 'application/json',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => {
            Toastify({
                text: 'Section Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();

            const sectionSavedEvent = new CustomEvent('school:section-saved', {
                cancelable: true,
                detail: {
                    sectionItem: response.data.data,
                    returnModalId: sectionModalElement?.dataset.returnModalId || null,
                    isNew: !sectionId,
                },
            });

            document.dispatchEvent(sectionSavedEvent);
            sectionModalElement?.classList.add('hidden');
            this.reset();
            if (sectionModalElement) {
                delete sectionModalElement.dataset.returnModalId;
            }

            if (!sectionSavedEvent.defaultPrevented) {
                setTimeout(() => { window.location.reload(); }, 500);
            }
        })
        .catch(error => {
            const errors = error.response?.data?.errors;
            const sectionNameInput = sectionModalElement?.querySelector('[name="section_name"]');
            const sectionName = sectionNameInput?.value || sectionNameInput?.textContent || '';
            const classId = sectionModalElement?.querySelector('#sectionClassSelect')?.value || '';
            const groupId = sectionModalElement?.querySelector('#sectionGroupSelect')?.value || '';

            if (error.response?.status === 422 || errors) {
                if (error.response?.status === 422 && errors?.section_name && classId && groupId) {
                    document.dispatchEvent(new CustomEvent('school:section-already-exists', {
                        detail: {
                            sectionName,
                            classId,
                            groupId,
                            returnModalId: sectionModalElement?.dataset.returnModalId || null,
                        }
                    }));
                }

                showSectionErrors(errors);
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
