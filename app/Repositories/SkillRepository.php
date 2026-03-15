<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class SkillRepository
{
    public function all(): array
    {
        $query = Database::connection()->query(
            'SELECT id, name, category, level, link_url, sort_order, is_visible, created_at, updated_at
             FROM skills
             ORDER BY sort_order ASC, id DESC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function visible(): array
    {
        $query = Database::connection()->query(
            'SELECT id, name, category, level, link_url, sort_order, is_visible
             FROM skills
             WHERE is_visible = 1
             ORDER BY sort_order ASC, id DESC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO skills (name, category, level, link_url, sort_order, is_visible)
             VALUES (:name, :category, :level, :link_url, :sort_order, :is_visible)'
        );

        $statement->execute([
            'name' => $data['name'],
            'category' => $data['category'],
            'level' => $data['level'],
            'link_url' => $data['link_url'],
            'sort_order' => $data['sort_order'],
            'is_visible' => $data['is_visible'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE skills
             SET name = :name,
                 category = :category,
                 level = :level,
                 link_url = :link_url,
                 sort_order = :sort_order,
                 is_visible = :is_visible
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'category' => $data['category'],
            'level' => $data['level'],
            'link_url' => $data['link_url'],
            'sort_order' => $data['sort_order'],
            'is_visible' => $data['is_visible'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM skills WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
