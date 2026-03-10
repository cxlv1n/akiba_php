<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$formValues = panel_default_car_values();
$errors = [];

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    panel_require_csrf();
    $validation = panel_validate_car_input($_POST);
    $formValues = $validation['data'];
    $errors = $validation['errors'];

    if ($errors === []) {
        $newCarId = panel_insert_car($formValues);
        if ($newCarId > 0) {
            panel_upload_images($newCarId, panel_normalize_uploaded_files('images'));
            panel_redirect(panel_url('car_edit', ['id' => $newCarId]));
        }
        $errors['save'] = 'Не удалось создать автомобиль';
    }
}

panel_render('car_form.php', [
    'title' => 'Новый автомобиль',
    'page_title' => 'Новый автомобиль',
    'nav_active' => 'car_add',
    'is_new' => true,
    'errors' => $errors,
    'form_values' => $formValues,
]);
