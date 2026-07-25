<script>
    function clearGradeErrors() {
        document.querySelectorAll('#gradeForm .text-red-500').forEach(function(el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function showGradeErrors(errors) {
        Object.keys(errors).forEach(function(field) {
            const element = document.getElementById(field + '_error');
            if (element) {
                element.textContent = errors[field][0];
                element.classList.remove('hidden');
            }
        });
    }
</script>
