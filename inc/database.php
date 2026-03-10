<?php

declare(strict_types=1);

const DATABASE_CONFIG_FILE = __DIR__ . '/../config/database.php';

/**
 * @return array{driver:string,sqlite_path:string}
 */
function database_settings(): array
{
    static $settings = null;

    if ($settings !== null) {
        return $settings;
    }

    $defaults = [
        'driver' => 'sqlite',
        'sqlite_path' => __DIR__ . '/../data/akiba.sqlite3',
    ];

    $config = [];
    if (is_file(DATABASE_CONFIG_FILE)) {
        $loaded = require DATABASE_CONFIG_FILE;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    $settings = array_merge($defaults, $config);

    $envPath = getenv('AKIBA_SQLITE_PATH');
    if ($envPath !== false && trim((string) $envPath) !== '') {
        $settings['sqlite_path'] = trim((string) $envPath);
    }

    return $settings;
}

function sqlite_database_path(): string
{
    $settings = database_settings();
    return trim((string) ($settings['sqlite_path'] ?? ''));
}

function catalog_database_available(): bool
{
    $path = sqlite_database_path();
    return $path !== '' && is_file($path);
}

function db_connection(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if ($failed || !catalog_database_available()) {
        return null;
    }

    try {
        $pdo = new PDO('sqlite:' . sqlite_database_path());
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (Throwable $e) {
        $failed = true;
        error_log('SQLite connection failed: ' . $e->getMessage());
        return null;
    }
}

/**
 * @param array<string,mixed>|array<int,mixed> $params
 * @return array<int,array<string,mixed>>
 */
function db_fetch_all(string $sql, array $params = []): array
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return [];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return is_array($rows) ? $rows : [];
}

/**
 * @param array<string,mixed>|array<int,mixed> $params
 * @return array<string,mixed>|null
 */
function db_fetch_one(string $sql, array $params = []): ?array
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return null;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

/**
 * @param array<string,mixed>|array<int,mixed> $params
 * @return scalar|null
 */
function db_fetch_value(string $sql, array $params = [])
{
    $row = db_fetch_one($sql, $params);
    if (!is_array($row) || $row === []) {
        return null;
    }

    return reset($row);
}

/**
 * @param array<string,mixed>|array<int,mixed> $params
 */
function db_execute(string $sql, array $params = []): bool
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return false;
    }

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function db_last_insert_id(): int
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return 0;
    }

    return (int) $pdo->lastInsertId();
}

/**
 * @param callable(PDO):mixed $callback
 * @return mixed
 */
function db_transaction(callable $callback)
{
    $pdo = db_connection();
    if (!$pdo instanceof PDO) {
        return null;
    }

    $pdo->beginTransaction();

    try {
        $result = $callback($pdo);
        $pdo->commit();
        return $result;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
