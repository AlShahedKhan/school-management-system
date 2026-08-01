<script>
    function clearSubjectErrors() {
        document.querySelectorAll('[id$="_error"]').forEach(function(el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
        document.querySelectorAll('[data-subject-invalid="true"]').forEach(function(el) {
            el.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
            el.removeAttribute('aria-invalid');
            el.removeAttribute('data-subject-invalid');
        });
    }
    function showSubjectErrors(errors) {
        const messages = [];
        let firstInvalidElement = null;
        const inputIds = {
            class_id: 'subjectFormClass',
            group_id: 'subjectFormGroup',
            section_id: 'subjectFormSection',
            grade_id: 'subjectFormGrade',
        };

        Object.keys(errors).forEach(function(field) {
            const cleanField = field.replace('marks.', '');
            const element = document.getElementById(cleanField + '_error');
            const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            const input = document.getElementById(inputIds[cleanField] || cleanField);
            const dropdown = input?.closest('[data-dropdown-select]');
            const invalidTarget = dropdown?.querySelector('[data-dropdown-select-button]') || input;

            if (element) {
                element.textContent = message || 'This field is invalid.';
                element.classList.remove('hidden');
                firstInvalidElement ||= element.closest('.relative') || element;
            }

            if (invalidTarget) {
                invalidTarget.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                invalidTarget.setAttribute('aria-invalid', 'true');
                invalidTarget.setAttribute('data-subject-invalid', 'true');
                firstInvalidElement ||= invalidTarget.closest('.relative') || invalidTarget;
            }

            if (message) messages.push(message);
        });

        firstInvalidElement?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return messages;
    }
</script>
