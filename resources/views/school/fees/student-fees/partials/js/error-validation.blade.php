<script>
    function clearFeeErrors() {
        document.querySelectorAll('[id$="_error"]').forEach(function(el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }
    function showFeeErrors(errors) {
        Object.keys(errors).forEach(function(field) {
            const element = document.getElementById(field + '_error');
            if (element) {
                element.textContent = errors[field][0];
                element.classList.remove('hidden');
            }
        });
    }
</script>
