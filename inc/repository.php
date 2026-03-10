<?php

declare(strict_types=1);

const CATALOG_DATA_FILE = __DIR__ . '/../data/db_dump.json';
const CATALOG_SQLITE_FILE = __DIR__ . '/../data/akiba.sqlite3';
const REQUESTS_STORAGE_FILE = __DIR__ . '/../storage/requests.jsonl';
const REQUESTS_COUNTER_FILE = __DIR__ . '/../storage/requests_counter.txt';

/**
 * @return array{
 *   cars: array<int, array<string,mixed>>,
 *   cars_by_id: array<int, array<string,mixed>>,
 *   existing_request_max_id: int
 * }
 */
function load_catalog_dataset(): array
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    if (catalog_database_available()) {
        $cache = load_catalog_dataset_from_database();
        return $cache;
    }

    if (!is_file(CATALOG_DATA_FILE)) {
        $cache = [
            'cars' => [],
            'cars_by_id' => [],
            'existing_request_max_id' => 0,
        ];
        return $cache;
    }

    $raw = file_get_contents(CATALOG_DATA_FILE);
    if ($raw === false) {
        $cache = [
            'cars' => [],
            'cars_by_id' => [],
            'existing_request_max_id' => 0,
        ];
        return $cache;
    }

    $items = json_decode($raw, true);
    if (!is_array($items)) {
        $cache = [
            'cars' => [],
            'cars_by_id' => [],
            'existing_request_max_id' => 0,
        ];
        return $cache;
    }

    $cars = [];
    $imagesByCar = [];
    $existingRequestMaxId = 0;

    foreach ($items as $item) {
        if (!is_array($item) || !isset($item['model'], $item['pk'], $item['fields'])) {
            continue;
        }

        $model = (string) $item['model'];
        $pk = (int) $item['pk'];
        $fields = is_array($item['fields']) ? $item['fields'] : [];

        if ($model === 'catalog.car') {
            $car = [
                'id' => $pk,
                'name' => (string) ($fields['name'] ?? ''),
                'manufacturer' => (string) ($fields['manufacturer'] ?? ''),
                'model' => (string) ($fields['model'] ?? ''),
                'year' => (int) ($fields['year'] ?? 0),
                'price' => (float) ($fields['price'] ?? 0),
                'origin' => (string) ($fields['origin'] ?? ''),
                'mileage_km' => (int) ($fields['mileage_km'] ?? 0),
                'fuel' => (string) ($fields['fuel'] ?? ''),
                'drive' => (string) ($fields['drive'] ?? ''),
                'body_type' => (string) ($fields['body_type'] ?? ''),
                'engine_volume' => (string) ($fields['engine_volume'] ?? ''),
                'availability' => (string) ($fields['availability'] ?? ''),
                'description' => (string) ($fields['description'] ?? ''),
                'alt_name' => (string) ($fields['alt_name'] ?? ''),
                'is_active' => (bool) ($fields['is_active'] ?? false),
                'views_count' => (int) ($fields['views_count'] ?? 0),
                'created_at' => (string) ($fields['created_at'] ?? ''),
                'updated_at' => (string) ($fields['updated_at'] ?? ''),
                'images' => [],
                'image_url' => null,
            ];

            $cars[$pk] = $car;
            continue;
        }

        if ($model === 'catalog.carimage') {
            $carId = (int) ($fields['car'] ?? 0);
            if ($carId <= 0) {
                continue;
            }

            $imagePath = (string) ($fields['image'] ?? '');
            $imagesByCar[$carId][] = [
                'id' => $pk,
                'car_id' => $carId,
                'path' => $imagePath,
                'url' => $imagePath === '' ? null : media_url($imagePath),
                'is_main' => (bool) ($fields['is_main'] ?? false),
                'alt_text' => (string) ($fields['alt_text'] ?? ''),
                'created_at' => (string) ($fields['created_at'] ?? ''),
            ];
            continue;
        }

        if ($model === 'catalog.clientrequest') {
            $existingRequestMaxId = max($existingRequestMaxId, $pk);
            continue;
        }
    }

    foreach ($cars as $carId => &$car) {
        $images = $imagesByCar[$carId] ?? [];

        usort(
            $images,
            static function (array $a, array $b): int {
                if ($a['is_main'] !== $b['is_main']) {
                    return $a['is_main'] ? -1 : 1;
                }

                return strcmp((string) $b['created_at'], (string) $a['created_at']);
            }
        );

        $car['images'] = $images;
        $car['image_url'] = null;

        foreach ($images as $img) {
            if (($img['is_main'] ?? false) && !empty($img['url'])) {
                $car['image_url'] = $img['url'];
                break;
            }
        }

        if ($car['image_url'] === null && !empty($images[0]['url'])) {
            $car['image_url'] = $images[0]['url'];
        }
    }
    unset($car);

    $carsList = array_values($cars);
    usort(
        $carsList,
        static function (array $a, array $b): int {
            if ($a['year'] !== $b['year']) {
                return $b['year'] <=> $a['year'];
            }

            $manufacturerCmp = strcmp((string) $a['manufacturer'], (string) $b['manufacturer']);
            if ($manufacturerCmp !== 0) {
                return $manufacturerCmp;
            }

            return strcmp((string) $a['model'], (string) $b['model']);
        }
    );

    $carsById = [];
    foreach ($carsList as $car) {
        $carsById[(int) $car['id']] = $car;
    }

    $cache = [
        'cars' => $carsList,
        'cars_by_id' => $carsById,
        'existing_request_max_id' => $existingRequestMaxId,
    ];

    return $cache;
}

