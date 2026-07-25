<script>
    function openFeeModal(title) {
        document.getElementById('feeForm').reset();
        document.getElementById('fee_id').value = '';
        document.getElementById('feeModalTitle').innerText = typeof title === 'string' ? title : 'Edit Student Fee';
        document.getElementById('feeModal').classList.remove('hidden');
    }
    function closeFeeModal() {
        document.getElementById('feeModal').classList.add('hidden');
    }
</script>
