<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$autoloadCandidates = [
    BASE_PATH . '/vendor/autoload.php',
    dirname(BASE_PATH) . '/vendor/autoload.php',
];

foreach ($autoloadCandidates as $autoloadFile) {
    if (is_file($autoloadFile)) {
        require_once $autoloadFile;
        break;
    }
}

spl_autoload_register(static function (string $className): void {
    $prefix = 'App\\';
    if (strpos($className, $prefix) !== 0) {
        return;
    }

    $relativePath = str_replace('\\', '/', substr($className, strlen($prefix)));
    $filePath = BASE_PATH . '/app/' . $relativePath . '.php';

    if (is_file($filePath)) {
        require $filePath;
    }
});

App\Support\Env::load(BASE_PATH . '/.env');

date_default_timezone_set((string) App\Support\Env::get('APP_TIMEZONE', 'Europe/Rome'));

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        return App\Support\Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];
        $segments = explode('.', $key);
        $group = array_shift($segments);

        if (!is_string($group) || $group === '') {
            return $default;
        }

        if (!array_key_exists($group, $cache)) {
            $configFile = BASE_PATH . '/config/' . $group . '.php';
            $cache[$group] = is_file($configFile) ? require $configFile : [];
        }

        $value = $cache[$group];
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $baseUrl = trim((string) config('app.base_url', ''));
        if ($baseUrl === '') {
            $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            $scriptDir = $scriptName === '' ? '' : str_replace('\\', '/', dirname($scriptName));
            $scriptDir = rtrim($scriptDir, '/');
            if ($scriptDir === '.' || $scriptDir === '/') {
                $scriptDir = '';
            }

            if ($scriptDir !== '' && str_ends_with($scriptDir, '/public')) {
                $scriptDir = substr($scriptDir, 0, -7);
            }

            $baseUrl = $scriptDir;
        }

        if ($baseUrl !== '' && $baseUrl[0] !== '/') {
            $baseUrl = '/' . $baseUrl;
        }
        $baseUrl = rtrim($baseUrl, '/');

        $normalizedPath = ltrim($path, '/');
        if ($normalizedPath === '') {
            return $baseUrl === '' ? '/' : $baseUrl . '/';
        }

        return ($baseUrl === '' ? '' : $baseUrl) . '/' . $normalizedPath;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $assetPath): string
    {
        return app_url($assetPath);
    }
}

if (!function_exists('route_url')) {
    function route_url(string $route = ''): string
    {
        $normalizedRoute = ltrim($route, '/');
        if ($normalizedRoute === '') {
            return app_url('');
        }

        $routeMode = strtolower((string) config('app.route_mode', 'path'));
        if ($routeMode === 'query') {
            return app_url('?route=' . $normalizedRoute);
        }

        return app_url($normalizedRoute);
    }
}

if (!function_exists('redirect_to')) {
    function redirect_to(string $route): never
    {
        header('Location: ' . route_url($route), true, 302);
        exit;
    }
}

if (!function_exists('render')) {
    function render(string $viewPath, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $filePath = BASE_PATH . '/app/Views/' . ltrim($viewPath, '/') . '.php';
        if (!is_file($filePath)) {
            throw new RuntimeException('View not found: ' . $viewPath);
        }
        require $filePath;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            App\Support\Session::flashSet($key, $value);
            return null;
        }

        return App\Support\Session::flashGet($key);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return App\Support\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

App\Support\ErrorHandler::register((bool) config('app.debug', false));
