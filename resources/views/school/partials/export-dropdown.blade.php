<script>
(function() {
    function initDropdowns() {
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('button[aria-controls]');
            if (trigger) {
                const menuId = trigger.getAttribute('aria-controls');
                const menu = document.getElementById(menuId);
                if (menu) {
                    e.stopPropagation();
                    document.querySelectorAll('[role="menu"]').forEach(m => {
                        if (m !== menu) m.classList.add('hidden');
                    });
                    menu.classList.toggle('hidden');
                    trigger.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
                    return;
                }
            }

            const openMenu = e.target.closest('[role="menu"]');
            if (openMenu) {
                return;
            }

            document.querySelectorAll('[role="menu"]').forEach(m => {
                m.classList.add('hidden');
            });
            document.querySelectorAll('button[aria-controls]').forEach(b => {
                b.setAttribute('aria-expanded', 'false');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdowns);
    } else {
        initDropdowns();
    }
})();
</script>
