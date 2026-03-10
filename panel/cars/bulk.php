<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
panel_require_csrf();
$ids = array_filter(array_map('trim', explode(',', (string) ($_POST['ids'] ?? ''))));
$action = trim((string) ($_POST['action'] ?? ''));
$count = panel_bulk_action($ids, $action);
panel_json_response(['ok' => $count > 0, 'count' => $count, 'action' => $action], $count > 0 ? 200 : 400);
