<?php
$selectedSort = $filters['sort'] ?? '';
$sortLabel = 'Сортировка';
if ($selectedSort === 'price_asc') {
    $sortLabel = 'Цена: по возрастанию';
} elseif ($selectedSort === 'price_desc') {
    $sortLabel = 'Цена: по убыванию';
} elseif ($selectedSort === 'year_desc') {
    $sortLabel = 'Год: новее';
} elseif ($selectedSort === 'year_asc') {
    $sortLabel = 'Год: старше';
} elseif ($selectedSort === 'mileage_asc') {
    $sortLabel = 'Пробег: меньше';
} elseif ($selectedSort === 'mileage_desc') {
    $sortLabel = 'Пробег: больше';
}

$hasActiveFilters = false;
foreach ($filters as $value) {
    if ((string) $value !== '') {
        $hasActiveFilters = true;
        break;
    }
}

$manufacturers = $options['manufacturers'] ?? [];
$models = $options['models'] ?? [];
$fuels = $options['fuels'] ?? [];
$bodyTypes = $options['body_types'] ?? [];
$drives = $options['drives'] ?? [];
$years = $options['years'] ?? [];

$currentPage = (int) ($pagination['page'] ?? 1);
$totalPages = (int) ($pagination['num_pages'] ?? 1);
?>

