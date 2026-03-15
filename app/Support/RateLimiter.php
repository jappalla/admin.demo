<?php

declare(strict_types=1);

namespace App\Support;

/**
 * File-based rate limiter.
 *
 * Stores attempt timestamps in JSON files under a configurable directory.
 * Each key (e.g. "login:192.168.1.1") gets its own file to avoid locks.
 */
final class RateLimiter
{
    private string $storageDir;

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? (defined('BASE_PATH') ? BASE_PATH . '/storage/rate_limits' : sys_get_temp_dir() . '/rate_limits');

        if (!is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0755, true);
        }
    }

    /**
     * Check if the key has exceeded the allowed number of attempts within the window.
     *
     * @param string $key     Unique identifier (e.g. "login:127.0.0.1")
     * @param int    $maxHits Maximum allowed attempts
     * @param int    $windowSeconds Time window in seconds
     */
    public function tooManyAttempts(string $key, int $maxHits, int $windowSeconds): bool
    {
        $attempts = $this->getAttempts($key, $windowSeconds);
        return count($attempts) >= $maxHits;
    }

    /**
     * Record an attempt for the given key.
     */
    public function hit(string $key): void
    {
        $file = $this->filePath($key);
        $data = $this->readFile($file);
        $data[] = time();
        $this->writeFile($file, $data);
    }

    /**
     * Get the number of remaining attempts.
     */
    public function remainingAttempts(string $key, int $maxHits, int $windowSeconds): int
    {
        $attempts = $this->getAttempts($key, $windowSeconds);
        return max(0, $maxHits - count($attempts));
    }

    /**
     * Clear all attempts for a key.
     */
    public function clear(string $key): void
    {
        $file = $this->filePath($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /**
     * Get valid (non-expired) attempts for a key.
     *
     * @return int[] Array of timestamps
     */
    private function getAttempts(string $key, int $windowSeconds): array
    {
        $file = $this->filePath($key);
        $data = $this->readFile($file);
        $cutoff = time() - $windowSeconds;

        // Filter out expired entries
        $valid = array_values(array_filter($data, static fn(int $ts): bool => $ts > $cutoff));

        // Persist cleaned data
        if (count($valid) !== count($data)) {
            $this->writeFile($file, $valid);
        }

        return $valid;
    }

    private function filePath(string $key): string
    {
        return $this->storageDir . '/' . md5($key) . '.json';
    }

    /**
     * @return int[]
     */
    private function readFile(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $content = @file_get_contents($path);
        if ($content === false || $content === '') {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, 'is_int'));
    }

    /**
     * @param int[] $data
     */
    private function writeFile(string $path, array $data): void
    {
        @file_put_contents($path, json_encode($data), LOCK_EX);
    }
}
