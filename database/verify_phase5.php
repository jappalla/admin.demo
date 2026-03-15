<?php
declare(strict_types=1);

use App\Support\Database;

require dirname(__DIR__) . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This command can run only in CLI mode.\n");
    exit(1);
}

$pdo = Database::connection();
$tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
$tableLookup = array_fill_keys($tables, true);

$requiredTables = ['experiences', 'skills', 'users', 'settings', 'contact_messages', 'schema_migrations'];
$missingTables = [];
foreach ($requiredTables as $tableName) {
    if (!isset($tableLookup[$tableName])) {
        $missingTables[] = $tableName;
    }
}

if ($missingTables !== []) {
    fwrite(STDERR, 'Missing tables: ' . implode(', ', $missingTables) . "\n");
    exit(1);
}

$requiredSettingKeys = [
    'profile_text',
    'contact_email',
    'contact_linkedin_label',
    'contact_linkedin_url',
    'contact_phone',
    'contact_intro',
];

$placeholders = implode(', ', array_fill(0, count($requiredSettingKeys), '?'));
$statement = $pdo->prepare(
    'SELECT setting_key
     FROM settings
     WHERE setting_key IN (' . $placeholders . ')'
);
$statement->execute($requiredSettingKeys);
$rows = $statement->fetchAll(\PDO::FETCH_COLUMN);
$settingsLookup = array_fill_keys($rows, true);

$missingSettings = [];
foreach ($requiredSettingKeys as $key) {
    if (!isset($settingsLookup[$key])) {
        $missingSettings[] = $key;
    }
}

if ($missingSettings !== []) {
    fwrite(STDERR, 'Missing settings keys: ' . implode(', ', $missingSettings) . "\n");
    exit(1);
}

$messagesCount = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
fwrite(STDOUT, 'Phase5Schema=OK; ContactMessages=' . $messagesCount . "\n");
