<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('discountForm');
    if (!form) return;
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('invalid', function() { this.classList.add('border-red-500'); });
        input.addEventListener('input', function() { this.classList.remove('border-red-500'); });
    });
});
</script>
