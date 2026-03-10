<?php

declare(strict_types=1);

function panel_origin_choices(): array
{
    return [
        'JP' => 'Япония',
        'KR' => 'Корея',
        'CN' => 'Китай',
    ];
}

function panel_availability_choices(): array
{
    return [
        'in_stock' => 'В наличии',
        'on_order' => 'Под заказ',
        'sold' => 'Продано',
    ];
}

function panel_request_status_choices(): array
{
    return [
        'new' => 'Новая',
        'processing' => 'В работе',
        'completed' => 'Завершена',
        'cancelled' => 'Отменена',
    ];
}

function panel_origin_label(string $origin): string
{
    $choices = panel_origin_choices();
    return $choices[$origin] ?? $origin;
}

function panel_availability_label(string $availability): string
{
    $choices = panel_availability_choices();
    return $choices[$availability] ?? $availability;
}

function panel_request_status_label(string $status): string
{
    $choices = panel_request_status_choices();
    return $choices[$status] ?? $status;
}

function panel_bool($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_numeric($value)) {
        return (int) $value === 1;
    }

    return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
}

function panel_format_datetime(?string $value, string $format = 'd.m.Y H:i'): string
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return '—';
    }

    $timestamp = strtotime($raw);
    if ($timestamp === false) {
        return $raw;
    }

    return date($format, $timestamp);
}

function panel_slugify(string $value): string
{
    $slug = trim($value);
    if ($slug === '') {
        return 'car-' . date('YmdHis');
    }

    if (function_exists('iconv')) {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if (is_string($transliterated)) {
            $slug = $transliterated;
        }
    }

    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');

    return $slug !== '' ? $slug : 'car-' . date('YmdHis');
}

function panel_catalog_index(): array
{
    static $index = null;

    if ($index !== null) {
        return $index;
    }

    $dataset = load_catalog_dataset();
    $index = $dataset['cars_by_id'] ?? [];
    return $index;
}

function panel_enrich_car_row(array $row): array
{
    $id = (int) ($row['id'] ?? 0);
    $datasetIndex = panel_catalog_index();
    if (isset($datasetIndex[$id]) && is_array($datasetIndex[$id])) {
        $car = $datasetIndex[$id];
    } else {
        $car = [
            'id' => $id,
            'name' => (string) ($row['name'] ?? ''),
            'manufacturer' => (string) ($row['manufacturer'] ?? ''),
            'model' => (string) ($row['model'] ?? ''),
            'year' => (int) ($row['year'] ?? 0),
            'price' => (float) ($row['price'] ?? 0),
            'origin' => (string) ($row['origin'] ?? ''),
            'mileage_km' => (int) ($row['mileage_km'] ?? 0),
            'fuel' => (string) ($row['fuel'] ?? ''),
            'drive' => (string) ($row['drive'] ?? ''),
            'body_type' => (string) ($row['body_type'] ?? ''),
            'engine_volume' => (string) ($row['engine_volume'] ?? ''),
            'availability' => (string) ($row['availability'] ?? ''),
            'description' => (string) ($row['description'] ?? ''),
            'alt_name' => (string) ($row['alt_name'] ?? ''),
            'is_active' => panel_bool($row['is_active'] ?? false),
            'views_count' => (int) ($row['views_count'] ?? 0),
            'created_at' => (string) ($row['created_at'] ?? ''),
            'updated_at' => (string) ($row['updated_at'] ?? ''),
            'images' => [],
            'image_url' => null,
        ];
    }

    $car['origin_label'] = panel_origin_label((string) ($car['origin'] ?? ''));
    $car['availability_label'] = panel_availability_label((string) ($car['availability'] ?? ''));
    $car['images_count'] = is_array($car['images'] ?? null) ? count($car['images']) : 0;
    $car['created_at_display'] = panel_format_datetime((string) ($car['created_at'] ?? ''));
    $car['updated_at_display'] = panel_format_datetime((string) ($car['updated_at'] ?? ''));
    $car['public_url'] = route_url('catalog_detail', ['id' => $car['id']]);

    return $car;
}

function panel_all_cars(): array
{
    return array_map('panel_enrich_car_row', load_catalog_dataset()['cars'] ?? []);
}

function panel_find_user_by_id(int $userId): ?array
{
    if ($userId <= 0) {
        return null;
    }

    $row = db_fetch_one('SELECT * FROM auth_user WHERE id = :id LIMIT 1', [':id' => $userId]);
    return is_array($row) ? $row : null;
}

function panel_find_user_by_username(string $username): ?array
{
    $username = trim($username);
    if ($username === '') {
        return null;
    }

    $row = db_fetch_one('SELECT * FROM auth_user WHERE username = :username LIMIT 1', [':username' => $username]);
    return is_array($row) ? $row : null;
}

