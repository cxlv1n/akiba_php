<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$requestId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$request = panel_find_request($requestId);
if ($request === null) {
    http_response_code(404);
    echo 'Request not found';
    exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    panel_require_csrf();
    $status = trim((string) ($_POST['status'] ?? 'new'));
    $adminNotes = (string) ($_POST['admin_notes'] ?? '');
    panel_update_request($requestId, $status, $adminNotes);
    panel_redirect(panel_url('request_detail', ['id' => $requestId]));
}

$topbarActions = '<a href="' . e(panel_url('requests')) . '" class="btn btn--ghost btn--sm">К списку</a>';
panel_render('request_detail.php', [
    'title' => 'Заявка #' . $request['id'],
    'page_title' => 'Заявка #' . $request['id'] . ' — ' . e((string) $request['name']),
    'nav_active' => 'requests',
    'topbar_actions' => $topbarActions,
    'request' => $request,
]);
