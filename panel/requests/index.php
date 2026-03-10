<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$search = trim((string) ($_GET['q'] ?? ''));
$selectedStatus = trim((string) ($_GET['status'] ?? ''));
$filters = ['q' => $search, 'status' => $selectedStatus, 'limit' => 100];
$requests = panel_list_requests($filters);
$topbarActions = '<a href="' . e(panel_url('requests_export')) . '" class="btn btn--ghost btn--sm">Экспорт</a>';

panel_render('requests.php', [
    'title' => 'Заявки',
    'page_title' => 'Заявки <span class="topbar__count">' . panel_requests_total($filters) . '</span>',
    'nav_active' => 'requests',
    'topbar_actions' => $topbarActions,
    'requests' => $requests,
    'total' => panel_requests_total($filters),
    'new_count' => panel_new_requests_count(),
    'search' => $search,
    'selected_status' => $selectedStatus,
]);
