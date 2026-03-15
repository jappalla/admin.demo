<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class SettingsRepository
{
    public function getByKeys(array $keys): array
    {
        $normalizedKeys = array_values(array_filter(array_map(
            static fn (mixed $key): string => trim((string) $key),
            $keys
        ), static fn (string $key): bool => $key !== ''));

        if ($normalizedKeys === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($normalizedKeys), '?'));
        $statement = Database::connection()->prepare(
            'SELECT setting_key, setting_value
             FROM settings
             WHERE setting_key IN (' . $placeholders . ')'
        );
        $statement->execute($normalizedKeys);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($rows as $row) {
            $key = (string) ($row['setting_key'] ?? '');
            if ($key === '') {
                continue;
            }
            $settings[$key] = (string) ($row['setting_value'] ?? '');
        }

        return $settings;
    }

    public function setMany(array $settings): void
    {
        if ($settings === []) {
            return;
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO settings (setting_key, setting_value)
             VALUES (:setting_key, :setting_value)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );

        foreach ($settings as $key => $value) {
            $normalizedKey = trim((string) $key);
            if ($normalizedKey === '') {
                continue;
            }

            $statement->execute([
                'setting_key' => $normalizedKey,
                'setting_value' => (string) $value,
            ]);
        }
    }
}