<section class="catalog-section" id="catalog">
    <div class="container">
        <div class="catalog-header">
            <div class="catalog-header__badge">
                <span class="catalog-header__badge-dot"></span>
                <span>Автомобили под заказ</span>
            </div>
            <h1 class="catalog-header__title">Каталог автомобилей</h1>
            <p class="catalog-header__subtitle">Подберите идеальный автомобиль из Японии, Кореи или Китая</p>
        </div>

        <div class="catalog-content">
            <div class="catalog-filters">
                <div class="catalog-filters__header">
                    <button type="button" class="catalog-filters__toggle" id="filterToggleBtn" aria-expanded="<?= $hasActiveFilters ? 'true' : 'false' ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        <span>Фильтры</span>
                        <svg class="catalog-filters__toggle-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <div class="catalog-sort">
                        <button type="button" class="catalog-sort__btn" id="sortToggleBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="16" y2="12"/><line x1="4" y1="18" x2="12" y2="18"/></svg>
                            <span class="catalog-sort__text"><?= e($sortLabel) ?></span>
                            <svg class="catalog-sort__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="catalog-sort__dropdown" id="sortDropdownMenu">
                            <?php
                            $sortOptions = [
                                '' => 'По умолчанию',
                                'price_asc' => 'Цена: по возрастанию',
                                'price_desc' => 'Цена: по убыванию',
                                'year_desc' => 'Год: новее',
                                'year_asc' => 'Год: старше',
                                'mileage_asc' => 'Пробег: меньше',
                                'mileage_desc' => 'Пробег: больше',
                            ];
                            foreach ($sortOptions as $value => $label):
                                $active = $selectedSort === $value;
                            ?>
                                <button type="button" class="catalog-sort__option <?= $active ? 'catalog-sort__option--active' : '' ?>" data-sort="<?= e($value) ?>">
                                    <span><?= e($label) ?></span>
                                    <?php if ($active): ?><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg><?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($hasActiveFilters): ?>
                        <a href="<?= e(route_url('catalog_list')) ?>" class="catalog-filters__reset">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                            <span>Сбросить</span>
                        </a>
                    <?php endif; ?>
                </div>

                <form method="get" action="<?= e(route_url('catalog_list')) ?>" class="catalog-filters__form <?= $hasActiveFilters ? 'filter-form--expanded' : '' ?>" id="filterForm">
                    <input type="hidden" name="sort" value="<?= e((string) $selectedSort) ?>">
                    <div class="catalog-filters__grid">
                        <div class="filter-field">
                            <label class="filter-field__label">Авто из</label>
                            <select name="origin" class="filter-field__select" id="originSelect">
                                <option value="">Все страны</option>
                                <option value="CN" <?= ($filters['origin'] ?? '') === 'CN' ? 'selected' : '' ?>>🇨🇳 Китай</option>
                                <option value="JP" <?= ($filters['origin'] ?? '') === 'JP' ? 'selected' : '' ?>>🇯🇵 Япония</option>
                                <option value="KR" <?= ($filters['origin'] ?? '') === 'KR' ? 'selected' : '' ?>>🇰🇷 Корея</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-field__label">Марка</label>
                            <select name="manufacturer" class="filter-field__select" id="manufacturerSelect">
                                <option value="">Все марки</option>
                                <?php foreach ($manufacturers as $m): ?>
                                    <option value="<?= e($m) ?>" <?= ($filters['manufacturer'] ?? '') === $m ? 'selected' : '' ?>><?= e($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-field__label">Модель</label>
                            <select name="model" class="filter-field__select" id="modelSelect">
                                <option value="">Все модели</option>
                                <?php foreach ($models as $model): ?>
                                    <option value="<?= e($model) ?>" <?= ($filters['model'] ?? '') === $model ? 'selected' : '' ?>><?= e($model) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-field__label">Двигатель</label>
                            <select name="fuel" class="filter-field__select" id="fuelSelect">
                                <option value="">Все типы</option>
                                <?php foreach ($fuels as $f): ?>
                                    <option value="<?= e($f) ?>" <?= ($filters['fuel'] ?? '') === $f ? 'selected' : '' ?>><?= e($f) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-field__label">Кузов</label>
                            <select name="body_type" class="filter-field__select" id="bodyTypeSelect">
                                <option value="">Все кузова</option>
                                <?php foreach ($bodyTypes as $bt): ?>
                                    <option value="<?= e($bt) ?>" <?= ($filters['body_type'] ?? '') === $bt ? 'selected' : '' ?>><?= e($bt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-field__label">Привод</label>
                            <select name="drive" class="filter-field__select" id="driveSelect">
                                <option value="">Любой</option>
                                <?php foreach ($drives as $d): ?>
                                    <option value="<?= e($d) ?>" <?= ($filters['drive'] ?? '') === $d ? 'selected' : '' ?>><?= e($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-field filter-field--range">
                            <label class="filter-field__label">Пробег, км</label>
                            <div class="filter-field__range">
                                <input type="number" name="mileage_from" class="filter-field__input" value="<?= e((string) ($filters['mileage_from'] ?? '')) ?>" placeholder="от" min="0">
                                <span class="filter-field__separator">—</span>
                                <input type="number" name="mileage_to" class="filter-field__input" value="<?= e((string) ($filters['mileage_to'] ?? '')) ?>" placeholder="до" min="0">
                            </div>
                        </div>

                        <div class="filter-field filter-field--range">
                            <label class="filter-field__label">Цена, ₽</label>
                            <div class="filter-field__range">
                                <input type="number" name="price_from" class="filter-field__input" value="<?= e((string) ($filters['price_from'] ?? '')) ?>" placeholder="от" min="0" step="100000">
                                <span class="filter-field__separator">—</span>
                                <input type="number" name="price_to" class="filter-field__input" value="<?= e((string) ($filters['price_to'] ?? '')) ?>" placeholder="до" min="0" step="100000">
                            </div>
                        </div>

                        <div class="filter-field filter-field--range">
                            <label class="filter-field__label">Объём, л</label>
                            <div class="filter-field__range">
                                <input type="number" name="engine_volume_from" class="filter-field__input" value="<?= e((string) ($filters['engine_volume_from'] ?? '')) ?>" placeholder="от" min="0" step="0.1">
                                <span class="filter-field__separator">—</span>
                                <input type="number" name="engine_volume_to" class="filter-field__input" value="<?= e((string) ($filters['engine_volume_to'] ?? '')) ?>" placeholder="до" min="0" step="0.1">
                            </div>
                        </div>

                        <div class="filter-field filter-field--range">
                            <label class="filter-field__label">Год выпуска</label>
                            <div class="filter-field__range">
                                <select name="year_from" class="filter-field__select" id="yearFromSelect">
                                    <option value="">от</option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= e((string) $year) ?>" <?= ($filters['year_from'] ?? '') === (string) $year ? 'selected' : '' ?>><?= e((string) $year) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="filter-field__separator">—</span>
                                <select name="year_to" class="filter-field__select" id="yearToSelect">
                                    <option value="">до</option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= e((string) $year) ?>" <?= ($filters['year_to'] ?? '') === (string) $year ? 'selected' : '' ?>><?= e((string) $year) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="catalog-filters__actions">
                        <button type="submit" class="catalog-filters__submit">Найти автомобили</button>
                    </div>
                </form>

                <div class="catalog-stats">
                    <span class="catalog-stats__count">Найдено: <strong><?= e((string) ($pagination['total'] ?? 0)) ?></strong></span>
                </div>
            </div>

            <div class="popular__cards">
                <?php if (($cars ?? []) !== []): ?>
                    <?php foreach (($cars ?? []) as $index => $car): ?>
                        <article class="car-card">
                            <div class="car-card__img-wrap">
                                <img src="<?= e($car['image_url'] ?? asset('images/placeholder-car.jpg')) ?>" alt="<?= e((string) ($car['name'] ?? 'Автомобиль')) ?>" class="car-card__img" loading="lazy">
                            </div>
                            <div class="car-card__body">
                                <p class="car-card__pub">Номер публикации #<?= e((string) (1001 + $index + (($currentPage - 1) * 12))) ?></p>
                                <h3 class="car-card__title"><?= e((string) $car['manufacturer']) ?> <?= e((string) $car['model']) ?></h3>

                                <div class="car-card__row">
                                    <div class="car-card__col">
                                        <span class="car-card__label">Кузов</span>
                                        <span class="car-card__value"><?= e(value_or_dash((string) ($car['body_type'] ?? ''))) ?></span>
                                    </div>
                                    <div class="car-card__col">
                                        <span class="car-card__label">Год выпуска</span>
                                        <span class="car-card__value"><?= e((string) $car['year']) ?></span>
                                    </div>
                                </div>

                                <div class="car-card__row">
                                    <div class="car-card__col">
                                        <span class="car-card__label">Пробег</span>
                                        <span class="car-card__value"><?= e(format_intcomma((int) $car['mileage_km'])) ?> км</span>
                                    </div>
                                    <div class="car-card__col">
                                        <span class="car-card__label">Объем двигателя</span>
                                        <span class="car-card__value"><?= e(value_or_dash((string) ($car['engine_volume'] ?? ''))) ?></span>
                                    </div>
                                </div>

                                <div class="car-card__divider"></div>

                                <div class="car-card__price-row">
                                    <div>
                                        <span class="car-card__label">Стоимость:</span>
                                        <p class="car-card__price"><?= e(format_price((float) $car['price'])) ?></p>
                                    </div>

                                    <a class="btn car-card__btn" href="<?= e(route_url('catalog_detail', ['id' => (int) $car['id']])) ?>">Подробнее</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="catalog-empty">
                        <div class="catalog-empty__icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </div>
                        <h3 class="catalog-empty__title">Ничего не найдено</h3>
                        <p class="catalog-empty__text">Попробуйте изменить параметры поиска или сбросить фильтры</p>
                        <a href="<?= e(route_url('catalog_list')) ?>" class="catalog-empty__btn">Сбросить фильтры</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="catalog-pagination" aria-label="Пагинация каталога">
                    <?php if (!empty($pagination['has_previous'])): ?>
                        <a href="<?= e(build_list_url(array_merge($filters, ['page' => (string) $pagination['previous_page_number']]))) ?>" class="catalog-pagination__btn catalog-pagination__btn--prev">
                            <span>Назад</span>
                        </a>
                    <?php endif; ?>

                    <div class="catalog-pagination__pages">
                        <?php
                        for ($num = 1; $num <= $totalPages; $num++):
                            $showPage = $num === 1 || $num === $totalPages || ($num >= $currentPage - 2 && $num <= $currentPage + 2);
                            if (!$showPage) {
                                if ($num === $currentPage - 3 || $num === $currentPage + 3) {
                                    echo '<span class="catalog-pagination__dots">...</span>';
                                }
                                continue;
                            }
                        ?>
                            <?php if ($num === $currentPage): ?>
                                <span class="catalog-pagination__page catalog-pagination__page--active"><?= e((string) $num) ?></span>
                            <?php else: ?>
                                <a href="<?= e(build_list_url(array_merge($filters, ['page' => (string) $num]))) ?>" class="catalog-pagination__page"><?= e((string) $num) ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <?php if (!empty($pagination['has_next'])): ?>
                        <a href="<?= e(build_list_url(array_merge($filters, ['page' => (string) $pagination['next_page_number']]))) ?>" class="catalog-pagination__btn catalog-pagination__btn--next">
                            <span>Вперёд</span>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</section>
<style>
/* ================== CATALOG SECTION ================== */
.catalog-section {
    padding: 40px 0 80px;
    min-height: 100vh;
}

/* Header */
.catalog-header {
    text-align: center;
    margin-bottom: 48px;
    padding-top: 40px;
}

.catalog-header__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(236, 29, 37, 0.12);
    border: 1px solid rgba(236, 29, 37, 0.3);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #ec1d25;
    margin-bottom: 20px;
}

.catalog-header__badge-dot {
    width: 6px;
    height: 6px;
    background: #ec1d25;
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.catalog-header__title {
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 800;
    margin: 0 0 12px;
    background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.7) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.catalog-header__subtitle {
    font-size: 16px;
    color: rgba(255,255,255,0.6);
    margin: 0;
}

/* Filters */
.catalog-filters {
    position: relative;
    z-index: 50;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 32px;
    backdrop-filter: blur(20px);
}

.catalog-filters__header {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 0;
}

.catalog-filters__toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
}

.catalog-filters__toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(236, 29, 37, 0.4);
}

.catalog-filters__toggle[aria-expanded="true"] {
    background: rgba(236, 29, 37, 0.15);
    border-color: rgba(236, 29, 37, 0.4);
}

.catalog-filters__toggle[aria-expanded="true"] .catalog-filters__toggle-arrow {
    transform: rotate(180deg);
}

.catalog-filters__toggle-arrow {
    transition: transform 0.3s ease;
}

.catalog-filters__reset {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(236, 29, 37, 0.1);
    border: 1px solid rgba(236, 29, 37, 0.3);
    border-radius: 12px;
    color: #ec1d25;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.25s ease;
    margin-left: auto;
}

.catalog-filters__reset:hover {
    background: rgba(236, 29, 37, 0.2);
}

/* Sort */
.catalog-sort {
    position: relative;
}

.catalog-sort__btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
}

