<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('discountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearDiscountErrors();
        const id = document.getElementById('edit_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/fee-discounts/${id}` : '/api/fee-discounts';

        const payload = {
            student_scope: document.getElementById('discountStudentScope').value || 'all',
            student_ids: (document.getElementById('discount_student_ids').value || '').split(',').filter(Boolean),
            class_id: document.getElementById('discountClass').value,
            session_id: document.getElementById('discountSession').value,
            discount_scope: document.getElementById('discountScope').value || 'session',
            fee_type_id: document.getElementById('discountFeeType').value || null,
            minimum_grade: document.getElementById('discountMinGrade').value || null,
            discount_type: document.getElementById('discountType').value,
            discount_value: document.getElementById('discountValue').value,
            group_id: document.getElementById('discountGroup').value || null,
            section_id: document.getElementById('discountSection').value || null,
        };

        try {
            const res = await axios({ method, url, data: payload });
            Toastify({ text: id ? 'Discount Updated' : 'Discount Created', style: { background: '#22c55e' } }).showToast();
            closeDiscountModal();
            fetchDiscounts(currentPage);

            setDropdownValue('discountClassFilter', payload.class_id, '');
            document.getElementById('discountClassFilter').dispatchEvent(new Event('change', { bubbles: true }));
            setTimeout(() => {
                setDropdownValue('discountGroupFilter', payload.group_id || '', '');
                document.getElementById('discountGroupFilter').dispatchEvent(new Event('change', { bubbles: true }));
                setTimeout(() => {
                    setDropdownValue('discountSectionFilter', payload.section_id || '', '');
                    document.getElementById('discountSectionFilter').dispatchEvent(new Event('change', { bubbles: true }));
                    setTimeout(() => {
                        setDropdownValue('discountSessionFilter', payload.session_id, '');
                    }, 200);
                }, 200);
            }, 200);
        } catch (err) {
            if (err.response?.status === 422 && err.response?.data?.errors) {
                showDiscountErrors(err.response.data.errors);
                return;
            }
            Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Something went wrong' });
        }
    });
});
</script>