/**
 * @return array{
 *   cars: array<int, array<string,mixed>>,
 *   cars_by_id: array<int, array<string,mixed>>,
 *   existing_request_max_id: int
 * }
 */
function load_catalog_dataset_from_database(): array
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return [
            'cars' => [],
            'cars_by_id' => [],
            'existing_request_max_id' => 0,
        ];
    }

    $carRows = db_fetch_all('SELECT * FROM catalog_car');
    $imageRows = db_fetch_all('SELECT * FROM catalog_carimage ORDER BY car_id, is_main DESC, created_at DESC, id DESC');
    $existingRequestMaxId = (int) (db_fetch_value('SELECT COALESCE(MAX(id), 0) FROM catalog_clientrequest') ?? 0);

    $cars = [];
    foreach ($carRows as $row) {
        $id = (int) ($row['id'] ?? 0);
        if ($id <= 0) {
            continue;
        }

        $cars[$id] = [
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
            'is_active' => (bool) ($row['is_active'] ?? false),
            'views_count' => (int) ($row['views_count'] ?? 0),
            'created_at' => (string) ($row['created_at'] ?? ''),
            'updated_at' => (string) ($row['updated_at'] ?? ''),
            'images' => [],
            'image_url' => null,
        ];
    }

    foreach ($imageRows as $row) {
        $carId = (int) ($row['car_id'] ?? 0);
        if ($carId <= 0 || !isset($cars[$carId])) {
            continue;
        }

        $imagePath = (string) ($row['image'] ?? '');
        $image = [
            'id' => (int) ($row['id'] ?? 0),
            'car_id' => $carId,
            'path' => $imagePath,
            'url' => $imagePath === '' ? null : media_url($imagePath),
            'is_main' => (bool) ($row['is_main'] ?? false),
            'alt_text' => (string) ($row['alt_text'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? ''),
        ];

        $cars[$carId]['images'][] = $image;

        if ($cars[$carId]['image_url'] === null && !empty($image['url'])) {
            $cars[$carId]['image_url'] = $image['url'];
        }
    }

    $carsList = array_values($cars);
    usort(
        $carsList,
        static function (array $a, array $b): int {
            if ($a['year'] !== $b['year']) {
                return $b['year'] <=> $a['year'];
            }

            $manufacturerCmp = strcmp((string) $a['manufacturer'], (string) $b['manufacturer']);
            if ($manufacturerCmp !== 0) {
                return $manufacturerCmp;
            }

            return strcmp((string) $a['model'], (string) $b['model']);
        }
    );

    $carsById = [];
    foreach ($carsList as $car) {
        $carsById[(int) $car['id']] = $car;
    }

    return [
        'cars' => $carsList,
        'cars_by_id' => $carsById,
        'existing_request_max_id' => $existingRequestMaxId,
    ];
}

