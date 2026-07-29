<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('discountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearDiscountErrors();
        const id = document.getElementById('edit_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/fee-discounts/${id}` : '/api/fee-discounts';

        const payload = {
            student_id: document.getElementById('discountStudent').value,
            class_id: document.getElementById('discountClass').value,
            session_id: document.getElementById('discountSession').value,
            fee_type_id: document.getElementById('discountFeeType').value,
            fee_name: document.getElementById('discountFeeName').value || 'N/A',
            discount_type: document.getElementById('discountType').value,
            discount_value: document.getElementById('discountValue').value,
            before_discount: document.getElementById('beforeDiscount').value,
            discount_amount: document.getElementById('discount_amount').value,
            after_discount: document.getElementById('afterDiscount').value,
            group_id: document.getElementById('discountGroup').value || null,
            section_id: document.getElementById('discountSection').value || null,
            start_date: document.getElementById('discountStartDate').value,
            end_date: document.getElementById('discountEndDate').value,
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
