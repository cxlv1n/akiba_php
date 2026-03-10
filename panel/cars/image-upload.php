<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
panel_require_csrf();
$carId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$images = panel_upload_images($carId, panel_normalize_uploaded_files('images'));
panel_json_response(['ok' => true, 'images' => $images]);
