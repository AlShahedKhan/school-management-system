<script>
function openDiscountModal() {
    document.getElementById('edit_id').value = '';
    document.getElementById('discountForm').reset();
    document.getElementById('discountModalTitle').innerText = 'Add Discount';
    document.getElementById('discountValue').value = '0';
    document.getElementById('afterDiscount').value = '';
    document.getElementById('beforeDiscount').value = '';

    loadDiscountClassSelect();
    setTimeout(() => {
        loadDiscountGroupSelect();
        setTimeout(() => {
            loadDiscountSectionSelect();
            setTimeout(() => {
                loadDiscountSessionSelect();
                setTimeout(() => {
                    setDropdownValue('discountStudent', '', 'Select Student');
                    setDropdownValue('discountFeeType', '', 'Select Fee Type');
                    document.getElementById('discountFeeName').value = '';
                    document.getElementById('beforeDiscount').value = '';
                    runCalc();
                    document.getElementById('discountModal').classList.remove('hidden');
                }, 100);
            }, 200);
        }, 200);
    }, 200);
}

function closeDiscountModal() {
    document.getElementById('discountModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('openDiscountModalBtn');
    if (openBtn) openBtn.addEventListener('click', openDiscountModal);
    const closeBtn = document.getElementById('closeDiscountModal');
    if (closeBtn) closeBtn.addEventListener('click', closeDiscountModal);
});
</script>
