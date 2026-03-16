<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AdminContentService;
use App\Services\ContactNotificationService;
use App\Support\Csrf;
use App\Support\RateLimiter;
use App\Support\SimplePdf;
use Throwable;

final class HomeController
{
    public function __construct(
        private readonly AdminContentService $content,
        private readonly ContactNotificationService $contactNotification
    ) {}

    public function index(): void
    {
        $pageTitle = (string) config('app.seo_title', 'Antonio Trapasso | Curriculum Vitae');
        $pageDescription = (string) config(
            'app.seo_description',
            'Curriculum vitae personale di Antonio Trapasso: esperienza, competenze e contatti professionali.'
        );
        $isHomePage = true;

        $experiences = $this->content->listPublishedExperiences();
        if ($experiences === []) {
            $experiences = $this->fallbackExperiences();
        }

        $skills = $this->content->listPublishedSkills();
        if ($skills === []) {
            $skills = $this->fallbackSkills();
        }

        $experiences = array_map(function (array $experience): array {
            $description = (string) ($experience['description'] ?? '');
            $experience['description_html'] = $this->content->renderRichText($description);
            return $experience;
        }, $experiences);

        $settings = $this->content->profileContactSettings();
        $profileHtml = $this->content->renderProfileHtml((string) ($settings['profile_text'] ?? ''));
        $contactIntroHtml = $this->content->renderRichText((string) ($settings['contact_intro'] ?? ''));
        $contactSuccess = flash('contact_success');
        $contactError = flash('contact_error');

        $projects = require BASE_PATH . '/config/projects.php';
        $totalTests = $this->calculateTotalTests();

        require BASE_PATH . '/partials/header.php';
        render('home', [
            'experiences' => $experiences,
            'skills' => $skills,
            'settings' => $settings,
            'profileHtml' => $profileHtml,
            'contactIntroHtml' => $contactIntroHtml,
            'contactSuccess' => $contactSuccess,
            'contactError' => $contactError,
            'projects' => $projects,
            'totalTests' => $totalTests,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function exportHubPage(): void
    {
        $pageTitle = 'Export CV PDF | Antonio Trapasso';
        $pageDescription = 'Anteprima e strumenti di export del curriculum in PDF.';
        $pageCanonical = route_url('cv/export');
        $isHomePage = false;

        $experiences = $this->content->listPublishedExperiences();
        if ($experiences === []) {
            $experiences = $this->fallbackExperiences();
        }

        $skills = $this->content->listPublishedSkills();
        if ($skills === []) {
            $skills = $this->fallbackSkills();
        }

        $settings = $this->content->profileContactSettings();
        $profileHtml = $this->content->renderProfileHtml((string) ($settings['profile_text'] ?? ''));
        $profilePreview = trim(preg_replace('/\s+/', ' ', strip_tags($profileHtml)) ?? '');
        if ($profilePreview === '') {
            $profilePreview = 'Profilo non disponibile.';
        }

        $pdfInlineUrl = route_url('cv/pdf');
        $pdfDownloadUrl = route_url('cv/pdf?download=1');
        $profilePreview = substr($profilePreview, 0, 240) . (strlen($profilePreview) > 240 ? '...' : '');

        require BASE_PATH . '/partials/header.php';
        render('cv-export', [
            'experiencesCount' => count($experiences),
            'skillsCount' => count($skills),
            'profilePreview' => $profilePreview,
            'pdfInlineUrl' => $pdfInlineUrl,
            'pdfDownloadUrl' => $pdfDownloadUrl,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function downloadCvPdf(): void
    {
        $experiences = $this->content->listPublishedExperiences();
        if ($experiences === []) {
            $experiences = $this->fallbackExperiences();
        }

        $skills = $this->content->listPublishedSkills();
        if ($skills === []) {
            $skills = $this->fallbackSkills();
        }

        $settings = $this->content->profileContactSettings();
        $profileHtml = $this->content->renderProfileHtml((string) ($settings['profile_text'] ?? ''));
        $profilePlainText = trim(preg_replace('/\s+/', ' ', strip_tags($profileHtml)) ?? '');
        if ($profilePlainText === '') {
            $profilePlainText = 'Profilo non disponibile.';
        }

        $lines = [
            'Curriculum Vitae - Antonio Trapasso',
            'Data export: ' . date('Y-m-d H:i'),
            '',
            'PROFILO',
            $profilePlainText,
            '',
            'ESPERIENZE',
        ];

        foreach ($experiences as $experience) {
            $role = trim((string) ($experience['role'] ?? ''));
            if ($role === '') {
                continue;
            }

            $descriptionHtml = $this->content->renderRichText((string) ($experience['description'] ?? ''));
            $description = trim(preg_replace('/\s+/', ' ', strip_tags($descriptionHtml)) ?? '');
            $period = $this->formatExperiencePeriod($experience);
            $line = '- ' . $role . ($period !== '' ? ' (' . $period . ')' : '');

            $lines[] = $line;
            if ($description !== '') {
                $lines[] = '  ' . $description;
            }
        }

        $lines[] = '';
        $lines[] = 'COMPETENZE';
        $skillNames = array_values(array_filter(array_map(
            static fn(array $skill): string => trim((string) ($skill['name'] ?? '')),
            $skills
        ), static fn(string $name): bool => $name !== ''));
        $lines[] = $skillNames !== [] ? implode(', ', $skillNames) : 'Nessuna competenza disponibile.';

        $lines[] = '';
        $lines[] = 'CONTATTI';
        $contactEmail = trim((string) ($settings['contact_email'] ?? ''));
        $contactPhone = trim((string) ($settings['contact_phone'] ?? ''));
        $contactLinkedin = trim((string) ($settings['contact_linkedin_url'] ?? ''));

        if ($contactEmail !== '') {
            $lines[] = 'Email: ' . $contactEmail;
        }
        if ($contactPhone !== '') {
            $lines[] = 'Telefono: ' . $contactPhone;
        }
        if ($contactLinkedin !== '') {
            $lines[] = 'LinkedIn: ' . $contactLinkedin;
        }

        $pdf = SimplePdf::fromLines($lines, 'Antonio Trapasso CV');
        $downloadRequested = isset($_GET['download']) && (string) $_GET['download'] === '1';

        header('Content-Type: application/pdf');
        if ($downloadRequested) {
            header('Content-Disposition: attachment; filename="Antonio_Trapasso_CV.pdf"');
        } else {
            header('Content-Disposition: inline; filename="Antonio_Trapasso_CV.pdf"');
        }
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        echo $pdf;
        exit;
    }

    public function sendContactMessage(): void
    {
        $token = $_POST['csrf_token'] ?? null;
        if (!is_string($token) || !Csrf::isValid($token)) {
            http_response_code(419);
            echo 'CSRF token non valido.';
            exit;
        }

        // Rate limit: 5 contact messages per hour per IP
        $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $rateLimiter = new RateLimiter();
        $rateLimitKey = 'contact:' . $clientIp;

        if ($rateLimiter->tooManyAttempts($rateLimitKey, 5, 3600)) {
            flash('contact_error', 'Troppi messaggi inviati. Riprova tra un\'ora.');
            header('Location: ' . route_url('') . '#contatti', true, 302);
            exit;
        }

        try {
            $messageId = $this->content->createContactMessage($_POST);
            $this->contactNotification->sendContactMessage($_POST, $messageId);
            $rateLimiter->hit($rateLimitKey);
            Csrf::regenerate();
            flash('contact_success', 'Messaggio inviato con successo. ID richiesta: ' . $messageId . '.');
        } catch (Throwable $exception) {
            $message = (bool) config('app.debug', false)
                ? 'Invio messaggio fallito. ' . $exception->getMessage()
                : 'Invio messaggio fallito. Verifica i campi e riprova.';
            flash('contact_error', $message);
        }

        header('Location: ' . route_url('') . '#contatti', true, 302);
        exit;
    }

    private function fallbackExperiences(): array
    {
        return [
            [
                'id' => 0,
                'role' => 'Senior Web Developer',
                'description' => 'Progettazione e sviluppo di piattaforme enterprise con stack PHP, JS e integrazione API.',
            ],
            [
                'id' => 0,
                'role' => 'Frontend Specialist',
                'description' => 'Implementazione UI responsive, attenzione all\'accessibilita e ottimizzazione Core Web Vitals.',
            ],
        ];
    }

    private function fallbackSkills(): array
    {
        return [
            ['id' => 0, 'name' => 'PHP'],
            ['id' => 0, 'name' => 'JavaScript'],
            ['id' => 0, 'name' => 'HTML5'],
            ['id' => 0, 'name' => 'CSS3'],
            ['id' => 0, 'name' => 'MySQL'],
            ['id' => 0, 'name' => 'SEO Tecnico'],
        ];
    }

    private function formatExperiencePeriod(array $experience): string
    {
        $startDate = trim((string) ($experience['start_date'] ?? ''));
        $endDate = trim((string) ($experience['end_date'] ?? ''));

        if ($startDate !== '' && $endDate !== '') {
            return $startDate . ' - ' . $endDate;
        }

        if ($startDate !== '') {
            return $startDate . ' - in corso';
        }

        if ($endDate !== '') {
            return 'fino al ' . $endDate;
        }

        return '';
    }

    private function calculateTotalTests(): int
    {
        // App (231) + Dashboard (333) + API (63) = 627
        // Derived from test suites; update config/projects.php metrics if counts change.
        return 627;
    }
}
