<?php

declare(strict_types=1);

$carsBaseUrl = panel_url('cars');
$exportUrl = panel_url('cars_export');
$searchQuery = build_query([
    'q' => $search,
    'origin' => $selected_origin,
    'availability' => $selected_availability,
    'is_active' => $selected_active,
]);
?>
<div class="toolbar">
    <form method="get" class="toolbar__search">
        <svg class="toolbar__search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="<?= e((string) $search) ?>" placeholder="Поиск по марке, модели..." class="toolbar__search-input">
        <?php if ($selected_origin !== ''): ?><input type="hidden" name="origin" value="<?= e((string) $selected_origin) ?>"><?php endif; ?>
        <?php if ($selected_availability !== ''): ?><input type="hidden" name="availability" value="<?= e((string) $selected_availability) ?>"><?php endif; ?>
        <?php if ($selected_active !== ''): ?><input type="hidden" name="is_active" value="<?= e((string) $selected_active) ?>"><?php endif; ?>
        <?php if ($selected_sort !== ''): ?><input type="hidden" name="sort" value="<?= e((string) $selected_sort) ?>"><?php endif; ?>
    </form>

    <div class="toolbar__filters">
        <div class="filter-group">
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search])) ?>" class="filter-chip <?= $selected_origin === '' ? 'filter-chip--active' : '' ?>">Все</a>
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => 'JP', 'availability' => $selected_availability, 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_origin === 'JP' ? 'filter-chip--active' : '' ?>">🇯🇵 Япония</a>
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => 'KR', 'availability' => $selected_availability, 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_origin === 'KR' ? 'filter-chip--active' : '' ?>">🇰🇷 Корея</a>
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => 'CN', 'availability' => $selected_availability, 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_origin === 'CN' ? 'filter-chip--active' : '' ?>">🇨🇳 Китай</a>
        </div>

        <div class="filter-group">
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => 'in_stock', 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_availability === 'in_stock' ? 'filter-chip--active' : '' ?>">В наличии</a>
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => 'on_order', 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_availability === 'on_order' ? 'filter-chip--active' : '' ?>">Под заказ</a>
            <a href="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => 'sold', 'sort' => $selected_sort])) ?>" class="filter-chip <?= $selected_availability === 'sold' ? 'filter-chip--active' : '' ?>">Продано</a>
        </div>

        <select class="sort-select" onchange="location.href=this.value">
            <option value="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => $selected_availability, 'sort' => '-created'])) ?>" <?= $selected_sort === '-created' ? 'selected' : '' ?>>Сначала новые</option>
            <option value="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => $selected_availability, 'sort' => '-price'])) ?>" <?= $selected_sort === '-price' ? 'selected' : '' ?>>Цена ↓</option>
            <option value="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => $selected_availability, 'sort' => 'price'])) ?>" <?= $selected_sort === 'price' ? 'selected' : '' ?>>Цена ↑</option>
            <option value="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => $selected_availability, 'sort' => '-views'])) ?>" <?= $selected_sort === '-views' ? 'selected' : '' ?>>По просмотрам</option>
            <option value="<?= e($carsBaseUrl . '?' . build_query(['q' => $search, 'origin' => $selected_origin, 'availability' => $selected_availability, 'sort' => '-year'])) ?>" <?= $selected_sort === '-year' ? 'selected' : '' ?>>Год ↓</option>
        </select>
    </div>
</div>

<div class="bulk-bar" id="bulkBar">
    <div class="bulk-bar__info"><span class="bulk-bar__count" id="bulkCount">0</span> выбрано</div>
    <div class="bulk-bar__actions">
        <button class="btn btn--sm btn--ghost" type="button" onclick="bulkAction('activate')">Активировать</button>
        <button class="btn btn--sm btn--ghost" type="button" onclick="bulkAction('deactivate')">Деактивировать</button>
        <button class="btn btn--sm btn--ghost" type="button" onclick="bulkAction('set_in_stock')">В наличии</button>
        <button class="btn btn--sm btn--ghost" type="button" onclick="bulkAction('set_on_order')">Под заказ</button>
        <button class="btn btn--sm btn--ghost" type="button" onclick="bulkAction('set_sold')">Продано</button>
        <button class="btn btn--sm btn--danger" type="button" onclick="bulkAction('delete')">Удалить</button>
    </div>
    <button class="bulk-bar__close" type="button" onclick="clearSelection()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>

