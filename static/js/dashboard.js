/* ============================================================
   AKIBA AUTO — Dashboard JS
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // ============ Sidebar toggle (mobile) ============
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar--open');
        });

        // Закрытие при клике вне сайдбара
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('sidebar--open') &&
                !sidebar.contains(e.target) &&
                !toggle.contains(e.target)) {
                sidebar.classList.remove('sidebar--open');
            }
        });
    }

    // ============ Escape для модалок ============
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal--open').forEach(m => {
                m.classList.remove('modal--open');
            });
        }
    });
});


