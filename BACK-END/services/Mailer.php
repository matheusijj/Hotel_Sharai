<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
  private array $cfg;
  public function __construct(array $config){
    $this->cfg = $config['smtp'] ?? [];
  }

  public function isConfigured(): bool {
    return !empty($this->cfg['host']) && !empty($this->cfg['username']) && !empty($this->cfg['password']);
  }

  public function send(string $to, string $subject, string $htmlBody, string $textBody = ''): array {
    $result = ['sent' => false, 'error' => null];
    if (!$this->isConfigured()) { $result['error'] = 'SMTP não configurado'; return $result; }

    // Autoload do Composer (usar apenas BACK-END/vendor)
    $auto = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($auto)) { require_once $auto; }
    if (!class_exists(PHPMailer::class)) {
      $result['error'] = 'PHPMailer não encontrado. Instale via Composer';
      return $result;
    }

    $mail = new PHPMailer(true);
    try {
      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';
      if (method_exists($mail, 'setLanguage')) {
        $mail->setLanguage('pt_br');
      }
      $mail->addCustomHeader('Content-Language', 'pt-BR');
      $mail->isSMTP();
      $mail->Host = $this->cfg['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $this->cfg['username'];
      $mail->Password = $this->cfg['password'];
      $mail->Port = (int)($this->cfg['port'] ?? 587);
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      if (!empty($this->cfg['debug'])) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
      }

      $fromEmail = $this->cfg['from_email'] ?? $this->cfg['username'];
      $fromName = $this->cfg['from_name'] ?? 'Hotel';

      $mail->setFrom($fromEmail, $fromName);
      $mail->addAddress($to);
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = $textBody ?: strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $htmlBody));

      $mail->send();
      $result['sent'] = true;
      return $result;
    } catch (\Throwable $e) {
      $result['error'] = $e->getMessage();
      return $result;
    }
  }
}
