<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$cars = panel_list_cars($_GET);
$search = trim((string) ($_GET['q'] ?? ''));
$selectedOrigin = trim((string) ($_GET['origin'] ?? ''));
$selectedAvailability = trim((string) ($_GET['availability'] ?? ''));
$selectedActive = trim((string) ($_GET['is_active'] ?? ''));
$selectedSort = trim((string) ($_GET['sort'] ?? '-created'));
$exportQuery = build_query([
    'q' => $search,
    'origin' => $selectedOrigin,
    'availability' => $selectedAvailability,
    'is_active' => $selectedActive,
    'sort' => $selectedSort,
]);
$topbarActions = '<a href="' . e(panel_url('cars_export') . ($exportQuery !== '' ? '?' . $exportQuery : '')) . '" class="btn btn--ghost btn--sm">Экспорт</a>';
$topbarActions .= '<a href="' . e(panel_url('car_add')) . '" class="btn btn--primary btn--sm">Добавить</a>';

panel_render('cars.php', [
    'title' => 'Автомобили',
    'page_title' => 'Автомобили <span class="topbar__count">' . count($cars) . '</span>',
    'nav_active' => 'cars',
    'topbar_actions' => $topbarActions,
    'cars' => $cars,
    'total' => count($cars),
    'search' => $search,
    'selected_origin' => $selectedOrigin,
    'selected_availability' => $selectedAvailability,
    'selected_active' => $selectedActive,
    'selected_sort' => $selectedSort !== '' ? $selectedSort : '-created',
]);
