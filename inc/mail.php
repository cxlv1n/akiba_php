<?php

declare(strict_types=1);

const MAIL_CONFIG_FILE = __DIR__ . '/../config/mail.php';
const DEFAULT_MAIL_LOG_FILE = __DIR__ . '/../storage/mail.log';

/**
 * @return array{
 *   request_to: array<int,string>,
 *   from_email: string,
 *   from_name: string,
 *   subject_prefix: string,
 *   log_file: string
 * }
 */
function mail_settings(): array
{
    static $settings = null;

    if ($settings !== null) {
        return $settings;
    }

    $defaults = [
        'request_to' => ['akibaauto@gmail.com'],
        'from_email' => default_mail_from_email(),
        'from_name' => 'AkibaAuto',
        'subject_prefix' => 'AkibaAuto',
        'log_file' => DEFAULT_MAIL_LOG_FILE,
    ];

    $config = [];
    if (is_file(MAIL_CONFIG_FILE)) {
        $loaded = require MAIL_CONFIG_FILE;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    $settings = array_merge($defaults, $config);

    $envTo = getenv('REQUEST_EMAIL_TO');
    if ($envTo !== false && trim((string) $envTo) !== '') {
        $settings['request_to'] = normalize_email_list($envTo);
    }

    $envFromEmail = getenv('DEFAULT_FROM_EMAIL');
    if ($envFromEmail !== false && trim((string) $envFromEmail) !== '') {
        $settings['from_email'] = trim((string) $envFromEmail);
    }

    $envFromName = getenv('DEFAULT_FROM_NAME');
    if ($envFromName !== false && trim((string) $envFromName) !== '') {
        $settings['from_name'] = trim((string) $envFromName);
    }

    $envSubjectPrefix = getenv('REQUEST_EMAIL_SUBJECT_PREFIX');
    if ($envSubjectPrefix !== false && trim((string) $envSubjectPrefix) !== '') {
        $settings['subject_prefix'] = trim((string) $envSubjectPrefix);
    }

    $envLogFile = getenv('REQUEST_EMAIL_LOG_FILE');
    if ($envLogFile !== false && trim((string) $envLogFile) !== '') {
        $settings['log_file'] = trim((string) $envLogFile);
    }

    $settings['request_to'] = normalize_email_list($settings['request_to'] ?? []);
    $settings['from_email'] = trim((string) ($settings['from_email'] ?? ''));
    $settings['from_name'] = trim((string) ($settings['from_name'] ?? 'AkibaAuto'));
    $settings['subject_prefix'] = trim((string) ($settings['subject_prefix'] ?? 'AkibaAuto'));
    $settings['log_file'] = trim((string) ($settings['log_file'] ?? DEFAULT_MAIL_LOG_FILE));

    if ($settings['from_email'] === '') {
        $settings['from_email'] = default_mail_from_email();
    }

    return $settings;
}

/**
 * @param array<string,mixed>|string $value
 * @return array<int,string>
 */
function normalize_email_list($value): array
{
    $items = is_array($value)
        ? $value
        : (preg_split('/[\s,;]+/', (string) $value) ?: []);
    $emails = [];

    foreach ($items as $item) {
        $email = trim((string) $item);
        if ($email === '') {
            continue;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            continue;
        }

        $emails[] = strtolower($email);
    }

    return array_values(array_unique($emails));
}

function default_mail_from_email(): string
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $host = strtolower(trim(preg_replace('/:\d+$/', '', $host) ?? ''));

    if ($host === '' || $host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP) !== false) {
        return 'no-reply@akibaauto.local';
    }

    return 'no-reply@' . $host;
}

function mail_header_encode(string $value): string
{
    if ($value === '' || preg_match('/^[\x20-\x7E]+$/', $value) === 1) {
        return $value;
    }

    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($value, 'UTF-8', 'B', "\r\n");
    }

    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

/**
 * @param array<string,mixed> $data
 */
function render_email_template(string $templatePath, array $data = []): string
{
    $templateFile = __DIR__ . '/../templates/' . ltrim($templatePath, '/');
    if (!is_file($templateFile)) {
        return '';
    }

    extract($data, EXTR_SKIP);

    ob_start();
    include $templateFile;
    return (string) ob_get_clean();
}

