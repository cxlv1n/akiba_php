<?php

declare(strict_types=1);

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
date_default_timezone_set('Asia/Vladivostok');

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/mail.php';

/**
 * @param array<string,mixed> $data
 */
function render(string $templatePath, array $data = []): void
{
    $templateFile = __DIR__ . '/../templates/' . ltrim($templatePath, '/');
    if (!is_file($templateFile)) {
        http_response_code(500);
        echo 'Template not found';
        return;
    }

    extract($data, EXTR_SKIP);

    ob_start();
    include $templateFile;
    $content = (string) ob_get_clean();

    include __DIR__ . '/../templates/layout.php';
}

/**
 * @param array<string,mixed> $payload
 */
function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function not_found(): void
{
    http_response_code(404);
    render('pages/404.php', [
        'title' => 'Страница не найдена',
        'active_page' => '',
    ]);
}