.catalog-sort__btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

.catalog-sort__arrow {
    transition: transform 0.3s ease;
}

.catalog-sort__dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 220px;
    background: rgba(20, 20, 22, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    padding: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.25s ease;
    z-index: 1000;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.catalog-sort__dropdown.catalog-sort__dropdown--open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.catalog-sort__option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 16px;
    background: transparent;
    border: none;
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.catalog-sort__option:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
}

.catalog-sort__option--active {
    background: rgba(236, 29, 37, 0.15);
    color: #ec1d25;
}

.catalog-sort__option--active:hover {
    background: rgba(236, 29, 37, 0.2);
    color: #ec1d25;
}

/* Filter Form */
.catalog-filters__form {
    display: none;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.catalog-filters__form.filter-form--expanded {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.catalog-filters__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

/* Filter Field */
.filter-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-field__label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-field__label svg {
    opacity: 0.6;
}

.filter-field__select,
.filter-field__input {
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.25s ease;
    width: 100%;
}

.filter-field__select:hover,
.filter-field__input:hover {
    border-color: rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.08);
}

.filter-field__select:focus,
.filter-field__input:focus {
    outline: none;
    border-color: rgba(236, 29, 37, 0.5);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(236, 29, 37, 0.15);
}

.filter-field__select option {
    background: #1a1a1c;
    color: #fff;
}

.filter-field--range .filter-field__range {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-field--range .filter-field__input,
.filter-field--range .filter-field__select {
    flex: 1;
    min-width: 0;
}

.filter-field__separator {
    color: rgba(255, 255, 255, 0.3);
    font-weight: 500;
}

/* Filter Actions */
.catalog-filters__actions {
    display: flex;
    justify-content: center;
}

.catalog-filters__submit {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: linear-gradient(135deg, #ec1d25 0%, #d11920 100%);
    border: none;
    border-radius: 14px;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(236, 29, 37, 0.4);
}

.catalog-filters__submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(236, 29, 37, 0.5);
}

/* Stats */
.catalog-stats {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.catalog-stats__count,
.catalog-stats__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.5);
}

.catalog-stats__count strong {
    color: #fff;
    font-weight: 700;
}

.catalog-stats__badge--sample {
    color: #f59e0b;
}

/* Empty State */
.catalog-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
}

