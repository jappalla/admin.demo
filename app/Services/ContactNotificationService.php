<?php
declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class ContactNotificationService
{
    public function sendContactMessage(array $payload, int $messageId): void
    {
        $recipientEmail = trim((string) env('CONTACT_RECIPIENT_EMAIL', 'info.databrother@gmail.com'));
        $recipientName = trim((string) env('CONTACT_RECIPIENT_NAME', 'Antonio Trapasso'));
        $fromEmail = trim((string) env('CONTACT_SENDER_EMAIL', 'no-reply@testscript.info'));
        $fromName = trim((string) env('CONTACT_SENDER_NAME', 'Antonio Trapasso CV'));

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('CONTACT_RECIPIENT_EMAIL is not a valid email address.');
        }

        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('CONTACT_SENDER_EMAIL is not a valid email address.');
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $senderEmail = strtolower(trim((string) ($payload['email'] ?? '')));
        $subject = trim((string) ($payload['subject'] ?? ''));
        $message = trim((string) ($payload['message'] ?? ''));
        $submittedAt = date('Y-m-d H:i:s');

        if ($fullName === '' || $senderEmail === '' || $message === '') {
            throw new InvalidArgumentException('Contact payload is incomplete.');
        }

        if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Sender email is not valid.');
        }

        $safeSubject = $subject !== '' ? $subject : 'Richiesta dal form contatti';
        $ownerSubject = '[antonio.trapasso] ' . $safeSubject;
        $ownerPlainBody = $this->buildOwnerPlainBody(
            $messageId,
            $submittedAt,
            $fullName,
            $senderEmail,
            $safeSubject,
            $message
        );
        $ownerHtmlBody = $this->buildOwnerHtmlBody(
            $messageId,
            $submittedAt,
            $fullName,
            $senderEmail,
            $safeSubject,
            $message
        );

        $this->sendViaSmtp(
            toEmail: $recipientEmail,
            toName: $recipientName,
            fromEmail: $fromEmail,
            fromName: $fromName,
            replyToEmail: $senderEmail,
            replyToName: $fullName,
            subject: $ownerSubject,
            plainBody: $ownerPlainBody,
            htmlBody: $ownerHtmlBody,
            isAutoReply: false,
            messageId: $messageId
        );

        $autoReplyEnabled = env('CONTACT_AUTOREPLY_ENABLED', 'true');
        if (!in_array(strtolower(trim($autoReplyEnabled)), ['1', 'true', 'on', 'yes'], true)) {
            return;
        }

        $replyHours = max(1, (int) env('CONTACT_AUTOREPLY_RESPONSE_HOURS', '24'));
        $autoReplySubject = trim((string) env(
            'CONTACT_AUTOREPLY_SUBJECT',
            'Conferma ricezione del tuo messaggio'
        ));

        $autoReplyPlainBody = $this->buildAutoReplyPlainBody(
            $messageId,
            $submittedAt,
            $fullName,
            $safeSubject,
            $message,
            $replyHours
        );
        $autoReplyHtmlBody = $this->buildAutoReplyHtmlBody(
            $messageId,
            $submittedAt,
            $fullName,
            $safeSubject,
            $message,
            $replyHours
        );

        $this->sendViaSmtp(
            toEmail: $senderEmail,
            toName: $fullName,
            fromEmail: $fromEmail,
            fromName: $fromName,
            replyToEmail: $recipientEmail,
            replyToName: $recipientName,
            subject: $autoReplySubject,
            plainBody: $autoReplyPlainBody,
            htmlBody: $autoReplyHtmlBody,
            isAutoReply: true,
            messageId: $messageId
        );
    }

    private function sendViaSmtp(
        string $toEmail,
        string $toName,
        string $fromEmail,
        string $fromName,
        string $replyToEmail,
        string $replyToName,
        string $subject,
        string $plainBody,
        string $htmlBody,
        bool $isAutoReply,
        int $messageId
    ): void {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Recipient email is not valid.');
        }

        if (!filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Reply-to email is not valid.');
        }

        $smtpHost = trim((string) env('CONTACT_SMTP_HOST', (string) env('SMTP_HOST', '')));
        $smtpUser = trim((string) env('CONTACT_SMTP_USER', (string) env('SMTP_USER', '')));
        $smtpPass = trim((string) env('CONTACT_SMTP_PASS', (string) env('SMTP_PASS', '')));
        $smtpPort = (int) env('CONTACT_SMTP_PORT', (string) env('SMTP_PORT', '587'));
        $smtpSecure = strtolower(trim((string) env('CONTACT_SMTP_SECURE', (string) env('SMTP_SECURE', 'tls'))));
        $returnPath = trim((string) env('CONTACT_RETURN_PATH', $fromEmail));
        $smtpConfigured = $smtpHost !== '' && $smtpUser !== '' && $smtpPass !== '';
        $phpMailerReady = $this->loadPhpMailerAutoload();

        if ($smtpConfigured && $phpMailerReady) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                $mail->Password = $smtpPass;
                $mail->Port = $smtpPort > 0 ? $smtpPort : 587;
                $mail->SMTPAutoTLS = true;
                $mail->Timeout = 20;
                $mail->SMTPKeepAlive = false;
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                if ($smtpSecure === 'ssl' || $smtpSecure === 'smtps') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                }

                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($toEmail, $toName);
                $mail->addReplyTo($replyToEmail, $replyToName);
                if (filter_var($returnPath, FILTER_VALIDATE_EMAIL)) {
                    $mail->Sender = $returnPath;
                }

                $mail->Subject = $subject;
                $mail->Body = $htmlBody;
                $mail->AltBody = $plainBody;
                $mail->isHTML(true);

                $mail->addCustomHeader('X-Entity-Ref-ID', 'contact-' . $messageId);
                $mail->addCustomHeader('X-Priority', '3');

                if ($isAutoReply) {
                    $mail->addCustomHeader('Auto-Submitted', 'auto-replied');
                    $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');
                }

                $mail->send();
                return;
            } catch (Throwable $exception) {
                error_log('Contact SMTP send failed, fallback to native mail: ' . $exception->getMessage());
            }
        } else {
            if (!$smtpConfigured) {
                error_log('Contact SMTP not configured, fallback to native mail.');
            }
            if (!$phpMailerReady) {
                error_log('PHPMailer not available, fallback to native mail.');
            }
        }

        $this->sendWithNativeMail(
            toEmail: $toEmail,
            toName: $toName,
            fromEmail: $fromEmail,
            fromName: $fromName,
            replyToEmail: $replyToEmail,
            replyToName: $replyToName,
            returnPath: $returnPath,
            subject: $subject,
            plainBody: $plainBody,
            isAutoReply: $isAutoReply,
            messageId: $messageId
        );
    }

    private function sendWithNativeMail(
        string $toEmail,
        string $toName,
        string $fromEmail,
        string $fromName,
        string $replyToEmail,
        string $replyToName,
        string $returnPath,
        string $subject,
        string $plainBody,
        bool $isAutoReply,
        int $messageId
    ): void {
        $safeToName = $this->sanitizeHeaderValue($toName);
        $safeFromName = $this->sanitizeHeaderValue($fromName);
        $safeReplyName = $this->sanitizeHeaderValue($replyToName);

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $this->encodeHeader($safeFromName) . ' <' . $fromEmail . '>',
            'Reply-To: ' . $this->encodeHeader($safeReplyName) . ' <' . $replyToEmail . '>',
            'X-Entity-Ref-ID: contact-' . $messageId,
            'X-Priority: 3',
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        if ($isAutoReply) {
            $headers[] = 'Auto-Submitted: auto-replied';
            $headers[] = 'X-Auto-Response-Suppress: All';
        }

        $additionalParams = '';
        if (filter_var($returnPath, FILTER_VALIDATE_EMAIL)) {
            $additionalParams = '-f ' . $returnPath;
        }

        $target = $toEmail;
        if ($safeToName !== '') {
            $target = $this->encodeHeader($safeToName) . ' <' . $toEmail . '>';
        }

        $sent = $additionalParams !== ''
            ? @mail($target, $this->encodeHeader($subject), $plainBody, implode("\r\n", $headers), $additionalParams)
            : @mail($target, $this->encodeHeader($subject), $plainBody, implode("\r\n", $headers));

        if (!$sent) {
            throw new RuntimeException('Invio email non riuscito (SMTP e fallback mail() falliti).');
        }
    }

    private function buildOwnerPlainBody(
        int $messageId,
        string $submittedAt,
        string $fullName,
        string $senderEmail,
        string $subject,
        string $message
    ): string {
        return implode("\n", [
            'Nuovo messaggio ricevuto dal form contatti.',
            '',
            'Riferimento: #' . $messageId,
            'Data: ' . $submittedAt,
            'Nome: ' . $fullName,
            'Email: ' . $senderEmail,
            'Oggetto: ' . $subject,
            '',
            'Messaggio:',
            $message,
        ]);
    }

    private function buildOwnerHtmlBody(
        int $messageId,
        string $submittedAt,
        string $fullName,
        string $senderEmail,
        string $subject,
        string $message
    ): string {
        $safeMessage = nl2br($this->escapeHtml($message), false);

        return implode('', [
            '<!doctype html><html><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;color:#0f172a;">',
            '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">',
            '<tr><td align="center">',
            '<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">',
            '<tr><td style="background:#0f172a;padding:18px 24px;">',
            '<h1 style="margin:0;font-size:18px;line-height:1.4;color:#ffffff;">Nuovo messaggio dal sito</h1>',
            '</td></tr>',
            '<tr><td style="padding:24px;">',
            '<p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">Hai ricevuto una nuova richiesta dal form contatti.</p>',
            '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;line-height:1.6;color:#0f172a;">',
            '<tr><td style="padding:4px 0;width:140px;color:#475569;"><strong>Riferimento</strong></td><td style="padding:4px 0;">#' . $messageId . '</td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Data</strong></td><td style="padding:4px 0;">' . $this->escapeHtml($submittedAt) . '</td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Nome</strong></td><td style="padding:4px 0;">' . $this->escapeHtml($fullName) . '</td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Email</strong></td><td style="padding:4px 0;"><a href="mailto:' . $this->escapeHtml($senderEmail) . '" style="color:#0ea5e9;text-decoration:none;">' . $this->escapeHtml($senderEmail) . '</a></td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Oggetto</strong></td><td style="padding:4px 0;">' . $this->escapeHtml($subject) . '</td></tr>',
            '</table>',
            '<div style="margin-top:18px;padding:14px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">',
            '<p style="margin:0 0 8px;font-size:13px;color:#475569;"><strong>Messaggio</strong></p>',
            '<p style="margin:0;font-size:14px;line-height:1.7;color:#0f172a;">' . $safeMessage . '</p>',
            '</div>',
            '</td></tr>',
            '</table>',
            '</td></tr></table>',
            '</body></html>',
        ]);
    }

    private function buildAutoReplyPlainBody(
        int $messageId,
        string $submittedAt,
        string $fullName,
        string $subject,
        string $message,
        int $replyHours
    ): string {
        return implode("\n", [
            'Ciao ' . $fullName . ',',
            '',
            'grazie per avermi scritto. Ho ricevuto il tuo messaggio e ti rispondero entro circa ' . $replyHours . ' ore.',
            '',
            'Riferimento: #' . $messageId,
            'Data invio: ' . $submittedAt,
            'Oggetto: ' . $subject,
            '',
            'Copia del messaggio inviato:',
            $message,
            '',
            'Grazie,',
            'Antonio Trapasso',
        ]);
    }

    private function buildAutoReplyHtmlBody(
        int $messageId,
        string $submittedAt,
        string $fullName,
        string $subject,
        string $message,
        int $replyHours
    ): string {
        $safeMessage = nl2br($this->escapeHtml($message), false);

        return implode('', [
            '<!doctype html><html><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;color:#0f172a;">',
            '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">',
            '<tr><td align="center">',
            '<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">',
            '<tr><td style="background:#0f172a;padding:18px 24px;">',
            '<h1 style="margin:0;font-size:18px;line-height:1.4;color:#ffffff;">Conferma ricezione messaggio</h1>',
            '</td></tr>',
            '<tr><td style="padding:24px;">',
            '<p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#334155;">Ciao ' . $this->escapeHtml($fullName) . ',</p>',
            '<p style="margin:0 0 14px;font-size:14px;line-height:1.8;color:#334155;">ho ricevuto correttamente il tuo messaggio. Ti rispondero entro circa <strong>' . $replyHours . ' ore</strong>.</p>',
            '<div style="margin:16px 0;padding:14px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">',
            '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;line-height:1.6;color:#0f172a;">',
            '<tr><td style="padding:4px 0;width:140px;color:#475569;"><strong>Riferimento</strong></td><td style="padding:4px 0;">#' . $messageId . '</td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Data invio</strong></td><td style="padding:4px 0;">' . $this->escapeHtml($submittedAt) . '</td></tr>',
            '<tr><td style="padding:4px 0;color:#475569;"><strong>Oggetto</strong></td><td style="padding:4px 0;">' . $this->escapeHtml($subject) . '</td></tr>',
            '</table>',
            '</div>',
            '<p style="margin:0 0 8px;font-size:13px;color:#475569;"><strong>Copia del tuo messaggio</strong></p>',
            '<div style="padding:14px;border:1px solid #e2e8f0;border-radius:10px;background:#ffffff;">',
            '<p style="margin:0;font-size:14px;line-height:1.7;color:#0f172a;">' . $safeMessage . '</p>',
            '</div>',
            '<p style="margin:18px 0 0;font-size:14px;line-height:1.7;color:#334155;">Grazie,<br><strong>Antonio Trapasso</strong></p>',
            '</td></tr>',
            '</table>',
            '</td></tr></table>',
            '</body></html>',
        ]);
    }

    private function loadPhpMailerAutoload(): bool
    {
        if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            return true;
        }

        $documentRoot = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');

        $autoloadCandidates = [
            BASE_PATH . '/vendor/autoload.php',
            dirname(BASE_PATH) . '/vendor/autoload.php',
            dirname(BASE_PATH, 2) . '/vendor/autoload.php',
            ($documentRoot !== '' ? rtrim($documentRoot, '/\\') . '/vendor/autoload.php' : ''),
            ($documentRoot !== '' ? dirname(rtrim($documentRoot, '/\\')) . '/vendor/autoload.php' : ''),
        ];

        foreach ($autoloadCandidates as $autoloadPath) {
            if ($autoloadPath === '' || !is_file($autoloadPath)) {
                continue;
            }

            require_once $autoloadPath;
            if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                return true;
            }
        }

        $srcCandidates = [
            BASE_PATH . '/vendor/phpmailer/phpmailer/src',
            dirname(BASE_PATH) . '/vendor/phpmailer/phpmailer/src',
            dirname(BASE_PATH, 2) . '/vendor/phpmailer/phpmailer/src',
            ($documentRoot !== '' ? rtrim($documentRoot, '/\\') . '/vendor/phpmailer/phpmailer/src' : ''),
            ($documentRoot !== '' ? dirname(rtrim($documentRoot, '/\\')) . '/vendor/phpmailer/phpmailer/src' : ''),
        ];

        foreach ($srcCandidates as $srcPath) {
            if ($srcPath === '') {
                continue;
            }

            $exceptionFile = $srcPath . '/Exception.php';
            $smtpFile = $srcPath . '/SMTP.php';
            $mailerFile = $srcPath . '/PHPMailer.php';
            if (!is_file($exceptionFile) || !is_file($smtpFile) || !is_file($mailerFile)) {
                continue;
            }

            require_once $exceptionFile;
            require_once $smtpFile;
            require_once $mailerFile;

            if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                return true;
            }
        }

        return false;
    }

    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function sanitizeHeaderValue(string $value): string
    {
        return str_replace(["\r", "\n"], '', trim($value));
    }

    private function encodeHeader(string $value): string
    {
        $safe = $this->sanitizeHeaderValue($value);
        if ($safe === '') {
            return '';
        }

        return mb_encode_mimeheader($safe, 'UTF-8', 'B', "\r\n");
    }
}
