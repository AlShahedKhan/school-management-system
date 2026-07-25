<script>
    function clearExamErrors() {
        document.querySelectorAll('[id$="_error"]').forEach(function(el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function showExamErrors(errors) {
        if (!errors || typeof errors !== 'object') {
            return;
        }

        Object.keys(errors).forEach(function(field) {
            const element = document.getElementById(field + '_error');
            if (element) {
                element.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                element.classList.remove('hidden');
            }
        });
    }

    document.getElementById('exam_name')?.addEventListener('blur', function () {
        const errorEl = document.getElementById('exam_name_error');
        if (!this.value.trim()) {
            errorEl.textContent = 'Exam name is required.';
            errorEl.classList.remove('hidden');
        } else {
            errorEl.classList.add('hidden');
        }
    });

    document.getElementById('examFormClass')?.addEventListener('change', function () {
        const errorEl = document.getElementById('class_id_error');
        if (!this.value) {
            errorEl.textContent = 'Class is required.';
            errorEl.classList.remove('hidden');
        } else {
            errorEl.classList.add('hidden');
        }
    });
</script>