.catalog-empty__icon {
    margin-bottom: 24px;
    color: rgba(255, 255, 255, 0.2);
}

.catalog-empty__title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 12px;
    color: #fff;
}

.catalog-empty__text {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.5);
    margin: 0 0 24px;
}

.catalog-empty__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 24px;
    background: rgba(236, 29, 37, 0.15);
    border: 1px solid rgba(236, 29, 37, 0.3);
    border-radius: 12px;
    color: #ec1d25;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.25s ease;
}

.catalog-empty__btn:hover {
    background: #ec1d25;
    border-color: #ec1d25;
    color: #fff;
}

/* ================== PAGINATION ================== */
.catalog-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 48px;
    padding: 24px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
}

.catalog-pagination__btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.25s ease;
}

.catalog-pagination__btn:hover {
    background: rgba(236, 29, 37, 0.15);
    border-color: rgba(236, 29, 37, 0.4);
    color: #ec1d25;
}

.catalog-pagination__pages {
    display: flex;
    align-items: center;
    gap: 8px;
}

.catalog-pagination__page {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 600;
    transition: all 0.25s ease;
}

.catalog-pagination__page:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.catalog-pagination__page--active {
    background: #ec1d25;
    border-color: #ec1d25;
    color: #fff;
}

.catalog-pagination__dots {
    color: rgba(255, 255, 255, 0.3);
    padding: 0 8px;
}

