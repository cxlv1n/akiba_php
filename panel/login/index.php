<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (panel_current_user() !== null) {
    panel_redirect(panel_url('dashboard'));
}

$error = null;
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    panel_require_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $user = panel_authenticate($username, $password);
    if ($user !== null) {
        panel_login_user($user);
        $next = trim((string) ($_GET['next'] ?? ''));
        panel_redirect($next !== '' ? $next : panel_url('dashboard'));
    }
    $error = 'Неверный логин или пароль';
}

panel_render_login($error);
