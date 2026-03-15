<?php
declare(strict_types=1);

use App\Support\Database;

require dirname(__DIR__) . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This command can run only in CLI mode.\n");
    exit(1);
}

$defaultConnection = (string) config('database.default', 'mysql');
$connection = config('database.connections.' . $defaultConnection, []);
if (!is_array($connection) || $connection === []) {
    fwrite(STDERR, "Database configuration not found.\n");
    exit(1);
}

$databaseName = (string) ($connection['database'] ?? '');
$charset = (string) ($connection['charset'] ?? 'utf8mb4');
$collation = (string) ($connection['collation'] ?? 'utf8mb4_unicode_ci');

if ($databaseName === '') {
    fwrite(STDERR, "Database name is empty.\n");
    exit(1);
}

$serverConnection = Database::serverConnection();
$quotedDatabase = '`' . str_replace('`', '``', $databaseName) . '`';
$serverConnection->exec(
    sprintf(
        'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s',
        $quotedDatabase,
        $charset,
        $collation
    )
);
fwrite(STDOUT, "Database ensured: {$databaseName}\n");

$pdo = Database::connection();
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(190) NOT NULL UNIQUE,
        batch INT NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$migrationFiles = glob(__DIR__ . '/migrations/*.sql');
if ($migrationFiles === false) {
    fwrite(STDERR, "Unable to read migration directory.\n");
    exit(1);
}
sort($migrationFiles);

$appliedMigrations = $pdo
    ->query('SELECT filename FROM schema_migrations ORDER BY filename ASC')
    ->fetchAll(PDO::FETCH_COLUMN);
$appliedLookup = array_fill_keys($appliedMigrations, true);

$batch = ((int) $pdo->query('SELECT COALESCE(MAX(batch), 0) FROM schema_migrations')->fetchColumn()) + 1;
$appliedCount = 0;
$skippedCount = 0;

foreach ($migrationFiles as $migrationFile) {
    $filename = basename($migrationFile);
    if (isset($appliedLookup[$filename])) {
        fwrite(STDOUT, "Skip: {$filename}\n");
        $skippedCount++;
        continue;
    }

    $sql = file_get_contents($migrationFile);
    if ($sql === false || trim($sql) === '') {
        fwrite(STDERR, "Invalid migration file: {$filename}\n");
        exit(1);
    }

    try {
        $pdo->beginTransaction();
        $transactionStarted = $pdo->inTransaction();
        $pdo->exec($sql);

        $insert = $pdo->prepare(
            'INSERT INTO schema_migrations (filename, batch) VALUES (:filename, :batch)'
        );
        $insert->execute([
            'filename' => $filename,
            'batch' => $batch,
        ]);

        if ($transactionStarted && $pdo->inTransaction()) {
            $pdo->commit();
        }
        fwrite(STDOUT, "Applied: {$filename}\n");
        $appliedCount++;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fwrite(STDERR, "Migration failed ({$filename}): {$exception->getMessage()}\n");
        exit(1);
    }
}

fwrite(STDOUT, "Done. Applied={$appliedCount}; Skipped={$skippedCount}\n");
