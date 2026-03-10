<?php

declare(strict_types=1);

return [
    // Получатели уведомлений о новых заявках.
    'request_to' => [
        'akibaauto@gmail.com',
    ],
    // Если адрес не задан, приложение соберет no-reply@<текущий_домен>.
    'from_email' => '',
    'from_name' => 'AkibaAuto',
    'subject_prefix' => 'AkibaAuto',
    'log_file' => __DIR__ . '/../storage/mail.log',
];
