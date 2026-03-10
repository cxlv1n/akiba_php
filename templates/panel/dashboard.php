<?php

declare(strict_types=1);
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<div class="stats-grid stats-grid--5">
    <div class="stat-card stat-card--accent">
        <div class="stat-card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0H9m-6-6h15m-6 0V6"/></svg>
        </div>
        <div class="stat-card__value"><?= e((string) $total_cars) ?></div>
        <div class="stat-card__label">Автомобилей</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
        <div class="stat-card__value"><?= e(format_intcomma($total_views)) ?></div>
        <div class="stat-card__label">Всего просмотров</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="stat-card__value"><?= e((string) $views_today) ?></div>
        <div class="stat-card__label">Сегодня</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-card__value"><?= e((string) $views_7d) ?></div>
        <div class="stat-card__label">За 7 дней</div>
    </div>
    <a href="<?= e(panel_url('requests') . '?' . build_query(['status' => 'new'])) ?>" class="stat-card stat-card--requests <?= $new_requests_count > 0 ? 'stat-card--pulse' : '' ?>">
        <div class="stat-card__icon stat-card__icon--yellow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="stat-card__value"><?= e((string) $new_requests_count) ?></div>
        <div class="stat-card__label">Новых заявок</div>
    </a>
</div>

<div class="status-pills">
    <div class="status-pill status-pill--green"><span class="status-pill__dot"></span>В наличии: <strong><?= e((string) $cars_in_stock) ?></strong></div>
    <div class="status-pill status-pill--yellow"><span class="status-pill__dot"></span>Под заказ: <strong><?= e((string) $cars_on_order) ?></strong></div>
    <div class="status-pill status-pill--gray"><span class="status-pill__dot"></span>Продано: <strong><?= e((string) $cars_sold) ?></strong></div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Просмотры за 30 дней</h2>
            <span class="badge"><?= e(format_intcomma($views_30d)) ?></span>
        </div>
        <div class="card__body" style="height:300px;">
            <canvas id="viewsChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">По странам</h2>
        </div>
        <div class="card__body" style="height:200px;display:flex;align-items:center;justify-content:center;">
            <canvas id="originChart"></canvas>
        </div>
        <div class="origin-legend">
            <?php foreach ($origin_data as $index => $item): ?>
                <div class="origin-legend__item">
                    <span class="origin-legend__dot origin-legend__dot--<?= e((string) ($index + 1)) ?>"></span>
                    <span class="origin-legend__name"><?= e((string) $item['origin']) ?></span>
                    <span class="origin-legend__count"><?= e((string) $item['count']) ?> авто</span>
                    <span class="origin-legend__views"><?= e(format_intcomma($item['total_views'])) ?> просм.</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Топ просматриваемых</h2>
        </div>
        <div class="card__body card__body--flush">
            <table class="dtable">
                <thead>
                    <tr><th style="width:36px">#</th><th>Автомобиль</th><th>Страна</th><th>Просмотры</th></tr>
                </thead>
                <tbody>
                <?php if ($top_cars !== []): ?>
                    <?php foreach ($top_cars as $index => $car): ?>
                        <tr>
                            <td class="dtable__rank"><?= e((string) ($index + 1)) ?></td>
                            <td><a href="<?= e(panel_url('car_edit', ['id' => $car['id']])) ?>"><?= e((string) $car['manufacturer']) ?> <?= e((string) $car['model']) ?> <?= e((string) $car['year']) ?></a></td>
                            <td><span class="origin-tag origin-tag--<?= e((string) $car['origin']) ?>"><?= e((string) $car['origin_label']) ?></span></td>
                            <td class="dtable__num"><?= e(format_intcomma($car['views_count'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="dtable__empty">Пока нет данных</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Недавно добавленные</h2>
            <a href="<?= e(panel_url('car_add')) ?>" class="card__action">+ Добавить</a>
        </div>
        <div class="card__body card__body--flush">
            <?php if ($recent_cars !== []): ?>
                <?php foreach ($recent_cars as $car): ?>
                    <a href="<?= e(panel_url('car_edit', ['id' => $car['id']])) ?>" class="recent-car">
                        <div class="recent-car__img">
                            <?php if (!empty($car['image_url'])): ?>
                                <img src="<?= e((string) $car['image_url']) ?>" alt="">
                            <?php else: ?>
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="recent-car__info">
                            <div class="recent-car__name"><?= e((string) $car['manufacturer']) ?> <?= e((string) $car['model']) ?></div>
                            <div class="recent-car__meta"><?= e((string) $car['year']) ?> · <?= e((string) $car['origin_label']) ?> · <span class="recent-car__price"><?= e(format_intcomma($car['price'])) ?> ₽</span></div>
                        </div>
                        <span class="avail-tag avail-tag--<?= e((string) $car['availability']) ?>"><?= e((string) $car['availability_label']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card__empty">
                    <p>Автомобили не добавлены</p>
                    <a href="<?= e(panel_url('car_add')) ?>" class="btn btn--primary btn--sm">+ Добавить</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($recent_requests !== []): ?>
<div class="card">
    <div class="card__header">
        <h2 class="card__title">Последние заявки</h2>
        <a href="<?= e(panel_url('requests')) ?>" class="card__action">Все заявки →</a>
    </div>
    <div class="card__body card__body--flush">
        <table class="dtable">
            <thead>
                <tr>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Бюджет</th>
                    <th>Статус</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recent_requests as $request): ?>
                <tr class="<?= $request['status'] === 'new' ? 'dtable__row--new' : '' ?>">
                    <td><a href="<?= e(panel_url('request_detail', ['id' => $request['id']])) ?>"><?= e((string) $request['name']) ?></a></td>
                    <td><a href="tel:<?= e((string) $request['phone']) ?>" class="dtable__phone"><?= e((string) $request['phone']) ?></a></td>
                    <td><?= e(value_or_dash((string) $request['budget'])) ?></td>
                    <td><span class="req-status-badge req-status-badge--<?= e((string) $request['status']) ?>"><?= e((string) $request['status_label']) ?></span></td>
                    <td class="dtable__date"><?= e((string) $request['created_at_display']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!window.Chart) {
        return;
    }

    Chart.defaults.color = 'rgba(255,255,255,0.4)';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;

    const chartLabels = <?= json_encode($chart_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const chartData = <?= json_encode($chart_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const originLabels = <?= json_encode(array_column($origin_data, 'origin'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const originValues = <?= json_encode(array_map(static function ($item) { return (int) $item['count']; }, $origin_data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    const vCtx = document.getElementById('viewsChart');
    if (vCtx) {
        const g = vCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        g.addColorStop(0, 'rgba(236,29,37,0.25)');
        g.addColorStop(1, 'rgba(236,29,37,0)');
        new Chart(vCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    borderColor: '#ec1d25',
                    backgroundColor: g,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#ec1d25',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,.7)',
                        borderColor: 'rgba(255,255,255,.08)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 10,
                        displayColors: false,
                        callbacks: { label: i => i.raw + ' просмотров' }
                    }
                },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { maxTicksLimit: 8 } },
                    y: { grid: { color: 'rgba(255,255,255,.04)' }, beginAtZero: true }
                }
            }
        });
    }

    const oCtx = document.getElementById('originChart');
    if (oCtx && originLabels.length) {
        new Chart(oCtx, {
            type: 'doughnut',
            data: {
                labels: originLabels,
                datasets: [{
                    data: originValues,
                    backgroundColor: ['#ec1d25', '#3b82f6', '#8b5cf6', '#f59e0b', '#10b981'],
                    borderColor: '#161616',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false } } }
        });
    }
});
</script>
