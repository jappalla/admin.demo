<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Servizio invio email per developer admin.
 * Usa le credenziali SMTP dal .env root (via parent autoload).
 */
final class MailService
{
    public function sendPasswordReset(string $to, string $token): bool
    {
        // Build reset URL using route_url() which handles basePath correctly
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'developer.testscript.info';
        $path = route_url('admin/reset-password');
        $resetUrl = $scheme . '://' . $host . $path . '?token=' . $token;

        $subject = 'Reset password — Developer Admin';
        $html = $this->template(
            'Reset Password',
            'Hai richiesto il reset della password per il pannello admin developer.',
            'Clicca il pulsante qui sotto per reimpostare la tua password. Il link scadrà tra 1 ora.',
            $resetUrl,
            'Reimposta Password'
        );

        $text = "Reset Password — Developer Admin\n\n"
            . "Hai richiesto il reset della password.\n"
            . "Vai su: {$resetUrl}\n\n"
            . "Il link scadrà tra 1 ora.\n\n— testscript.info";

        return $this->send($to, $subject, $html, $text);
    }

    private function send(string $to, string $subject, string $html, string $text): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = (string) ($_ENV['SMTP_HOST'] ?? '');
            $mail->SMTPAuth   = true;
            $mail->Username   = (string) ($_ENV['SMTP_USER'] ?? '');
            $mail->Password   = (string) ($_ENV['SMTP_PASS'] ?? '');
            $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? 465);
            $mail->CharSet    = 'UTF-8';
            $mail->isHTML(true);

            $secure = (string) ($_ENV['SMTP_SECURE'] ?? 'ssl');
            $mail->SMTPSecure = $secure === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom(
                (string) ($_ENV['SMTP_FROM_EMAIL'] ?? $_ENV['SMTP_USER'] ?? ''),
                (string) ($_ENV['SMTP_FROM_NAME'] ?? 'Testscript')
            );

            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[developer/MailService] Error: ' . $e->getMessage());
            return false;
        }
    }

    private function template(
        string $title,
        string $greeting,
        string $message,
        string $actionUrl,
        string $actionLabel
    ): string {
        return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;padding:0;background:#0a0a0a;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:40px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#171717;border-radius:12px;border:1px solid #262626;overflow:hidden;">
        <tr><td style="background:linear-gradient(135deg,#06b6d4,#8b5cf6);padding:24px 32px;">
          <h1 style="margin:0;font-size:22px;color:#fff;font-weight:700;">{$title}</h1>
        </td></tr>
        <tr><td style="padding:32px;">
          <p style="margin:0 0 16px;color:#d4d4d4;font-size:15px;line-height:1.6;">{$greeting}</p>
          <p style="margin:0 0 24px;color:#a3a3a3;font-size:14px;line-height:1.6;">{$message}</p>
          <table cellpadding="0" cellspacing="0"><tr><td style="background:linear-gradient(135deg,#06b6d4,#8b5cf6);border-radius:8px;">
            <a href="{$actionUrl}" style="display:inline-block;padding:12px 28px;color:#fff;text-decoration:none;font-size:14px;font-weight:600;">{$actionLabel}</a>
          </td></tr></table>
          <p style="margin:24px 0 0;color:#737373;font-size:12px;line-height:1.5;">Se non hai richiesto il reset, ignora questa email.</p>
        </td></tr>
        <tr><td style="padding:16px 32px;border-top:1px solid #262626;">
          <p style="margin:0;color:#525252;font-size:11px;text-align:center;">© testscript.info — Developer Admin</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
