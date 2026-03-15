<?php
declare(strict_types=1);

use App\Repositories\UserRepository;

require dirname(__DIR__) . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This command can run only in CLI mode.\n");
    exit(1);
}

$email = strtolower(trim((string) env('ADMIN_EMAIL', 'admin@local.test')));
$password = (string) env('ADMIN_PASSWORD', 'ChangeMe123!');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "ADMIN_EMAIL is invalid.\n");
    exit(1);
}

if (strlen($password) < 10) {
    fwrite(STDERR, "ADMIN_PASSWORD must be at least 10 characters.\n");
    exit(1);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if (!is_string($passwordHash) || $passwordHash === '') {
    fwrite(STDERR, "Unable to hash admin password.\n");
    exit(1);
}

$repository = new UserRepository();
$repository->upsertAdmin($email, $passwordHash);

fwrite(STDOUT, "Admin user seeded/updated: {$email}\n");
