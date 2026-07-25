<script>
    function deleteExpense(id) {
        Swal.fire({
            title: 'Delete Expense',
            html: `
                <div class="text-slate-600 text-sm mt-2">
                    This expense record will be <b>permanently deleted</b>.<br>
                    This action cannot be undone.
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            focusCancel: true,
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>Delete',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            buttonsStyling: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                title: 'text-xl font-bold text-slate-800',
                confirmButton: 'px-5 py-2 rounded-lg font-semibold',
                cancelButton: 'px-5 py-2 rounded-lg font-semibold'
            }
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
            axios.delete('{{ url('/api/expenses') }}/' + id, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                Swal.close();
                Toastify({
                    text: "✓ Expense deleted successfully",
                    gravity: "top",
                    position: "right",
                    duration: 1800,
                    close: true,
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(135deg, #ef4444, #dc2626)",
                        borderRadius: "10px"
                    }
                }).showToast();
                setTimeout(() => {
                    window.location.reload();
                }, 1800);
            }).catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: error.response?.data?.message ?? 'Something went wrong. Please try again.',
                    confirmButtonColor: '#dc2626'
                });

            });
        });
    }
</script>