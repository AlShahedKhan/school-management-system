<script>
    function setLabel(id, text) {
        const el = document.querySelector(`#${id}Button [data-dropdown-select-label]`);
        if (el) el.textContent = text;
    }
    function populateDueDaySelect() {
        const data = [];
        for (let i = 1; i <= 31; i++) {
            data.push({ id: i, label: 'Every Month ' + i + '-day' });
        }
        populateDropdown('due_dayMenu', data, 'id', 'label');
    }
    function resetDropdownLabel(id) {
        const label = document.querySelector('#' + id + 'Button [data-dropdown-select-label]');
        if (label) label.textContent = label.dataset.placeholder || 'Select...';
        const input = document.getElementById(id);
        if (input) input.value = '';
    }
    function openFeeModal(title) {
        document.getElementById('feeForm').reset();
        document.getElementById('feeFormGroupMenu') && (document.getElementById('feeFormGroupMenu').innerHTML = '');
        document.getElementById('feeFormSectionMenu') && (document.getElementById('feeFormSectionMenu').innerHTML = '');
        document.getElementById('feeFormSessionMenu') && (document.getElementById('feeFormSessionMenu').innerHTML = '');
        document.getElementById('record_id').value = '';
        document.getElementById('feeModalTitle').innerText = typeof title === 'string' ? title : 'Fee Template';
        document.getElementById('feeModal').classList.remove('hidden');
        setLabel('feeFormGroup', 'Select Group');
        setLabel('feeFormSection', 'Select Section');
        setLabel('feeFormSession', 'Select Session');
        resetDropdownLabel('fee_type_name');
        resetDropdownLabel('frequency');
        resetDropdownLabel('food_type');
        resetDropdownLabel('exam_id');
        resetDropdownLabel('due_day');
        loadFeeClassSelect();
        document.querySelectorAll('.hidden-field').forEach(el => el.style.display = 'none');
        const freq = document.getElementById('div_frequency');
        if (freq) freq.style.display = 'none';
        const fn = document.getElementById('fee_name_input');
        if (fn) { fn.value = ''; fn.readOnly = false; }
        const amt = document.getElementById('amount');
        if (amt) amt.value = '';
        const pd = document.getElementById('pay_date');
        if (pd) pd.value = '';
        populateDueDaySelect();
        const feeBtn = document.querySelector('#fee_type_wrapper [data-dropdown-select-button]');
        if (feeBtn) { feeBtn.disabled = true; feeBtn.style.opacity = '0.5'; feeBtn.style.cursor = 'not-allowed'; }
    }
    function closeFeeModal() {
        document.getElementById('feeModal').classList.add('hidden');
    }
</script>