/**
 * @return array<int, array<string,mixed>>
 */
function catalog_base_cars(): array
{
    $dataset = load_catalog_dataset();
    $cars = [];

    foreach ($dataset['cars'] as $car) {
        if (($car['is_active'] ?? false) && (float) ($car['price'] ?? 0) > 0) {
            $cars[] = $car;
        }
    }

    return $cars;
}

/**
 * @return array<string,string>
 */
function catalog_filters_from_query(array $query): array
{
    $allowed = [
        'origin',
        'manufacturer',
        'model',
        'fuel',
        'body_type',
        'drive',
        'mileage_from',
        'mileage_to',
        'price_from',
        'price_to',
        'engine_volume_from',
        'engine_volume_to',
        'year_from',
        'year_to',
        'sort',
    ];

    $filters = [];
    foreach ($allowed as $key) {
        $value = $query[$key] ?? '';
        $filters[$key] = trim((string) $value);
    }

    return $filters;
}

/**
 * @param array<int, array<string,mixed>> $cars
 * @return array<int, array<string,mixed>>
 */
function apply_catalog_filters(array $cars, array $filters): array
{
    return array_values(array_filter(
        $cars,
        static function (array $car) use ($filters): bool {
            if ($filters['origin'] !== '' && $car['origin'] !== $filters['origin']) {
                return false;
            }

            if ($filters['manufacturer'] !== '' && $car['manufacturer'] !== $filters['manufacturer']) {
                return false;
            }

            if ($filters['model'] !== '' && $car['model'] !== $filters['model']) {
                return false;
            }

            if ($filters['fuel'] !== '' && stripos((string) $car['fuel'], $filters['fuel']) === false) {
                return false;
            }

            if ($filters['body_type'] !== '' && stripos((string) $car['body_type'], $filters['body_type']) === false) {
                return false;
            }

            if ($filters['drive'] !== '' && stripos((string) $car['drive'], $filters['drive']) === false) {
                return false;
            }

            $mileage = (int) ($car['mileage_km'] ?? 0);
            if ($filters['mileage_from'] !== '' && $mileage < (int) $filters['mileage_from']) {
                return false;
            }
            if ($filters['mileage_to'] !== '' && $mileage > (int) $filters['mileage_to']) {
                return false;
            }

            $price = (float) ($car['price'] ?? 0);
            if ($filters['price_from'] !== '' && $price < (float) $filters['price_from']) {
                return false;
            }
            if ($filters['price_to'] !== '' && $price > (float) $filters['price_to']) {
                return false;
            }

            $year = (int) ($car['year'] ?? 0);
            if ($filters['year_from'] !== '' && $year < (int) $filters['year_from']) {
                return false;
            }
            if ($filters['year_to'] !== '' && $year > (int) $filters['year_to']) {
                return false;
            }

            if ($filters['engine_volume_from'] !== '' || $filters['engine_volume_to'] !== '') {
                $volumeRaw = str_replace(',', '.', trim((string) ($car['engine_volume'] ?? '')));
                if ($volumeRaw === '' || !is_numeric($volumeRaw)) {
                    return false;
                }

                $volume = (float) $volumeRaw;
                if ($filters['engine_volume_from'] !== '' && is_numeric(str_replace(',', '.', $filters['engine_volume_from']))) {
                    $from = (float) str_replace(',', '.', $filters['engine_volume_from']);
                    if ($volume < $from) {
                        return false;
                    }
                }

                if ($filters['engine_volume_to'] !== '' && is_numeric(str_replace(',', '.', $filters['engine_volume_to']))) {
                    $to = (float) str_replace(',', '.', $filters['engine_volume_to']);
                    if ($volume > $to) {
                        return false;
                    }
                }
            }

            return true;
        }
    ));
}