<div class="card">
    <div class="card__body card__body--flush">
        <table class="dtable dtable--cars">
            <thead>
                <tr>
                    <th style="width:36px"><input type="checkbox" class="bulk-check-all" id="checkAll" onchange="toggleAll(this)"></th>
                    <th style="width:60px"></th>
                    <th>Автомобиль</th>
                    <th>Год</th>
                    <th>Цена</th>
                    <th>Страна</th>
                    <th>Статус</th>
                    <th>Просм.</th>
                    <th>Фото</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($cars !== []): ?>
                <?php foreach ($cars as $car): ?>
                    <tr class="<?= !(bool) $car['is_active'] ? 'dtable__row--inactive' : '' ?>" data-car-id="<?= e((string) $car['id']) ?>">
                        <td><input type="checkbox" class="bulk-check" value="<?= e((string) $car['id']) ?>" onchange="updateBulk()"></td>
                        <td>
                            <div class="dtable__thumb">
                                <?php if (!empty($car['image_url'])): ?>
                                    <img src="<?= e((string) $car['image_url']) ?>" alt="">
                                <?php else: ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><a href="<?= e(panel_url('car_edit', ['id' => $car['id']])) ?>" class="dtable__car-name"><?= e((string) $car['manufacturer']) ?> <?= e((string) $car['model']) ?></a></td>
                        <td><?= e((string) $car['year']) ?></td>
                        <td class="dtable__price"><?= e(format_intcomma($car['price'])) ?> ₽</td>
                        <td><span class="origin-tag origin-tag--<?= e((string) $car['origin']) ?>"><?= e((string) $car['origin_label']) ?></span></td>
                        <td><span class="avail-tag avail-tag--<?= e((string) $car['availability']) ?>"><?= e((string) $car['availability_label']) ?></span></td>
                        <td class="dtable__num"><?= e((string) $car['views_count']) ?></td>
                        <td class="dtable__num"><?= e((string) $car['images_count']) ?></td>
                        <td>
                            <div class="dtable__actions">
                                <a href="<?= e(panel_url('car_edit', ['id' => $car['id']])) ?>" class="icon-btn" title="Редактировать">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <button class="icon-btn" type="button" title="Дублировать" onclick="duplicateCar(<?= e((string) $car['id']) ?>)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                                <button class="icon-btn icon-btn--danger" type="button" title="Удалить" onclick="deleteCar(<?= e((string) $car['id']) ?>, <?= json_encode(trim((string) $car['manufacturer'] . ' ' . (string) $car['model']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="dtable__empty">
                        <div class="dtable__empty-content">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0H9m-6-6h15m-6 0V6"/></svg>
                            <p>Автомобили не найдены</p>
                            <a href="<?= e(panel_url('car_add')) ?>" class="btn btn--primary btn--sm">Добавить первый</a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="deleteModal">
    <div class="modal__overlay" onclick="closeDeleteModal()"></div>
    <div class="modal__box">
        <h3 class="modal__title">Удалить автомобиль?</h3>
        <p class="modal__text">Вы уверены, что хотите удалить <strong id="deleteCarName"></strong>? Это действие нельзя отменить.</p>
        <div class="modal__actions">
            <button class="btn btn--ghost" type="button" onclick="closeDeleteModal()">Отмена</button>
            <form id="deleteForm" method="post" style="display:inline">
                <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">
                <button type="submit" class="btn btn--danger">Удалить</button>
            </form>
        </div>
    </div>
</div>

<script>
const panelCarsConfig = {
    csrf: window.AKIBA_PANEL_CSRF,
    bulkUrl: <?= json_encode(panel_url('cars_bulk'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    duplicateBase: <?= json_encode(panel_url('car_duplicate', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    deleteBase: <?= json_encode(panel_url('car_delete', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    editBase: <?= json_encode(panel_url('car_edit', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};

function carActionUrl(base, id) {
    return base.replace('ID', String(id));
}

function deleteCar(id, name) {
    document.getElementById('deleteCarName').textContent = name;
    document.getElementById('deleteForm').action = carActionUrl(panelCarsConfig.deleteBase, id);
    document.getElementById('deleteModal').classList.add('modal--open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal--open');
}

function duplicateCar(id) {
    if (!confirm('Создать копию этого автомобиля?')) return;
    fetch(carActionUrl(panelCarsConfig.duplicateBase, id), {
        method: 'POST',
        headers: { 'X-CSRF-Token': panelCarsConfig.csrf, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(data => {
        if (data.ok && data.new_id) {
            window.location.href = carActionUrl(panelCarsConfig.editBase, data.new_id);
        }
    });
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.bulk-check:checked')).map(c => c.value);
}

function updateBulk() {
    const ids = getSelectedIds();
    const bar = document.getElementById('bulkBar');
    const count = document.getElementById('bulkCount');
    if (ids.length > 0) {
        bar.classList.add('bulk-bar--visible');
        count.textContent = ids.length;
    } else {
        bar.classList.remove('bulk-bar--visible');
    }
    const all = document.querySelectorAll('.bulk-check');
    const checked = document.querySelectorAll('.bulk-check:checked');
    const checkAll = document.getElementById('checkAll');
    checkAll.checked = all.length > 0 && all.length === checked.length;
    checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
}

function toggleAll(el) {
    document.querySelectorAll('.bulk-check').forEach(c => { c.checked = el.checked; });
    updateBulk();
}

function clearSelection() {
    document.querySelectorAll('.bulk-check').forEach(c => { c.checked = false; });
    const checkAll = document.getElementById('checkAll');
    checkAll.checked = false;
    checkAll.indeterminate = false;
    document.getElementById('bulkBar').classList.remove('bulk-bar--visible');
}

function bulkAction(action) {
    const ids = getSelectedIds();
    if (!ids.length) return;

    let msg = 'Применить действие к ' + ids.length + ' авто?';
    if (action === 'delete') {
        msg = 'Удалить ' + ids.length + ' автомобилей? Это нельзя отменить!';
    }
    if (!confirm(msg)) return;

    const body = new FormData();
    body.append('ids', ids.join(','));
    body.append('action', action);
    body.append('_token', panelCarsConfig.csrf);

    fetch(panelCarsConfig.bulkUrl, {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            location.reload();
        } else if (data.error) {
            alert(data.error);
        }
    });
}
</script>
