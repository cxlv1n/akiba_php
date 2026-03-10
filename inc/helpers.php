<?php

declare(strict_types=1);

function app_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $projectRoot = realpath(__DIR__ . '/..');
    $documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));

    if (is_string($projectRoot) && is_string($documentRoot)) {
        $normalizedProjectRoot = str_replace('\\', '/', $projectRoot);
        $normalizedDocumentRoot = str_replace('\\', '/', $documentRoot);

        if (starts_with($normalizedProjectRoot, $normalizedDocumentRoot)) {
            $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocumentRoot));
            if ($relativePath === false || $relativePath === '' || $relativePath === '/') {
                $basePath = '';
                return $basePath;
            }

            $basePath = rtrim($relativePath, '/');
            return $basePath;
        }
    }

    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $dirName = str_replace('\\', '/', dirname($scriptName));

    if ($dirName === '/' || $dirName === '.' || $dirName === '\\') {
        $basePath = '';
        return $basePath;
    }

    $basePath = rtrim($dirName, '/');

    return $basePath;
}

function app_url(string $path = '/'): string
{
    $basePath = app_base_path();
    $normalized = '/' . ltrim($path, '/');

    if ($normalized === '/') {
        return $basePath === '' ? '/' : $basePath . '/';
    }

    return ($basePath === '' ? '' : $basePath) . $normalized;
}

function current_scheme(): string
{
    $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($forwardedProto === 'https') {
        return 'https';
    }

    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    if ($https === 'on' || $https === '1') {
        return 'https';
    }

    $port = (string) ($_SERVER['SERVER_PORT'] ?? '');
    if ($port === '443') {
        return 'https';
    }

    return 'http';
}

function current_host(): string
{
    return trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
}

function base_url(): string
{
    $host = current_host();
    if ($host === '') {
        return '';
    }

    return current_scheme() . '://' . $host;
}

function absolute_url(string $pathOrUrl): string
{
    $value = trim($pathOrUrl);
    if ($value === '') {
        return '';
    }

    if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $value) === 1) {
        return $value;
    }

    $base = base_url();
    if ($base === '') {
        return $value;
    }

    if ($value[0] !== '/') {
        $value = '/' . $value;
    }

    return $base . $value;
}

function current_url(): string
{
    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? app_url('/'));
    return absolute_url($requestUri);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $path): string
{
    return app_url('/static/' . ltrim($path, '/'));
}

function media_url(string $path): string
{
    return app_url('/media/' . ltrim($path, '/'));
}

function route_url(string $name, array $params = []): string
{
    switch ($name) {
        case 'home':
            return app_url('/');
        case 'about':
            return app_url('/about/');
        case 'process':
            return app_url('/process/');
        case 'contacts':
            return app_url('/contacts/');
        case 'catalog_list':
            return app_url('/catalog/');
        case 'catalog_detail':
            if (!isset($params['id'])) {
                return app_url('/catalog/');
            }
            return app_url('/catalog/detail.php') . '?id=' . rawurlencode((string) $params['id']);
        case 'api_request':
            return app_url('/api/request/');
        default:
            return app_url('/');
    }
}

function format_intcomma($value): string
{
    if ($value === null || $value === '') {
        return '0';
    }

    return number_format((float) $value, 0, '.', ' ');
}

function format_price($value): string
{
    return format_intcomma($value) . ' ₽';
}

function value_or_dash($value): string
{
    $trimmed = trim((string) $value);
    return $trimmed === '' ? '—' : $trimmed;
}

function origin_label(string $origin): string
{
    switch ($origin) {
        case 'CN':
            return 'Китай';
        case 'JP':
            return 'Япония';
        case 'KR':
            return 'Корея';
        default:
            return $origin;
    }
}

function build_query(array $params): string
{
    $filtered = [];
    foreach ($params as $key => $value) {
        if ($value === null) {
            continue;
        }

        $str = trim((string) $value);
        if ($str === '') {
            continue;
        }

        $filtered[$key] = $str;
    }

    return http_build_query($filtered);
}

function build_list_url(array $params = []): string
{
    $base = route_url('catalog_list');
    $query = build_query($params);

    return $query === '' ? $base : $base . '?' . $query;
}

function year_now(): string
{
    return date('Y');
}

function request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);
    $basePath = app_base_path();

    if ($path === null || $path === '') {
        return '/';
    }

    if ($basePath !== '' && starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath));
    }

    if ($path === '' || $path === false) {
        return '/';
    }

    return $path;
}

function starts_with(string $haystack, string $needle): bool
{
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}

function first_letter(string $value): string
{
    $trimmed = trim($value);
    if ($trimmed === '') {
        return 'К';
    }

    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($trimmed, 0, 1, 'UTF-8'), 'UTF-8');
    }

    return strtoupper(substr($trimmed, 0, 1));
}