/**
 * @param array<int, array<string,mixed>> $cars
 * @return array<int, array<string,mixed>>
 */
function sort_cars(array $cars, string $sort): array
{
    $sorted = $cars;

    usort(
        $sorted,
        static function (array $a, array $b) use ($sort): int {
            switch ($sort) {
                case 'price_asc':
                    return (float) $a['price'] <=> (float) $b['price'];
                case 'price_desc':
                    return (float) $b['price'] <=> (float) $a['price'];
                case 'year_desc':
                    return (int) $b['year'] <=> (int) $a['year'];
                case 'year_asc':
                    return (int) $a['year'] <=> (int) $b['year'];
                case 'mileage_asc':
                    return (int) $a['mileage_km'] <=> (int) $b['mileage_km'];
                case 'mileage_desc':
                    return (int) $b['mileage_km'] <=> (int) $a['mileage_km'];
                default:
                    return compare_cars_default($a, $b);
            }
        }
    );

    return $sorted;
}

function compare_cars_default(array $a, array $b): int
{
    if ((int) $a['year'] !== (int) $b['year']) {
        return (int) $b['year'] <=> (int) $a['year'];
    }

    $manufacturerCmp = strcmp((string) $a['manufacturer'], (string) $b['manufacturer']);
    if ($manufacturerCmp !== 0) {
        return $manufacturerCmp;
    }

    return strcmp((string) $a['model'], (string) $b['model']);
}

/**
 * @param array<int, array<string,mixed>> $cars
 * @return array{items: array<int, array<string,mixed>>, page:int, per_page:int, total:int, num_pages:int, has_previous:bool, has_next:bool, previous_page_number:int, next_page_number:int}
 */
function paginate_cars(array $cars, int $page, int $perPage = 12): array
{
    $total = count($cars);
    $numPages = max(1, (int) ceil($total / $perPage));
    $safePage = max(1, min($page, $numPages));

    $offset = ($safePage - 1) * $perPage;
    $items = array_slice($cars, $offset, $perPage);

    return [
        'items' => $items,
        'page' => $safePage,
        'per_page' => $perPage,
        'total' => $total,
        'num_pages' => $numPages,
        'has_previous' => $safePage > 1,
        'has_next' => $safePage < $numPages,
        'previous_page_number' => max(1, $safePage - 1),
        'next_page_number' => min($numPages, $safePage + 1),
    ];
}

/**
 * @return array{
 *   manufacturers: array<int,string>,
 *   models: array<int,string>,
 *   fuels: array<int,string>,
 *   body_types: array<int,string>,
 *   drives: array<int,string>,
 *   years: array<int,int>,
 *   min_mileage:int,
 *   max_mileage:int,
 *   min_price:float,
 *   max_price:float,
 *   min_year:int,
 *   max_year:int
 * }
 */
function catalog_filter_options(array $baseCars, string $selectedManufacturer = ''): array
{
    $manufacturers = unique_sorted(array_map(
        static function (array $c): string {
            return (string) $c['manufacturer'];
        },
        $baseCars
    ));

    $modelsSource = $baseCars;
    if ($selectedManufacturer !== '') {
        $modelsSource = array_values(array_filter(
            $baseCars,
            static function (array $c) use ($selectedManufacturer): bool {
                return (string) $c['manufacturer'] === $selectedManufacturer;
            }
        ));
    }

    $models = unique_sorted(array_map(
        static function (array $c): string {
            return (string) $c['model'];
        },
        $modelsSource
    ));
    $fuels = unique_sorted(array_map(
        static function (array $c): string {
            return (string) $c['fuel'];
        },
        $baseCars
    ));
    $bodyTypes = unique_sorted(array_map(
        static function (array $c): string {
            return (string) $c['body_type'];
        },
        $baseCars
    ));
    $drives = unique_sorted(array_map(
        static function (array $c): string {
            return (string) $c['drive'];
        },
        $baseCars
    ));

    $years = array_values(array_unique(array_map(
        static function (array $c): int {
            return (int) $c['year'];
        },
        $baseCars
    )));
    sort($years, SORT_NUMERIC);

    $mileages = array_map(
        static function (array $c): int {
            return (int) $c['mileage_km'];
        },
        $baseCars
    );
    $prices = array_map(
        static function (array $c): float {
            return (float) $c['price'];
        },
        $baseCars
    );

    return [
        'manufacturers' => $manufacturers,
        'models' => $models,
        'fuels' => $fuels,
        'body_types' => $bodyTypes,
        'drives' => $drives,
        'years' => $years,
        'min_mileage' => $mileages === [] ? 0 : min($mileages),
        'max_mileage' => $mileages === [] ? 0 : max($mileages),
        'min_price' => $prices === [] ? 0 : min($prices),
        'max_price' => $prices === [] ? 0 : max($prices),
        'min_year' => $years === [] ? 0 : min($years),
        'max_year' => $years === [] ? 0 : max($years),
    ];
}

