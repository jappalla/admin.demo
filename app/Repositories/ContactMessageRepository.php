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
}
