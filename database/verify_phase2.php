<?php
declare(strict_types=1);

use App\Support\Database;

require dirname(__DIR__) . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This command can run only in CLI mode.\n");
    exit(1);
}

$requiredTables = [
    'schema_migrations',
    'experiences',
    'skills',
    'users',
    'settings',
];

$pdo = Database::connection();
$statement = $pdo->query('SHOW TABLES');
$tables = $statement->fetchAll(PDO::FETCH_COLUMN);
$lookup = array_fill_keys($tables, true);

$missing = [];
foreach ($requiredTables as $tableName) {
    if (!isset($lookup[$tableName])) {
        $missing[] = $tableName;
    }
}

if ($missing !== []) {
    fwrite(STDERR, 'Missing tables: ' . implode(', ', $missing) . "\n");
    exit(1);
}

$migrationCount = (int) $pdo->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn();
fwrite(STDOUT, 'Phase2Schema=OK; MigrationRows=' . $migrationCount . "\n");