/**
 * @param array<int,string> $values
 * @return array<int,string>
 */
function unique_sorted(array $values): array
{
    $filtered = [];

    foreach ($values as $value) {
        $value = trim($value);
        if ($value === '') {
            continue;
        }
        $filtered[] = $value;
    }

    $filtered = array_values(array_unique($filtered));
    sort($filtered, SORT_NATURAL | SORT_FLAG_CASE);

    return $filtered;
}

/**
 * @return array<string,mixed>
 */
function catalog_page_data(array $query): array
{
    $filters = catalog_filters_from_query($query);
    $baseCars = catalog_base_cars();
    $filtered = apply_catalog_filters($baseCars, $filters);
    $sorted = sort_cars($filtered, $filters['sort']);

    $page = isset($query['page']) ? max(1, (int) $query['page']) : 1;
    $pagination = paginate_cars($sorted, $page, 12);
    $options = catalog_filter_options($baseCars, $filters['manufacturer']);

    return [
        'cars' => $pagination['items'],
        'filters' => $filters,
        'pagination' => $pagination,
        'options' => $options,
    ];
}

/**
 * @return array<int,array<string,mixed>>
 */
function home_cars(int $limit = 8): array
{
    return array_slice(catalog_base_cars(), 0, $limit);
}

/**
 * @return array<string,mixed>|null
 */
function find_car_by_id(int $id): ?array
{
    $dataset = load_catalog_dataset();
    $car = $dataset['cars_by_id'][$id] ?? null;
    if ($car === null) {
        return null;
    }

    if (!(bool) ($car['is_active'] ?? false) || (float) ($car['price'] ?? 0) <= 0) {
        return null;
    }

    return $car;
}

/**
 * @param array<string,mixed> $car
 * @param array<int,array<string,mixed>> $allCars
 * @return array<int,array<string,mixed>>
 */
function related_cars(array $car, array $allCars, int $limit = 6): array
{
    $sortedByPrice = $allCars;
    usort(
        $sortedByPrice,
        static function (array $a, array $b): int {
            return (float) $a['price'] <=> (float) $b['price'];
        }
    );

    $result = [];
    $seen = [(int) $car['id'] => true];

    foreach ($sortedByPrice as $candidate) {
        if (count($result) >= $limit) {
            break;
        }

        if ((int) $candidate['id'] === (int) $car['id']) {
            continue;
        }

        if ((string) $candidate['manufacturer'] === (string) $car['manufacturer']) {
            $result[] = $candidate;
            $seen[(int) $candidate['id']] = true;
        }
    }

    if (count($result) < $limit) {
        foreach ($sortedByPrice as $candidate) {
            if (count($result) >= $limit) {
                break;
            }
            if (isset($seen[(int) $candidate['id']])) {
                continue;
            }

            if ((string) $candidate['origin'] === (string) $car['origin']) {
                $result[] = $candidate;
                $seen[(int) $candidate['id']] = true;
            }
        }
    }

    if (count($result) < $limit) {
        foreach ($sortedByPrice as $candidate) {
            if (count($result) >= $limit) {
                break;
            }
            if (isset($seen[(int) $candidate['id']])) {
                continue;
            }
            $result[] = $candidate;
            $seen[(int) $candidate['id']] = true;
        }
    }

    return array_slice($result, 0, $limit);
}

