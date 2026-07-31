<script>
function resetDropdownLabel(id) {
    const label = document.querySelector('#' + id + 'Button [data-dropdown-select-label]');
    if (label) label.textContent = label.dataset.placeholder || 'Select...';
    const input = document.getElementById(id);
    if (input) input.value = '';
}

function openDiscountModal() {
    document.getElementById('edit_id').value = '';
    document.getElementById('discountForm').reset();
    document.getElementById('discountModalTitle').innerText = 'Add Discount';
    document.getElementById('discountValue').value = '0';
    document.getElementById('afterDiscount').value = '';
    document.getElementById('beforeDiscount').value = '';
    document.getElementById('discount_amount').value = '';

    loadDiscountClassSelect();
    setTimeout(() => {
        loadDiscountGroupSelect();
        setTimeout(() => {
            loadDiscountSectionSelect();
            setTimeout(() => {
                loadDiscountSessionSelect();
                setTimeout(() => {
                    resetDropdownLabel('discountScope');
                    resetDropdownLabel('discountStudentScope');
                    resetDiscountStudentPicker();
                    setDiscountScope('session');
                    resetDiscountFeeType();
                    populateDropdown('discountMinGradeMenu', [], 'id', 'grade_name');
                    setDropdownValue('discountMinGrade', '', 'Select Minimum Qualifying Grade');
                    document.getElementById('beforeDiscount').value = '';
                    document.getElementById('afterDiscount').value = '';
                    document.getElementById('discount_amount').value = '';
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
