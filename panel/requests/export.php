<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
panel_require_auth();
$requests = panel_list_requests(['limit' => 0]);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="akiba_requests.csv"');
echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Имя', 'Телефон', 'Бюджет', 'Пожелания', 'Статус', 'Заметки менеджера', 'Дата'], ';');
foreach ($requests as $request) {
    fputcsv($out, [
        $request['id'],
        $request['name'],
        $request['phone'],
        $request['budget'],
        $request['wishes'],
        $request['status_label'],
        $request['admin_notes'],
        $request['created_at_display'],
    ], ';');
}
fclose($out);
exit;