/**
 * @param array<string,mixed> $payload
 * @return array{
 *   subject: string,
 *   text: string,
 *   html: string,
 *   recipients: array<int,string>
 * }
 */
function build_request_email_message(array $payload, int $requestId): array
{
    $settings = mail_settings();
    $car = null;
    $carLabel = 'Не выбран';

    if (!empty($payload['car_id'])) {
        $car = find_car_by_id((int) $payload['car_id']);
        if ($car !== null) {
            $carLabel = trim(sprintf(
                '%s %s, %s (ID %d)',
                (string) $car['manufacturer'],
                (string) $car['model'],
                (string) $car['year'],
                (int) $car['id']
            ));
        }
    }

    $prefix = $settings['subject_prefix'];
    $subject = $prefix !== ''
        ? sprintf('%s: Новая заявка №%d', $prefix, $requestId)
        : sprintf('Новая заявка №%d', $requestId);

    $context = [
        'request_id' => $requestId,
        'name' => trim((string) ($payload['name'] ?? '')),
        'phone' => trim((string) ($payload['phone'] ?? '')),
        'budget' => value_or_dash((string) ($payload['budget'] ?? '')),
        'wishes' => value_or_dash((string) ($payload['wishes'] ?? '')),
        'source_page' => value_or_dash((string) ($payload['source_page'] ?? '')),
        'car_label' => $carLabel,
        'submitted_at' => date('d.m.Y H:i'),
    ];

    $text = implode("\n", [
        'Новая заявка с сайта AkibaAuto',
        '',
        'ID заявки: ' . $context['request_id'],
        'Имя: ' . $context['name'],
        'Телефон: ' . $context['phone'],
        'Бюджет: ' . $context['budget'],
        'Пожелания: ' . $context['wishes'],
        'Автомобиль: ' . $context['car_label'],
        'Страница: ' . $context['source_page'],
        'Отправлено: ' . $context['submitted_at'],
    ]);

    $html = render_email_template('emails/request.php', $context);

    return [
        'subject' => $subject,
        'text' => $text,
        'html' => $html !== '' ? $html : nl2br(e($text)),
        'recipients' => $settings['request_to'],
    ];
}

/**
 * @param array<int,string> $to
 * @param array{from_email:string,from_name:string} $settings
 */
function send_multipart_mail(array $to, string $subject, string $textBody, string $htmlBody, array $settings): bool
{
    if ($to === []) {
        return false;
    }

    $boundary = 'akiba-' . md5((string) microtime(true) . $subject);
    $fromEmail = trim((string) ($settings['from_email'] ?? ''));
    $fromName = trim((string) ($settings['from_name'] ?? ''));
    $fromHeader = $fromEmail;

    if ($fromName !== '' && $fromEmail !== '') {
        $fromHeader = mail_header_encode($fromName) . ' <' . $fromEmail . '>';
    }

    $headers = [
        'MIME-Version: 1.0',
        'From: ' . $fromHeader,
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    if ($fromEmail !== '') {
        $headers[] = 'Reply-To: ' . $fromEmail;
    }

    $body = [];
    $body[] = '--' . $boundary;
    $body[] = 'Content-Type: text/plain; charset=UTF-8';
    $body[] = 'Content-Transfer-Encoding: 8bit';
    $body[] = '';
    $body[] = $textBody;
    $body[] = '';
    $body[] = '--' . $boundary;
    $body[] = 'Content-Type: text/html; charset=UTF-8';
    $body[] = 'Content-Transfer-Encoding: 8bit';
    $body[] = '';
    $body[] = $htmlBody;
    $body[] = '';
    $body[] = '--' . $boundary . '--';
    $body[] = '';

    return @mail(
        implode(', ', $to),
        mail_header_encode($subject),
        implode("\r\n", $body),
        implode("\r\n", $headers)
    );
}

/**
 * @param array<string,mixed> $payload
 * @param array<string,mixed> $meta
 */
function log_mail_event(array $payload, array $meta = []): void
{
    $settings = mail_settings();
    $logFile = trim((string) ($settings['log_file'] ?? DEFAULT_MAIL_LOG_FILE));

    if ($logFile === '') {
        return;
    }

    $directory = dirname($logFile);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $record = [
        'created_at' => date(DATE_ATOM),
        'payload' => $payload,
        'meta' => $meta,
    ];

    file_put_contents(
        $logFile,
        json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}
