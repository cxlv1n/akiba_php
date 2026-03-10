<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/bootstrap.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = request_path();

if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}
if ($path === '') {
    $path = '/';
}

if ($path === '/api/request') {
    if ($method !== 'POST') {
        json_response(['success' => false, 'error' => 'Method not allowed'], 405);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = [];

    if (is_string($raw) && trim($raw) !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $payload = $decoded;
        }
    }

    if ($payload === []) {
        $payload = $_POST;
    }

    $name = trim((string) ($payload['name'] ?? ''));
    $phone = trim((string) ($payload['phone'] ?? ''));

    if ($name === '' || $phone === '') {
        json_response([
            'success' => false,
            'error' => 'Имя и телефон обязательны',
        ], 400);
        exit;
    }

    $budget = trim((string) ($payload['budget'] ?? ''));
    $wishes = trim((string) ($payload['wishes'] ?? ''));
    $sourcePageRaw = trim((string) ($payload['source_page'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));
    $sourcePage = $sourcePageRaw !== '' ? absolute_url($sourcePageRaw) : current_url();
    $carId = isset($payload['car_id']) && is_numeric((string) $payload['car_id']) ? (int) $payload['car_id'] : null;

    $requestPayload = [
        'name' => $name,
        'phone' => $phone,
        'budget' => $budget,
        'wishes' => $wishes,
        'source_page' => $sourcePage,
        'car_id' => $carId,
    ];

    $requestId = save_client_request($requestPayload);
    $emailSent = send_request_email($requestPayload, $requestId);

    json_response([
        'success' => true,
        'message' => 'Заявка успешно отправлена',
        'request_id' => $requestId,
        'email_sent' => $emailSent,
    ], $emailSent ? 200 : 202);
    exit;
}

if ($path === '/') {
    render('pages/home.php', [
        'title' => 'Главная — AkibaAuto',
        'active_page' => 'home',
        'cars' => home_cars(8),
        'reviews' => default_reviews(),
    ]);
    exit;
}

if ($path === '/about') {
    render('pages/about.php', [
        'title' => 'О компании — AkibaAuto',
        'active_page' => 'about',
    ]);
    exit;
}

if ($path === '/process') {
    render('pages/process.php', [
        'title' => 'Процесс покупки — AkibaAuto',
        'active_page' => 'process',
    ]);
    exit;
}

if ($path === '/contacts') {
    render('pages/contacts.php', [
        'title' => 'Контакты — AkibaAuto',
        'active_page' => 'contacts',
    ]);
    exit;
}

if ($path === '/catalog') {
    $catalog = catalog_page_data($_GET);
    render('catalog/list.php', [
        'title' => 'Каталог — AkibaAuto',
        'active_page' => 'catalog',
        'cars' => $catalog['cars'],
        'filters' => $catalog['filters'],
        'pagination' => $catalog['pagination'],
        'options' => $catalog['options'],
    ]);
    exit;
}

if (($path === '/catalog/detail.php' || $path === '/catalog/detail') && isset($_GET['id']) && is_numeric((string) $_GET['id'])) {
    $carId = (int) $_GET['id'];
    $car = find_car_by_id($carId);

    if ($car === null) {
        not_found();
        exit;
    }

    record_car_view($carId);

    $allCars = catalog_base_cars();
    render('catalog/detail.php', [
        'title' => trim((string) $car['manufacturer'] . ' ' . (string) $car['model']) . ' — AkibaAuto',
        'active_page' => 'catalog',
        'car' => $car,
        'popular_cars' => related_cars($car, $allCars, 6),
        'request_car_id' => (int) $car['id'],
    ]);
    exit;
}

if (preg_match('#^/catalog/(\d+)$#', $path, $matches) === 1) {
    $carId = (int) $matches[1];
    $car = find_car_by_id($carId);

    if ($car === null) {
        not_found();
        exit;
    }

    record_car_view($carId);

    $allCars = catalog_base_cars();
    render('catalog/detail.php', [
        'title' => trim((string) $car['manufacturer'] . ' ' . (string) $car['model']) . ' — AkibaAuto',
        'active_page' => 'catalog',
        'car' => $car,
        'popular_cars' => related_cars($car, $allCars, 6),
        'request_car_id' => (int) $car['id'],
    ]);
    exit;
}

not_found();