/**
 * @return array<int,array<string,string>>
 */
function default_reviews(): array
{
    return [
        [
            'author' => 'Иван С.',
            'text' => 'Отличная компания! Заказал Toyota через AkibaAuto, всё прошло гладко. Машина пришла в срок, все документы в порядке. Рекомендую!',
            'date' => '15 января 2025',
            'rating' => '5',
        ],
        [
            'author' => 'Мария К.',
            'text' => 'Очень довольна покупкой. Сотрудники помогли с выбором, ответили на все вопросы. Доставка быстрая, машина в отличном состоянии.',
            'date' => '10 января 2025',
            'rating' => '5',
        ],
        [
            'author' => 'Дмитрий В.',
            'text' => 'Покупал Hyundai через эту компанию. Всё прозрачно, никаких скрытых платежей. Машина соответствует описанию. Спасибо!',
            'date' => '5 января 2025',
            'rating' => '5',
        ],
        [
            'author' => 'Анна П.',
            'text' => 'Быстро нашли нужную модель, помогли с оформлением. Очень профессиональный подход. Буду обращаться ещё.',
            'date' => '28 декабря 2024',
            'rating' => '5',
        ],
        [
            'author' => 'Сергей М.',
            'text' => 'Отличный сервис! Все этапы покупки под контролем, регулярные обновления о статусе доставки. Машина пришла в идеальном состоянии.',
            'date' => '20 декабря 2024',
            'rating' => '5',
        ],
        [
            'author' => 'Елена Р.',
            'text' => 'Первый раз покупала авто из Японии. Компания AkibaAuto помогла на каждом этапе. Всё объяснили, сопроводили сделку. Очень довольна!',
            'date' => '12 декабря 2024',
            'rating' => '5',
        ],
    ];
}

function next_request_id(): int
{
    $dataset = load_catalog_dataset();
    $initial = (int) $dataset['existing_request_max_id'];

    if (!is_file(REQUESTS_COUNTER_FILE)) {
        if (!is_dir(dirname(REQUESTS_COUNTER_FILE))) {
            mkdir(dirname(REQUESTS_COUNTER_FILE), 0775, true);
        }
        file_put_contents(REQUESTS_COUNTER_FILE, (string) $initial);
    }

    $fp = fopen(REQUESTS_COUNTER_FILE, 'c+');
    if ($fp === false) {
        return (int) (time() % 1000000);
    }

    flock($fp, LOCK_EX);
    rewind($fp);
    $current = trim((string) stream_get_contents($fp));
    $currentId = ctype_digit($current) ? (int) $current : $initial;
    $nextId = $currentId + 1;

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $nextId);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return $nextId;
}

/**
 * @param array<string,mixed> $payload
 */
function save_client_request(array $payload): int
{
    if (catalog_database_available()) {
        $savedId = save_client_request_to_database($payload);
        if ($savedId > 0) {
            append_request_record([
                'id' => $savedId,
                'name' => (string) ($payload['name'] ?? ''),
                'phone' => (string) ($payload['phone'] ?? ''),
                'budget' => (string) ($payload['budget'] ?? ''),
                'wishes' => (string) ($payload['wishes'] ?? ''),
                'source_page' => (string) ($payload['source_page'] ?? ''),
                'car_id' => isset($payload['car_id']) ? (int) $payload['car_id'] : null,
                'created_at' => date(DATE_ATOM),
                'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
                'user_agent' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
            ]);
            return $savedId;
        }
    }

    $requestId = next_request_id();

    $record = [
        'id' => $requestId,
        'name' => (string) ($payload['name'] ?? ''),
        'phone' => (string) ($payload['phone'] ?? ''),
        'budget' => (string) ($payload['budget'] ?? ''),
        'wishes' => (string) ($payload['wishes'] ?? ''),
        'source_page' => (string) ($payload['source_page'] ?? ''),
        'car_id' => isset($payload['car_id']) ? (int) $payload['car_id'] : null,
        'created_at' => date(DATE_ATOM),
        'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
        'user_agent' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
    ];

    append_request_record($record);

    return $requestId;
}

