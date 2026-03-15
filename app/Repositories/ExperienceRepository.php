<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class ExperienceRepository
{
    public function all(): array
    {
        $query = Database::connection()->query(
            'SELECT id, role, description, start_date, end_date, sort_order, is_visible, created_at, updated_at
             FROM experiences
             ORDER BY sort_order ASC, id DESC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function visible(): array
    {
        $query = Database::connection()->query(
            'SELECT id, role, description, start_date, end_date, sort_order, is_visible
             FROM experiences
             WHERE is_visible = 1
             ORDER BY sort_order ASC, id DESC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO experiences (role, description, start_date, end_date, sort_order, is_visible)
             VALUES (:role, :description, :start_date, :end_date, :sort_order, :is_visible)'
        );

        $statement->execute([
            'role' => $data['role'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'sort_order' => $data['sort_order'],
            'is_visible' => $data['is_visible'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE experiences
             SET role = :role,
                 description = :description,
                 start_date = :start_date,
                 end_date = :end_date,
                 sort_order = :sort_order,
                 is_visible = :is_visible
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'role' => $data['role'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'sort_order' => $data['sort_order'],
            'is_visible' => $data['is_visible'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM experiences WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
