# AkibaAuto PHP

Проект перенесен с Django на PHP с сохранением фронтенда из `akiba_cursor`.

## Что реализовано

- Публичные страницы: `/`, `/about/`, `/process/`, `/contacts/`
- Каталог: `/catalog/` (фильтры, сортировка, пагинация)
- Карточка авто: `/catalog/{id}/` (галерея, блок похожих авто)
- API формы заявки: `POST /api/request/`
- Публичный сайт теперь читает каталог из SQLite-базы `data/akiba.sqlite3`
- Заявки сохраняются в SQLite и дублируются в `storage/requests.jsonl`
- Email-уведомления о новых заявках по конфигу из `config/mail.php`
- Админ-панель: `/panel/`
  - вход
  - дашборд
  - список авто
  - создание/редактирование/удаление/дублирование авто
  - загрузка и управление изображениями
  - заметки к авто
  - список заявок и смена статусов

## Источник данных

- Основная база: `data/akiba.sqlite3` (копия `db.sqlite3` из Django-проекта).
- `data/db_dump.json` оставлен как резервный дамп.
- Изображения подключены из `media/`.
- Статические файлы подключены из `static/`.

## Локальный запуск

```bash
php -S 127.0.0.1:8080 -t /Users/ilyamamaev/Documents/Akiba/akiba_php
```

## Деплой на хостинг

1. Загрузить содержимое папки `/Users/ilyamamaev/Documents/Akiba/akiba_php` в корень сайта.
2. Выдать права на запись для папок:
   - `storage/`
   - `media/cars/gallery/`
3. Админка доступна по `/panel/`
4. Вход в админку использует пользователей из таблицы `auth_user` в `data/akiba.sqlite3`
   - текущий логин в базе: `admin`
   - нужен тот же пароль, который использовался в Django-проекте
4. Настроить почту:
   - основной конфиг: `/Users/ilyamamaev/Documents/Akiba/akiba_php/config/mail.php`
   - по умолчанию получатель уже стоит как на старом сайте: `akibaauto@gmail.com`
   - при необходимости можно переопределить через env:
     - `REQUEST_EMAIL_TO`
     - `DEFAULT_FROM_EMAIL`
     - `DEFAULT_FROM_NAME`
     - `REQUEST_EMAIL_SUBJECT_PREFIX`
     - `REQUEST_EMAIL_LOG_FILE`
5. Для диагностики результатов отправки смотреть `storage/mail.log`
