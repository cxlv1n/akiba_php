<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_logout_user();
panel_redirect(panel_url('login'));
