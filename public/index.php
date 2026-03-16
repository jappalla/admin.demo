<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Controllers\Admin\AdminController;
use App\Controllers\HomeController;
use App\Repositories\ContactMessageRepository;
use App\Repositories\ExperienceRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SkillRepository;
use App\Repositories\UserRepository;
use App\Services\AdminContentService;
use App\Services\AuthService;
use App\Services\ContactNotificationService;

$resolveRoute = static function (): string {
    $queryRoute = trim((string) ($_GET['route'] ?? ''), '/');
    if ($queryRoute !== '') {
        return $queryRoute;
    }

    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $path = trim($path, '/');
    if ($path === '' || $path === 'index.php') {
        return '';
    }

    $baseUrl = trim((string) config('app.base_url', ''), '/');
    if ($baseUrl === '') {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDirectory = trim((string) dirname($scriptName), '/');
        if ($scriptDirectory === '.' || $scriptDirectory === '') {
            $scriptDirectory = '';
        }
        if ($scriptDirectory !== '' && str_ends_with($scriptDirectory, '/public')) {
            $scriptDirectory = substr($scriptDirectory, 0, -7);
        }

        $baseUrl = $scriptDirectory;
    }

    if ($baseUrl !== '') {
        if ($path === $baseUrl || $path === $baseUrl . '/index.php') {
            return '';
        }

        if (str_starts_with($path, $baseUrl . '/')) {
            $path = substr($path, strlen($baseUrl) + 1);
        }
    }

    if (str_starts_with($path, 'public/')) {
        $path = substr($path, 7);
    }

    if ($path === '' || $path === 'index.php') {
        return '';
    }

    return $path;
};

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$route = $resolveRoute();

$contentService = new AdminContentService(
    new ExperienceRepository(),
    new SkillRepository(),
    new SettingsRepository(),
    new ContactMessageRepository()
);

$homeController = new HomeController(
    $contentService,
    new ContactNotificationService()
);
$adminController = new AdminController(
    new AuthService(new UserRepository()),
    $contentService
);

$routes = [
    'GET ' => static function () use ($homeController): void {
        $homeController->index();
    },
    'GET admin' => static function () use ($adminController): void {
        $adminController->loginPage();
    },
    'GET admin/panel' => static function () use ($adminController): void {
        $adminController->dashboardPage();
    },
    'POST contact/send' => static function () use ($homeController): void {
        $homeController->sendContactMessage();
    },
    'GET cv/export' => static function () use ($homeController): void {
        $homeController->exportHubPage();
    },
    'GET cv/pdf' => static function () use ($homeController): void {
        $homeController->downloadCvPdf();
    },
    'POST admin/login' => static function () use ($adminController): void {
        $adminController->login();
    },
    'POST admin/logout' => static function () use ($adminController): void {
        $adminController->logout();
    },
    'GET admin/forgot-password' => static function () use ($adminController): void {
        $adminController->forgotPasswordPage();
    },
    'POST admin/forgot-password' => static function () use ($adminController): void {
        $adminController->forgotPassword();
    },
    'GET admin/reset-password' => static function () use ($adminController): void {
        $adminController->resetPasswordPage();
    },
    'POST admin/reset-password' => static function () use ($adminController): void {
        $adminController->resetPassword();
    },
    'POST admin/experience/create' => static function () use ($adminController): void {
        $adminController->createExperience();
    },
    'POST admin/experience/update' => static function () use ($adminController): void {
        $adminController->updateExperience();
    },
    'POST admin/experience/delete' => static function () use ($adminController): void {
        $adminController->deleteExperience();
    },
    'POST admin/skill/create' => static function () use ($adminController): void {
        $adminController->createSkill();
    },
    'POST admin/skill/update' => static function () use ($adminController): void {
        $adminController->updateSkill();
    },
    'POST admin/skill/delete' => static function () use ($adminController): void {
        $adminController->deleteSkill();
    },
    'POST admin/settings/update' => static function () use ($adminController): void {
        $adminController->updateProfileContacts();
    },
    'POST admin/message/read' => static function () use ($adminController): void {
        $adminController->markMessageRead();
    },
    'POST admin/message/delete' => static function () use ($adminController): void {
        $adminController->deleteMessage();
    },
    'GET admin/api/experiences' => static function () use ($adminController): void {
        $adminController->apiExperiences();
    },
    'GET admin/api/skills' => static function () use ($adminController): void {
        $adminController->apiSkills();
    },
    'GET admin/api/messages' => static function () use ($adminController): void {
        $adminController->apiMessages();
    },
];

$routeKey = $method . ' ' . $route;
if (isset($routes[$routeKey])) {
    $routes[$routeKey]();
    return;
}

http_response_code(404);

$pageTitle = '404 — Pagina non trovata';
$pageDescription = 'La pagina richiesta non esiste.';
$pageRobots = 'noindex, nofollow';
$isHomePage = false;

require dirname(__DIR__) . '/partials/header.php';
require dirname(__DIR__) . '/app/Views/errors/404.php';
require dirname(__DIR__) . '/partials/footer.php';