/**
 * @param array<string,mixed> $payload
 */
function save_client_request_to_database(array $payload): int
{
    $createdAt = date('Y-m-d H:i:s');
    $carId = isset($payload['car_id']) && (int) $payload['car_id'] > 0 ? (int) $payload['car_id'] : null;

    $ok = db_execute(
        'INSERT INTO catalog_clientrequest (name, phone, budget, wishes, source_page, status, admin_notes, created_at, updated_at, car_id)
         VALUES (:name, :phone, :budget, :wishes, :source_page, :status, :admin_notes, :created_at, :updated_at, :car_id)',
        [
            ':name' => (string) ($payload['name'] ?? ''),
            ':phone' => (string) ($payload['phone'] ?? ''),
            ':budget' => (string) ($payload['budget'] ?? ''),
            ':wishes' => (string) ($payload['wishes'] ?? ''),
            ':source_page' => (string) ($payload['source_page'] ?? ''),
            ':status' => 'new',
            ':admin_notes' => '',
            ':created_at' => $createdAt,
            ':updated_at' => $createdAt,
            ':car_id' => $carId,
        ]
    );

    return $ok ? db_last_insert_id() : 0;
}

/**
 * @param array<string,mixed> $record
 */
function append_request_record(array $record): void
{
    if (!is_dir(dirname(REQUESTS_STORAGE_FILE))) {
        mkdir(dirname(REQUESTS_STORAGE_FILE), 0775, true);
    }

    file_put_contents(
        REQUESTS_STORAGE_FILE,
        json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

function record_car_view(int $carId): void
{
    if ($carId <= 0 || !catalog_database_available()) {
        return;
    }

    $today = date('Y-m-d');

    db_transaction(
        static function (PDO $pdo) use ($carId, $today): void {
            $updateCar = $pdo->prepare('UPDATE catalog_car SET views_count = COALESCE(views_count, 0) + 1 WHERE id = :id');
            $updateCar->execute([':id' => $carId]);

            $existing = $pdo->prepare('SELECT id, views FROM catalog_carviewlog WHERE car_id = :car_id AND date = :date LIMIT 1');
            $existing->execute([
                ':car_id' => $carId,
                ':date' => $today,
            ]);
            $row = $existing->fetch(PDO::FETCH_ASSOC);

            if (is_array($row) && isset($row['id'])) {
                $updateLog = $pdo->prepare('UPDATE catalog_carviewlog SET views = :views WHERE id = :id');
                $updateLog->execute([
                    ':views' => (int) ($row['views'] ?? 0) + 1,
                    ':id' => (int) $row['id'],
                ]);
                return;
            }

            $insertLog = $pdo->prepare('INSERT INTO catalog_carviewlog (date, views, car_id) VALUES (:date, :views, :car_id)');
            $insertLog->execute([
                ':date' => $today,
                ':views' => 1,
                ':car_id' => $carId,
            ]);
        }
    );
}

/**
 * @param array<string,mixed> $payload
 */
function send_request_email(array $payload, int $requestId): bool
{
    $message = build_request_email_message($payload, $requestId);
    $settings = mail_settings();
    $success = send_multipart_mail(
        $message['recipients'],
        $message['subject'],
        $message['text'],
        $message['html'],
        $settings
    );

    log_mail_event(
        [
            'request_id' => $requestId,
            'recipients' => $message['recipients'],
            'subject' => $message['subject'],
            'source_page' => (string) ($payload['source_page'] ?? ''),
            'success' => $success,
        ],
        [
            'from_email' => $settings['from_email'],
            'from_name' => $settings['from_name'],
        ]
    );

    return $success;
}
