<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    panel_redirect(panel_url('cars'));
}
panel_require_csrf();
$carId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$ok = panel_delete_car($carId);
if (strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest') {
    panel_json_response(['ok' => $ok]);
}
panel_redirect(panel_url('cars'));