function panel_touch_user_login(int $userId): void
{
    if ($userId <= 0) {
        return;
    }

    db_execute('UPDATE auth_user SET last_login = :last_login WHERE id = :id', [
        ':last_login' => date('Y-m-d H:i:s'),
        ':id' => $userId,
    ]);
}

function panel_new_requests_count(): int
{
    return (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_clientrequest WHERE status = :status', [':status' => 'new']) ?? 0);
}

function panel_dashboard_data(): array
{
    $today = date('Y-m-d');
    $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
    $thirtyDaysAgo = date('Y-m-d', strtotime('-29 days'));

    $totalCars = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_car WHERE is_active = 1') ?? 0);
    $totalViews = (int) (db_fetch_value('SELECT COALESCE(SUM(views_count), 0) FROM catalog_car') ?? 0);
    $viewsToday = (int) (db_fetch_value('SELECT COALESCE(SUM(views), 0) FROM catalog_carviewlog WHERE date = :date', [':date' => $today]) ?? 0);
    $views7d = (int) (db_fetch_value('SELECT COALESCE(SUM(views), 0) FROM catalog_carviewlog WHERE date >= :date', [':date' => $sevenDaysAgo]) ?? 0);
    $views30d = (int) (db_fetch_value('SELECT COALESCE(SUM(views), 0) FROM catalog_carviewlog WHERE date >= :date', [':date' => $thirtyDaysAgo]) ?? 0);
    $carsInStock = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_car WHERE is_active = 1 AND availability = :availability', [':availability' => 'in_stock']) ?? 0);
    $carsOnOrder = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_car WHERE is_active = 1 AND availability = :availability', [':availability' => 'on_order']) ?? 0);
    $carsSold = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_car WHERE availability = :availability', [':availability' => 'sold']) ?? 0);
    $newRequestsCount = panel_new_requests_count();
    $totalRequests = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_clientrequest') ?? 0);

    $recentRequests = panel_list_requests(['limit' => 5]);

    $topRows = db_fetch_all('SELECT * FROM catalog_car WHERE is_active = 1 ORDER BY views_count DESC, updated_at DESC LIMIT 10');
    $topCars = [];
    foreach ($topRows as $row) {
        $topCars[] = panel_enrich_car_row($row);
    }

    $recentRows = db_fetch_all('SELECT * FROM catalog_car ORDER BY created_at DESC, id DESC LIMIT 5');
    $recentCars = [];
    foreach ($recentRows as $row) {
        $recentCars[] = panel_enrich_car_row($row);
    }

    $originStatsRaw = db_fetch_all(
        'SELECT origin, COUNT(*) AS count, COALESCE(SUM(views_count), 0) AS total_views
         FROM catalog_car
         WHERE is_active = 1
         GROUP BY origin
         ORDER BY count DESC, total_views DESC'
    );
    $originData = [];
    foreach ($originStatsRaw as $row) {
        $originData[] = [
            'origin' => panel_origin_label((string) ($row['origin'] ?? '')),
            'code' => (string) ($row['origin'] ?? ''),
            'count' => (int) ($row['count'] ?? 0),
            'total_views' => (int) ($row['total_views'] ?? 0),
        ];
    }

    $chartRows = db_fetch_all(
        'SELECT date, COALESCE(SUM(views), 0) AS total
         FROM catalog_carviewlog
         WHERE date >= :date
         GROUP BY date
         ORDER BY date ASC',
        [':date' => $thirtyDaysAgo]
    );
    $chartMap = [];
    foreach ($chartRows as $row) {
        $chartMap[(string) ($row['date'] ?? '')] = (int) ($row['total'] ?? 0);
    }

    $chartLabels = [];
    $chartData = [];
    $current = strtotime($thirtyDaysAgo);
    $end = strtotime($today);
    while ($current !== false && $current <= $end) {
        $dateKey = date('Y-m-d', $current);
        $chartLabels[] = date('d.m', $current);
        $chartData[] = $chartMap[$dateKey] ?? 0;
        $current = strtotime('+1 day', $current);
    }

    return [
        'total_cars' => $totalCars,
        'total_views' => $totalViews,
        'views_today' => $viewsToday,
        'views_7d' => $views7d,
        'views_30d' => $views30d,
        'cars_in_stock' => $carsInStock,
        'cars_on_order' => $carsOnOrder,
        'cars_sold' => $carsSold,
        'new_requests_count' => $newRequestsCount,
        'total_requests' => $totalRequests,
        'recent_requests' => $recentRequests,
        'top_cars' => $topCars,
        'recent_cars' => $recentCars,
        'origin_data' => $originData,
        'chart_labels' => $chartLabels,
        'chart_data' => $chartData,
    ];
}

function panel_list_cars(array $filters = []): array
{
    $cars = panel_all_cars();
    $search = trim((string) ($filters['q'] ?? ''));
    $origin = trim((string) ($filters['origin'] ?? ''));
    $availability = trim((string) ($filters['availability'] ?? ''));
    $active = (string) ($filters['is_active'] ?? '');
    $sort = trim((string) ($filters['sort'] ?? '-created'));

    $cars = array_values(array_filter(
        $cars,
        static function (array $car) use ($search, $origin, $availability, $active): bool {
            if ($search !== '') {
                $haystack = mb_strtolower(
                    trim((string) ($car['manufacturer'] ?? '')) . ' ' . trim((string) ($car['model'] ?? '')) . ' ' . trim((string) ($car['name'] ?? '')),
                    'UTF-8'
                );
                if (mb_stripos($haystack, mb_strtolower($search, 'UTF-8'), 0, 'UTF-8') === false) {
                    return false;
                }
            }

            if ($origin !== '' && (string) ($car['origin'] ?? '') !== $origin) {
                return false;
            }

            if ($availability !== '' && (string) ($car['availability'] ?? '') !== $availability) {
                return false;
            }

            if ($active === '1' && !(bool) ($car['is_active'] ?? false)) {
                return false;
            }

            if ($active === '0' && (bool) ($car['is_active'] ?? false)) {
                return false;
            }

            return true;
        }
    ));

    usort(
        $cars,
        static function (array $a, array $b) use ($sort): int {
            switch ($sort) {
                case 'price':
                    return (float) ($a['price'] ?? 0) <=> (float) ($b['price'] ?? 0);
                case '-price':
                    return (float) ($b['price'] ?? 0) <=> (float) ($a['price'] ?? 0);
                case 'year':
                    return (int) ($a['year'] ?? 0) <=> (int) ($b['year'] ?? 0);
                case '-year':
                    return (int) ($b['year'] ?? 0) <=> (int) ($a['year'] ?? 0);
                case '-views':
                    return (int) ($b['views_count'] ?? 0) <=> (int) ($a['views_count'] ?? 0);
                case 'views':
                    return (int) ($a['views_count'] ?? 0) <=> (int) ($b['views_count'] ?? 0);
                case 'name':
                    return strcmp((string) ($a['manufacturer'] ?? ''), (string) ($b['manufacturer'] ?? ''));
                case '-name':
                    return strcmp((string) ($b['manufacturer'] ?? ''), (string) ($a['manufacturer'] ?? ''));
                case 'created':
                    return strcmp((string) ($a['created_at'] ?? ''), (string) ($b['created_at'] ?? ''));
                case '-created':
                default:
                    return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
            }
        }
    );

    return $cars;
}

function panel_find_car(int $carId): ?array
{
    if ($carId <= 0) {
        return null;
    }

    $row = db_fetch_one('SELECT * FROM catalog_car WHERE id = :id LIMIT 1', [':id' => $carId]);
    if (!is_array($row)) {
        return null;
    }

    $car = panel_enrich_car_row($row);
    $car['images'] = panel_car_images($carId);
    $car['images_count'] = count($car['images']);
    if ($car['image_url'] === null && isset($car['images'][0]['url'])) {
        $car['image_url'] = $car['images'][0]['url'];
    }
    $car['notes'] = panel_car_notes($carId);

    return $car;
}

function panel_default_car_values(): array
{
    return [
        'name' => '',
        'manufacturer' => '',
        'model' => '',
        'year' => '',
        'price' => '',
        'origin' => 'JP',
        'mileage_km' => '',
        'fuel' => '',
        'drive' => '',
        'body_type' => '',
        'engine_volume' => '',
        'availability' => 'on_order',
        'description' => '',
        'alt_name' => '',
        'is_active' => true,
        'views_count' => 0,
        'created_at' => '',
        'updated_at' => '',
    ];
}

function panel_validate_car_input(array $input): array
{
    $data = panel_default_car_values();
    $errors = [];

    $data['manufacturer'] = trim((string) ($input['manufacturer'] ?? ''));
    $data['model'] = trim((string) ($input['model'] ?? ''));
    $data['name'] = trim((string) ($input['name'] ?? ''));
    if ($data['name'] === '' && ($data['manufacturer'] !== '' || $data['model'] !== '')) {
        $data['name'] = trim($data['manufacturer'] . ' ' . $data['model']);
    }

    $year = (int) preg_replace('/\D+/', '', (string) ($input['year'] ?? ''));
    $priceRaw = str_replace([' ', ','], ['', '.'], trim((string) ($input['price'] ?? '')));
    $mileage = (int) preg_replace('/\D+/', '', (string) ($input['mileage_km'] ?? ''));

    $data['year'] = $year;
    $data['price'] = $priceRaw;
    $data['mileage_km'] = $mileage;
    $data['origin'] = trim((string) ($input['origin'] ?? 'JP'));
    $data['fuel'] = trim((string) ($input['fuel'] ?? ''));
    $data['drive'] = trim((string) ($input['drive'] ?? ''));
    $data['body_type'] = trim((string) ($input['body_type'] ?? ''));
    $data['engine_volume'] = trim((string) ($input['engine_volume'] ?? ''));
    $data['availability'] = trim((string) ($input['availability'] ?? 'on_order'));
    $data['description'] = trim((string) ($input['description'] ?? ''));
    $data['alt_name'] = trim((string) ($input['alt_name'] ?? ''));
    $data['is_active'] = isset($input['is_active']) && panel_bool($input['is_active']);

    if ($data['manufacturer'] === '') {
        $errors['manufacturer'] = 'Укажите производителя';
    }
    if ($data['model'] === '') {
        $errors['model'] = 'Укажите модель';
    }
    if ($data['name'] === '') {
        $errors['name'] = 'Укажите название автомобиля';
    }
    if ($year < 1950 || $year > ((int) date('Y') + 1)) {
        $errors['year'] = 'Проверьте год выпуска';
    }
    if (!is_numeric($priceRaw) || (float) $priceRaw < 0) {
        $errors['price'] = 'Проверьте цену';
    }
    if ($mileage < 0) {
        $errors['mileage_km'] = 'Проверьте пробег';
    }
    if (!isset(panel_origin_choices()[$data['origin']])) {
        $errors['origin'] = 'Выберите страну';
    }
    if (!isset(panel_availability_choices()[$data['availability']])) {
        $errors['availability'] = 'Выберите статус';
    }

    if ($data['alt_name'] === '') {
        $data['alt_name'] = panel_slugify($data['name']);
    } else {
        $data['alt_name'] = panel_slugify($data['alt_name']);
    }

    return ['data' => $data, 'errors' => $errors];
}

function panel_insert_car(array $data): int
{
    $now = date('Y-m-d H:i:s');
    $ok = db_execute(
        'INSERT INTO catalog_car (name, manufacturer, model, year, price, origin, mileage_km, fuel, description, body_type, engine_volume, drive, availability, alt_name, created_at, updated_at, is_active, views_count)
         VALUES (:name, :manufacturer, :model, :year, :price, :origin, :mileage_km, :fuel, :description, :body_type, :engine_volume, :drive, :availability, :alt_name, :created_at, :updated_at, :is_active, :views_count)',
        [
            ':name' => (string) $data['name'],
            ':manufacturer' => (string) $data['manufacturer'],
            ':model' => (string) $data['model'],
            ':year' => (int) $data['year'],
            ':price' => (float) $data['price'],
            ':origin' => (string) $data['origin'],
            ':mileage_km' => (int) $data['mileage_km'],
            ':fuel' => (string) $data['fuel'],
            ':description' => (string) $data['description'],
            ':body_type' => (string) $data['body_type'],
            ':engine_volume' => (string) $data['engine_volume'],
            ':drive' => (string) $data['drive'],
            ':availability' => (string) $data['availability'],
            ':alt_name' => (string) $data['alt_name'],
            ':created_at' => $now,
            ':updated_at' => $now,
            ':is_active' => $data['is_active'] ? 1 : 0,
            ':views_count' => 0,
        ]
    );

    return $ok ? db_last_insert_id() : 0;
}

function panel_update_car(int $carId, array $data): bool
{
    return db_execute(
        'UPDATE catalog_car
         SET name = :name,
             manufacturer = :manufacturer,
             model = :model,
             year = :year,
             price = :price,
             origin = :origin,
             mileage_km = :mileage_km,
             fuel = :fuel,
             description = :description,
             body_type = :body_type,
             engine_volume = :engine_volume,
             drive = :drive,
             availability = :availability,
             alt_name = :alt_name,
             updated_at = :updated_at,
             is_active = :is_active
         WHERE id = :id',
        [
            ':id' => $carId,
            ':name' => (string) $data['name'],
            ':manufacturer' => (string) $data['manufacturer'],
            ':model' => (string) $data['model'],
            ':year' => (int) $data['year'],
            ':price' => (float) $data['price'],
            ':origin' => (string) $data['origin'],
            ':mileage_km' => (int) $data['mileage_km'],
            ':fuel' => (string) $data['fuel'],
            ':description' => (string) $data['description'],
            ':body_type' => (string) $data['body_type'],
            ':engine_volume' => (string) $data['engine_volume'],
            ':drive' => (string) $data['drive'],
            ':availability' => (string) $data['availability'],
            ':alt_name' => (string) $data['alt_name'],
            ':updated_at' => date('Y-m-d H:i:s'),
            ':is_active' => $data['is_active'] ? 1 : 0,
        ]
    );
}

function panel_normalize_uploaded_files(string $key): array
{
    if (!isset($_FILES[$key])) {
        return [];
    }

    $files = $_FILES[$key];
    if (!is_array($files['name'] ?? null)) {
        return [
            [
                'name' => (string) ($files['name'] ?? ''),
                'type' => (string) ($files['type'] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'] ?? ''),
                'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
                'size' => (int) ($files['size'] ?? 0),
            ],
        ];
    }

    $normalized = [];
    foreach ($files['name'] as $index => $name) {
        $normalized[] = [
            'name' => (string) $name,
            'type' => (string) ($files['type'][$index] ?? ''),
            'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
            'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE),
            'size' => (int) ($files['size'][$index] ?? 0),
        ];
    }

    return $normalized;
}

function panel_store_uploaded_image(array $file, int $carId): ?string
{
    if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        return null;
    }

    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        return null;
    }

    $relativeDir = 'cars/gallery';
    $absoluteDir = __DIR__ . '/../media/' . $relativeDir;
    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0775, true);
    }

    $filename = $carId . '_' . time() . '_' . bin2hex(random_bytes(10)) . '.' . $extension;
    $absolutePath = $absoluteDir . '/' . $filename;
    if (!move_uploaded_file($tmpName, $absolutePath)) {
        return null;
    }

    return $relativeDir . '/' . $filename;
}

