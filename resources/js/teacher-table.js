const resetButton = document.getElementById('btnResetMobile');
const desktopSearch = document.getElementById('teacherSearch');
const mobileSearch = document.getElementById('teacherSearchMobile');

if (resetButton) {
    resetButton.addEventListener('click', function () {
        window.location.reload();
    });
}

if (desktopSearch && mobileSearch && typeof window.fetchTeachers === 'function') {
    desktopSearch.addEventListener('input', function () {
        mobileSearch.value = this.value;
        window.fetchTeachers(1);
    });

    mobileSearch.addEventListener('input', function () {
        desktopSearch.value = this.value;
        window.fetchTeachers(1);
    });
}
