<?php
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../services/Mailer.php';

if (!isset($_GET['public'])) {
  Auth::requireLogin();
}

$config = require __DIR__ . '/../config.php';
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
  $config['smtp']['debug'] = 1;
}
$mailer = new \App\Services\Mailer($config);

header('Content-Type: text/plain; charset=utf-8');

if (!$mailer->isConfigured()) {
  echo "SMTP não configurado em BACK-END/config.php -> chave 'smtp'";
  exit;
}

$to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL)
  ? $_GET['to']
  : 'matheusdafonsecagomes@gmail.com';

$subject = 'Teste de e-mail - Hotel';
$html = '<p>Este é um e-mail de teste enviado pelo sistema do Hotel.</p>';
$cfg = $config['smtp'];
echo "Host: {$cfg['host']}\nPort: {$cfg['port']}\nEncryption: {$cfg['encryption']}\nFrom: " . ($cfg['from_email'] ?? $cfg['username']) . "\nDebug: " . (!empty($cfg['debug']) ? 'on' : 'off') . "\n\n";
$res = $mailer->send($to, $subject, $html, 'Este é um e-mail de teste enviado pelo sistema do Hotel.');

if ($res['sent']) {
  echo "OK: e-mail enviado para {$to}";
} else {
  echo "ERRO ao enviar: " . ($res['error'] ?? 'desconhecido');
}
