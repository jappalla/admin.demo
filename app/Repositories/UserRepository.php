<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, password_hash, role, is_active
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($user) ? $user : null;
    }

    public function findById(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, role, is_active
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($user) ? $user : null;
    }

    public function upsertAdmin(string $email, string $passwordHash): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO users (email, password_hash, role, is_active)
             VALUES (:email, :password_hash, :role, :is_active)
             ON DUPLICATE KEY UPDATE
                password_hash = VALUES(password_hash),
                role = VALUES(role),
                is_active = VALUES(is_active)'
        );

        $statement->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => 'admin',
            'is_active' => 1,
        ]);
    }

    public function saveResetToken(int $userId, string $token): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET password_reset_token = :token,
                 password_reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR)
             WHERE id = :id'
        );
        $statement->execute(['token' => $token, 'id' => $userId]);
    }

    public function findByResetToken(string $token): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, role, is_active
             FROM users
             WHERE password_reset_token = :token
               AND password_reset_expires > NOW()
             LIMIT 1'
        );
        $statement->execute(['token' => $token]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($user) ? $user : null;
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET password_hash = :hash,
                 password_reset_token = NULL,
                 password_reset_expires = NULL
             WHERE id = :id'
        );
        $statement->execute(['hash' => $passwordHash, 'id' => $userId]);
    }
}
