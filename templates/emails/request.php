<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Новая заявка</title>
</head>
<body style="margin:0;padding:0;background:#f4f1ea;font-family:Arial,sans-serif;color:#23201c;">
    <div style="max-width:680px;margin:0 auto;padding:32px 16px;">
        <div style="background:#23201c;color:#f5efe2;padding:24px 28px;border-radius:20px 20px 0 0;">
            <div style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;opacity:0.8;">AkibaAuto</div>
            <h1 style="margin:10px 0 0;font-size:28px;line-height:1.2;">Новая заявка с сайта</h1>
        </div>

        <div style="background:#ffffff;padding:28px;border-radius:0 0 20px 20px;border:1px solid #e8dfd2;border-top:none;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">ID заявки</td>
                    <td style="padding:0 0 14px;font-size:16px;font-weight:700;text-align:right;"><?= e((string) $request_id) ?></td>
                </tr>
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">Имя</td>
                    <td style="padding:0 0 14px;font-size:16px;text-align:right;"><?= e((string) $name) ?></td>
                </tr>
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">Телефон</td>
                    <td style="padding:0 0 14px;font-size:16px;text-align:right;"><?= e((string) $phone) ?></td>
                </tr>
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">Бюджет</td>
                    <td style="padding:0 0 14px;font-size:16px;text-align:right;"><?= e((string) $budget) ?></td>
                </tr>
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">Автомобиль</td>
                    <td style="padding:0 0 14px;font-size:16px;text-align:right;"><?= e((string) $car_label) ?></td>
                </tr>
                <tr>
                    <td style="padding:0 0 14px;font-size:14px;color:#7a6f63;">Страница</td>
                    <td style="padding:0 0 14px;font-size:16px;text-align:right;">
                        <?php if ((string) $source_page !== '—' && preg_match('#^https?://#i', (string) $source_page) === 1): ?>
                            <a href="<?= e((string) $source_page) ?>" style="color:#8d5b2b;text-decoration:none;"><?= e((string) $source_page) ?></a>
                        <?php else: ?>
                            <?= e((string) $source_page) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0;font-size:14px;color:#7a6f63;">Отправлено</td>
                    <td style="padding:0;font-size:16px;text-align:right;"><?= e((string) $submitted_at) ?></td>
                </tr>
            </table>

            <div style="margin-top:28px;padding:20px;background:#f7f3ec;border-radius:16px;">
                <div style="margin:0 0 10px;font-size:14px;color:#7a6f63;">Пожелания</div>
                <div style="font-size:16px;line-height:1.6;white-space:pre-line;"><?= nl2br(e((string) $wishes)) ?></div>
            </div>
        </div>
    </div>
</body>
</html>
