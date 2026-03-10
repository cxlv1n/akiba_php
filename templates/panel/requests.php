<?php

declare(strict_types=1);

$requestsBaseUrl = panel_url('requests');
?>
<div class="toolbar">
    <form method="get" class="toolbar__search">
        <svg class="toolbar__search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="<?= e((string) $search) ?>" placeholder="Поиск по имени, телефону, пожеланиям..." class="toolbar__search-input">
        <?php if ($selected_status !== ''): ?><input type="hidden" name="status" value="<?= e((string) $selected_status) ?>"><?php endif; ?>
    </form>

    <div class="toolbar__filters">
        <div class="filter-group">
            <a href="<?= e($requestsBaseUrl . '?' . build_query(['q' => $search])) ?>" class="filter-chip <?= $selected_status === '' ? 'filter-chip--active' : '' ?>">Все</a>
            <a href="<?= e($requestsBaseUrl . '?' . build_query(['q' => $search, 'status' => 'new'])) ?>" class="filter-chip <?= $selected_status === 'new' ? 'filter-chip--active' : '' ?>">🔴 Новые <?php if ($new_count > 0): ?><strong>(<?= e((string) $new_count) ?>)</strong><?php endif; ?></a>
            <a href="<?= e($requestsBaseUrl . '?' . build_query(['q' => $search, 'status' => 'processing'])) ?>" class="filter-chip <?= $selected_status === 'processing' ? 'filter-chip--active' : '' ?>">🟡 В работе</a>
            <a href="<?= e($requestsBaseUrl . '?' . build_query(['q' => $search, 'status' => 'completed'])) ?>" class="filter-chip <?= $selected_status === 'completed' ? 'filter-chip--active' : '' ?>">🟢 Завершены</a>
            <a href="<?= e($requestsBaseUrl . '?' . build_query(['q' => $search, 'status' => 'cancelled'])) ?>" class="filter-chip <?= $selected_status === 'cancelled' ? 'filter-chip--active' : '' ?>">⚫ Отменены</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card__body card__body--flush">
        <table class="dtable dtable--requests">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Бюджет</th>
                    <th>Пожелания</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($requests !== []): ?>
                <?php foreach ($requests as $request): ?>
                    <tr class="<?= $request['status'] === 'new' ? 'dtable__row--new' : '' ?>" data-req-id="<?= e((string) $request['id']) ?>">
                        <td class="dtable__rank"><?= e((string) $request['id']) ?></td>
                        <td><a href="<?= e(panel_url('request_detail', ['id' => $request['id']])) ?>" class="dtable__car-name"><?= e((string) $request['name']) ?></a></td>
                        <td><a href="tel:<?= e((string) $request['phone']) ?>" class="dtable__phone"><?= e((string) $request['phone']) ?></a></td>
                        <td><?= e(value_or_dash((string) $request['budget'])) ?></td>
                        <td class="dtable__wishes"><?= e(mb_strlen((string) $request['wishes'], 'UTF-8') > 60 ? mb_substr((string) $request['wishes'], 0, 57, 'UTF-8') . '...' : value_or_dash((string) $request['wishes'])) ?></td>
                        <td>
                            <select class="status-select status-select--<?= e((string) $request['status']) ?>" data-req-id="<?= e((string) $request['id']) ?>" onchange="changeRequestStatus(this)">
                                <?php foreach (panel_request_status_choices() as $key => $label): ?>
                                    <option value="<?= e($key) ?>" <?= (string) $request['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="dtable__date"><?= e(panel_format_datetime((string) $request['created_at'], 'd.m.Y')) ?><br><small><?= e(panel_format_datetime((string) $request['created_at'], 'H:i')) ?></small></td>
                        <td>
                            <a href="<?= e(panel_url('request_detail', ['id' => $request['id']])) ?>" class="icon-btn" title="Подробнее">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="dtable__empty">
                        <div class="dtable__empty-content">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <p>Заявок пока нет</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const panelRequestsConfig = {
    csrf: window.AKIBA_PANEL_CSRF,
    setStatusBase: <?= json_encode(panel_url('request_set_status', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};

function changeRequestStatus(el) {
    const reqId = el.dataset.reqId;
    const status = el.value;
    const body = new FormData();
    body.append('status', status);
    body.append('_token', panelRequestsConfig.csrf);

    fetch(panelRequestsConfig.setStatusBase.replace('ID', String(reqId)), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            el.className = 'status-select status-select--' + status;
            const row = el.closest('tr');
            row.classList.toggle('dtable__row--new', status === 'new');
        }
    });
}
</script>
