<script>
    function clearSubjectErrors() {
        document.querySelectorAll('[id$="_error"]').forEach(function(el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }
    function showSubjectErrors(errors) {
        Object.keys(errors).forEach(function(field) {
            const cleanField = field.replace('marks.', '');
            const element = document.getElementById(cleanField + '_error');
            if (element) {
                element.textContent = errors[field][0];
                element.classList.remove('hidden');
            }
        });
    }
</script>