/* ================== RESPONSIVE ================== */
@media (max-width: 768px) {
    .catalog-section {
        padding: 24px 0 60px;
    }
    
    .catalog-header {
        margin-bottom: 32px;
    }
    
    .catalog-filters {
        padding: 16px;
        border-radius: 16px;
    }
    
    .catalog-filters__header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .catalog-filters__toggle {
        justify-content: center;
    }
    
    .catalog-filters__reset {
        margin-left: 0;
        justify-content: center;
    }
    
    .catalog-sort {
        width: 100%;
    }
    
    .catalog-sort__btn {
        width: 100%;
        justify-content: center;
    }
    
    .catalog-sort__dropdown {
        right: 0;
        left: 0;
    }
    
    .catalog-filters__grid {
        grid-template-columns: 1fr;
    }
    
    .catalog-pagination {
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .catalog-pagination__btn {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .catalog-pagination__page {
        width: 36px;
        height: 36px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const sortToggleBtn = document.getElementById('sortToggleBtn');
    const sortDropdownMenu = document.getElementById('sortDropdownMenu');

    // Toggle filters
    if (filterToggleBtn && filterForm) {
        filterToggleBtn.addEventListener('click', function() {
            const isExpanded = filterForm.classList.toggle('filter-form--expanded');
            filterToggleBtn.setAttribute('aria-expanded', isExpanded);
        });
    }

    // Sort dropdown
    if (sortToggleBtn && sortDropdownMenu) {
        sortToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sortDropdownMenu.classList.toggle('catalog-sort__dropdown--open');
        });

        sortDropdownMenu.addEventListener('click', function(e) {
            const option = e.target.closest('.catalog-sort__option');
            if (option) {
                e.preventDefault();
                const sortValue = option.dataset.sort;
                const url = new URL(window.location);
                
                if (sortValue) {
                    url.searchParams.set('sort', sortValue);
                } else {
                    url.searchParams.delete('sort');
                }
                
                window.location.href = url.toString();
            }
        });

        document.addEventListener('click', function(e) {
            if (!sortToggleBtn.contains(e.target) && !sortDropdownMenu.contains(e.target)) {
                sortDropdownMenu.classList.remove('catalog-sort__dropdown--open');
            }
        });
    }

    // Form submission
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();

            formData.forEach((value, key) => {
                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            });

            const newUrl = '<?= e(route_url('catalog_list')) ?>' + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        });
    }
});
</script>
