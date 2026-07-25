<script>
const exportBtn = document.getElementById('btnExport1');
const exportDropdown = document.getElementById('exportDropdown');

exportBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    exportDropdown.classList.toggle('hidden');
    exportBtn.setAttribute('aria-expanded', String(!exportDropdown.classList.contains('hidden')));
});

document.addEventListener('click', function () {
    exportDropdown.classList.add('hidden');
    exportBtn.setAttribute('aria-expanded', 'false');
});

exportDropdown.addEventListener('click', function (e) {
    e.stopPropagation();
});
</script>
