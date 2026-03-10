<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
$user = panel_require_auth();
panel_require_csrf();
$carId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$text = trim((string) ($_POST['text'] ?? ''));
$note = panel_add_note($carId, (int) ($user['id'] ?? 0), $text);
panel_json_response($note !== null ? ['ok' => true, 'note' => $note] : ['ok' => false, 'error' => 'Пустая заметка'], $note !== null ? 200 : 400);
