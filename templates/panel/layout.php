<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> — Akiba Auto</title>
    <link rel="icon" type="image/png" href="<?= e(asset('images/akiba_auto_min.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/dashboard.css')) ?>?v=1">
    <?= $extraHead ?>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar__logo">
            <a href="<?= e(panel_url('dashboard')) ?>">
                <img src="<?= e(asset('images/logo-akiba.png')) ?>" alt="Akiba Auto">
            </a>
        </div>

        <nav class="sidebar__nav">
            <a href="<?= e(panel_url('dashboard')) ?>" class="sidebar__link <?= $navActive === 'dashboard' ? 'sidebar__link--active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>Дашборд</span>
            </a>
            <a href="<?= e(panel_url('cars')) ?>" class="sidebar__link <?= $navActive === 'cars' ? 'sidebar__link--active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0H9m-6-6h15m-6 0V6"/></svg>
                <span>Автомобили</span>
            </a>
            <a href="<?= e(panel_url('car_add')) ?>" class="sidebar__link <?= $navActive === 'car_add' ? 'sidebar__link--active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Добавить авто</span>
            </a>

            <div class="sidebar__divider"></div>

            <a href="<?= e(panel_url('requests')) ?>" class="sidebar__link <?= $navActive === 'requests' ? 'sidebar__link--active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Заявки</span>
                <?php if ($newRequestsCount > 0): ?>
                    <span class="sidebar__badge"><?= e((string) $newRequestsCount) ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="sidebar__bottom">
            <a href="<?= e(route_url('home')) ?>" class="sidebar__link" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Открыть сайт</span>
            </a>
            <div class="sidebar__user">
                <div class="sidebar__avatar"><?= e(strtoupper(substr((string) ($currentUser['username'] ?? 'A'), 0, 1))) ?></div>
                <div class="sidebar__user-info">
                    <div class="sidebar__username"><?= e((string) ($currentUser['username'] ?? 'admin')) ?></div>
                    <a href="<?= e(panel_url('logout')) ?>" class="sidebar__logout">Выйти</a>
                </div>
            </div>
        </div>
    </aside>

    <main class="main" id="main">
        <header class="topbar">
            <button class="topbar__burger" id="sidebarToggle" aria-label="Меню">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 class="topbar__title"><?= $pageTitle ?></h1>
            <div class="topbar__right">
                <?= $topbarActions ?>
            </div>
        </header>

        <div class="content">
            <?= $content ?>
        </div>
    </main>

    <script>window.AKIBA_PANEL_CSRF = <?= json_encode($csrfToken, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script src="<?= e(asset('js/dashboard.js')) ?>?v=1"></script>
    <?= $extraJs ?>
</body>
</html>
