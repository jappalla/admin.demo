<?php
declare(strict_types=1);

namespace App\Support;

use ErrorException;
use Throwable;

final class ErrorHandler
{
    public static function register(bool $debug): void
    {
        set_error_handler(static function (
            int $severity,
            string $message,
            string $file,
            int $line
        ): bool {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(static function (Throwable $exception) use ($debug): void {
            http_response_code(500);

            if ($debug) {
                $message = e($exception->getMessage());
                $location = e($exception->getFile() . ':' . (string) $exception->getLine());
                echo '<h1>Unhandled Exception</h1>';
                echo '<p>' . $message . '</p>';
                echo '<pre>' . $location . '</pre>';
                return;
            }

            echo '<h1>Errore inatteso</h1><p>Riprova piu tardi.</p>';
        });
    }
}
