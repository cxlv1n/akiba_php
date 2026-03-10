<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/panel_repository.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionDir = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0775, true);
    }
    session_save_path($sessionDir);
    session_start();
}

const PANEL_SESSION_USER_ID = 'akiba_panel_user_id';
const PANEL_CSRF_TOKEN_KEY = 'akiba_panel_csrf';

function panel_url(string $name, array $params = []): string
{
    switch ($name) {
        case 'login':
            return app_url('/panel/login/');
        case 'logout':
            return app_url('/panel/logout/');
        case 'dashboard':
            return app_url('/panel/');
        case 'cars':
            return app_url('/panel/cars/');
        case 'car_add':
            return app_url('/panel/cars/add.php');
        case 'car_edit':
            return app_url('/panel/cars/edit.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'car_delete':
            return app_url('/panel/cars/delete.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'car_duplicate':
            return app_url('/panel/cars/duplicate.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'cars_bulk':
            return app_url('/panel/cars/bulk.php');
        case 'cars_export':
            return app_url('/panel/cars/export.php');
        case 'car_toggle_active':
            return app_url('/panel/cars/toggle-active.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'car_set_status':
            return app_url('/panel/cars/set-status.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'image_upload':
            return app_url('/panel/cars/image-upload.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'image_delete':
            return app_url('/panel/images/delete.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'image_set_main':
            return app_url('/panel/images/set-main.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'note_add':
            return app_url('/panel/cars/note-add.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'note_delete':
            return app_url('/panel/notes/delete.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'requests':
            return app_url('/panel/requests/');
        case 'request_detail':
            return app_url('/panel/requests/detail.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'request_set_status':
            return app_url('/panel/requests/set-status.php') . '?id=' . rawurlencode((string) ($params['id'] ?? ''));
        case 'requests_export':
            return app_url('/panel/requests/export.php');
        default:
            return app_url('/panel/');
    }
}

function panel_redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function panel_current_user(): ?array
{
    static $user = null;
    static $resolved = false;

    if ($resolved) {
        return $user;
    }

    $resolved = true;
    $userId = isset($_SESSION[PANEL_SESSION_USER_ID]) ? (int) $_SESSION[PANEL_SESSION_USER_ID] : 0;
    if ($userId <= 0) {
        return null;
    }

    $user = panel_find_user_by_id($userId);
    if ($user === null || !(bool) ($user['is_staff'] ?? false) || !(bool) ($user['is_active'] ?? false)) {
        unset($_SESSION[PANEL_SESSION_USER_ID]);
        $user = null;
    }

    return $user;
}

function panel_require_auth(): array
{
    $user = panel_current_user();
    if ($user !== null) {
        return $user;
    }

    $next = current_url();
    panel_redirect(panel_url('login') . '?next=' . rawurlencode($next));
}

function panel_login_user(array $user): void
{
    $_SESSION[PANEL_SESSION_USER_ID] = (int) ($user['id'] ?? 0);
    panel_touch_user_login((int) ($user['id'] ?? 0));
}

function panel_logout_user(): void
{
    unset($_SESSION[PANEL_SESSION_USER_ID]);
}

function panel_csrf_token(): string
{
    if (empty($_SESSION[PANEL_CSRF_TOKEN_KEY]) || !is_string($_SESSION[PANEL_CSRF_TOKEN_KEY])) {
        $_SESSION[PANEL_CSRF_TOKEN_KEY] = bin2hex(random_bytes(24));
    }

    return $_SESSION[PANEL_CSRF_TOKEN_KEY];
}

function panel_verify_csrf(?string $token): bool
{
    $current = $_SESSION[PANEL_CSRF_TOKEN_KEY] ?? '';
    return is_string($token) && is_string($current) && $current !== '' && hash_equals($current, $token);
}

function panel_require_csrf(): void
{
    $token = (string) ($_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (panel_verify_csrf($token)) {
        return;
    }

    http_response_code(403);
    echo 'CSRF token mismatch';
    exit;
}

function panel_json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function panel_render(string $templatePath, array $data = []): void
{
    $templateFile = __DIR__ . '/../templates/panel/' . ltrim($templatePath, '/');
    if (!is_file($templateFile)) {
        http_response_code(500);
        echo 'Panel template not found';
        return;
    }

    $title = (string) ($data['title'] ?? 'Панель');
    $pageTitle = (string) ($data['page_title'] ?? $title);
    $navActive = (string) ($data['nav_active'] ?? '');
    $extraHead = (string) ($data['extra_head'] ?? '');
    $extraJs = (string) ($data['extra_js'] ?? '');
    $topbarActions = (string) ($data['topbar_actions'] ?? '');
    $currentUser = panel_current_user();
    $newRequestsCount = panel_new_requests_count();
    $csrfToken = panel_csrf_token();

    extract($data, EXTR_SKIP);

    ob_start();
    include $templateFile;
    $content = (string) ob_get_clean();

    include __DIR__ . '/../templates/panel/layout.php';
}

function panel_render_login(?string $error = null): void
{
    $templateFile = __DIR__ . '/../templates/panel/login.php';
    $csrfToken = panel_csrf_token();
    include $templateFile;
}

function panel_verify_django_password(string $password, string $encoded): bool
{
    $parts = explode('$', $encoded, 4);
    if (count($parts) !== 4) {
        return false;
    }

    list($algorithm, $iterationsRaw, $salt, $hash) = $parts;
    if ($algorithm !== 'pbkdf2_sha256' || !ctype_digit($iterationsRaw)) {
        return false;
    }

    $iterations = (int) $iterationsRaw;
    $derived = base64_encode(hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true));

    return hash_equals($hash, $derived);
}

function panel_authenticate(string $username, string $password): ?array
{
    $user = panel_find_user_by_username($username);
    if ($user === null) {
        return null;
    }

    if (!(bool) ($user['is_staff'] ?? false) || !(bool) ($user['is_active'] ?? false)) {
        return null;
    }

    if (!panel_verify_django_password($password, (string) ($user['password'] ?? ''))) {
        return null;
    }

    return $user;
}
