<?php
$auto = __DIR__ . '/vendor/autoload.php';
if (file_exists($auto)) { require_once $auto; }

if (class_exists(\Dotenv\Dotenv::class)) {
  $dotenvLoaded = false;
  $paths = [__DIR__, dirname(__DIR__)];
  foreach ($paths as $path) {
    if (file_exists($path . '/.env')) {
      $dotenv = Dotenv\Dotenv::createImmutable($path);
      $dotenv->load();
      $dotenvLoaded = true;
      break;
    }
  }
}

$env = function(string $key, $default = null) {
  if (array_key_exists($key, $_ENV)) return $_ENV[$key];
  if (array_key_exists($key, $_SERVER)) return $_SERVER[$key];
  $v = getenv($key);
  return ($v !== false && $v !== null) ? $v : $default;
};

return [
  'db_host' => '127.0.0.1',
  'db_name' => 'Hotel',
  'db_user' => 'root',
  'db_pass' => '',
  'smtp' => [
    'host' => $env('MAIL_HOST', 'smtp.gmail.com'),
    'port' => (int)$env('MAIL_PORT', 587),
    'username' => (string)$env('MAIL_USERNAME', ''),
    'password' => (string)$env('MAIL_PASSWORD', ''),
    'encryption' => 'tls',
    'from_email' => (string)($env('MAIL_FROM') ?: $env('MAIL_USERNAME', '')),
    'from_name' => (string)$env('MAIL_FROM_NAME', 'Sistema de Reservas'),
  ],
];