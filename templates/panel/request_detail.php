<?php

declare(strict_types=1);
?>
<form method="post" class="car-form">
    <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">Данные клиента</h2>
                    <span class="req-status-badge req-status-badge--<?= e((string) $request['status']) ?>"><?= e((string) $request['status_label']) ?></span>
                </div>
                <div class="card__body">
                    <div class="req-detail-grid">
                        <div class="req-detail-item"><div class="req-detail-item__label">Имя</div><div class="req-detail-item__value"><?= e((string) $request['name']) ?></div></div>
                        <div class="req-detail-item"><div class="req-detail-item__label">Телефон</div><div class="req-detail-item__value"><a href="tel:<?= e((string) $request['phone']) ?>" class="req-detail-item__phone"><?= e((string) $request['phone']) ?></a></div></div>
                        <div class="req-detail-item"><div class="req-detail-item__label">Бюджет</div><div class="req-detail-item__value"><?= e(value_or_dash((string) $request['budget'])) ?></div></div>
                        <div class="req-detail-item"><div class="req-detail-item__label">Дата заявки</div><div class="req-detail-item__value"><?= e((string) $request['created_at_display']) ?></div></div>
                    </div>

                    <?php if (!empty($request['car'])): ?>
                        <div class="req-detail-source" style="margin-bottom:20px;">
                            <span class="req-detail-item__label">Автомобиль:</span>
                            <a href="<?= e(panel_url('car_edit', ['id' => $request['car']['id']])) ?>" class="req-detail-source__link"><?= e((string) $request['car']['manufacturer']) ?> <?= e((string) $request['car']['model']) ?> <?= e((string) $request['car']['year']) ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ((string) $request['wishes'] !== ''): ?>
                        <div class="req-detail-wishes">
                            <div class="req-detail-item__label">Пожелания</div>
                            <div class="req-detail-wishes__text"><?= nl2br(e((string) $request['wishes'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ((string) $request['source_page'] !== ''): ?>
                        <div class="req-detail-source">
                            <span class="req-detail-item__label">Страница:</span>
                            <a href="<?= e((string) $request['source_page']) ?>" target="_blank" class="req-detail-source__link"><?= e((string) $request['source_page']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title">Заметки менеджера</h2></div>
                <div class="card__body">
                    <div class="field">
                        <textarea name="admin_notes" rows="5" class="field__input" placeholder="Комментарии, детали звонка, договорённости..."><?= e((string) $request['admin_notes']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card card--sticky">
                <div class="card__header"><h2 class="card__title">Управление</h2></div>
                <div class="card__body">
                    <div class="field">
                        <label class="field__label" for="status">Статус</label>
                        <select name="status" id="status">
                            <?php foreach (panel_request_status_choices() as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= (string) $request['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-meta">
                        <div class="form-meta__item"><span class="form-meta__label">Создана</span><span class="form-meta__value"><?= e((string) $request['created_at_display']) ?></span></div>
                        <div class="form-meta__item"><span class="form-meta__label">Обновлена</span><span class="form-meta__value"><?= e((string) $request['updated_at_display']) ?></span></div>
                    </div>
                </div>
                <div class="card__footer">
                    <button type="submit" class="btn btn--primary btn--full">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</form>
