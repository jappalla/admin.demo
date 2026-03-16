<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class ContactMessageRepository
{
    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO contact_messages (full_name, email, subject, message, status)
             VALUES (:full_name, :email, :subject, :message, :status)'
        );

        $statement->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => $data['status'] ?? 'new',
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function latest(int $limit = 50): array
    {
        $normalizedLimit = max(1, min($limit, 200));
        $statement = Database::connection()->prepare(
            'SELECT id, full_name, email, subject, message, status, created_at
             FROM contact_messages
             ORDER BY id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $normalizedLimit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, full_name, email, subject, message, status, created_at
             FROM contact_messages
             WHERE id = :id'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE contact_messages SET status = :status WHERE id = :id'
        );
        $statement->bindValue(':status', $status);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM contact_messages WHERE id = :id'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function count(): int
    {
        $statement = Database::connection()->query(
            'SELECT COUNT(*) FROM contact_messages'
        );

        return (int) $statement->fetchColumn();
    }

    public function paginated(int $page, int $perPage = 10): array
    {
        $normalizedPerPage = max(1, min($perPage, 100));
        $normalizedPage = max(1, $page);
        $offset = ($normalizedPage - 1) * $normalizedPerPage;

        $statement = Database::connection()->prepare(
            'SELECT id, full_name, email, subject, message, status, created_at
             FROM contact_messages
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', $normalizedPerPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
