<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\AdminContentService;
use App\Services\AuthService;
use App\Services\MailService;
use App\Repositories\UserRepository;
use App\Support\Csrf;
use App\Support\RateLimiter;
use Throwable;

final class AdminController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly AdminContentService $content,
        private readonly UserRepository $users = new UserRepository(),
        private readonly MailService $mail = new MailService(),
    ) {}

    public function loginPage(): void
    {
        if ($this->auth->check()) {
            redirect_to('admin/panel');
        }

        $pageTitle = 'Admin Login | Antonio Trapasso CV';
        $pageDescription = 'Accesso area amministrativa per gestione contenuti.';
        $pageRobots = 'noindex,nofollow';
        $pageCanonical = route_url('admin');
        $isHomePage = false;
        $successMessage = flash('success');
        $errorMessage = flash('error');

        require BASE_PATH . '/partials/header.php';
        render('admin/login', [
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function dashboardPage(): void
    {
        $user = $this->requireAuthenticatedUser();

        $pageTitle = 'Admin Dashboard | Antonio Trapasso CV';
        $pageDescription = 'Dashboard amministrazione contenuti.';
        $pageRobots = 'noindex,nofollow';
        $pageCanonical = route_url('admin/panel');
        $isHomePage = false;
        $successMessage = flash('success');
        $errorMessage = flash('error');
        $experiences = $this->content->listExperiences();
        $skills = $this->content->listSkills();
        $settings = $this->content->profileContactSettings();

        $msgPage = max(1, (int) ($_GET['msg_page'] ?? 1));
        $msgPerPage = 10;
        $messages = $this->content->paginatedMessages($msgPage, $msgPerPage);
        $msgTotal = $this->content->countMessages();
        $msgTotalPages = max(1, (int) ceil($msgTotal / $msgPerPage));

        require BASE_PATH . '/partials/header.php';
        render('admin/dashboard', [
            'user' => $user,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'experiences' => $experiences,
            'skills' => $skills,
            'settings' => $settings,
            'messages' => $messages,
            'msgPage' => $msgPage,
            'msgTotalPages' => $msgTotalPages,
            'msgTotal' => $msgTotal,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function login(): void
    {
        $this->assertValidCsrf();

        $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $rateLimiter = new RateLimiter();
        $rateLimitKey = 'login:' . $clientIp;

        if ($rateLimiter->tooManyAttempts($rateLimitKey, 5, 60)) {
            flash('error', 'Troppi tentativi di login. Riprova tra un minuto.');
            redirect_to('admin');
        }

        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($this->auth->login($email, $password)) {
            $rateLimiter->clear($rateLimitKey);
            flash('success', 'Login eseguito con successo.');
            redirect_to('admin/panel');
        }

        $rateLimiter->hit($rateLimitKey);
        flash('error', 'Credenziali non valide oppure utente non attivo.');
        redirect_to('admin');
    }

    public function logout(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();
        $this->auth->logout();
        flash('success', 'Logout completato.');
        redirect_to('admin');
    }

    // ── Password Recovery ────────────────────────────────

    public function forgotPasswordPage(): void
    {
        if ($this->auth->check()) {
            redirect_to('admin/panel');
        }

        $pageTitle = 'Password dimenticata | Developer Admin';
        $pageDescription = 'Recupero password admin.';
        $pageRobots = 'noindex,nofollow';
        $pageCanonical = route_url('admin/forgot-password');
        $isHomePage = false;
        $successMessage = flash('success');
        $errorMessage = flash('error');

        require BASE_PATH . '/partials/header.php';
        render('admin/forgot-password', [
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function forgotPassword(): void
    {
        $this->assertValidCsrf();

        $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $rateLimiter = new RateLimiter();
        $rateLimitKey = 'forgot:' . $clientIp;

        if ($rateLimiter->tooManyAttempts($rateLimitKey, 3, 3600)) {
            flash('error', 'Troppe richieste. Riprova tra un\'ora.');
            redirect_to('admin/forgot-password');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Inserisci un indirizzo email valido.');
            redirect_to('admin/forgot-password');
        }

        $rateLimiter->hit($rateLimitKey);

        $user = $this->users->findByEmail($email);
        if ($user && ($user['role'] ?? '') === 'admin') {
            $token = bin2hex(random_bytes(32));
            $this->users->saveResetToken((int) $user['id'], $token);
            $this->mail->sendPasswordReset($email, $token);
        }

        // Always show success message (don't reveal if email exists)
        flash('success', 'Se l\'email è registrata, riceverai le istruzioni per il reset.');
        redirect_to('admin/forgot-password');
    }

    public function resetPasswordPage(): void
    {
        if ($this->auth->check()) {
            redirect_to('admin/panel');
        }

        $token = (string) ($_GET['token'] ?? '');

        $pageTitle = 'Nuova Password | Developer Admin';
        $pageDescription = 'Reimposta la tua password admin.';
        $pageRobots = 'noindex,nofollow';
        $pageCanonical = route_url('admin/reset-password');
        $isHomePage = false;
        $successMessage = flash('success');
        $errorMessage = flash('error');

        $validToken = false;
        if ($token !== '') {
            $user = $this->users->findByResetToken($token);
            $validToken = ($user !== null);
        }

        require BASE_PATH . '/partials/header.php';
        render('admin/reset-password', [
            'token' => $token,
            'validToken' => $validToken,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ]);
        require BASE_PATH . '/partials/footer.php';
    }

    public function resetPassword(): void
    {
        $this->assertValidCsrf();

        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($token === '') {
            flash('error', 'Token mancante.');
            redirect_to('admin');
        }

        if (strlen($password) < 8) {
            flash('error', 'La password deve essere di almeno 8 caratteri.');
            redirect_to('admin/reset-password?token=' . urlencode($token));
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Le password non corrispondono.');
            redirect_to('admin/reset-password?token=' . urlencode($token));
        }

        $user = $this->users->findByResetToken($token);
        if (!$user) {
            flash('error', 'Token non valido o scaduto. Richiedi un nuovo link.');
            redirect_to('admin/forgot-password');
        }

        $this->users->updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        flash('success', 'Password reimpostata con successo. Effettua il login.');
        redirect_to('admin');
    }

    // ── CRUD ─────────────────────────────────────────────

    public function createExperience(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        try {
            $id = $this->content->createExperience($_POST);
            Csrf::regenerate();
            flash('success', 'Esperienza creata con ID ' . $id . '.');
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Creazione esperienza fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function updateExperience(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $updated = $this->content->updateExperience($id, $_POST);
            if ($updated) {
                Csrf::regenerate();
                flash('success', 'Esperienza aggiornata.');
            } else {
                flash('error', 'Nessuna esperienza aggiornata: controlla ID e valori.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Aggiornamento esperienza fallito.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function deleteExperience(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $deleted = $this->content->deleteExperience($id);
            if ($deleted) {
                Csrf::regenerate();
                flash('success', 'Esperienza eliminata.');
            } else {
                flash('error', 'Nessuna esperienza eliminata: ID non trovato.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Eliminazione esperienza fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function createSkill(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        try {
            $id = $this->content->createSkill($_POST);
            Csrf::regenerate();
            flash('success', 'Competenza creata con ID ' . $id . '.');
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Creazione competenza fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function updateSkill(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $updated = $this->content->updateSkill($id, $_POST);
            if ($updated) {
                Csrf::regenerate();
                flash('success', 'Competenza aggiornata.');
            } else {
                flash('error', 'Nessuna competenza aggiornata: controlla ID e valori.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Aggiornamento competenza fallito.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function deleteSkill(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $deleted = $this->content->deleteSkill($id);
            if ($deleted) {
                Csrf::regenerate();
                flash('success', 'Competenza eliminata.');
            } else {
                flash('error', 'Nessuna competenza eliminata: ID non trovato.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Eliminazione competenza fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function updateProfileContacts(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        try {
            $this->content->updateProfileContacts($_POST);
            Csrf::regenerate();
            flash('success', 'Profilo e contatti aggiornati.');
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Aggiornamento profilo/contatti fallito.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function apiExperiences(): void
    {
        $this->requireAuthenticatedUser();
        $this->json([
            'ok' => true,
            'data' => $this->content->listExperiences(),
        ]);
    }

    public function apiSkills(): void
    {
        $this->requireAuthenticatedUser();
        $this->json([
            'ok' => true,
            'data' => $this->content->listSkills(),
        ]);
    }

    public function apiMessages(): void
    {
        $this->requireAuthenticatedUser();
        $this->json([
            'ok' => true,
            'data' => $this->content->listContactMessages(100),
        ]);
    }

    public function markMessageRead(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $updated = $this->content->markMessageAsRead($id);
            if ($updated) {
                Csrf::regenerate();
                flash('success', 'Messaggio segnato come letto.');
            } else {
                flash('error', 'Messaggio non trovato.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Operazione fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    public function deleteMessage(): void
    {
        $this->requireAuthenticatedUser();
        $this->assertValidCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $deleted = $this->content->deleteMessage($id);
            if ($deleted) {
                Csrf::regenerate();
                flash('success', 'Messaggio eliminato.');
            } else {
                flash('error', 'Messaggio non trovato.');
            }
        } catch (Throwable $exception) {
            flash('error', $this->buildErrorMessage('Eliminazione messaggio fallita.', $exception));
        }

        redirect_to('admin/panel');
    }

    private function requireAuthenticatedUser(): array
    {
        $user = $this->auth->user();
        if (is_array($user)) {
            return $user;
        }

        flash('error', 'Sessione non valida, effettua di nuovo il login.');
        redirect_to('admin');
    }

    private function assertValidCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? null;
        $isValid = is_string($token) && Csrf::isValid($token);
        if ($isValid) {
            return;
        }

        http_response_code(419);
        echo 'CSRF token non valido.';
        exit;
    }

    private function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function buildErrorMessage(string $prefix, Throwable $exception): string
    {
        if ((bool) config('app.debug', false)) {
            return $prefix . ' ' . $exception->getMessage();
        }

        return $prefix;
    }
}
