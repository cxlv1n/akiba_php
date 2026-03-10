<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$cars = panel_list_cars($_GET);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="akiba_catalog.csv"');
echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Производитель', 'Модель', 'Название', 'Год', 'Цена', 'Страна', 'Пробег', 'Топливо', 'Привод', 'Кузов', 'Двигатель', 'Статус', 'Активно', 'Просмотры', 'Фото', 'Создано'], ';');
foreach ($cars as $car) {
    fputcsv($out, [
        $car['id'],
        $car['manufacturer'],
        $car['model'],
        $car['name'],
        $car['year'],
        (string) $car['price'],
        $car['origin_label'],
        $car['mileage_km'],
        $car['fuel'],
        $car['drive'],
        $car['body_type'],
        $car['engine_volume'],
        $car['availability_label'],
        !empty($car['is_active']) ? 'Да' : 'Нет',
        $car['views_count'],
        $car['images_count'],
        panel_format_datetime((string) $car['created_at']),
    ], ';');
}
fclose($out);
exit;
