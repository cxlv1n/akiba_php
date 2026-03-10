<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход — Akiba Auto</title>
    <link rel="icon" type="image/png" href="<?= e(asset('images/akiba_auto_min.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/dashboard.css')) ?>?v=1">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-card__logo">
            <img src="<?= e(asset('images/logo-akiba.png')) ?>" alt="Akiba Auto">
        </div>
        <h1 class="login-card__title">Панель управления</h1>
        <p class="login-card__subtitle">Войдите чтобы продолжить</p>

        <?php if (!empty($error)): ?>
            <div class="login-card__error"><?= e((string) $error) ?></div>
        <?php endif; ?>

        <form method="post" class="login-card__form">
            <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">
            <div class="field">
                <label class="field__label" for="id_username">Логин</label>
                <input type="text" name="username" id="id_username" class="field__input" required autofocus placeholder="Имя пользователя">
            </div>
            <div class="field">
                <label class="field__label" for="id_password">Пароль</label>
                <input type="password" name="password" id="id_password" class="field__input" required placeholder="Пароль">
            </div>
            <button type="submit" class="btn btn--primary btn--full">Войти</button>
        </form>
    </div>
</body>
</html>
