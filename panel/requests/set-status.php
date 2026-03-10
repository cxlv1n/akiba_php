<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
panel_require_csrf();
$requestId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$status = trim((string) ($_POST['status'] ?? ''));
$result = panel_set_request_status($requestId, $status);
panel_json_response($result !== null ? ['ok' => true] + $result : ['ok' => false], $result !== null ? 200 : 400);
