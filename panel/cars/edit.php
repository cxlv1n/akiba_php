<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$carId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$car = panel_find_car($carId);
if ($car === null) {
    http_response_code(404);
    echo 'Car not found';
    exit;
}

$formValues = array_merge(panel_default_car_values(), $car);
$errors = [];

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    panel_require_csrf();
    $validation = panel_validate_car_input($_POST);
    $formValues = $validation['data'];
    $errors = $validation['errors'];
    $formValues['views_count'] = $car['views_count'];
    $formValues['created_at'] = $car['created_at'];
    $formValues['updated_at'] = $car['updated_at'];

    if ($errors === []) {
        if (panel_update_car($carId, $formValues)) {
            panel_upload_images($carId, panel_normalize_uploaded_files('images'));
            panel_redirect(panel_url('car_edit', ['id' => $carId]));
        }
        $errors['save'] = 'Не удалось сохранить изменения';
    }
}

$topbarActions = '<button class="btn btn--ghost btn--sm" type="button" onclick="duplicateCar(' . $carId . ')">Дублировать</button>';
$topbarActions .= '<a href="' . e(route_url('catalog_detail', ['id' => $carId])) . '" class="btn btn--ghost btn--sm" target="_blank">На сайте</a>';

panel_render('car_form.php', [
    'title' => trim((string) $car['manufacturer'] . ' ' . (string) $car['model']),
    'page_title' => 'Редактирование · ' . e((string) $car['manufacturer']) . ' ' . e((string) $car['model']) . ' ' . e((string) $car['year']),
    'nav_active' => 'cars',
    'topbar_actions' => $topbarActions,
    'is_new' => false,
    'errors' => $errors,
    'car' => panel_find_car($carId),
    'form_values' => $errors === [] ? array_merge(panel_default_car_values(), panel_find_car($carId) ?? []) : $formValues,
]);
