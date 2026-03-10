<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
panel_require_csrf();
$carId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$status = trim((string) ($_POST['status'] ?? ''));
$newStatus = panel_set_car_status($carId, $status);
panel_json_response(['ok' => $newStatus !== null, 'availability' => $newStatus, 'display' => $newStatus !== null ? panel_availability_label($newStatus) : '']);
