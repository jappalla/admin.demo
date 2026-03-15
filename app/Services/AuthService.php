<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Support\Csrf;
use App\Support\Session;

final class AuthService
{
    private const SESSION_USER_ID = 'auth_user_id';

    public function __construct(
        private readonly UserRepository $users
    ) {
    }

    public function login(string $email, string $password): bool
    {
        $normalizedEmail = strtolower(trim($email));
        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $user = $this->users->findByEmail($normalizedEmail);
        if (!is_array($user)) {
            return false;
        }

        $isAdmin = (($user['role'] ?? '') === 'admin');
        $isActive = ((int) ($user['is_active'] ?? 0) === 1);
        if (!$isAdmin || !$isActive) {
            return false;
        }

        $passwordHash = (string) ($user['password_hash'] ?? '');
        if ($passwordHash === '' || !password_verify($password, $passwordHash)) {
            return false;
        }

        Session::start();
        Session::regenerate();
        Session::set(self::SESSION_USER_ID, (int) $user['id']);
        Csrf::regenerate();
        return true;
    }

    public function logout(): void
    {
        Session::start();
        Session::remove(self::SESSION_USER_ID);
        Session::regenerate();
        Csrf::regenerate();
    }

    public function user(): ?array
    {
        Session::start();
        $userId = Session::get(self::SESSION_USER_ID);
        if (!is_int($userId) || $userId <= 0) {
            return null;
        }

        $user = $this->users->findById($userId);
        if (!is_array($user)) {
            return null;
        }

        $isAdmin = (($user['role'] ?? '') === 'admin');
        $isActive = ((int) ($user['is_active'] ?? 0) === 1);
        if (!$isAdmin || !$isActive) {
            return null;
        }

        return $user;
    }

    public function check(): bool
    {
        return is_array($this->user());
    }
}
