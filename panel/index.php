<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
panel_require_auth();
$dashboard = panel_dashboard_data();
panel_render('dashboard.php', array_merge($dashboard, [
    'title' => 'Дашборд',
    'page_title' => 'Дашборд',
    'nav_active' => 'dashboard',
]));
