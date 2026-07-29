<script>
    function openFeeModal() {
        document.getElementById('feeForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('feeModalTitle').innerText = 'Edit Student Fee';
        document.getElementById('feeModal').classList.remove('hidden');
    }
    function closeFeeModal() {
        document.getElementById('feeModal').classList.add('hidden');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('closeFeeModal');
        if (closeBtn) closeBtn.addEventListener('click', closeFeeModal);
    });
</script>