function panel_car_images(int $carId): array
{
    $rows = db_fetch_all(
        'SELECT * FROM catalog_carimage WHERE car_id = :car_id ORDER BY is_main DESC, created_at DESC, id DESC',
        [':car_id' => $carId]
    );
    $images = [];
    foreach ($rows as $row) {
        $path = (string) ($row['image'] ?? '');
        $images[] = [
            'id' => (int) ($row['id'] ?? 0),
            'car_id' => (int) ($row['car_id'] ?? 0),
            'path' => $path,
            'url' => $path === '' ? null : media_url($path),
            'is_main' => panel_bool($row['is_main'] ?? false),
            'alt_text' => (string) ($row['alt_text'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? ''),
            'created_at_display' => panel_format_datetime((string) ($row['created_at'] ?? '')),
        ];
    }

    return $images;
}

function panel_upload_images(int $carId, array $files): array
{
    if ($carId <= 0 || $files === []) {
        return [];
    }

    $hasMain = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_carimage WHERE car_id = :car_id AND is_main = 1', [':car_id' => $carId]) ?? 0) > 0;
    $car = panel_find_car($carId);
    $altText = $car ? trim((string) ($car['manufacturer'] ?? '') . ' ' . (string) ($car['model'] ?? '')) : '';
    $created = [];

    foreach ($files as $index => $file) {
        $relativePath = panel_store_uploaded_image($file, $carId);
        if ($relativePath === null) {
            continue;
        }

        $isMain = !$hasMain && $index === 0 ? 1 : 0;
        $createdAt = date('Y-m-d H:i:s');
        $ok = db_execute(
            'INSERT INTO catalog_carimage (image, is_main, alt_text, created_at, car_id)
             VALUES (:image, :is_main, :alt_text, :created_at, :car_id)',
            [
                ':image' => $relativePath,
                ':is_main' => $isMain,
                ':alt_text' => $altText,
                ':created_at' => $createdAt,
                ':car_id' => $carId,
            ]
        );
        if (!$ok) {
            continue;
        }

        $created[] = [
            'id' => db_last_insert_id(),
            'url' => media_url($relativePath),
            'is_main' => $isMain === 1,
        ];
    }

    return $created;
}

function panel_cleanup_media_path(string $relativePath): void
{
    $relativePath = trim($relativePath);
    if ($relativePath === '' || strpos($relativePath, '..') !== false) {
        return;
    }

    $count = (int) (db_fetch_value('SELECT COUNT(*) FROM catalog_carimage WHERE image = :image', [':image' => $relativePath]) ?? 0);
    if ($count > 0) {
        return;
    }

    $absolutePath = __DIR__ . '/../media/' . ltrim($relativePath, '/');
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

function panel_delete_image(int $imageId): bool
{
    $image = db_fetch_one('SELECT * FROM catalog_carimage WHERE id = :id LIMIT 1', [':id' => $imageId]);
    if (!is_array($image)) {
        return false;
    }

    $carId = (int) ($image['car_id'] ?? 0);
    $wasMain = panel_bool($image['is_main'] ?? false);
    $path = (string) ($image['image'] ?? '');

    $ok = db_execute('DELETE FROM catalog_carimage WHERE id = :id', [':id' => $imageId]);
    if (!$ok) {
        return false;
    }

    panel_cleanup_media_path($path);

    if ($wasMain && $carId > 0) {
        $replacement = db_fetch_one('SELECT id FROM catalog_carimage WHERE car_id = :car_id ORDER BY created_at DESC, id DESC LIMIT 1', [':car_id' => $carId]);
        if (is_array($replacement) && isset($replacement['id'])) {
            db_execute('UPDATE catalog_carimage SET is_main = CASE WHEN id = :id THEN 1 ELSE 0 END WHERE car_id = :car_id', [
                ':id' => (int) $replacement['id'],
                ':car_id' => $carId,
            ]);
        }
    }

    return true;
}

function panel_set_main_image(int $imageId): bool
{
    $image = db_fetch_one('SELECT id, car_id FROM catalog_carimage WHERE id = :id LIMIT 1', [':id' => $imageId]);
    if (!is_array($image)) {
        return false;
    }

    db_execute('UPDATE catalog_carimage SET is_main = 0 WHERE car_id = :car_id', [':car_id' => (int) $image['car_id']]);
    return db_execute('UPDATE catalog_carimage SET is_main = 1 WHERE id = :id', [':id' => $imageId]);
}

function panel_car_notes(int $carId): array
{
    $rows = db_fetch_all(
        'SELECT n.*, u.username AS author_username
         FROM catalog_carnote n
         LEFT JOIN auth_user u ON u.id = n.author_id
         WHERE n.car_id = :car_id
         ORDER BY n.created_at DESC, n.id DESC',
        [':car_id' => $carId]
    );

    $notes = [];
    foreach ($rows as $row) {
        $notes[] = [
            'id' => (int) ($row['id'] ?? 0),
            'text' => (string) ($row['text'] ?? ''),
            'author' => (string) ($row['author_username'] ?? ''),
            'author_id' => isset($row['author_id']) ? (int) $row['author_id'] : null,
            'created_at' => (string) ($row['created_at'] ?? ''),
            'created_at_display' => panel_format_datetime((string) ($row['created_at'] ?? '')),
        ];
    }

    return $notes;
}

function panel_add_note(int $carId, int $authorId, string $text): ?array
{
    $text = trim($text);
    if ($carId <= 0 || $text === '') {
        return null;
    }

    $createdAt = date('Y-m-d H:i:s');
    $ok = db_execute(
        'INSERT INTO catalog_carnote (text, created_at, author_id, car_id) VALUES (:text, :created_at, :author_id, :car_id)',
        [
            ':text' => $text,
            ':created_at' => $createdAt,
            ':author_id' => $authorId > 0 ? $authorId : null,
            ':car_id' => $carId,
        ]
    );
    if (!$ok) {
        return null;
    }

    $noteId = db_last_insert_id();
    $note = db_fetch_one(
        'SELECT n.*, u.username AS author_username
         FROM catalog_carnote n
         LEFT JOIN auth_user u ON u.id = n.author_id
         WHERE n.id = :id LIMIT 1',
        [':id' => $noteId]
    );
    if (!is_array($note)) {
        return null;
    }

    return [
        'id' => (int) ($note['id'] ?? 0),
        'text' => (string) ($note['text'] ?? ''),
        'author' => (string) ($note['author_username'] ?? ''),
        'created_at' => panel_format_datetime((string) ($note['created_at'] ?? '')),
    ];
}

function panel_delete_note(int $noteId): bool
{
    return db_execute('DELETE FROM catalog_carnote WHERE id = :id', [':id' => $noteId]);
}

function panel_delete_car(int $carId): bool
{
    if ($carId <= 0) {
        return false;
    }

    $images = db_fetch_all('SELECT image FROM catalog_carimage WHERE car_id = :car_id', [':car_id' => $carId]);

    $ok = db_transaction(
        static function (PDO $pdo) use ($carId): bool {
            $pdo->prepare('UPDATE catalog_clientrequest SET car_id = NULL, updated_at = :updated_at WHERE car_id = :car_id')->execute([
                ':updated_at' => date('Y-m-d H:i:s'),
                ':car_id' => $carId,
            ]);
            $pdo->prepare('DELETE FROM catalog_carnote WHERE car_id = :car_id')->execute([':car_id' => $carId]);
            $pdo->prepare('DELETE FROM catalog_carviewlog WHERE car_id = :car_id')->execute([':car_id' => $carId]);
            $pdo->prepare('DELETE FROM catalog_carimage WHERE car_id = :car_id')->execute([':car_id' => $carId]);
            return $pdo->prepare('DELETE FROM catalog_car WHERE id = :id')->execute([':id' => $carId]);
        }
    );

    if (!$ok) {
        return false;
    }

    foreach ($images as $image) {
        panel_cleanup_media_path((string) ($image['image'] ?? ''));
    }

    return true;
}

function panel_duplicate_car(int $carId): int
{
    $car = db_fetch_one('SELECT * FROM catalog_car WHERE id = :id LIMIT 1', [':id' => $carId]);
    if (!is_array($car)) {
        return 0;
    }

    $images = db_fetch_all('SELECT * FROM catalog_carimage WHERE car_id = :car_id ORDER BY is_main DESC, created_at ASC, id ASC', [':car_id' => $carId]);
    $newId = 0;

    db_transaction(
        static function (PDO $pdo) use ($car, $images, &$newId): void {
            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare(
                'INSERT INTO catalog_car (name, manufacturer, model, year, price, origin, mileage_km, fuel, description, body_type, engine_volume, drive, availability, alt_name, created_at, updated_at, is_active, views_count)
                 VALUES (:name, :manufacturer, :model, :year, :price, :origin, :mileage_km, :fuel, :description, :body_type, :engine_volume, :drive, :availability, :alt_name, :created_at, :updated_at, :is_active, :views_count)'
            );
            $stmt->execute([
                ':name' => (string) ($car['name'] ?? '') . ' (копия)',
                ':manufacturer' => (string) ($car['manufacturer'] ?? ''),
                ':model' => (string) ($car['model'] ?? ''),
                ':year' => (int) ($car['year'] ?? 0),
                ':price' => (float) ($car['price'] ?? 0),
                ':origin' => (string) ($car['origin'] ?? ''),
                ':mileage_km' => (int) ($car['mileage_km'] ?? 0),
                ':fuel' => (string) ($car['fuel'] ?? ''),
                ':description' => (string) ($car['description'] ?? ''),
                ':body_type' => (string) ($car['body_type'] ?? ''),
                ':engine_volume' => (string) ($car['engine_volume'] ?? ''),
                ':drive' => (string) ($car['drive'] ?? ''),
                ':availability' => 'on_order',
                ':alt_name' => '',
                ':created_at' => $now,
                ':updated_at' => $now,
                ':is_active' => 0,
                ':views_count' => 0,
            ]);
            $newId = (int) $pdo->lastInsertId();

            if ($newId <= 0) {
                return;
            }

            $imageStmt = $pdo->prepare(
                'INSERT INTO catalog_carimage (image, is_main, alt_text, created_at, car_id)
                 VALUES (:image, :is_main, :alt_text, :created_at, :car_id)'
            );
            foreach ($images as $image) {
                $imageStmt->execute([
                    ':image' => (string) ($image['image'] ?? ''),
                    ':is_main' => panel_bool($image['is_main'] ?? false) ? 1 : 0,
                    ':alt_text' => (string) ($image['alt_text'] ?? ''),
                    ':created_at' => $now,
                    ':car_id' => $newId,
                ]);
            }
        }
    );

    return $newId;
}

function panel_bulk_action(array $ids, string $action): int
{
    $ids = array_values(array_filter(array_map('intval', $ids), static function (int $id): bool {
        return $id > 0;
    }));
    if ($ids === [] || $action === '') {
        return 0;
    }

    if ($action === 'delete') {
        $count = 0;
        foreach ($ids as $id) {
            if (panel_delete_car($id)) {
                $count++;
            }
        }
        return $count;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    switch ($action) {
        case 'activate':
            db_execute('UPDATE catalog_car SET is_active = 1, updated_at = ? WHERE id IN (' . $placeholders . ')', array_merge([date('Y-m-d H:i:s')], $ids));
            return count($ids);
        case 'deactivate':
            db_execute('UPDATE catalog_car SET is_active = 0, updated_at = ? WHERE id IN (' . $placeholders . ')', array_merge([date('Y-m-d H:i:s')], $ids));
            return count($ids);
        case 'set_in_stock':
        case 'set_on_order':
        case 'set_sold':
            $statusMap = [
                'set_in_stock' => 'in_stock',
                'set_on_order' => 'on_order',
                'set_sold' => 'sold',
            ];
            db_execute('UPDATE catalog_car SET availability = ?, updated_at = ? WHERE id IN (' . $placeholders . ')', array_merge([$statusMap[$action], date('Y-m-d H:i:s')], $ids));
            return count($ids);
        default:
            return 0;
    }
}

function panel_toggle_car_active(int $carId): ?bool
{
    $car = db_fetch_one('SELECT is_active FROM catalog_car WHERE id = :id LIMIT 1', [':id' => $carId]);
    if (!is_array($car)) {
        return null;
    }

    $newValue = panel_bool($car['is_active'] ?? false) ? 0 : 1;
    db_execute('UPDATE catalog_car SET is_active = :is_active, updated_at = :updated_at WHERE id = :id', [
        ':is_active' => $newValue,
        ':updated_at' => date('Y-m-d H:i:s'),
        ':id' => $carId,
    ]);

    return $newValue === 1;
}

function panel_set_car_status(int $carId, string $status): ?string
{
    if (!isset(panel_availability_choices()[$status])) {
        return null;
    }

    $ok = db_execute('UPDATE catalog_car SET availability = :availability, updated_at = :updated_at WHERE id = :id', [
        ':availability' => $status,
        ':updated_at' => date('Y-m-d H:i:s'),
        ':id' => $carId,
    ]);

    return $ok ? $status : null;
}

function panel_list_requests(array $filters = []): array
{
    $sql = 'SELECT * FROM catalog_clientrequest WHERE 1 = 1';
    $params = [];

    $status = trim((string) ($filters['status'] ?? ''));
    if ($status !== '' && isset(panel_request_status_choices()[$status])) {
        $sql .= ' AND status = :status';
        $params[':status'] = $status;
    }

    $search = trim((string) ($filters['q'] ?? ''));
    if ($search !== '') {
        $sql .= ' AND (name LIKE :search OR phone LIKE :search OR wishes LIKE :search OR budget LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY created_at DESC, id DESC';

    $limit = isset($filters['limit']) ? (int) $filters['limit'] : 100;
    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $rows = db_fetch_all($sql, $params);
    $requests = [];
    foreach ($rows as $row) {
        $requests[] = panel_normalize_request($row);
    }

    return $requests;
}

function panel_requests_total(array $filters = []): int
{
    $sql = 'SELECT COUNT(*) FROM catalog_clientrequest WHERE 1 = 1';
    $params = [];

    $status = trim((string) ($filters['status'] ?? ''));
    if ($status !== '' && isset(panel_request_status_choices()[$status])) {
        $sql .= ' AND status = :status';
        $params[':status'] = $status;
    }

    $search = trim((string) ($filters['q'] ?? ''));
    if ($search !== '') {
        $sql .= ' AND (name LIKE :search OR phone LIKE :search OR wishes LIKE :search OR budget LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    return (int) (db_fetch_value($sql, $params) ?? 0);
}

function panel_normalize_request(array $row): array
{
    $request = [
        'id' => (int) ($row['id'] ?? 0),
        'name' => (string) ($row['name'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'budget' => (string) ($row['budget'] ?? ''),
        'wishes' => (string) ($row['wishes'] ?? ''),
        'source_page' => (string) ($row['source_page'] ?? ''),
        'status' => (string) ($row['status'] ?? 'new'),
        'status_label' => panel_request_status_label((string) ($row['status'] ?? 'new')),
        'admin_notes' => (string) ($row['admin_notes'] ?? ''),
        'created_at' => (string) ($row['created_at'] ?? ''),
        'updated_at' => (string) ($row['updated_at'] ?? ''),
        'created_at_display' => panel_format_datetime((string) ($row['created_at'] ?? '')),
        'updated_at_display' => panel_format_datetime((string) ($row['updated_at'] ?? '')),
        'car_id' => isset($row['car_id']) ? (int) $row['car_id'] : null,
    ];

    if (!empty($request['car_id'])) {
        $car = panel_find_car((int) $request['car_id']);
        if ($car !== null) {
            $request['car'] = $car;
        }
    }

    return $request;
}

function panel_find_request(int $requestId): ?array
{
    if ($requestId <= 0) {
        return null;
    }

    $row = db_fetch_one('SELECT * FROM catalog_clientrequest WHERE id = :id LIMIT 1', [':id' => $requestId]);
    return is_array($row) ? panel_normalize_request($row) : null;
}

function panel_update_request(int $requestId, string $status, string $adminNotes): bool
{
    if (!isset(panel_request_status_choices()[$status])) {
        return false;
    }

    return db_execute('UPDATE catalog_clientrequest SET status = :status, admin_notes = :admin_notes, updated_at = :updated_at WHERE id = :id', [
        ':status' => $status,
        ':admin_notes' => $adminNotes,
        ':updated_at' => date('Y-m-d H:i:s'),
        ':id' => $requestId,
    ]);
}

function panel_set_request_status(int $requestId, string $status): ?array
{
    if (!isset(panel_request_status_choices()[$status])) {
        return null;
    }

    $ok = db_execute('UPDATE catalog_clientrequest SET status = :status, updated_at = :updated_at WHERE id = :id', [
        ':status' => $status,
        ':updated_at' => date('Y-m-d H:i:s'),
        ':id' => $requestId,
    ]);

    if (!$ok) {
        return null;
    }

    return [
        'status' => $status,
        'display' => panel_request_status_label($status),
    ];
}
