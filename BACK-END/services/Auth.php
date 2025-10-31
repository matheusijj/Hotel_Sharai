<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../models/User.php';

class Auth {
  public static function isLoggedIn(): bool { return !empty($_SESSION['user_id']); }
  public static function requireLogin(): void { if (!self::isLoggedIn()) { header('Location: login.php'); exit; } }
  public static function currentUser(?PDO $pdo = null): ?array {
    if (!self::isLoggedIn()) return null;
    if (!$pdo) {
      require __DIR__ . '/../db.php';
    }
    $userModel = new User($pdo);
    return $userModel->getById((int)$_SESSION['user_id']);
  }
}
