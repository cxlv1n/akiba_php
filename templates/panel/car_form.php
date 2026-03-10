<?php

declare(strict_types=1);

$values = $form_values ?? panel_default_car_values();
$carId = isset($car['id']) ? (int) $car['id'] : 0;
$carImages = $car['images'] ?? [];
$carNotes = $car['notes'] ?? [];
?>
<form method="post" enctype="multipart/form-data" class="car-form" id="carForm">
    <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <strong>Исправьте ошибки:</strong>
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= e((string) $field) ?>: <?= e((string) $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card__header"><h2 class="card__title">Основная информация</h2></div>
                <div class="card__body">
                    <div class="field-row field-row--2">
                        <div class="field">
                            <label class="field__label" for="manufacturer">Производитель *</label>
                            <input type="text" name="manufacturer" id="manufacturer" class="field__input" value="<?= e((string) $values['manufacturer']) ?>" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="model">Модель *</label>
                            <input type="text" name="model" id="model" class="field__input" value="<?= e((string) $values['model']) ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field__label" for="name">Полное название</label>
                        <input type="text" name="name" id="name" class="field__input" value="<?= e((string) $values['name']) ?>">
                    </div>
                    <div class="field-row field-row--3">
                        <div class="field">
                            <label class="field__label" for="year">Год выпуска *</label>
                            <input type="number" name="year" id="year" class="field__input" value="<?= e((string) $values['year']) ?>" min="1950" max="<?= e((string) (((int) date('Y')) + 1)) ?>" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="price">Цена, ₽ *</label>
                            <input type="text" name="price" id="price" class="field__input" value="<?= e((string) $values['price']) ?>" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="origin">Страна *</label>
                            <select name="origin" id="origin">
                                <?php foreach (panel_origin_choices() as $key => $label): ?>
                                    <option value="<?= e($key) ?>" <?= (string) $values['origin'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title">Характеристики</h2></div>
                <div class="card__body">
                    <div class="field-row field-row--3">
                        <div class="field">
                            <label class="field__label" for="mileage_km">Пробег, км *</label>
                            <input type="number" name="mileage_km" id="mileage_km" class="field__input" value="<?= e((string) $values['mileage_km']) ?>" min="0" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="engine_volume">Двигатель</label>
                            <input type="text" name="engine_volume" id="engine_volume" class="field__input" value="<?= e((string) $values['engine_volume']) ?>">
                        </div>
                        <div class="field">
                            <label class="field__label" for="fuel">Топливо</label>
                            <input type="text" name="fuel" id="fuel" class="field__input" value="<?= e((string) $values['fuel']) ?>">
                        </div>
                    </div>
                    <div class="field-row field-row--2">
                        <div class="field">
                            <label class="field__label" for="drive">Привод</label>
                            <input type="text" name="drive" id="drive" class="field__input" value="<?= e((string) $values['drive']) ?>">
                        </div>
                        <div class="field">
                            <label class="field__label" for="body_type">Тип кузова</label>
                            <input type="text" name="body_type" id="body_type" class="field__input" value="<?= e((string) $values['body_type']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title">Фотографии</h2></div>
                <div class="card__body">
                    <?php if (!$is_new && $carImages !== []): ?>
                        <div class="photo-grid" id="photoGrid">
                            <?php foreach ($carImages as $image): ?>
                                <div class="photo-card <?= !empty($image['is_main']) ? 'photo-card--main' : '' ?>" data-id="<?= e((string) $image['id']) ?>">
                                    <img src="<?= e((string) $image['url']) ?>" alt="<?= e((string) $image['alt_text']) ?>">
                                    <div class="photo-card__overlay">
                                        <?php if (empty($image['is_main'])): ?>
                                            <button type="button" class="photo-card__btn" onclick="setMainImage(<?= e((string) $image['id']) ?>)" title="Сделать основным">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="photo-card__btn photo-card__btn--del" onclick="deleteImage(<?= e((string) $image['id']) ?>)" title="Удалить">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                    <?php if (!empty($image['is_main'])): ?><span class="photo-card__badge">Основное</span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="dropzone" id="dropzone">
                        <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="dropzone__input">
                        <div class="dropzone__content">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="dropzone__text">Перетащите фото сюда или <span class="dropzone__link">выберите файлы</span></p>
                            <p class="dropzone__hint">JPG, PNG, WebP · до 10 МБ</p>
                        </div>
                        <div class="dropzone__preview" id="dropzonePreview"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title">Описание</h2></div>
                <div class="card__body">
                    <div class="field">
                        <textarea name="description" rows="6" class="field__input"><?= e((string) $values['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <?php if (!$is_new): ?>
                <div class="card">
                    <div class="card__header"><h2 class="card__title">Заметки</h2><span class="badge"><?= e((string) count($carNotes)) ?></span></div>
                    <div class="card__body">
                        <div class="notes-list" id="notesList">
                            <?php if ($carNotes !== []): ?>
                                <?php foreach ($carNotes as $note): ?>
                                    <div class="note-item" data-note-id="<?= e((string) $note['id']) ?>">
                                        <div class="note-item__header">
                                            <span class="note-item__author"><?= e($note['author'] !== '' ? (string) $note['author'] : '—') ?></span>
                                            <span class="note-item__date"><?= e((string) $note['created_at_display']) ?></span>
                                            <button type="button" class="note-item__delete" onclick="deleteNote(<?= e((string) $note['id']) ?>)" title="Удалить">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </div>
                                        <div class="note-item__text"><?= nl2br(e((string) $note['text'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="notes-empty" id="notesEmpty">Заметок пока нет</div>
                            <?php endif; ?>
                        </div>

                        <div class="note-form">
                            <textarea id="noteText" rows="2" class="field__input" placeholder="Добавить заметку..."></textarea>
                            <button type="button" class="btn btn--sm btn--primary" onclick="addNote()" style="margin-top:8px">Добавить заметку</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-sidebar">
            <div class="card card--sticky">
                <div class="card__header"><h2 class="card__title">Настройки</h2></div>
                <div class="card__body">
                    <div class="field">
                        <label class="field__label" for="availability">Статус</label>
                        <select name="availability" id="availability">
                            <?php foreach (panel_availability_choices() as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= (string) $values['availability'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label field__label--checkbox">
                            <input type="checkbox" name="is_active" value="1" <?= !empty($values['is_active']) ? 'checked' : '' ?>>
                            <span>Активно (видно на сайте)</span>
                        </label>
                    </div>
                    <div class="field">
                        <label class="field__label" for="alt_name">URL-имя</label>
                        <input type="text" name="alt_name" id="alt_name" class="field__input" value="<?= e((string) $values['alt_name']) ?>">
                    </div>

                    <?php if (!$is_new): ?>
                        <div class="form-meta">
                            <div class="form-meta__item"><span class="form-meta__label">Просмотры</span><span class="form-meta__value"><?= e(format_intcomma($values['views_count'])) ?></span></div>
                            <div class="form-meta__item"><span class="form-meta__label">Создано</span><span class="form-meta__value"><?= e(panel_format_datetime((string) $values['created_at'])) ?></span></div>
                            <div class="form-meta__item"><span class="form-meta__label">Изменено</span><span class="form-meta__value"><?= e(panel_format_datetime((string) $values['updated_at'])) ?></span></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card__footer">
                    <button type="submit" class="btn btn--primary btn--full"><?= $is_new ? 'Создать автомобиль' : 'Сохранить изменения' ?></button>
                    <?php if (!$is_new): ?>
                        <button type="button" class="btn btn--danger-ghost btn--full" onclick="deleteCar()">Удалить</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if (!$is_new): ?>
<div class="modal" id="deleteModal">
    <div class="modal__overlay" onclick="closeDeleteModal()"></div>
    <div class="modal__box">
        <h3 class="modal__title">Удалить автомобиль?</h3>
        <p class="modal__text">Все данные и фотографии будут удалены. Это действие нельзя отменить.</p>
        <div class="modal__actions">
            <button class="btn btn--ghost" type="button" onclick="closeDeleteModal()">Отмена</button>
            <form action="<?= e(panel_url('car_delete', ['id' => $carId])) ?>" method="post" style="display:inline">
                <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">
                <button type="submit" class="btn btn--danger">Удалить</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const panelCarForm = {
    isNew: <?= $is_new ? 'true' : 'false' ?>,
    carId: <?= json_encode($carId, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    csrf: window.AKIBA_PANEL_CSRF,
    duplicateUrl: <?= json_encode(panel_url('car_duplicate', ['id' => $carId > 0 ? $carId : 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    imageUploadUrl: <?= json_encode(panel_url('image_upload', ['id' => $carId > 0 ? $carId : 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    imageDeleteUrl: <?= json_encode(panel_url('image_delete', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    imageSetMainUrl: <?= json_encode(panel_url('image_set_main', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    noteAddUrl: <?= json_encode(panel_url('note_add', ['id' => $carId > 0 ? $carId : 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    noteDeleteUrl: <?= json_encode(panel_url('note_delete', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};

function panelReplaceId(url, id) {
    return url.replace('ID', String(id));
}

<?php if (!$is_new): ?>
function duplicateCar(id) {
    if (!confirm('Создать копию этого автомобиля?')) return;
    fetch(panelReplaceId(panelCarForm.duplicateUrl, id), {
        method: 'POST',
        headers: { 'X-CSRF-Token': panelCarForm.csrf, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(data => {
        if (data.ok && data.new_id) {
            window.location.href = <?= json_encode(panel_url('car_edit', ['id' => 'ID']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>.replace('ID', String(data.new_id));
        }
    });
}

function addNote() {
    const textarea = document.getElementById('noteText');
    const text = textarea.value.trim();
    if (!text) return;

    const body = new FormData();
    body.append('text', text);
    body.append('_token', panelCarForm.csrf);

    fetch(panelReplaceId(panelCarForm.noteAddUrl, panelCarForm.carId), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (!data.ok || !data.note) return;

        const list = document.getElementById('notesList');
        const empty = document.getElementById('notesEmpty');
        if (empty) empty.remove();

        const div = document.createElement('div');
        div.className = 'note-item';
        div.dataset.noteId = data.note.id;
        div.innerHTML = `
            <div class="note-item__header">
                <span class="note-item__author">${data.note.author || '—'}</span>
                <span class="note-item__date">${data.note.created_at}</span>
                <button type="button" class="note-item__delete" onclick="deleteNote(${data.note.id})" title="Удалить">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="note-item__text"></div>`;
        div.querySelector('.note-item__text').textContent = data.note.text;
        list.prepend(div);
        textarea.value = '';
    });
}

function deleteNote(id) {
    if (!confirm('Удалить заметку?')) return;
    const body = new FormData();
    body.append('_token', panelCarForm.csrf);

    fetch(panelReplaceId(panelCarForm.noteDeleteUrl, id), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            const el = document.querySelector('[data-note-id="' + id + '"]');
            if (el) el.remove();
        }
    });
}

function setMainImage(id) {
    const body = new FormData();
    body.append('_token', panelCarForm.csrf);
    fetch(panelReplaceId(panelCarForm.imageSetMainUrl, id), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) location.reload();
    });
}

function deleteImage(id) {
    if (!confirm('Удалить это фото?')) return;
    const body = new FormData();
    body.append('_token', panelCarForm.csrf);
    fetch(panelReplaceId(panelCarForm.imageDeleteUrl, id), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            const el = document.querySelector('[data-id="' + id + '"]');
            if (el) el.remove();
        }
    });
}

function deleteCar() {
    document.getElementById('deleteModal').classList.add('modal--open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal--open');
}
<?php else: ?>
function duplicateCar() {}
function addNote() {}
function deleteNote() {}
function setMainImage() {}
function deleteImage() {}
function deleteCar() {}
function closeDeleteModal() {}
<?php endif; ?>

const dropzone = document.getElementById('dropzone');
const input = document.getElementById('imageInput');
const preview = document.getElementById('dropzonePreview');

if (dropzone && input) {
    dropzone.addEventListener('click', e => {
        if (e.target.closest('.dropzone__preview')) return;
        input.click();
    });

    dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dropzone--over'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dropzone--over'));
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('dropzone--over');
        if (!e.dataTransfer.files.length) return;
        if (panelCarForm.isNew) {
            input.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files);
        } else {
            uploadFiles(e.dataTransfer.files);
        }
    });

    input.addEventListener('change', () => {
        if (!input.files.length) return;
        if (panelCarForm.isNew) {
            showPreview(input.files);
        } else {
            uploadFiles(input.files);
        }
    });
}

function showPreview(files) {
    if (!preview) return;
    preview.innerHTML = '';
    Array.from(files).forEach(file => {
        const url = URL.createObjectURL(file);
        const div = document.createElement('div');
        div.className = 'dropzone__thumb';
        div.innerHTML = '<img src="' + url + '">';
        preview.appendChild(div);
    });
}

function uploadFiles(files) {
    if (!files.length) return;
    const body = new FormData();
    Array.from(files).forEach(file => body.append('images[]', file));
    body.append('_token', panelCarForm.csrf);

    fetch(panelReplaceId(panelCarForm.imageUploadUrl, panelCarForm.carId), {
        method: 'POST',
        body
    }).then(r => r.json()).then(data => {
        if (data.ok) location.reload();
    });
}
</script>
