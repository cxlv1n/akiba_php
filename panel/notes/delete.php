<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
panel_require_csrf();
$noteId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
panel_json_response(['ok' => panel_delete_note($noteId)]);
